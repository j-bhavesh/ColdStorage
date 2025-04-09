<?php

namespace App\Exports;

use App\Models\StorageLoading;

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

class StorageLoadingReportExport implements FromCollection, WithHeadings, WithStyles, WithMapping, WithCustomStartCell
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
        $query = StorageLoading::with([
            'agreement.farmer',
            'agreement.seedVariety',
            'transporter',
            'vehicle',
            'coldStorage',
            'agreement.farmerUser'
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
            'Farmer Id',
            'Farmer Name',
            'Village',
            'Phone',
            'Seed Verity',
            'RST No.',
            'Transporter',
            'Vehicle No.',
            'Cold Storage',
            'Received Bags',
            'Pending Bags',
            'Extra Bags',
            'Net WT',
        ];
    }

    public function map($storageLoading): array
    {
        $receivedBgs = $storageLoading->bag_quantity ?? 0;
        $pendingBgs = $storageLoading->pending_bags ?? 0;
        $surplusBgs = $storageLoading->extra_bags ?? 0;

        return[
            $storageLoading->created_at ? $storageLoading->created_at->format(env('DATE_FORMATE')) : '',
            $storageLoading->agreement->farmer->farmer_id ?? '',
            $storageLoading->agreement->farmer->name ?? '',
            $storageLoading->agreement->farmer->village_name ?? '',
            $storageLoading->agreement->farmerUser->phone ?? '',
            $storageLoading->agreement->seedVariety->name ?? '',
            $storageLoading->rst_number ?? '',
            $storageLoading->transporter->name ?? '',
            $storageLoading->vehicle_number ?? '',
            $storageLoading->coldStorage->name ?? '',
            number_format($receivedBgs, 0),
            number_format($pendingBgs, 0),
            number_format($surplusBgs, 0),
            number_format($storageLoading->net_weight ?? 0, 2)
        ];
    }

    // Make headers bold
    public function styles(Worksheet $sheet)
    {
        // Bold header row (row 1 since no logo now)
        $sheet->getStyle('A1:N1')->getFont()->setBold(true);

        // Align numeric fields to the right
        $numericColsRight = ['I', 'G'];

        // foreach ($numericColsRight as $col) {
        //     $sheet->getStyle($col)->getAlignment()->setHorizontal('right');
        // }

        return [];
    }

    // Start table from row 6 (leaving space for logo)
    public function startCell(): string
    {
        return 'A1';
    }
}
