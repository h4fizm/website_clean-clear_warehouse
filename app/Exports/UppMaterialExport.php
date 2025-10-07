<?php

namespace App\Exports;

use App\Models\ItemTransaction;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class UppMaterialExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $filters;

    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    /**
     * @return Builder
     */
    public function query(): Builder
    {
        $filters = $this->filters;

        $query = ItemTransaction::with(['item', 'user'])
            ->select(
                'base_transactions.no_surat_persetujuan',
                'base_transactions.tahapan',
                'base_transactions.status',
                'base_transactions.created_at',
                'base_transactions.updated_at',
                'base_transactions.keterangan_transaksi',
                'pemusnahan_transactions.tanggal_pemusnahan',
                'pemusnahan_transactions.aktivitas_pemusnahan',
                'pemusnahan_transactions.penanggungjawab', // ✅ FIXED: Ambil dari tabel pemusnahan_transactions
                'base_transactions.user_id',
                'base_transactions.item_id',
                DB::raw('GROUP_CONCAT(CONCAT(items.nama_material, " (", base_transactions.jumlah, " pcs)") SEPARATOR "\\n") as materials') // ✅ FIXED: Gunakan base_transactions
            )
            ->leftJoin('pemusnahan_transactions', 'base_transactions.id', '=', 'pemusnahan_transactions.base_transaction_id')
            ->join('items', 'base_transactions.item_id', '=', 'items.id')
            ->where('base_transactions.jenis_transaksi', 'pemusnahan');

        // Menggunakan klausa havingRaw agar filter diterapkan setelah GROUP BY
        $query->when($filters['start_date'], function ($q, $date) {
            return $q->havingRaw('MIN(base_transactions.created_at) >= ?', [$date]);
        });

        $query->when($filters['end_date'], function ($q, $date) {
            return $q->havingRaw('MIN(base_transactions.created_at) <= ?', [$date]);
        });

        // PERBAIKAN: Tambahkan semua kolom non-agregat ke klausa GROUP BY
        $query->groupBy(
            'base_transactions.no_surat_persetujuan',
            'base_transactions.tahapan',
            'base_transactions.status',
            'base_transactions.created_at',
            'base_transactions.updated_at',
            'base_transactions.keterangan_transaksi',
            'pemusnahan_transactions.tanggal_pemusnahan',
            'pemusnahan_transactions.aktivitas_pemusnahan',
            'base_transactions.user_id',
            'base_transactions.item_id',
            'pemusnahan_transactions.penanggungjawab'
        );

        return $query->latest('base_transactions.created_at');
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'No. Surat',
            'Tahapan',
            'Status',
            'Tanggal Pengajuan',
            'Tanggal Pemusnahan',
            'Aktivitas Pemusnahan',
            'Keterangan',
            'Daftar Material (Jumlah)',
            'Penanggung Jawab', // ✅ PERBAIKAN: Ubah nama heading
            'Tanggal Terakhir Update'
        ];
    }

    /**
     * @param mixed $row
     * @return array
     */
    public function map($row): array
    {
        return [
            $row->no_surat_persetujuan,
            $row->tahapan,
            ucfirst($row->status),
            Carbon::parse($row->created_at)->locale('id')->translatedFormat('l, d F Y'),
            $row->tanggal_pemusnahan ? Carbon::parse($row->tanggal_pemusnahan)->locale('id')->translatedFormat('l, d F Y') : '-',
            strip_tags($row->aktivitas_pemusnahan),
            strip_tags($row->keterangan_transaksi),
            $row->materials,
            $row->penanggungjawab, // ✅ FIXED: Gunakan kolom penanggungjawab
            Carbon::parse($row->updated_at)->locale('id')->translatedFormat('l, d F Y')
        ];
    }
}