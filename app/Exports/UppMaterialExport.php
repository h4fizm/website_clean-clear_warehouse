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
                'item_transactions.no_surat_persetujuan',
                'item_transactions.tahapan',
                'item_transactions.status',
                'item_transactions.created_at',
                'item_transactions.updated_at',
                'item_transactions.keterangan_transaksi',
                'item_transactions.tanggal_pemusnahan',
                'item_transactions.aktivitas_pemusnahan',
                'item_transactions.user_id',
                'item_transactions.item_id',
                DB::raw('GROUP_CONCAT(CONCAT(items.nama_material, " (", item_transactions.jumlah, " pcs)")) as materials')
            )
            ->join('items', 'item_transactions.item_id', '=', 'items.id')
            ->where('jenis_transaksi', 'pemusnahan');

        // PERBAIKAN: Tambahkan 'item_transactions.' ke kolom 'created_at'
        $query->when($filters['start_date'], function ($q, $date) {
            return $q->whereDate('item_transactions.created_at', '>=', $date);
        });

        // PERBAIKAN: Tambahkan 'item_transactions.' ke kolom 'created_at'
        $query->when($filters['end_date'], function ($q, $date) {
            return $q->whereDate('item_transactions.created_at', '<=', $date);
        });

        // PERBAIKAN: Tambahkan semua kolom non-agregat ke klausa GROUP BY
        $query->groupBy(
            'item_transactions.no_surat_persetujuan',
            'item_transactions.tahapan',
            'item_transactions.status',
            'item_transactions.created_at',
            'item_transactions.updated_at',
            'item_transactions.keterangan_transaksi',
            'item_transactions.tanggal_pemusnahan',
            'item_transactions.aktivitas_pemusnahan',
            'item_transactions.user_id',
            'item_transactions.item_id'
        );

        // PERBAIKAN: Tambahkan 'item_transactions.' ke kolom 'created_at'
        return $query->latest('item_transactions.created_at');
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
            'User Penanggung Jawab',
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
            $row->user->name ?? 'N/A',
            Carbon::parse($row->updated_at)->locale('id')->translatedFormat('l, d F Y')
        ];
    }
}