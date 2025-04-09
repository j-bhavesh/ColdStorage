<?php

namespace App\Livewire\Admin;

use App\Models\Farmer;
use App\Models\Vehicle;
use App\Services\ChallanService;
use App\Http\Requests\ChallanRequest;
use Livewire\Component;
use Livewire\WithPagination;

class ChallansTable extends Component
{
    use WithPagination;

    public $paginationTheme = 'bootstrap';
    
    public $search = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    public $isOpen = false;
    public $challanId;
    public $farmer_id;
    public $farmer_name;
    public $vehicle_id;
    public $vehicle_number;
    public $challan_number;
    public $farmers;
    public $vehicles;
    public $showOverrideModal = false;
    public $overrideData = [];

    protected $listeners = ['refreshTable' => '$refresh'];
    protected $challanService;

    public function boot(ChallanService $challanService)
    {
        $this->challanService = $challanService;
    }

    public function mount()
    {
        $this->farmers = $this->challanService->getActiveFarmers();
        $this->vehicles = $this->challanService->getActiveVehicles();
    }

    protected function rules()
    {
        $rules = (new ChallanRequest())->rules();
        return $rules;
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

    public function openModal()
    {
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->resetValidation();
        $this->reset(['challanId', 'farmer_id', 'vehicle_id', 'vehicle_number', 'challan_number']);
    }

    public function create()
    {
        $this->resetValidation();
        $this->reset(['challanId', 'farmer_id', 'vehicle_id', 'vehicle_number', 'challan_number']);
        $this->openModal();
    }

    public function store()
    {
        $validatedData = $this->validate($this->rules());
        try {
            $this->challanService->create($validatedData);
            session()->flash('message', 'Challan created successfully.');
            $this->closeModal();
            $this->dispatch('refreshTable');
        } catch (\Exception $e) {
            session()->flash('error', 'Error creating challan: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $challan = $this->challanService->findById($id);
        $this->challanId = $id;
        $this->farmer_id = $challan->farmer_id;
        $this->farmer_name = "{$challan->farmer->name} ({$challan->farmer->farmer_id})";
        $this->vehicle_id = $challan->vehicle_id;
        $this->vehicle_number = $challan->vehicle_number;
        $this->challan_number = $challan->challan_number;
        $this->openModal();
    }

    public function update()
    {
        $validatedData = $this->validate($this->rules());
        try {
            $this->challanService->update($this->challanId, $validatedData);
            session()->flash('message', 'Challan updated successfully.');
            $this->closeModal();
            $this->dispatch('refreshTable');
        } catch (\Exception $e) {
            session()->flash('error', 'Error updating challan: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            $this->challanService->delete($id);
            session()->flash('message', 'Challan permanently deleted.');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to delete challan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $challans = $this->challanService->getAll(
            $this->search,
            $this->sortField,
            $this->sortDirection
        );

        return view('livewire.admin.challans.index', [
            'challans' => $challans
        ]);
    }
} 