<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ColdStorage;
use App\Services\ColdStorageService;
use App\Http\Requests\ColdStorageRequest;

class ColdStoragesTable extends Component
{
    use WithPagination;

    protected $coldStorageService;

    public $paginationTheme = 'bootstrap';

    public $search = '';
    public $sortField = 'id';
    public $sortDirection = 'desc';
    public $perPage = 10;

    // Form properties
    public $isOpen = false;
    public $id;
    public $name;
    public $address;
    public $capacity;
    public $remarks;

    protected $queryString = [
        'search' => ['except' => ''],
        'sortField' => ['except' => 'id'],
        'sortDirection' => ['except' => 'desc'],
        'perPage' => ['except' => 10],
    ];

    protected $listeners = ['refreshTable' => '$refresh'];

    protected function rules()
    {
        return (new ColdStorageRequest())->rules();
    }

    public function boot(ColdStorageService $coldStorageService)
    {
        $this->coldStorageService = $coldStorageService;
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
        $coldStorage = $this->coldStorageService->findById($id);
        if ($coldStorage) {
            $this->id = $coldStorage->id;
            $this->name = $coldStorage->name;
            $this->address = $coldStorage->address;
            $this->capacity = $coldStorage->capacity;
            $this->remarks = $coldStorage->remarks;
            $this->isOpen = true;
        }
    }

    public function store()
    {
        $this->validate();

        try {
            $this->coldStorageService->create([
                'name' => $this->name,
                'address' => $this->address,
                'capacity' => $this->capacity,
                'remarks' => $this->remarks,
            ]);

            session()->flash('message', 'Cold storage created successfully.');
            $this->closeModal();
            $this->resetInputFields();
        } catch (\Exception $e) {
            $this->addError('name', $e->getMessage());
        }
    }

    public function update()
    {
        $this->validate();

        try {
            $this->coldStorageService->update($this->id, [
                'name' => $this->name,
                'address' => $this->address,
                'capacity' => $this->capacity,
                'remarks' => $this->remarks,
            ]);

            session()->flash('message', 'Cold storage updated successfully.');
            $this->closeModal();
            $this->resetInputFields();
        } catch (\Exception $e) {
            $this->addError('name', $e->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            $this->coldStorageService->delete($id);
            session()->flash('message', 'Cold storage and all associated records permanently deleted.');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to delete cold storage: ' . $e->getMessage());
        }
    }

    private function resetInputFields()
    {
        $this->id = null;
        $this->name = '';
        $this->address = '';
        $this->capacity = '';
        $this->remarks = '';
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->resetInputFields();
    }

    public function render()
    {
        return view('livewire.admin.cold-storages.index', [
            'coldStorages' => $this->coldStorageService->getAll(
                $this->search,
                $this->sortField,
                $this->sortDirection,
                $this->perPage
            )
        ]);
    }
} 