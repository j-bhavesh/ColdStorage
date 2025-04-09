<?php

namespace App\Exports;

use App\Models\StorageLoading;
use App\Models\Farmer;
use App\Models\StorageUnloading;
use App\Models\Agreement;
use App\Models\SeedVariety;
use App\Models\Transporter;
use App\Models\Vehicle;
use App\Models\ColdStorage;
use App\Models\UnloadingCompany;
use App\Models\Challan;
use App\Models\AdvancePayment;
use App\Models\PackagingDistribution;
use App\Models\SeedDistribution;

use Illuminate\Http\Request;
use Carbon\Carbon;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class ProcessingReportExport implements FromCollection, WithHeadings, WithStyles, WithMapping, WithCustomStartCell
{
    protected $storageLoadings;
    protected $totals;
    protected $selectedFarmer;
    protected $request;

    public function __construct($storageLoadings, $totals, $selectedFarmer, $request)
    {
        $this->storageLoadings = $storageLoadings;
        $this->totals = $totals;
        $this->selectedFarmer = $selectedFarmer;
        $this->request = $request;
    }

    public function collection()
    {
        return $this->storageLoadings;
    }

    public function headings(): array
    {
        return [
            'DATE',
            'Farmer Id',
            'Farmer Name',
            'Phone',
            'Farmer Village',
            'VEHICLE NO.',
            'RST NO.',
            'TRANSPORTER',
            'BAG',
            'NET WT',
            'FINAL WT',
            'RATE',
            'AMOUNT',
            'COLD',
            'VARIETY',
        ];
    }

    public function map($loading): array
    {
        $bags = $loading->bag_quantity ?? 0;
        $netWeight = $loading->net_weight ?? 0;
        $finalWeight = $netWeight - $bags; // as per your total_final_weight logic
        $rate = number_format($loading->agreement->rate_per_kg ?? 0, 2);
        $amount = $netWeight * $rate;

        return [
            $loading->created_at ? $loading->created_at->format('d-m-Y') : '',
            $loading->agreement->farmer->farmer_id ?? '',
            $loading->agreement->farmer->name ?? '',
            $loading->agreement->farmerUser->phone ?? '',
            $loading->agreement->farmer->village_name ?? '',
            $loading->vehicle_number ?? '',
            $loading->rst_number ?? '', // <-- ensure field exists in your table
            $loading->transporter->name ?? '',
            $bags,
            number_format($netWeight, 2),
            number_format($finalWeight, 2),
            $rate,
            number_format($amount, 2),
            $loading->coldStorage->name ?? '',
            $loading->agreement->seedVariety->name ?? '',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Bold header row
        $sheet->getStyle('A1:O1')->getFont()->setBold(true);

        // Totals row
        $row = $this->storageLoadings->count() + 2;

        $sheet->setCellValue("A{$row}", 'TOTAL');
        $sheet->mergeCells("A{$row}:H{$row}");

        $sheet->setCellValue("I{$row}", $this->totals['total_bags']);
        $sheet->setCellValue("J{$row}", number_format($this->totals['total_net_weight'], 2));
        $sheet->setCellValue("K{$row}", number_format($this->totals['total_final_weight'], 2));
        $sheet->setCellValue("M{$row}", number_format($this->totals['total_amount'], 2));

        $sheet->getStyle("A{$row}:O{$row}")->getFont()->setBold(true);

        $sheet->getStyle("A{$row}:O{$row}")->getAlignment()->setHorizontal('left');

        return [];
    }

    public function startCell(): string
    {
        return 'A1';
    }
}
