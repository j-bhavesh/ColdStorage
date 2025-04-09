<?php

namespace App\Exports;

use App\Models\Farmer;
use App\Models\User;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class FarmersRegistrationsExport implements 
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
        // $allFarmers = Farmer::with(['user'])->orderBy('created_at', 'desc')->get();
        $farmersQuery = Farmer::with(['user']);
        if (!empty($this->financialYear)) {
            // [$startYear, $endYear] = explode('-', $this->financialYear);

            // $startDate = Carbon::createFromDate($startYear, 2, 1)->startOfDay();
            // $endDate   = Carbon::createFromDate($endYear, 1, 31)->endOfDay();
            $startDate = \Carbon\Carbon::parse($this->financialYear['startDate'])->startOfDay();
            $endDate = \Carbon\Carbon::parse($this->financialYear['endDate'])->endOfDay();

            $farmersQuery->whereBetween('created_at', [$startDate, $endDate]);
        }

        $results = $farmersQuery->orderBy('created_at', 'desc')->get();
        
        return $results;
    }

    // Format each row before export
    public function map($farmer): array
    {
        return [
            $farmer->farmer_id ?? '',
            $farmer->name ?? '',
            $farmer->village_name ?? '',
            $farmer->user->phone ?? '',
            optional($farmer->created_at)->format('d/m/Y'),
        ];
    }

    public function headings(): array
    {
        return [
            'Farmer Id',
            'Farmer Name',
            'Village',
            'Phone',
            'Registration Date',
        ];
    }

    // Make headers bold
    public function styles(Worksheet $sheet)
    {
        // Bold header row (row 1 since no logo now)
        $sheet->getStyle('A1:E1')->getFont()->setBold(true);

        // Align numeric fields to the right
        $numericColsRight = ['E'];

        foreach ($numericColsRight as $col) {
            $sheet->getStyle($col)->getAlignment()->setHorizontal('right');
        }

        // Align numeric fields to the left
        // $numericColsRight = ['D'];
        
        // foreach ($numericColsRight as $col) {
        //     $sheet->getStyle($col)->getAlignment()->setHorizontal('left');
        // }

        return [];
    }

    // Start table from row 6 (leaving space for logo)
    public function startCell(): string
    {
        return 'A1';
    }
}
