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

        // ✅ PERBAIKAN: Mengambil nama material unik secara langsung dari kolom yang ada
        $materials = Item::select('nama_material')->distinct()->pluck('nama_material');

        $rows = new Collection();

        foreach ($materials as $material) {
            // Ambil item sesuai filter tanggal dan nama material
            $items = Item::where('nama_material', $material)
                ->when($start, fn($q) => $q->whereDate('created_at', '>=', $start))
                ->when($end, fn($q) => $q->whereDate('created_at', '<=', $end))
                ->get();

            // ✅ PERBAIKAN: Menggunakan kategori_material yang tersimpan di database
            $calculateStock = function ($collection) {
                $stock = ['Baru' => 0, 'Baik' => 0, 'Rusak' => 0, 'Afkir' => 0];
                foreach ($collection as $item) {
                    $kategori = $item->kategori_material;
                    if (array_key_exists($kategori, $stock)) {
                        $stock[$kategori] += $item->stok_akhir;
                    }
                }
                return $stock;
            };

            $pusatStock = $calculateStock($items->whereNull('facility_id'));
            $fasilitasStock = $calculateStock($items->whereNotNull('facility_id'));

            $capacity = MaterialCapacity::where('material_base_name', $material)->value('capacity') ?? '-';
            $lastUpdate = $items->max('updated_at');

            // Baris untuk Gudang Region
            $rows->push([
                'material' => $material,
                'gudang' => 'Gudang Region',
                'baru' => $pusatStock['Baru'],
                'baik' => $pusatStock['Baik'],
                'rusak' => $pusatStock['Rusak'],
                'afkir' => $pusatStock['Afkir'],
                'layak_edar' => $pusatStock['Baru'] + $pusatStock['Baik'],
                'capacity' => $capacity,
                'updated_at' => $lastUpdate,
            ]);

            // Baris untuk SPBE/BPT (Global)
            $rows->push([
                'material' => $material,
                'gudang' => 'SPBE/BPT (Global)',
                'baru' => $fasilitasStock['Baru'],
                'baik' => $fasilitasStock['Baik'],
                'rusak' => $fasilitasStock['Rusak'],
                'afkir' => $fasilitasStock['Afkir'],
                'layak_edar' => $fasilitasStock['Baru'] + $fasilitasStock['Baik'],
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
