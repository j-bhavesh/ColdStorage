<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\UnloadingCompany;
use App\Services\UnloadingCompanyService;
use App\Http\Requests\UnloadingCompanyRequest;

class UnloadingCompaniesTable extends Component
{
    use WithPagination;

    protected $unloadingCompanyService;

    public $paginationTheme = 'bootstrap';

    public $search = '';
    public $sortField = 'id';
    public $sortDirection = 'desc';
    
    // Form properties
    public $isOpen = false;
    public $id;
    public $name;
    public $contact_person;
    public $contact_number;
    public $address;
    public $status = 'active';

    protected $queryString = [
        'search' => ['except' => ''],
        'sortField' => ['except' => 'id'],
        'sortDirection' => ['except' => 'desc'],
    ];

    protected $listeners = ['refreshTable' => '$refresh'];

    protected function rules()
    {
        return (new UnloadingCompanyRequest())->rules();
    }

    public function boot(UnloadingCompanyService $unloadingCompanyService)
    {
        $this->unloadingCompanyService = $unloadingCompanyService;
    }

    public function resetForm()
    {
        $this->reset(['id', 'name', 'contact_person', 'contact_number', 'address', 'status']);
        $this->resetErrorBag();
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
        $validatedData = $this->validate($this->rules());

        $this->unloadingCompanyService->create($validatedData);

        session()->flash('message', 'Unloading company created successfully.');
        $this->closeModal();
        $this->resetForm();
    }

    public function edit($id)
    {
        $unloadingCompany = $this->unloadingCompanyService->getById($id);
        $this->id = $id;
        $this->name = $unloadingCompany->name;
        $this->contact_person = $unloadingCompany->contact_person;
        $this->contact_number = $unloadingCompany->contact_number;
        $this->address = $unloadingCompany->address;
        $this->status = $unloadingCompany->status;
        $this->openModal();
    }

    public function update()
    {
        $validatedData = $this->validate($this->rules());

        if ($this->id) {
            $this->unloadingCompanyService->update($this->id, $validatedData);
            session()->flash('message', 'Unloading company updated successfully.');
        }

        $this->closeModal();
        $this->resetForm();
    }

    public function delete($id)
    {
        try {
            $this->unloadingCompanyService->delete($id);
            session()->flash('message', 'Unloading company and all associated records permanently deleted.');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to delete unloading company: ' . $e->getMessage());
        }
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

    public function render()
    {
        return view('livewire.admin.unloading-companies.index', [
            'unloadingCompanies' => $this->unloadingCompanyService->getAll(
                $this->search,
                $this->sortField,
                $this->sortDirection
            )
        ]);
    }
} 