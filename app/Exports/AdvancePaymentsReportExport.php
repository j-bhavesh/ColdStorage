<?php

namespace App\Exports;

use App\Models\AdvancePayment;

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

class AdvancePaymentsReportExport implements 
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
        $query =  AdvancePayment::with([
            'farmer', 
            'farmer.user', 
        ]);

        if (!empty($this->financialYear)) {
            $startDate = \Carbon\Carbon::parse($this->financialYear['startDate'])->startOfDay();
            $endDate = \Carbon\Carbon::parse($this->financialYear['endDate'])->endOfDay();

            $query->whereBetween('payment_date', [$startDate, $endDate]);
        }
        return $query->orderBy('id', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'Farmer Id',
            'Farmer Name',
            'Phone',
            'Amount',
            'Distribution Date',
            'Tanken By',
            'Taken By Name',
        ];
    }

    // Format each row before export
    public function map($advancePayment): array
    {
        return [
            $advancePayment->farmer->farmer_id ?? '',
            $advancePayment->farmer->name ?? '',
            $advancePayment->farmer->user->phone ?? '',
            (float) $advancePayment->amount,
            Date::dateTimeToExcel($advancePayment->payment_date),
            $advancePayment->taken_by ?? '',
            $advancePayment->taken_by_name ?? '',
        ];
    }

    // Make headers bold
    public function styles(Worksheet $sheet)
    {
        // Bold header row (row 1 since no logo now)
        $sheet->getStyle('A1:G1')->getFont()->setBold(true);

        // Align numeric fields to the right
        $numericColsRight = ['E'];

        foreach ($numericColsRight as $col) {
            $sheet->getStyle($col)->getAlignment()->setHorizontal('right');
        }

        return [];
    }

    public function columnFormats(): array
    {
        return [
            'D' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'E' => NumberFormat::FORMAT_DATE_DDMMYYYY,
        ];
    }

    // Start table from row 6 (leaving space for logo)
    public function startCell(): string
    {
        return 'A1';
    }
}
