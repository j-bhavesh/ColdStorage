<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\SeedsBooking;
use App\Models\Farmer;
use App\Models\Company;
use App\Models\SeedVariety;
use App\Http\Requests\SeedsBookingRequest;
use App\Services\SeedsBookingService;

use App\Exports\SeedsBookingReportExport;
use Maatwebsite\Excel\Facades\Excel;

class SeedsBookingTable extends Component
{
    use WithPagination;

    public $paginationTheme = 'bootstrap';
    
    public $seedsBookingId, $farmer_id, $company_id, $seed_variety_id, $bag_quantity, $booking_amount, $bag_rate, $booking_type = 'debit', $status = 'active', $isOpen = false;

    public $search = '';
    public $sortField = 'id';
    public $sortDirection = 'desc';
    public $farmer_name;

    public $selectedYear = '';
    public $financialYears = [];

    protected function rules()
    {
        $rules = (new SeedsBookingRequest())->rules();
        
        return $rules;
    }

    public function boot(SeedsBookingService $seedsBookingService)
    {
        $this->seedsBookingService = $seedsBookingService;
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

    // Listen for the SeedsBooking-create event
    protected $listeners = ['seeds-booking-create' => 'create'];

    public function resetForm()
    {
        $this->reset([
            'seedsBookingId', 
            'company_id', 
            'farmer_id', 
            'seed_variety_id', 
            'bag_quantity', 
            'booking_type',
            'booking_amount',
            'bag_rate'
        ]);
        $this->status = 'active';
        $this->resetErrorBag();
    }

    public function updatedFarmerId($value)
    {
        // $this->seed_variety_id = null;
        // $this->bag_quantity = null;
    }

    public function updatedSeedVarietyId($value)
    {
        // Removed agreement-related code
    }

    public function create()
    {
        $this->resetForm();
        $this->openModal();
    }

    public function openModal()
    {
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
    }

    public function store()
    {
        try {
            $validatedData = $this->validate($this->rules());
            $this->seedsBookingService->create($validatedData);
            $this->closeModal();
            $this->resetForm();
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors
            foreach ($e->errors() as $field => $messages) {
                $this->addError($field, $messages[0]);
            }
        } catch (\Exception $e) {
            // Handle other types of errors
            session()->flash('error', $e->getMessage());
        }
    } 

    public function edit($id)
    {
        $seeds_booking = $this->seedsBookingService->getById($id);

        $this->seedsBookingId = $id;
        $this->farmer_id = $seeds_booking->farmer_id;
        $this->farmer_name  = $seeds_booking->farmer->name. " ({$seeds_booking->farmer->farmer_id}) - {$seeds_booking->farmer->village_name}";
        $this->company_id = $seeds_booking->company_id;
        $this->seed_variety_id = $seeds_booking->seed_variety_id;
        $this->bag_quantity = $seeds_booking->bag_quantity;
        $this->booking_type = $seeds_booking->booking_type;
        $this->booking_amount = $seeds_booking->booking_amount;
        $this->bag_rate = $seeds_booking->bag_rate;
        $this->status = $seeds_booking->status;

        $this->openModal();
    }

    // Update the booking amount while change the booking type
    public function updatedBookingType($value)
    {
        $this->validateOnly('booking_type');

        if( $this->booking_type == 'debit' ) {
            $this->booking_amount = "0.00";
        }
    }

    public function update()
    {
        try {
            $validatedData = $this->validate($this->rules());
            if ($this->seedsBookingId) {
                $seeds_booking = $this->seedsBookingService->getById($this->seedsBookingId);
                $this->seedsBookingService->update($seeds_booking, $validatedData);
                $this->closeModal();
                $this->resetForm();
            }
        } catch (\Exception $e) {
            $this->addError('bag_quantity', $e->getMessage());
        }
    }


    public function delete($id)
    {
        try {
            $this->seedsBookingService->delete($id);
            session()->flash('message', 'Seeds booking and all associated records permanently deleted.');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to delete seeds booking: ' . $e->getMessage());
        }
    }

    public function sortBy($field)
    {
        if($this->sortField === $field){
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        }else{
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function validateBookingAmount()
    {
        
        if( $this->booking_type === 'debit' ) {
            $this->validateOnly('booking_amount', [
                'booking_amount' => 'nullable|numeric|min:0|required_if:booking_type,cash'
            ]);
        } else {
            $this->validateOnly('booking_amount', [
                'booking_amount' => 'numeric|min:1|required_if:booking_type,cash'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.admin.seeds-booking.index', [
            'seedsBooking' => $this->seedsBookingService->searchAndSort(
                $this->search,
                $this->sortField,
                $this->sortDirection,
                financialYear: $this->financialYears
            ), 
            'farmers' => Farmer::with('user')->orderBy('name')->get(),
            'companies' => Company::orderBy('name')->get(),
            'seedVarieties' => SeedVariety::orderBy('name')->get()
        ]);
    }

    public function exportData()
    {
        $fileSuffix = !empty($this->selectedYear) ? str_replace(' - ', '_to_', $this->selectedYear) : '';

        return Excel::download(
            new SeedsBookingReportExport($this->seedsBookingService, $this->financialYears), 
            config('app.name') . "_SeedsBooking_{$fileSuffix}" . now()->format('Ymd-His') . '.xlsx'
        );
    }
}
