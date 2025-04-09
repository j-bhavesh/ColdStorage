<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\StorageUnloading;
use App\Services\StorageUnloadingService;
use App\Http\Requests\StorageUnloadingRequest;

use App\Exports\StorageUnLoadingReportExport;
use Maatwebsite\Excel\Facades\Excel;

class StorageUnloadingTable extends Component
{
    use WithPagination;

    protected $storageUnloadingService;

    public $paginationTheme = 'bootstrap';

    public $search = '';
    public $perPage = 10;
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    
    // Form properties
    public $isOpen = false;
    public $id;
    public $company_id;
    public $cold_storage_id;
    public $transporter_id;
    public $vehicle_id;
    public $vehicle_number;
    public $seed_variety_id;
    public $rst_no;
    public $chamber_no;
    public $location;
    public $bag_quantity;
    public $weight;
    public $transporterChanged = false;

    public $selectedYear = '';
    public $financialYears = [];

    protected $queryString = [
        'search' => ['except' => ''],
        'sortField' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

    protected $listeners = ['refreshTable' => '$refresh'];

    public function updatedTransporterId($value)
    {
        // Reset vehicle_id when transporter changes
        $this->vehicle_id = '';
        // $this->vehicle_number = '';
        $this->transporterChanged = true;
    }

    protected function rules()
    {
        return (new StorageUnloadingRequest())->rules();
    }

    public function boot(StorageUnloadingService $storageUnloadingService)
    {
        $this->storageUnloadingService = $storageUnloadingService;
    }

    public function updatingSearch()
    {
        $this->resetPage();
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
        $storageUnloading = $this->storageUnloadingService->findById($id);
        if ($storageUnloading) {
            $this->id = $storageUnloading->id;
            $this->company_id = $storageUnloading->company_id;
            $this->cold_storage_id = $storageUnloading->cold_storage_id;
            $this->transporter_id = $storageUnloading->transporter_id;
            $this->vehicle_id = $storageUnloading->vehicle_id;
            $this->vehicle_number = $storageUnloading->vehicle_number;
            $this->seed_variety_id = $storageUnloading->seed_variety_id;
            $this->rst_no = $storageUnloading->rst_no;
            $this->chamber_no = $storageUnloading->chamber_no;
            $this->location = $storageUnloading->location;
            $this->bag_quantity = $storageUnloading->bag_quantity;
            $this->weight = $storageUnloading->weight;

            $this->isOpen = true;
        }
    }

    public function store()
    {
        $this->validate();

        try {
            $this->storageUnloadingService->create([
                'company_id' => $this->company_id,
                'cold_storage_id' => $this->cold_storage_id,
                'transporter_id' => $this->transporter_id,
                'vehicle_id' => $this->vehicle_id,
                'vehicle_number' => $this->vehicle_number,
                'seed_variety_id' => $this->seed_variety_id,
                'rst_no' => $this->rst_no,
                'chamber_no' => $this->chamber_no,
                'location' => $this->location,
                'bag_quantity' => $this->bag_quantity,
                'weight' => $this->weight,
            ]);

            session()->flash('message', 'Storage unloading created successfully.');
            $this->closeModal();
            $this->resetInputFields();
        } catch (\Exception $e) {
            $this->addError('weight', $e->getMessage());
        }
    }

    public function update()
    {
        // $this->validate();
        $validatedData = $this->validate($this->rules());

        try {
            $this->storageUnloadingService->update($this->id,$validatedData);

            session()->flash('message', 'Storage unloading updated successfully.');
            $this->closeModal();
            $this->resetInputFields();
        } catch (\Exception $e) {
            $this->addError('weight', $e->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            $this->storageUnloadingService->delete($id);
            session()->flash('message', 'Storage unloading permanently deleted.');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to delete storage unloading: ' . $e->getMessage());
        }
    }

    private function resetInputFields()
    {
        $this->id = null;
        $this->company_id = '';
        $this->cold_storage_id = '';
        $this->transporter_id = '';
        $this->vehicle_id = '';
        $this->vehicle_number = '';
        $this->seed_variety_id = '';
        $this->rst_no = '';
        $this->chamber_no = '';
        $this->location = '';
        $this->bag_quantity = '';
        $this->weight = '';
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->resetInputFields();
    }

    public function render()
    {
        $vehicles = $this->transporter_id 
            ? $this->storageUnloadingService->getVehiclesByTransporter($this->transporter_id)
            : collect();

        // Reset the flag after rendering
        $this->transporterChanged = false;

        return view('livewire.admin.storage-unloadings.index', [
            'storageUnloadings' => $this->storageUnloadingService->getAll(
                search: $this->search,
                sortField: $this->sortField,
                sortDirection: $this->sortDirection,
                perPage: $this->perPage,
                financialYear: $this->financialYears
            ),
            'unloadingCompanies' => $this->storageUnloadingService->getActiveUnloadingCompanies(),
            'coldStorages' => $this->storageUnloadingService->getActiveColdStorages(),
            'transporters' => $this->storageUnloadingService->getActiveTransporters(),
            'vehicles' => $vehicles,
            'seedVarieties' => $this->storageUnloadingService->getActiveSeedVarieties(),
        ]);
    }

    public function exportData()
    {
        $fileSuffix = !empty($this->selectedYear) ? str_replace(' - ', '_to_', $this->selectedYear) : '';

        return Excel::download(
            new StorageUnLoadingReportExport($this->financialYears), 
            config('app.name') . "_StorageUnLoadings_{$fileSuffix}" . now()->format('Ymd-His') . '.xlsx'
        );
    }
} 