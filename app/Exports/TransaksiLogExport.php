<?php

namespace App\Exports;

use App\Models\ItemTransaction;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class TransaksiLogExport implements FromQuery, WithHeadings, WithMapping
{
    protected $filters;

    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    /**
     * ✅ FUNGSI DIPERBARUI: Menghapus kolom 'Aktivitas' dan menambahkan 'Aktivitas Asal' & 'Aktivitas Tujuan'.
     */
    public function headings(): array
    {
        return [
            'Tanggal Transaksi',
            'Kode Material',
            'Nama Material',
            'Lokasi Asal',
            'Lokasi Tujuan / Tujuan Sales',
            'Stok Awal Asal',
            'Jumlah',
            'Stok Akhir Asal',
            'User PJ',
            'No. Surat Persetujuan',
            'No. BA Serah Terima',
            'Aktivitas Asal',      // <-- KOLOM BARU
            'Aktivitas Tujuan',    // <-- KOLOM BARU
        ];
    }

    /**
     * ✅ FUNGSI DIPERBARUI: Logika mapping kini menghasilkan data untuk dua kolom aktivitas.
     */
    public function map($transaction): array
    {
        $asal = $transaction->facilityFrom->name ?? $transaction->regionFrom->name_region ?? 'N/A';
        $tujuan = 'N/A';
        $aktivitasAsal = 'N/A';
        $aktivitasTujuan = 'N/A';

        if ($transaction->jenis_transaksi == 'sales') {
            $tujuan = $transaction->tujuan_sales;
            $aktivitasAsal = 'Penyaluran';
            $aktivitasTujuan = 'Transaksi Sales';
        } else { // Asumsikan sisanya adalah transfer
            $tujuan = $transaction->facilityTo->name ?? $transaction->regionTo->name_region ?? 'N/A';
            $aktivitasAsal = 'Penyaluran';
            $aktivitasTujuan = 'Penerimaan';
        }

        return [
            Carbon::parse($transaction->created_at)->format('d-m-Y H:i:s'),
            $transaction->item->kode_material ?? 'N/A',
            $transaction->item->nama_material ?? 'N/A',
            $asal,
            $tujuan,
            $transaction->stok_awal_asal ?? 0,
            $transaction->jumlah,
            $transaction->stok_akhir_asal ?? 0,
            $transaction->user->name ?? 'N/A',
            $transaction->no_surat_persetujuan ?? '-',
            $transaction->no_ba_serah_terima ?? '-',
            $aktivitasAsal,      // <-- DATA BARU
            $aktivitasTujuan,    // <-- DATA BARU
        ];
    }

    /**
     * Query utama untuk export.
     *
     * @return Builder
     */
    public function query(): Builder
    {
        // Ambil filter, pastikan ada nilai default null jika tidak ada
        $search = $this->filters['search'] ?? null;
        $startDate = $this->filters['start_date'] ?? null;
        $endDate = $this->filters['end_date'] ?? null;
        $jenisTransaksi = $this->filters['jenis_transaksi'] ?? null;

        $query = ItemTransaction::with([
            'item',
            'user',
            'facilityFrom',
            'facilityTo',
            'regionFrom',
            'regionTo'
        ]);

        $query->when($search, function ($q) use ($search) {
            $q->where(function ($subQuery) use ($search) {
                $subQuery->orWhere('no_surat_persetujuan', 'like', "%{$search}%")
                    ->orWhere('no_ba_serah_terima', 'like', "%{$search}%")
                    ->orWhere('tujuan_sales', 'like', "%{$search}%");

                $subQuery->orWhereHas('item', function ($itemQuery) use ($search) {
                    $itemQuery->where('nama_material', 'like', "%{$search}%")
                        ->orWhere('kode_material', 'like', "%{$search}%");
                });

                $subQuery->orWhereHas('user', fn($q) => $q->where('name', 'like', "%{$search}%"));
                $subQuery->orWhereHas('facilityFrom', fn($q) => $q->where('name', 'like', "%{$search}%"));
                $subQuery->orWhereHas('facilityTo', fn($q) => $q->where('name', 'like', "%{$search}%"));
                $subQuery->orWhereHas('regionFrom', fn($q) => $q->where('name_region', 'like', "%{$search}%"));
                $subQuery->orWhereHas('regionTo', fn($q) => $q->where('name_region', 'like', "%{$search}%"));
            });
        });

        // Terapkan filter jenis transaksi
        $query->when($jenisTransaksi, function ($q, $jenis) {
            return $q->where('jenis_transaksi', $jenis);
        });

        $query->when($startDate, fn($q, $date) => $q->whereDate('created_at', '>=', $date));
        $query->when($endDate, fn($q, $date) => $q->whereDate('created_at', '<=', $date));

        return $query->latest('created_at');
    }
}

