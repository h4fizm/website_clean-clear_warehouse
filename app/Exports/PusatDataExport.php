<?php

namespace App\Exports;

use App\Models\Item;
use App\Models\ItemTransaction;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class PusatDataExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
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
        // UBAH INI: Tambahkan kolom "Total Sales" dan "Total Pemusnahan"
        return [
            'Kode Material',
            'Nama Material',
            'Stok Awal',
            'Total Penerimaan',
            'Total Penyaluran',
            'Total Sales',
            'Total Pemusnahan',
            'Stok Akhir',
            'Update Terakhir'
        ];
    }

    /**
     * Memetakan data dari setiap item ke baris Excel.
     */
    public function map($item): array
    {
        // UBAH INI: Sesuaikan perhitungan stok akhir dengan menyertakan sales dan pemusnahan
        $stokAkhir = $item->stok_awal + $item->penerimaan_total - $item->penyaluran_total - $item->sales_total - $item->pemusnahan_total;

        // UBAH INI: Tambahkan data sales_total dan pemusnahan_total ke baris Excel
        return [
            $item->kode_material,
            $item->nama_material,
            $item->stok_awal,
            $item->penerimaan_total,
            $item->penyaluran_total,
            $item->sales_total,
            $item->pemusnahan_total,
            $stokAkhir, // Gunakan hasil kalkulasi baru
            Carbon::parse($item->updated_at)->locale('id')->translatedFormat('l, d F Y'),
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

        // Filter tanggal (sudah benar, tidak perlu diubah)
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

        // UBAH INI: Tambahkan subquery untuk `sales_total` dan `pemusnahan_total`
        $query->addSelect([
            'penerimaan_total' => ItemTransaction::query()
                ->join('items as source_item', 'item_transactions.item_id', '=', 'source_item.id')
                ->whereColumn('source_item.kode_material', 'items.kode_material')
                ->whereColumn('item_transactions.region_to', 'items.region_id')
                ->when($filters['start_date'], function ($subQ, $date) {
                    $subQ->whereDate('item_transactions.created_at', '>=', $date);
                })
                ->when($filters['end_date'], function ($subQ, $date) {
                    $subQ->whereDate('item_transactions.created_at', '<=', $date);
                })
                ->selectRaw('COALESCE(SUM(item_transactions.jumlah), 0)'),

            'penyaluran_total' => ItemTransaction::selectRaw('COALESCE(SUM(jumlah), 0)')
                ->whereColumn('item_id', 'items.id')
                ->where('jenis_transaksi', 'transfer') // Penyaluran adalah transfer keluar
                ->when($filters['start_date'], function ($subQ, $date) {
                    $subQ->whereDate('created_at', '>=', $date);
                })
                ->when($filters['end_date'], function ($subQ, $date) {
                    $subQ->whereDate('created_at', '<=', $date);
                }),

            // TAMBAHKAN INI: Subquery untuk menghitung total sales
            'sales_total' => ItemTransaction::selectRaw('COALESCE(SUM(jumlah), 0)')
                ->whereColumn('item_id', 'items.id')
                ->where('jenis_transaksi', 'sales')
                ->when($filters['start_date'], function ($subQ, $date) {
                    $subQ->whereDate('created_at', '>=', $date);
                })
                ->when($filters['end_date'], function ($subQ, $date) {
                    $subQ->whereDate('created_at', '<=', $date);
                }),

            // TAMBAHKAN INI: Subquery untuk menghitung total pemusnahan
            'pemusnahan_total' => ItemTransaction::selectRaw('COALESCE(SUM(jumlah), 0)')
                ->whereColumn('item_id', 'items.id')
                ->where('jenis_transaksi', 'pemusnahan')
                ->when($filters['start_date'], function ($subQ, $date) {
                    $subQ->whereDate('created_at', '>=', $date);
                })
                ->when($filters['end_date'], function ($subQ, $date) {
                    $subQ->whereDate('created_at', '<=', $date);
                }),
        ]);

        // Urutkan by updated_at
        return $query->latest('updated_at');
    }
}
