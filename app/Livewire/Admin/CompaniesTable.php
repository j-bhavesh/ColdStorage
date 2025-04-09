<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;

use App\Models\Company;
use App\Services\CompanyService;
use App\Http\Requests\CompanyRequest;

class CompaniesTable extends Component
{
    use WithPagination;
    use WithFileUploads;

    protected $companyService;

    public $paginationTheme = 'bootstrap';

    public $search = '';
    public $sortField = 'id';
    public $sortDirection = 'desc';

    public $company_id;
    public $name;
    public $contact_person;
    public $contact_number;
    public $address;
    public $isOpen = false;

    protected function rules()
    {
        // Get rules from CompanyRequest
        $rules = (new CompanyRequest())->rules();
        
        // Remove user_id rule as it's handled by the service
        unset($rules['user_id']);
        
        return $rules;
    }

    public function boot(CompanyService $companyService)
    {
        $this->companyService = $companyService;
    }
    
    // Listen for the company-create event
    protected $listeners = ['company-create' => 'create'];

    // Reset form
    public function resetForm()
    {
        $this->reset(['company_id', 'name', 'contact_person', 'contact_number', 'address']);
        $this->resetErrorBag();
    }

     // Create new company
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

        $this->companyService->createCompany($validatedData);

        session()->flash('message', 'Company created successfully.');
        $this->closeModal();
        $this->resetForm();
    }

    public function edit($id)
    {
        $company = $this->companyService->getCompanyById($id);
        $this->company_id = $id;
        $this->name = $company->name;
        $this->contact_person = $company->contact_person;
        $this->contact_number = $company->contact_number;
        $this->address = $company->address;
        $this->openModal();
    }

    public function update()
    {
        $validatedData = $this->validate($this->rules());

        if ($this->company_id) {
            $company = $this->companyService->getCompanyById($this->company_id);
            $this->companyService->updateCompany($company, $validatedData);
            session()->flash('message', 'Company updated successfully.');
        }

        $this->closeModal();
        $this->resetForm();
    }

    public function delete($id)
    {
        try {
            $company = $this->companyService->getCompanyById($id);
            $this->companyService->deleteCompany($company);
            session()->flash('message', 'Company and all associated records permanently deleted.');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to delete company: ' . $e->getMessage());
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
        return view('livewire.admin.companies.index', [
            'companies' => $this->companyService->searchAndSortCompanies(
                $this->search,
                $this->sortField,
                $this->sortDirection
            )
        ]);
    }
}