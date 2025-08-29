<?php

namespace App\Exports;

use App\Models\Item;
use App\Models\ItemTransaction;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class PusatDataExport implements FromQuery, WithHeadings, WithMapping
{
    protected $filters;

    // Terima filter dari controller
    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    /**
     * Mendefinisikan judul kolom di file Excel.
     */
    public function headings(): array
    {
        return [
            'Kode Material',
            'Nama Material',
            'Stok Awal',
            'Total Penerimaan',
            'Total Penyaluran',
            'Stok Akhir',
            'Update Terakhir'
        ];
    }

    /**
     * Memetakan data dari setiap item ke baris Excel.
     */
    public function map($item): array
    {
        // Hitung stok akhir secara manual karena subquery tidak bisa langsung di-map
        $stokAkhir = $item->stok_awal + $item->penerimaan_total - $item->penyaluran_total;

        return [
            $item->kode_material,
            $item->nama_material,
            $item->stok_awal,
            $item->penerimaan_total,
            $item->penyaluran_total,
            $stokAkhir, // Gunakan hasil kalkulasi
            Carbon::parse($item->updated_at)->format('d-m-Y H:i:s'),
        ];
    }

    /**
     * Query utama untuk export.
     *
     * @return Builder
     */
    public function query(): Builder
    {
        $filters = $this->filters;

        $query = Item::query()
            ->whereNull('facility_id') // hanya item pusat
            ->select('items.*');

        // Filter pencarian
        $query->when($filters['search'], function ($q, $search) {
            return $q->where(function ($subQ) use ($search) {
                $subQ->where('nama_material', 'like', '%' . $search . '%')
                    ->orWhere('kode_material', 'like', '%' . $search . '%');
            });
        });

        // Filter tanggal
        $query->when($filters['start_date'] || $filters['end_date'], function ($q) use ($filters) {
            $q->where(function ($sub) use ($filters) {
                $sub->whereHas('transactions', function ($subQ) use ($filters) {
                    if ($filters['start_date']) {
                        $subQ->whereDate('created_at', '>=', $filters['start_date']);
                    }
                    if ($filters['end_date']) {
                        $subQ->whereDate('created_at', '<=', $filters['end_date']);
                    }
                });

                if ($filters['start_date']) {
                    $sub->orWhereDate('items.updated_at', '>=', $filters['start_date']);
                }
                if ($filters['end_date']) {
                    $sub->whereDate('items.updated_at', '<=', $filters['end_date']);
                }
            });
        });

        // Subquery kalkulasi
        $query->addSelect([
            'penerimaan_total' => ItemTransaction::query()
                ->join('items as source_item', 'item_transactions.item_id', '=', 'source_item.id')
                ->whereColumn('source_item.kode_material', 'items.kode_material')
                ->whereColumn('item_transactions.region_to', 'items.region_id')
                ->when(isset($filters['start_date']) && $filters['start_date'], function ($subQ, $date) {
                    $subQ->whereDate('item_transactions.created_at', '>=', $date);
                })
                ->when(isset($filters['end_date']) && $filters['end_date'], function ($subQ, $date) {
                    $subQ->whereDate('item_transactions.created_at', '<=', $date);
                })
                ->selectRaw('COALESCE(SUM(item_transactions.jumlah), 0)'),

            'penyaluran_total' => ItemTransaction::selectRaw('COALESCE(SUM(jumlah), 0)')
                ->whereColumn('item_id', 'items.id')
                ->when(isset($filters['start_date']) && $filters['start_date'], function ($subQ, $date) {
                    $subQ->whereDate('created_at', '>=', $date);
                })
                ->when(isset($filters['end_date']) && $filters['end_date'], function ($subQ, $date) {
                    $subQ->whereDate('created_at', '<=', $date);
                }),
        ]);

        // Urutkan by updated_at
        return $query->latest('updated_at');
    }
}
