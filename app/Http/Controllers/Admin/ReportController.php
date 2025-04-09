<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Farmer;
use App\Models\StorageLoading;
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
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

use App\Exports\ProcessingReportExport;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    /**
     * Display the reports dashboard
     */
    public function index()
    {
        return view('admin.reports.index');
    }

    /**
     * Generate detailed processing report like the one in the image
     */
    public function processingReport(Request $request)
    {
        $request->validate([
            'farmer_id' => 'nullable|exists:farmers,id',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
            'cold_storage_id' => 'nullable|exists:cold_storages,id',
            'transporter_id' => 'nullable|exists:transporters,id',
        ]);

        $query = StorageLoading::with([
            'agreement.farmer',
            'agreement.farmerUser',
            'agreement.seedVariety',
            'transporter',
            'vehicle',
            'coldStorage'
        ]);

        // Apply filters
        if ($request->farmer_id) {
            $query->whereHas('agreement.farmer', function($q) use ($request) {
                $q->where('id', $request->farmer_id);
            });
        }

        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->cold_storage_id) {
            $query->where('cold_storage_id', $request->cold_storage_id);
        }

        if ($request->transporter_id) {
            $query->where('transporter_id', $request->transporter_id);
        }

        $storageLoadings = $query->orderBy('created_at', 'desc')->get();

        // Calculate totals with rate from agreement table
        $totals = [
            'total_bags' => $storageLoadings->sum(function($loading) {
                return $loading->bag_quantity + ($loading->extra_bags ?? 0);
            }),
            'total_net_weight' => $storageLoadings->sum('net_weight'),
            // 'total_final_weight' => $storageLoadings->sum('net_weight'), // Simplified for now
            'total_final_weight' => $storageLoadings->sum(function($loading) {
                $totalbags = !empty($loading->extra_bags) ? $loading->extra_bags+ $loading->bag_quantity : $loading->bag_quantity;
                $netWeight = $loading->net_weight;
                $finalNetWeight = $netWeight - $totalbags;

                return $finalNetWeight;

                // $netWeight = $loading->net_weight;
                // return $netWeight;
            }),
            'total_amount' => $storageLoadings->sum(function($loading) {
                $rate = $loading->agreement->rate_per_kg ?? 0;
                return $loading->net_weight * $rate;

                // $totalbags = !empty($loading->extra_bags) ? $loading->extra_bags+ $loading->bag_quantity : $loading->bag_quantity;
                // $netWeight = $loading->net_weight;
                // $finalNetWeight = $netWeight - $totalbags;

                // $rate = $loading->agreement->rate_per_kg ?? 0;
                // return $finalNetWeight * $rate;
            }),
        ];

        // Get filter options
        $farmers = Farmer::orderBy('name')->get();
        $coldStorages = ColdStorage::orderBy('name')->get();
        $transporters = Transporter::orderBy('name')->get();

        // Get selected farmer if farmer_id is provided
        $selectedFarmer = null;
        if ($request->farmer_id) {
            $selectedFarmer = Farmer::find($request->farmer_id);
        }

        return view('admin.reports.processing', compact(
            'storageLoadings',
            'totals',
            'farmers',
            'coldStorages',
            'transporters',
            'selectedFarmer'
        ));
    }

    public function exportPdf(Request $request)
    {
        $request->validate([
            'farmer_id' => 'nullable|exists:farmers,id',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
            'cold_storage_id' => 'nullable|exists:cold_storages,id',
            'transporter_id' => 'nullable|exists:transporters,id',
        ]);

        $query = StorageLoading::with([
            'agreement.farmer',
            'agreement.farmerUser',
            'agreement.seedVariety',
            'transporter',
            'vehicle',
            'coldStorage'
        ]);

        // Apply filters
        if ($request->farmer_id) {
            $query->whereHas('agreement.farmer', function($q) use ($request) {
                $q->where('id', $request->farmer_id);
            });
        }

        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->cold_storage_id) {
            $query->where('cold_storage_id', $request->cold_storage_id);
        }

        if ($request->transporter_id) {
            $query->where('transporter_id', $request->transporter_id);
        }

        $storageLoadings = $query->orderBy('created_at', 'desc')->get();

        // Calculate totals
        // $totals = [
        //     'total_bags' => $storageLoadings->sum(function($loading) {
        //         return $loading->bag_quantity + ($loading->extra_bags ?? 0);
        //     }),
        //     'total_net_weight' => $storageLoadings->sum('net_weight'),
        //     'total_final_weight' => $storageLoadings->sum('net_weight'),
        //     'total_amount' => $storageLoadings->sum(function($loading) {
        //         $rate = $loading->agreement->rate ?? 0;
        //         return $loading->net_weight * $rate;
        //     }),
        // ];
        $totals = [
            'total_bags' => $storageLoadings->sum(function($loading) {
                return $loading->bag_quantity + ($loading->extra_bags ?? 0);
            }),
            'total_net_weight' => $storageLoadings->sum('net_weight'),
            // 'total_final_weight' => $storageLoadings->sum('net_weight'), // Simplified for now
            'total_final_weight' => $storageLoadings->sum(function($loading) {
                $totalbags = !empty($loading->extra_bags) ? $loading->extra_bags+ $loading->bag_quantity : $loading->bag_quantity;
                $netWeight = $loading->net_weight;
                $finalNetWeight = $netWeight - $totalbags;

                return $finalNetWeight;

                // $netWeight = $loading->net_weight;
                // return $netWeight;
            }),
            'total_amount' => $storageLoadings->sum(function($loading) {
                $rate = $loading->agreement->rate_per_kg ?? 0;
                return $loading->net_weight * $rate;

                // $totalbags = !empty($loading->extra_bags) ? $loading->extra_bags+ $loading->bag_quantity : $loading->bag_quantity;
                // $netWeight = $loading->net_weight;
                // $finalNetWeight = $netWeight - $totalbags;

                // $rate = $loading->agreement->rate_per_kg ?? 0;
                // return $finalNetWeight * $rate;
            }),
        ];

        // Get selected farmer if farmer_id is provided
        $selectedFarmer = null;
        if ($request->farmer_id) {
            $selectedFarmer = Farmer::find($request->farmer_id);
        }

        // Generate PDF with landscape orientation
        $pdf = PDF::loadView('admin.reports.processing-pdf', compact(
            'storageLoadings',
            'totals',
            'selectedFarmer',
            'request'
        ));

        // Set landscape orientation
        $pdf->setPaper('A4', 'landscape');

        $filename = 'processing_report_' . date('Y-m-d_H-i-s') . '.pdf';

        return $pdf->download($filename);
    }

    public function exportToExcel(Request $request) {
        $request->validate([
            'farmer_id' => 'nullable|exists:farmers,id',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
            'cold_storage_id' => 'nullable|exists:cold_storages,id',
            'transporter_id' => 'nullable|exists:transporters,id',
        ]);

        $query = StorageLoading::with([
            'agreement.farmer',
            'agreement.farmerUser',
            'agreement.seedVariety',
            'transporter',
            'vehicle',
            'coldStorage'
        ]);

        if ($request->farmer_id) {
            $query->whereHas('agreement.farmer', function ($q) use ($request) {
                $q->where('id', $request->farmer_id);
            });
        }

        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->cold_storage_id) {
            $query->where('cold_storage_id', $request->cold_storage_id);
        }

        if ($request->transporter_id) {
            $query->where('transporter_id', $request->transporter_id);
        }

        $storageLoadings = $query->orderBy('created_at', 'desc')->get();

        $totals = [
            'total_bags' => $storageLoadings->sum(function ($loading) {
                return $loading->bag_quantity + ($loading->extra_bags ?? 0);
            }),
            'total_net_weight' => $storageLoadings->sum('net_weight'),
            'total_final_weight' => $storageLoadings->sum(function ($loading) {
                $totalbags = !empty($loading->extra_bags) ? $loading->extra_bags + $loading->bag_quantity : $loading->bag_quantity;
                $netWeight = $loading->net_weight;
                return $netWeight - $totalbags;
            }),
            'total_amount' => $storageLoadings->sum(function ($loading) {
                $rate = $loading->agreement->rate_per_kg ?? 0;
                return $loading->net_weight * $rate;
            }),
        ];

        $selectedFarmer = null;
        if ($request->farmer_id) {
            $selectedFarmer = Farmer::find($request->farmer_id);
        }

        return Excel::download(
            new ProcessingReportExport($storageLoadings, $totals, $selectedFarmer, $request),
            config('app.name') . 'ProcessingReport_' . now()->format('Ymd-His') . '.xlsx'
        );
    }

    /**
     * Show farmer selection page
     */
    public function farmerReport(Request $request)
    {
        $farmers = Farmer::orderBy('name')->get();

        // If a farmer is selected via GET parameter
        if ($request->has('farmer_id') && $request->farmer_id) {
            $farmerId = $request->farmer_id;
            $farmerDetails = Farmer::with(['agreements', 'advancePayments', 'challans'])->findOrFail($farmerId);

            $agreements = $farmerDetails->agreements()->with(['seedVariety', 'storageLoadings'])->get();
            $advancePayments = $farmerDetails->advancePayments;
            $challans = $farmerDetails->challans;

            $totals = [
                'total_agreements' => $agreements->count(),
                'total_advance_payments' => $advancePayments->sum('amount'),
                'total_challans' => $challans->count(),
            ];

            return view('admin.reports.farmer', compact('farmerDetails', 'agreements', 'advancePayments', 'challans', 'totals', 'farmers'));
        }

        // Show farmer selection page
        return view('admin.reports.farmer', compact('farmers'));
    }

    /**
     * Show farmer agreements details
     */
    public function farmerAgreements($farmerId)
    {
        $farmer = Farmer::with(['agreements.seedVariety'])->findOrFail($farmerId);
        $agreements = $farmer->agreements()->with(['seedVariety'])->get();

        $totals = [
            'total_agreements' => $agreements->count(),
            'total_bags' => $agreements->sum('bag_quantity'),
            'total_amount' => $agreements->sum(function($agreement) {
                return $agreement->bag_quantity * $agreement->rate;
            }),
        ];

        return view('admin.reports.farmer-agreements', compact('farmer', 'agreements', 'totals'));
    }

    /**
     * Show farmer payments details
     */
    public function farmerPayments($farmerId)
    {
        $farmer = Farmer::findOrFail($farmerId);
        $payments = $farmer->advancePayments;

        $totals = [
            'total_payments' => $payments->count(),
            'total_amount' => $payments->sum('amount'),
        ];

        return view('admin.reports.farmer-payments', compact('farmer', 'payments', 'totals'));
    }

    /**
     * Show farmer challans details
     */
    public function farmerChallans($farmerId)
    {
        $farmer = Farmer::findOrFail($farmerId);
        $challans = $farmer->challans()->with(['vehicle'])->get();

        $totals = [
            'total_challans' => $challans->count(),
        ];

        return view('admin.reports.farmer-challans', compact('farmer', 'challans', 'totals'));
    }

    /**
     * Show farmer storage loadings details
     */
    public function farmerLoadings($farmerId)
    {
        $farmer = Farmer::with(['agreements.storageLoadings'])->findOrFail($farmerId);
        $agreements = $farmer->agreements()->with(['storageLoadings.coldStorage', 'storageLoadings.transporter', 'storageLoadings.vehicle', 'seedVariety'])->get();

        $farmerLoadings = collect();
        foreach($agreements as $agreement) {
            $farmerLoadings = $farmerLoadings->merge($agreement->storageLoadings);
        }

        $totals = [
            'total_loadings' => $farmerLoadings->count(),
            'total_bags' => $farmerLoadings->sum('bag_quantity'),
            'total_weight' => $farmerLoadings->sum('net_weight'),
        ];

        return view('admin.reports.farmer-loadings', compact('farmer', 'farmerLoadings', 'totals'));
    }

    /**
     * Generate storage report
     */
    public function storageReport(Request $request)
    {
        $request->validate([
            'cold_storage_id' => 'nullable|exists:cold_storages,id',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
        ]);

        $query = StorageLoading::with(['agreement.farmer', 'coldStorage']);

        if ($request->cold_storage_id) {
            $query->where('cold_storage_id', $request->cold_storage_id);
        }

        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $storageLoadings = $query->orderBy('created_at', 'desc')->get();
        $coldStorages = ColdStorage::orderBy('name')->get();

        return view('admin.reports.storage', compact('storageLoadings', 'coldStorages'));
    }

    /**
     * Generate financial report
     */
    public function financialReport(Request $request)
    {
        $request->validate([
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
        ]);

        $query = StorageLoading::with(['agreement.farmer']);

        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $storageLoadings = $query->orderBy('created_at', 'desc')->get();

        $totals = [
            'total_amount' => $storageLoadings->sum(function($loading) {
                return $loading->net_weight * 14.75; // Default rate
            }),
            'total_bags' => $storageLoadings->sum('bag_quantity'),
            'total_weight' => $storageLoadings->sum('net_weight'),
        ];

        return view('admin.reports.financial', compact('storageLoadings', 'totals'));
    }

    /**
     * Export report to Excel
     */
    public function exportExcel(Request $request)
    {
        $reportType = $request->report_type;
        $data = $this->getReportData($request);

        // Generate Excel using a library like PhpSpreadsheet
        // This is a placeholder - you'll need to implement Excel generation
        return response()->json(['message' => 'Excel export functionality to be implemented']);
    }

    /**
     * Get report data based on type
     */
    private function getReportData(Request $request)
    {
        $reportType = $request->report_type;

        switch ($reportType) {
            case 'processing':
                return $this->getProcessingReportData($request);
            case 'farmer':
                return $this->getFarmerReportData($request);
            case 'storage':
                return $this->getStorageReportData($request);
            case 'financial':
                return $this->getFinancialReportData($request);
            default:
                return [];
        }
    }

    /**
     * Get processing report data
     */
    private function getProcessingReportData(Request $request)
    {
        $query = StorageLoading::with([
            'agreement.farmer',
            'agreement.seedVariety',
            'transporter',
            'vehicle',
            'coldStorage'
        ]);

        // Apply filters
        if ($request->farmer_id) {
            $query->whereHas('agreement.farmer', function($q) use ($request) {
                $q->where('id', $request->farmer_id);
            });
        }

        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * Get farmer report data
     */
    private function getFarmerReportData(Request $request)
    {
        $farmerId = $request->farmer_id;
        return Farmer::with(['agreements', 'advancePayments', 'challans'])->find($farmerId);
    }

    /**
     * Get storage report data
     */
    private function getStorageReportData(Request $request)
    {
        $query = StorageLoading::with(['agreement.farmer', 'coldStorage']);

        if ($request->cold_storage_id) {
            $query->where('cold_storage_id', $request->cold_storage_id);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * Get financial report data
     */
    private function getFinancialReportData(Request $request)
    {
        $query = StorageLoading::with(['agreement.farmer']);

        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }
}