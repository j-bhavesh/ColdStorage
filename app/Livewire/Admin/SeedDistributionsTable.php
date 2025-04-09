<?php

namespace App\Livewire\Admin;

use App\Models\SeedDistribution;
use App\Models\SeedsBooking;
use App\Models\Farmer;
use App\Models\SeedVariety;
use App\Models\Company;
use App\Services\SeedDistributionService;
use App\Http\Requests\SeedDistributionRequest;
use Livewire\Component;
use Livewire\WithPagination;

use App\Exports\SeedsDistributionReportExport;
use Maatwebsite\Excel\Facades\Excel;


class SeedDistributionsTable extends Component
{
    use WithPagination;

    protected $seedDistributionService;

    public $paginationTheme = 'bootstrap';

    public $search = '';
    public $perPage = 10;
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    // Form properties
    public $isOpen = false;
    public $isEdit = true;
    public $id;
    public $seeds_booking_id;
    public $farmer_id;
    public $seed_variety_id;
    public $company_id;
    public $bag_quantity;
    public $distribution_date;
    public $vehicle_number;
    public $received_by;

    // Display properties for auto-fill
    public $farmer_name = '';
    public $seed_variety_name = '';
    public $company_name = '';

    public $seed_booking_name;

    // Dynamic data
    public $seedsBookings = [];

    public $selectedSeedsBooking = null;

    public $selectedYear = '';
    public $financialYears = [];

    protected $queryString = [
        'search' => ['except' => ''],
        'sortField' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

    protected $listeners = ['refreshTable' => '$refresh'];

    protected function rules()
    {
        return (new SeedDistributionRequest())->rules();
    }

    public function boot()
    {
        $this->seedDistributionService = app(SeedDistributionService::class);
    }

    public function mount()
    {
        $this->loadSeedsBooking();
    }

    public function updatedSelectedYear($value)
    {
        if ($value) {
            [$start, $end] = explode(' - ', $value);
            $this->selectedYear = $value;
            $this->financialYears = [
                'startDate' => \Carbon\Carbon::createFromFormat('d-m-Y', trim($start))->format('Y-m-d'),
                'endDate' => \Carbon\Carbon::createFromFormat('d-m-Y', trim($end))->format('Y-m-d'),
            ];
        } else {
            $this->financialYears = [];
            $this->selectedYear = '';
        }
    }

    /*public function loadSeedsBooking()
    {
        $this->seedsBookings = SeedsBooking::with(['farmer', 'company', 'seedVariety'])
            ->where('pending_bags', '>', 0)
            ->orWhere('pending_bags', null)
            ->get()
            ->map(function ($seedsBooking) {
                return [
                    'id' => $seedsBooking->id,
                    'name' => $seedsBooking->farmer->name . '-' .
                             $seedsBooking->company->name . '-' .
                             $seedsBooking->seedVariety->name . '-(#BID-' .
                             $seedsBooking->id.')'
                ];
            });
    }*/

    public function loadSeedsBooking()
    {
        $this->seedsBookings = SeedsBooking::with(['farmer', 'company', 'seedVariety'])
            ->where('pending_bags', '>', 0)
            ->orWhere('pending_bags', null) // Include bookings with no distributions yet
            ->get()
            ->filter(function ($seedsBooking) {
                // Double check: exclude bookings where all bags are distributed
                $totalDistributed = $seedsBooking->seedDistributions()->sum('bag_quantity');
                return $totalDistributed < $seedsBooking->bag_quantity;
            })
            ->map(function ($seedsBooking) {
                $pendingBags = $seedsBooking->pending_bags ?? $seedsBooking->bag_quantity;
                
                return [
                    'id' => $seedsBooking->id,
                    'name' => $seedsBooking->farmer->name . '-' .
                             $seedsBooking->company->name . '-' .
                             $seedsBooking->seedVariety->name . '-(#BID-' .
                             $seedsBooking->id . ') [Remaining: ' . $pendingBags . ']'
                ];
            });
    }

    public function updatedSeedsBookingId($value)
    {
        if ($value) {
            $seedsBooking = SeedsBooking::with(['farmer', 'company', 'seedVariety'])->find($value);
            if ($seedsBooking) {
                // Set IDs for form submission
                $this->farmer_id = $seedsBooking->farmer_id;
                $this->seed_variety_id = $seedsBooking->seed_variety_id;
                $this->company_id = $seedsBooking->company_id;
                $this->bag_quantity = !empty( $seedsBooking->pending_bags ) || $seedsBooking->pending_bags != 0 ? $seedsBooking->pending_bags : $seedsBooking->bag_quantity;

                // Set display values
                $this->farmer_name = $seedsBooking->farmer->name;
                $this->seed_variety_name = $seedsBooking->seedVariety->name;
                $this->company_name = $seedsBooking->company->name;

                // Set other default values
                // $this->distribution_date = now()->format('Y-m-d');
                // $this->vehicle_number = '';
                // $this->received_by = auth()->user()->name;

                // Set selected booking details for display
                $this->selectedSeedsBooking = [
                    'farmer_name' => $seedsBooking->farmer->name,
                    'seed_variety_name' => $seedsBooking->seedVariety->name,
                    'company_name' => $seedsBooking->company->name
                ];
            }
        } else {
            $this->resetSeedsBookingDetails();
            $this->selectedSeedsBooking = null;
        }
    }

    private function resetSeedsBookingDetails()
    {
        $this->farmer_id = '';
        $this->seed_variety_id = '';
        $this->company_id = '';
        $this->bag_quantity = '';
        $this->distribution_date = '';
        $this->vehicle_number = '';
        $this->received_by = '';

        // Reset display values
        $this->farmer_name = '';
        $this->seed_variety_name = '';
        $this->company_name = '';
        $this->selectedSeedsBooking = null;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }

        $this->sortField = $field;
    }

    public function create()
    {
        $this->resetInputFields();
        $this->isOpen = true;
    }

    public function edit($id)
    {
        $distribution = $this->seedDistributionService->findById($id);
        
        $pendingBags = $distribution->pending_bags ?? $distribution->bag_quantity;

        if ($distribution) {
            $this->id = $distribution->id;
            $this->seeds_booking_id = $distribution->seeds_booking_id;
            $this->seed_booking_name = $distribution->farmer->name . '-' .
                             $distribution->company->name . '-' .
                             $distribution->seedVariety->name . '-(#BID-' .
                             $distribution->id . ') [Remaining: ' . $pendingBags . ']';
            $this->farmer_id = $distribution->farmer_id;
            $this->seed_variety_id = $distribution->seed_variety_id;
            $this->company_id = $distribution->company_id;
            $this->bag_quantity = $distribution->bag_quantity;
            $this->distribution_date = $distribution->distribution_date->format('Y-m-d');
            $this->vehicle_number = $distribution->vehicle_number;
            $this->received_by = $distribution->received_by;

            // Set display values
            $this->farmer_name = $distribution->farmer->name;
            $this->seed_variety_name = $distribution->seedVariety->name;
            $this->company_name = $distribution->company->name;

            $this->isOpen = true;
        }
    }

    public function store()
    {
        $this->validate();

        try {
            $this->seedDistributionService->create([
                'seeds_booking_id' => $this->seeds_booking_id,
                'farmer_id' => $this->farmer_id,
                'seed_variety_id' => $this->seed_variety_id,
                'company_id' => $this->company_id,
                'bag_quantity' => $this->bag_quantity,
                'distribution_date' => $this->distribution_date,
                'vehicle_number' => $this->vehicle_number,
                'received_by' => $this->received_by,
            ]);

            session()->flash('message', 'Seed Distribution created successfully.');
            $this->closeModal();
            $this->resetInputFields();
        } catch (\Exception $e) {
            $this->addError('bag_quantity', $e->getMessage());
        }
    }

    public function update()
    {
        $this->validate();

        try {
            $this->seedDistributionService->update($this->id, [
                'seeds_booking_id' => $this->seeds_booking_id,
                'farmer_id' => $this->farmer_id,
                'seed_variety_id' => $this->seed_variety_id,
                'company_id' => $this->company_id,
                'bag_quantity' => $this->bag_quantity,
                'distribution_date' => $this->distribution_date,
                'vehicle_number' => $this->vehicle_number,
                'received_by' => $this->received_by,
            ]);

            session()->flash('message', 'Seed Distribution updated successfully.');
            $this->closeModal();
            $this->resetInputFields();
        } catch (\Exception $e) {
            $this->addError('bag_quantity', $e->getMessage());
        }
    }

    public function delete($id)
    {
        \Log::info('Delete method called with ID: ' . $id);
        try {
            $this->seedDistributionService->delete($id);
            session()->flash('message', 'Seed Distribution permanently deleted.');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to delete seed distribution: ' . $e->getMessage());
        }
    }

    private function resetInputFields()
    {
        $this->id = null;
        $this->seeds_booking_id = '';
        $this->farmer_id = '';
        $this->seed_variety_id = '';
        $this->company_id = '';
        $this->bag_quantity = '';
        $this->distribution_date = '';
        $this->vehicle_number = '';
        $this->received_by = '';
        $this->resetSeedsBookingDetails();
    }

    public function closeModal()
    {
        $this->isOpen = false;
    }

    public function render()
    {
        $filters = [];
        if ($this->search) {
            $filters['search'] = $this->search;
        }

        // Add sorting parameters
        $filters['sort_field'] = $this->sortField;
        $filters['sort_direction'] = $this->sortDirection;

        $seedDistributions = $this->seedDistributionService->getAll($filters, financialYear: $this->financialYears);

        return view('livewire.admin.seed-distributions.index', [
            'seedDistributions' => $seedDistributions,
            'seedsBookings' => $this->seedsBookings,
            'seedVarieties' => SeedVariety::all(),
            'companies' => Company::all(),
        ])->layout('layouts.admin');
    }

    public function exportData()
    {
        $fileSuffix = !empty($this->selectedYear) ? str_replace(' - ', '_to_', $this->selectedYear) : '';

        return Excel::download(
            new SeedsDistributionReportExport($this->financialYears), 
            config('app.name') . "_SeedsDistributions_{$fileSuffix}_" . now()->format('Ymd-His') . '.xlsx'
        );
    }
}
