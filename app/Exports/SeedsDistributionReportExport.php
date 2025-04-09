<?php

namespace App\Exports;

use App\Models\SeedDistribution;
use App\Models\SeedsBooking;
use App\Models\Farmer;
use App\Models\SeedVariety;
use App\Models\Company;

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

class SeedsDistributionReportExport implements 
    FromCollection,
    WithHeadings, 
    WithStyles, 
    WithMapping, 
    WithCustomStartCell,
    WithColumnFormatting
{
    protected $seedsDistributionData, $financialYear;

    public function __construct($financialYear = null)
    {
        $this->financialYear = $financialYear;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $query = SeedDistribution::with([
            'seedsBooking', 
            'farmer', 
            'farmer.user', 
            'seedVariety', 
            'company'
        ]);

        if (!empty($this->financialYear)) {
            // [$startYear, $endYear] = explode('-', $this->financialYear);

            // $startDate = Carbon::createFromDate($startYear, 2, 1)->startOfDay();
            // $endDate   = Carbon::createFromDate($endYear, 1, 31)->endOfDay();
            $startDate = $this->financialYear['startDate'];
            $endDate = $this->financialYear['endDate'];

            $query->whereBetween('distribution_date', [$startDate, $endDate]);
        }

        return $query->orderBy('id', 'desc')->get();
        // return $this->seedsDistributionData;
    }

    public function headings(): array
    {
        return [
            'Farmer Id',
            'Farmer Name',
            'Village',
            'Phone',
            'Seed Variety',
            'Company',
            'Total Bags',
            'Supplied Bags',
            'Pending Bags',
            'Distribution Date',
            'Vehicle Number',
            'Received By',
        ];
    }

    // Format each row before export
    public function map($seedsDistribution): array
    {
        return [
            $seedsDistribution->farmer->farmer_id ?? '',
            $seedsDistribution->farmer->name ?? '',
            $seedsDistribution->farmer->village_name ?? '',
            $seedsDistribution->farmer->user->phone ?? '',
            $seedsDistribution->seedVariety->name ?? '',
            $seedsDistribution->company->name ?? '',
            number_format($seedsDistribution->seedsBooking->bag_quantity ?? '-', 0),
            number_format($seedsDistribution->bag_quantity ?? '-', 0),
            number_format($seedsDistribution->pending_bags ?? '-', 0),
            Date::dateTimeToExcel($seedsDistribution->distribution_date),
            $seedsDistribution->vehicle_number,
            $seedsDistribution->received_by,
        ];
    }

    // Make headers bold
    public function styles(Worksheet $sheet)
    {
        // Bold header row (row 1 since no logo now)
        $sheet->getStyle('A1:L1')->getFont()->setBold(true);

        // Align numeric fields to the right
        $numericColsRight = ['G', 'H', 'I'];

        foreach ($numericColsRight as $col) {
            $sheet->getStyle($col)->getAlignment()->setHorizontal('right');
        }

        return [];
    }

    public function columnFormats(): array
    {
        return [
            'J' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            // 'K' => NumberFormat::FORMAT_CURRENCY_EUR_INTEGER,
        ];
    }

    // Start table from row 6 (leaving space for logo)
    public function startCell(): string
    {
        return 'A1';
    }
}
