<?php

namespace App\Exports;

use App\Models\Agreement;
use App\Models\Farmer;
use App\Models\SeedVariety;
use App\Services\AgreementService;
use Carbon\Carbon;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class AgreementsExport implements 
    FromCollection, 
    WithHeadings, 
    WithStyles, 
    WithMapping, 
    WithCustomStartCell
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
        $agreementQuery = Agreement::query()->with(['farmer', 'farmer.user', 'seedVariety']);
        
        if (!empty($this->financialYear)) {
            // [$startYear, $endYear] = explode('-', $this->financialYear);

            // $startDate = Carbon::createFromDate($startYear, 2, 1)->startOfDay();
            // $endDate   = Carbon::createFromDate($endYear, 1, 31)->endOfDay();
            $startDate = $this->financialYear['startDate'];
            $endDate = $this->financialYear['endDate'];

            $agreementQuery->whereBetween('agreement_date', [$startDate, $endDate]);
        }

        $results = $agreementQuery->orderBy('created_at', 'desc')->get();

        return $results;
        // return Agreement::with(['farmer', 'farmer.user', 'seedVariety'])->get();
    }

    // Format each row before export
    public function map($agreement): array
    {
        return [
            $agreement->farmer->farmer_id ?? '',
            $agreement->farmer->name ?? '',
            $agreement->farmer->village_name ?? '',
            $agreement->farmer->user->phone ?? '',
            $agreement->seedVariety->name ?? '',
            number_format($agreement->rate_per_kg, 2),
            optional($agreement->agreement_date)->format('d/m/Y'),
            number_format($agreement->vighas, 2),
            $agreement->bag_quantity,
            number_format($agreement->received_bags ?? 0, 0),
            number_format($agreement->pending_bags ?? 0, 0),
            number_format($agreement->surplus_bags ?? 0, 0),
        ];
    }

    public function headings(): array
    {
        return [
            'Farmer Id',
            'Farmer Name',
            'Village',
            'Phone',
            'Seed Variety',
            'Rate per KG',
            'Agreement Date',
            'Vighas',
            'Bag Quantity',
            'Received Bag',
            'Pending Bag',
            'Surplus Bag',
        ];
    }

    // Make headers bold
    public function styles(Worksheet $sheet)
    {
        // Bold header row (row 1 since no logo now)
        $sheet->getStyle('A1:L1')->getFont()->setBold(true);

        // Align numeric fields to the right
        $numericCols = ['F', 'G', 'H', 'I', 'J', 'K', 'L'];

        foreach ($numericCols as $col) {
            $sheet->getStyle($col)->getAlignment()->setHorizontal('right');
        }

        return [];
    }

    // Start table from row 6 (leaving space for logo)
    public function startCell(): string
    {
        return 'A1';
    }

    // Add logo (top left corner)
    // public function drawings()
    // {
    //     $drawing = new Drawing();
    //     $drawing->setName('Logo');
    //     $drawing->setDescription('Company Logo');
    //     $drawing->setPath(public_path('assets/images/brand-logo.png')); // path to logo
    //     $drawing->setHeight(100);
    //     $drawing->setCoordinates('D1'); // roughly center (adjust if needed)

    //     return $drawing;
    // }
}
