<?php

namespace App\Exports;

use App\Models\Item;
use App\Models\ItemTransaction;
use App\Models\MaterialCapacity;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AllMaterialStockExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $filters;
    protected $rowIndex = 0;

    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    public function headings(): array
    {
        return [
            'Material',
            'Gudang',
            'Baru',
            'Baik',
            'Rusak',
            'Afkir',
            'Layak Edar (Baru+Baik)',
            'Kapasitas',
            'Update Terakhir'
        ];
    }

    public function collection()
    {
        $start = $this->filters['start_date'] ?? null;
        $end = $this->filters['end_date'] ?? null;

        // Ambil semua material unik
        $materials = Item::selectRaw("TRIM(SUBSTRING_INDEX(nama_material, '-', 1)) as base_name")
            ->distinct()
            ->pluck('base_name');

        $rows = new Collection();

        foreach ($materials as $material) {
            // Ambil item sesuai filter tanggal
            $items = Item::where('nama_material', 'like', $material . '%')
                ->when($start, fn($q) => $q->whereDate('created_at', '>=', $start))
                ->when($end, fn($q) => $q->whereDate('created_at', '<=', $end))
                ->get();

            // Hitung stok meski kosong
            $calculateStock = function ($collection) {
                $stock = ['baru' => 0, 'baik' => 0, 'rusak' => 0, 'afkir' => 0];
                foreach ($collection as $item) {
                    if (str_contains($item->nama_material, 'Baru'))
                        $stock['baru'] += $item->stok_akhir;
                    if (str_contains($item->nama_material, 'Baik'))
                        $stock['baik'] += $item->stok_akhir;
                    if (str_contains($item->nama_material, 'Rusak'))
                        $stock['rusak'] += $item->stok_akhir;
                    if (str_contains($item->nama_material, 'Afkir'))
                        $stock['afkir'] += $item->stok_akhir;
                }
                return $stock;
            };

            $pusatStock = $calculateStock($items->whereNull('facility_id'));
            $fasilitasStock = $calculateStock($items->whereNotNull('facility_id'));

            $capacity = MaterialCapacity::where('material_base_name', $material)->value('capacity') ?? '-';
            $lastUpdate = $items->max('updated_at');

            // Tetap tampilkan meskipun semua stok 0
            $rows->push([
                'material' => $material,
                'gudang' => 'Gudang Region',
                'baru' => $pusatStock['baru'],
                'baik' => $pusatStock['baik'],
                'rusak' => $pusatStock['rusak'],
                'afkir' => $pusatStock['afkir'],
                'layak_edar' => $pusatStock['baru'] + $pusatStock['baik'],
                'capacity' => $capacity,
                'updated_at' => $lastUpdate,
            ]);

            $rows->push([
                'material' => $material,
                'gudang' => 'SPBE/BPT (Global)',
                'baru' => $fasilitasStock['baru'],
                'baik' => $fasilitasStock['baik'],
                'rusak' => $fasilitasStock['rusak'],
                'afkir' => $fasilitasStock['afkir'],
                'layak_edar' => $fasilitasStock['baru'] + $fasilitasStock['baik'],
                'capacity' => $capacity,
                'updated_at' => $lastUpdate,
            ]);

            // Tambahkan baris kosong antar material
            $rows->push([
                'material' => '',
                'gudang' => '',
                'baru' => '',
                'baik' => '',
                'rusak' => '',
                'afkir' => '',
                'layak_edar' => '',
                'capacity' => '',
                'updated_at' => ''
            ]);
        }

        return $rows;
    }


    public function map($row): array
    {
        return [
            $row['material'],
            $row['gudang'],
            $row['baru'],
            $row['baik'],
            $row['rusak'],
            $row['afkir'],
            $row['layak_edar'],
            $row['capacity'],
            $row['updated_at']
            ? Carbon::parse($row['updated_at'])->translatedFormat('d F Y H:i:s')
            : '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]], // Header tebal
        ];
    }
}
