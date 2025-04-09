<?php

namespace App\Exports;

use App\Models\StorageUnloading;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class StorageUnLoadingReportExport implements FromCollection, WithHeadings, WithStyles, WithMapping, WithCustomStartCell
{
    protected $financialYear;

    public function __construct($financialYear = null)
    {
        $this->financialYear = $financialYear;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $query = StorageUnloading::with([
            'unloadingCompany',
            'coldStorage',
            'transporter',
            'vehicle',
            'seedVariety'
        ]);

        if (!empty($this->financialYear)) {
            $startDate = \Carbon\Carbon::parse($this->financialYear['startDate'])->startOfDay();
            $endDate = \Carbon\Carbon::parse($this->financialYear['endDate'])->endOfDay();

            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'Distribution Date',
            'Company',
            'Cold Storage',
            'Transporter',
            'Vehicle No.',
            'Seed Verity',
            'RST No.',
            'Chamber No.',
            'Bag Quantity',
            'Weight',
            'Location',
        ];
    }

    public function map($storageUnloading): array
    {
        // dd($storageUnloading);
        $bagQty = $storageUnloading->bag_quantity ?? 0;
        // $pendingBgs = $storageUnloading->pending_bags ?? 0;
        // $surplusBgs = $storageUnloading->extra_bags ?? 0;

        return[
            $storageUnloading->created_at ? $storageUnloading->created_at->format(env('DATE_FORMATE')) : '',
            $storageUnloading->unloadingCompany->name ?? '',
            $storageUnloading->coldStorage->name ?? '',
            $storageUnloading->transporter->name ?? '',
            $storageUnloading->vehicle_number ?? '',
            $storageUnloading->seedVariety->name ?? '',
            $storageUnloading->rst_no ?? '',
            $storageUnloading->chamber_no ?? '',
            number_format($bagQty, 0),
            number_format($storageUnloading->weight ?? 0, 2),
            $storageUnloading->location ?? ''
        ];
    }

     // Make headers bold
    public function styles(Worksheet $sheet)
    {
        // Bold header row (row 1 since no logo now)
        $sheet->getStyle('A1:K1')->getFont()->setBold(true);

        // Align numeric fields to the right
        $numericColsRight = ['I', 'J'];

        foreach ($numericColsRight as $col) {
            $sheet->getStyle($col)->getAlignment()->setHorizontal('right');
        }

        return [];
    }

    // Start table from row 6 (leaving space for logo)
    public function startCell(): string
    {
        return 'A1';
    }
}
