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

    public function headings(): array
    {
        return [
            'Tanggal Transaksi',
            'Aktivitas',
            'Kode Material',
            'Nama Material',
            'Lokasi Asal',
            'Lokasi Tujuan',
            'Stok Awal Asal',
            'Jumlah',
            'Stok Akhir Asal',
            'User PJ',
            'No. Surat Persetujuan',
            'No. BA Serah Terima',
        ];
    }

    public function map($transaction): array
    {
        $asal = $transaction->facilityFrom->name ?? $transaction->regionFrom->name_region ?? 'N/A';
        $tujuan = $transaction->facilityTo->name ?? $transaction->regionTo->name_region ?? 'N/A';

        // Menentukan jenis aktivitas
        $aktivitas = 'N/A';
        if ($transaction->region_to || $transaction->facility_to) {
            $aktivitas = 'Penerimaan';
        } elseif ($transaction->region_from || $transaction->facility_from) {
            $aktivitas = 'Penyaluran';
        }

        return [
            Carbon::parse($transaction->created_at)->format('d-m-Y H:i:s'),
            $aktivitas,
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
        ];
    }

    /**
     * Query utama untuk export.
     *
     * @return Builder
     */
    public function query(): Builder
    {
        $search = $this->filters['search'];
        $startDate = $this->filters['start_date'];
        $endDate = $this->filters['end_date'];

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
                    ->orWhere('no_ba_serah_terima', 'like', "%{$search}%");

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

        $query->when($startDate, fn($q, $date) => $q->whereDate('created_at', '>=', $date));
        $query->when($endDate, fn($q, $date) => $q->whereDate('created_at', '<=', $date));

        return $query->latest('created_at');
    }
}
