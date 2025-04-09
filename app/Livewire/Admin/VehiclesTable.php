<?php

namespace App\Livewire\Admin;

use App\Models\Vehicle;
use App\Models\Transporter;
use App\Services\VehicleService;
use App\Http\Requests\VehicleRequest;
use Livewire\Component;
use Livewire\WithPagination;

class VehiclesTable extends Component
{
    use WithPagination;

    protected $vehicleService;

    public $paginationTheme = 'bootstrap';

    public $search = '';
    public $sortField = 'id';
    public $sortDirection = 'desc';
    public $perPage = 10;

    // Form properties
    public $isOpen = false;
    public $id;
    public $vehicle_number;
    public $transporter_id;
    public $vehicle_type;

    protected $queryString = [
        'search' => ['except' => ''],
        'sortField' => ['except' => 'id'],
        'sortDirection' => ['except' => 'desc'],
        'perPage' => ['except' => 10],
    ];

    protected $listeners = ['refreshTable' => '$refresh'];

    protected function rules()
    {
        return (new VehicleRequest())->rules();
    }

    public function boot()
    {
        $this->vehicleService = app(VehicleService::class);
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
        $vehicle = $this->vehicleService->findById($id);
        if ($vehicle) {
            $this->id = $vehicle->id;
            $this->vehicle_number = $vehicle->vehicle_number;
            $this->transporter_id = $vehicle->transporter_id;
            $this->vehicle_type = $vehicle->vehicle_type;
            $this->isOpen = true;
        }
    }

    public function store()
    {
        $this->validate();

        try {
            $this->vehicleService->create([
                'vehicle_number' => $this->vehicle_number,
                'transporter_id' => $this->transporter_id,
                'vehicle_type' => $this->vehicle_type,
            ]);

            session()->flash('message', 'Vehicle created successfully.');
            $this->closeModal();
            $this->resetInputFields();
        } catch (\Exception $e) {
            $this->addError('vehicle_number', $e->getMessage());
        }
    }

    public function update()
    {
        $this->validate();

        try {
            $this->vehicleService->update($this->id, [
                'vehicle_number' => $this->vehicle_number,
                'transporter_id' => $this->transporter_id,
                'vehicle_type' => $this->vehicle_type,
            ]);

            session()->flash('message', 'Vehicle updated successfully.');
            $this->closeModal();
            $this->resetInputFields();
        } catch (\Exception $e) {
            $this->addError('vehicle_number', $e->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            $this->vehicleService->delete($id);
            session()->flash('message', 'Vehicle and all associated records permanently deleted.');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to delete vehicle: ' . $e->getMessage());
        }
    }

    private function resetInputFields()
    {
        $this->id = null;
        $this->vehicle_number = '';
        $this->transporter_id = '';
        $this->vehicle_type = '';
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->resetInputFields();
    }

    public function render()
    {
        return view('livewire.admin.vehicles.index', [
            'vehicles' => $this->vehicleService->getAll(
                $this->search,
                $this->sortField,
                $this->sortDirection,
                $this->perPage
            ),
            'transporters' => Transporter::all(),
        ]);
    }
} 