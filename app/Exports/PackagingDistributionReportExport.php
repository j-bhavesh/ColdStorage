<?php

namespace App\Exports;

use App\Models\PackagingDistribution;
use App\Models\Agreement;

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

class PackagingDistributionReportExport implements 
    FromCollection,
    WithHeadings, 
    WithStyles, 
    WithMapping, 
    WithCustomStartCell,
    WithColumnFormatting
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
        $query = PackagingDistribution::with([
            'agreement.farmer',
            'agreement.seedVariety',
            'agreement.farmerUser'
        ]);

        if (!empty($this->financialYear)) {
            $startDate = \Carbon\Carbon::parse($this->financialYear['startDate'])->startOfDay();
            $endDate = \Carbon\Carbon::parse($this->financialYear['endDate'])->endOfDay();

            $query->whereBetween('distribution_date', [$startDate, $endDate]);
        }

        return $query->orderBy('id', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'Farmer Id',
            'Farmer Name',
            'Village',
            'Phone',
            'Seed Variety',
            'Total Bags',
            'Supplied Bags',
            'Pending Bags',
            'Vehicle Number',
            'Distribution Date',
            'Received By',
        ];
    }

    // Format each row before export
    public function map($packagingDistribution): array
    {
        return [
            $packagingDistribution->agreement->farmer->farmer_id ?? '',
            $packagingDistribution->agreement->farmer->name ?? '',
            $packagingDistribution->agreement->farmer->village_name ?? '',
            $packagingDistribution->agreement->farmerUser->phone ?? '',
            $packagingDistribution->agreement->seedVariety->name ?? '',
            $packagingDistribution->agreement->bag_quantity,
            number_format($packagingDistribution->received_bags ?? '-', 0),
            number_format($packagingDistribution->pending_bags ?? '-', 0),
            $packagingDistribution->vehicle_number,
            Date::dateTimeToExcel($packagingDistribution->distribution_date),
            $packagingDistribution->received_by,
        ];
    }

    // Make headers bold
    public function styles(Worksheet $sheet)
    {
        // Bold header row (row 1 since no logo now)
        $sheet->getStyle('A1:K1')->getFont()->setBold(true);

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
