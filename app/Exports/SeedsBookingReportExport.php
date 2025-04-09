<?php

namespace App\Exports;

use App\Models\SeedsBooking;
use App\Models\Farmer;
use App\Models\Company;
use App\Models\SeedVariety;
use App\Services\SeedsBookingService;

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

class SeedsBookingReportExport implements 
    FromCollection, 
    WithHeadings, 
    WithStyles, 
    WithMapping, 
    WithCustomStartCell,
    WithColumnFormatting

{
    protected $seedsBookingService;
    protected $seedsBookingData;
    protected $financialYear;

    public function __construct(SeedsBookingService $seedsBookingService, $financialYear = null)
    {
        $this->seedsBookingService = $seedsBookingService;

        $this->financialYear = $financialYear;
        
        // Fetch data once during construction
        $this->seedsBookingData = $this->seedsBookingService->getSeedsBookingReportData($this->financialYear); 
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        // dd($this->seedsBookingData);
        return $this->seedsBookingData;
    }

    public function headings(): array
    {
        return [
            'Farmer Id',
            'Farmer Name',
            'Village',
            'Phone',
            'Company',
            'Seed Variety',
            'Total Bags',
            'Supplied Bags',
            'Pending Bags',
            'Bag Rate',
            'Booking Type',
            'Booking Amount',
            'Booking Date',
        ];
    }

    // Format each row before export
    public function map($seedsBooking): array
    {
        return [
            $seedsBooking->farmer->farmer_id ?? '',
            $seedsBooking->farmer->name ?? '',
            $seedsBooking->farmer->village_name ?? '',
            $seedsBooking->farmer->user->phone ?? '',
            $seedsBooking->company->name ?? '',
            $seedsBooking->seedVariety->name ?? '',
            $seedsBooking->bag_quantity,
            number_format($seedsBooking->received_bags ?? 0, 0),
            number_format($seedsBooking->pending_bags ?? 0, 0),
            number_format($seedsBooking->bag_rate, 2),
            ucfirst($seedsBooking->booking_type),
            number_format($seedsBooking->booking_amount, 2),
            Date::dateTimeToExcel($seedsBooking->created_at),
        ];
    }

    // Make headers bold
    public function styles(Worksheet $sheet)
    {
        // Bold header row (row 1 since no logo now)
        $sheet->getStyle('A1:M1')->getFont()->setBold(true);

        // Align numeric fields to the right
        $numericColsRight = ['L'];

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


    public function columnFormats(): array
    {
        return [
            'M' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            // 'K' => NumberFormat::FORMAT_CURRENCY_EUR_INTEGER,
        ];
    }
}
