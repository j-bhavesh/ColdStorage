<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;

use App\Models\Transporter;
use App\Services\TransporterService;
use App\Http\Requests\TransporterRequest;

class TransporterTable extends Component
{
    use WithPagination;

    protected $transporterService;

    public $paginationTheme = 'bootstrap';

    public $search = '';
    public $sortField = 'id';
    public $sortDirection = 'desc';

    public $id;
    public $name;
    public $contact_number;
    public $isOpen = false;

    protected function rules()
    {
        $rules = (new TransporterRequest())->rules();
        return $rules;
    }

    public function mount(TransporterService $transporterService)
    {
        $this->transporterService = $transporterService;
    }

    public function resetForm()
    {
        $this->reset(['id', 'name', 'contact_number']);
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

    public function store(TransporterService $transporterService)
    {
        $validatedData = $this->validate($this->rules());
        $transporterService->createTransporter($validatedData);
        session()->flash('message', 'Transporter created successfully.');
        $this->closeModal();
        $this->resetForm();
    }

    public function edit(TransporterService $transporterService, $id)
    {
        $transporter = $transporterService->getTransporterById($id);
        $this->id = $transporter->id;
        $this->name = $transporter->name;
        $this->contact_number = $transporter->contact_number;
        $this->openModal();
    }

    public function update(TransporterService $transporterService)
    {
        $validatedData = $this->validate($this->rules());

        if ($this->id) {
            $transporterService->updateTransporter($this->id, $validatedData);
            session()->flash('message', 'Transporter updated successfully.');
        }

        $this->closeModal();
        $this->resetForm();
    }

    public function delete(TransporterService $transporterService, $id)
    {
        try {
            $transporterService->deleteTransporter($id);
            session()->flash('message', 'Transporter and all associated records permanently deleted.');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to delete transporter: ' . $e->getMessage());
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
        $transporters = Transporter::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('contact_number', 'like', '%' . $this->search . '%');
                });
            })
            ->with(['creator'])
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        return view('livewire.admin.transporters.index', [
            'transporters' => $transporters
        ]);
    }
}
