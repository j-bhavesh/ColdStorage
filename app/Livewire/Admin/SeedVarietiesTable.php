<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;

use App\Models\SeedVariety;

use App\Services\SeedVarietyService;
use App\Http\Requests\SeedVarietyRequest;

class SeedVarietiesTable extends Component
{
    use WithPagination;

    protected $seedVarietyService;

    public $paginationTheme = 'bootstrap';

    public $search = '';
    public $sortField = 'id';
    public $sortDirection = 'desc';

    public $id;
    public $name;
    public $description;
    public $isOpen = false;

    protected function rules()
    {
        $rules = (new SeedVarietyRequest())->rules();

        return $rules;
    }

    public function mount(SeedVarietyService $seedVarietyService)
    {
        $this->seedVarietyService = $seedVarietyService;
    }

    public function resetForm(){
        $this->reset(['id', 'name','description']);
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

    public function store(SeedVarietyService $seedVarietyService)
    {
        $validatedData = $this->validate($this->rules());

        try {
            $result = $seedVarietyService->createSeedVariety($validatedData);

            $message = $result['is_new']
                ? 'Seed variety created successfully.'
                : 'Seed variety already exists.';

            session()->flash('message', $message);
            
            $this->closeModal();
            $this->resetForm();
        } catch (\Exception $e) {
            $this->addError('name', $e->getMessage());
        }
    }

    public function edit(SeedVarietyService $seedVarietyService, $id)
    {
        $seedVariety = $seedVarietyService->getSeedVarietyById($id);
        $this->id = $seedVariety->id;
        $this->name = $seedVariety->name;
        $this->description = $seedVariety->description;
        $this->openModal();
    }

    public function update(SeedVarietyService $seedVarietyService)
    {
        $validatedData = $this->validate($this->rules());
        try {
            if($this->id){
                $seedVariety = $seedVarietyService->getSeedVarietyById($this->id);
                $seedVarietyService->updateSeedVariety($seedVariety, $validatedData);
                session()->flash('message', 'Seed variety updated successfully.');
            }
            $this->closeModal();
            $this->resetForm();
        } catch (\Exception $e) {
            $this->addError('name', $e->getMessage());
        }
    }

    public function delete(SeedVarietyService $seedVarietyService, $id)
    {
        try {
            $seedVariety = $seedVarietyService->getSeedVarietyById($id);
            $seedVarietyService->deleteSeedVariety($seedVariety);
            session()->flash('message', 'Seed variety and all associated records permanently deleted.');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to delete seed variety: ' . $e->getMessage());
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

        $seedVarieties = SeedVariety::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            })
            ->with(['creator'])
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        return view('livewire.admin.seed-varieties.index', [
            'seedVarieties' => $seedVarieties
        ]);

    }

}
