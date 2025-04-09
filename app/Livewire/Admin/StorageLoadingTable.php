<?php

namespace App\Livewire\Admin;

use App\Models\ColdStorage;
use App\Models\Transporter;
use App\Services\StorageLoadingService;
use App\Http\Requests\StorageLoadingRequest;
use Livewire\Component;
use Livewire\WithPagination;

use App\Exports\StorageLoadingReportExport;
use Maatwebsite\Excel\Facades\Excel;

class StorageLoadingTable extends Component
{
    use WithPagination;

    public $paginationTheme = 'bootstrap';

    public $search = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    public $isOpen = false;
    public $storageLoadingId;
    public $agreement_id;
    public $transporter_id;
    public $vehicle_id;
    public $vehicle_number;
    public $cold_storage_id;
    public $rst_number;
    public $chamber_no;
    public $bag_quantity;
    public $net_weight;
    public $extra_bags;
    public $remarks;
    public $selected_farmer_id;
    public $selected_farmer_name;
    public $selected_seed_variety_id;
    public $filteredAgreements = [];
    public $seedVarieties = [];
    public $farmers;
    public $transporters;
    public $coldStorages;
    public $showOverrideModal = false;
    public $overrideData = [];
    public $transporterChanged = false;

    public $selectedYear = '';
    public $financialYears = [];

    protected $listeners = ['refreshTable' => '$refresh'];
    protected $storageLoadingService;

    public function boot(StorageLoadingService $storageLoadingService)
    {
        $this->storageLoadingService = $storageLoadingService;
    }

    public function mount()
    {
        $this->farmers = $this->storageLoadingService->getFarmersWithActiveAgreements();
        $this->transporters = Transporter::orderBy('name')->get();
        $this->coldStorages = ColdStorage::orderBy('name')->get();
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

    // Create a separate method to refresh data
    public function refreshData()
    {
        $this->farmers = $this->storageLoadingService->getFarmersWithActiveAgreements();
        $this->transporters = Transporter::orderBy('name')->get();
        $this->coldStorages = ColdStorage::orderBy('name')->get();
        
        // If you have a list of storage loadings, refresh that too
        // $this->storageLoadings = $this->storageLoadingService->getAllStorageLoadings();
    }

    protected function rules()
    {
        $rules = (new StorageLoadingRequest())->rules();
        return $rules;
    }

    public function updatedSelectedFarmerId($value)
    {
        $this->agreement_id = null;
        if ($value) {
            $this->seedVarieties = $this->storageLoadingService->getSeedVarietiesForFarmer($value);
        } else {
            $this->seedVarieties = collect();
        }
        $this->bag_quantity = null;
    }

    public function updatedTransporterId($value)
    {
        // Reset vehicle_id when transporter changes
        $this->vehicle_id = '';
        // $this->vehicle_number = '';
        $this->transporterChanged = true;
    }

    public function updatedAgreementId($value)
    {
        if ($value) {
            $agreement = $this->storageLoadingService->findAgreementById($value);
            if ($agreement) {
                $usedBags = $this->storageLoadingService->getUsedBagsForAgreement($value);
                $this->bag_quantity = $agreement->bag_quantity - $usedBags;
                // $this->dispatch('bag-quantity-updated', ['quantity' => $this->bag_quantity]);
            }
        } else {
            $this->bag_quantity = null;
        }
    }

    public function updatedBagQuantity($value)
    {
        if (!$this->agreement_id || !$value) {
            return;
        }

        $check = $this->storageLoadingService->checkBagQuantity($this->agreement_id, $value, $this->storageLoadingId);

        if ($check['is_exceeded']) {
            $this->overrideData = $check;
            $this->showOverrideModal = true;
        } else {
            $this->extra_bags = null;
            $this->remarks = null;
        }
    }

    public function handleOverride($accepted)
    {
        if ($accepted) {
            $this->extra_bags = $this->bag_quantity - $this->overrideData['remaining_bags'];
            $this->bag_quantity = $this->overrideData['remaining_bags'];
        } else {
            $this->bag_quantity = $this->overrideData['remaining_bags'];
        }

        $this->showOverrideModal = false;
        $this->overrideData = [];
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function create()
    {
        $this->resetInputFields();
        $this->openModal();
        // $this->refreshData();

    }

    public function openModal()
    {
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->resetInputFields();
    }

    private function resetInputFields()
    {
        $this->storageLoadingId = null;
        $this->agreement_id = null;
        $this->transporter_id = null;
        $this->vehicle_id = null;
        $this->vehicle_number = null;
        $this->cold_storage_id = null;
        $this->rst_number = '';
        $this->chamber_no = '';
        $this->bag_quantity = null;
        $this->net_weight = null;
        $this->extra_bags = null;
        $this->remarks = '';
        $this->selected_farmer_id = null;
        $this->selected_seed_variety_id = null;
        $this->filteredAgreements = [];
        $this->seedVarieties = [];
    }

    public function store()
    {
        $validatedData = $this->validate($this->rules());

        try {
            $this->storageLoadingService->create($validatedData);
            // $this->refreshData();

            session()->flash('message', 'Storage loading created successfully.');
            $this->closeModal();
            $this->dispatch('refreshTable');
        } catch (\Exception $e) {
            session()->flash('error', 'Error creating storage loading: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $storageLoading = $this->storageLoadingService->findById($id);
        
        $this->storageLoadingId = $id;
        $this->agreement_id = $storageLoading->agreement_id;
        $this->transporter_id = $storageLoading->transporter_id;
        $this->vehicle_id = $storageLoading->vehicle_id;
        $this->vehicle_number = $storageLoading->vehicle_number;
        $this->cold_storage_id = $storageLoading->cold_storage_id;
        $this->rst_number = $storageLoading->rst_number;
        $this->chamber_no = $storageLoading->chamber_no;
        $this->bag_quantity = $storageLoading->bag_quantity;
        $this->net_weight = $storageLoading->net_weight;
        $this->extra_bags = $storageLoading->extra_bags;
        $this->remarks = $storageLoading->remarks;

        // Set farmer and seed variety based on agreement
        $agreement = $storageLoading->agreement;
        $this->selected_farmer_id = $agreement->farmer_id;
        $this->selected_farmer_name =$agreement->farmer->name. " ({$agreement->farmer->farmer_id})";
        $this->selected_seed_variety_id = $agreement->seed_variety_id;
        $this->filteredAgreements = $this->storageLoadingService->getAgreementsForFarmerAndSeedVariety(
            $this->selected_farmer_id,
            $this->selected_seed_variety_id
        );

        $this->openModal();
    }

    public function update()
    {
        $validatedData = $this->validate($this->rules());
        try {
            $this->storageLoadingService->update($this->storageLoadingId, $validatedData);
            // $this->refreshData();
            session()->flash('message', 'Storage loading updated successfully.');
            $this->closeModal();
            $this->dispatch('refreshTable');
        } catch (\Exception $e) {
            session()->flash('error', 'Error updating storage loading: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            $this->storageLoadingService->delete($id);
            // $this->refreshData();
            session()->flash('message', 'Storage loading permanently deleted.');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to delete storage loading: ' . $e->getMessage());
        }
    }

    public function render()
    {
        if ($this->selected_farmer_id) {
            $this->seedVarieties = $this->storageLoadingService->getSeedVarietiesForFarmer($this->selected_farmer_id);
        } else {
            $this->seedVarieties = [];
        }

        // Get vehicles based on selected transporter
        $vehicles = $this->transporter_id
            ? $this->storageLoadingService->getVehiclesByTransporter($this->transporter_id)
            : collect();

        // Reset the flag after rendering
        $this->transporterChanged = false;

        $storageLoadings = $this->storageLoadingService->getAll(
            search: $this->search,
            sortField: $this->sortField,
            sortDirection: $this->sortDirection,
            financialYear: $this->financialYears
        );

        return view('livewire.admin.storage-loadings.index', [
            'storageLoadings' => $storageLoadings,
            'farmers' => $this->farmers,
            'seedVarieties' => $this->seedVarieties,
            'transporters' => $this->transporters,
            'vehicles' => $vehicles,
            'coldStorages' => $this->coldStorages,
        ]);
    }

    public function exportData()
    {
        $fileSuffix = !empty($this->selectedYear) ? str_replace(' - ', '_to_', $this->selectedYear) : '';

        return Excel::download(
            new StorageLoadingReportExport($this->financialYears), 
            config('app.name') . "_StorageLoadings_{$fileSuffix}" . now()->format('Ymd-His') . '.xlsx'
        );
    }
}
