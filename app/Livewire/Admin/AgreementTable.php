<?php

namespace App\Livewire\Admin;

use App\Http\Requests\AgreementRequest;
use App\Models\Agreement;
use App\Models\Farmer;
use App\Models\SeedVariety;
use App\Services\AgreementService;
use Livewire\Component;
use Livewire\WithPagination;

use App\Exports\AgreementsExport;
use Maatwebsite\Excel\Facades\Excel;

class AgreementTable extends Component
{
    use WithPagination;

    protected $agreementService;

    public $paginationTheme = 'bootstrap';

    public $search = '';
    public $perPage = 10;
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    // Form properties
    public $isOpen = false;
    public $agreementId;
    public $farmer_id;
    public $farmer_name;
    public $seed_variety_id;
    public $rate_per_kg;
    public $agreement_date;
    public $vighas;
    public $bag_quantity;
    public $selected_farmer_village = '';

    public $selectedYear = '';
    public $financialYears = [];

    protected $queryString = [
        'search' => ['except' => ''],
        'sortField' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

    protected $listeners = ['refreshTable' => '$refresh'];

    protected function rules()
    {
        return (new AgreementRequest())->rules();
    }

    public function boot()
    {
        // // Determine current financial year
        // $currentYear = now()->month >= 2 ? now()->year : now()->year - 1;

        // // Generate financial years list dynamically
        // $this->financialYears = collect(range($currentYear, $currentYear + 2))
        //     ->map(fn($year) => "{$year}-" . ($year + 1))
        //     ->prepend('All Years') // Add first option as "All Years"
        //     ->toArray();

        // // Default selected year = current financial year
        // $this->selectedYear = "{$currentYear}-" . ($currentYear + 1);

        $this->agreementService = app(AgreementService::class);
    }

    // public function mount()
    // {
    //     // Determine current financial year
    //     $currentYear = now()->month >= 2 ? now()->year : now()->year - 1;

    //     // Generate financial years list dynamically
    //     $this->financialYears = collect(range($currentYear, $currentYear + 2))
    //         ->map(fn($year) => "{$year}-" . ($year + 1))
    //         ->prepend('All Years') // Add first option as "All Years"
    //         ->toArray();

    //     // Default selected year = current financial year
    //     $this->selectedYear = "{$currentYear}-" . ($currentYear + 1);

    //     // $this->agreementService = app(AgreementService::class);
    // }

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
        $agreement = $this->agreementService->findById($id);
        if ($agreement) {
            $this->agreementId = $agreement->id;
            $this->farmer_id = $agreement->farmer_id;
            $this->farmer_name  = $agreement->farmer->name. " ({$agreement->farmer->farmer_id})";
            $this->seed_variety_id = $agreement->seed_variety_id;
            $this->rate_per_kg = $agreement->rate_per_kg;
            $this->agreement_date = $agreement->agreement_date->format('Y-m-d');
            $this->vighas = $agreement->vighas;
            $this->bag_quantity = $agreement->bag_quantity;

            $this->isOpen = true;
        }
    }

    public function store()
    {
        $this->validate();

        try {
            $this->agreementService->create([
                'farmer_id' => $this->farmer_id,
                'seed_variety_id' => $this->seed_variety_id,
                'rate_per_kg' => $this->rate_per_kg,
                'agreement_date' => $this->agreement_date,
                'vighas' => $this->vighas,
                'bag_quantity' => $this->bag_quantity,
            ]);

            session()->flash('message', 'Agreement created successfully.');
            $this->closeModal();
            $this->resetInputFields();
        } catch (\Exception $e) {
            $this->addError('bag_quantity', $e->getMessage());
        }
    }

    public function update()
    {
        $this->validate();

        try {
            $agreement = Agreement::findOrFail($this->agreementId);

            $this->agreementService->update($agreement, [
                'farmer_id' => $this->farmer_id,
                'seed_variety_id' => $this->seed_variety_id,
                'rate_per_kg' => $this->rate_per_kg,
                'agreement_date' => $this->agreement_date,
                'vighas' => $this->vighas,
                'bag_quantity' => $this->bag_quantity,
            ]);

            session()->flash('message', 'Agreement updated successfully.');
            $this->closeModal();
            $this->resetInputFields();
        } catch (\Exception $e) {
            $this->addError('bag_quantity', $e->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            $this->agreementService->delete($id);
            session()->flash('message', 'Agreement and all associated records permanently deleted.');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to delete agreement: ' . $e->getMessage());
        }
    }

    public function updatedFarmerId($value)
    {
        if ($value) {
            $farmer = Farmer::find($value);
            $this->selected_farmer_village = $farmer ? $farmer->village_name : '';
        } else {
            $this->selected_farmer_village = '';
        }
    }

    private function resetInputFields()
    {
        $this->agreementId = null;
        $this->farmer_id = '';
        $this->seed_variety_id = '';
        $this->rate_per_kg = '';
        $this->agreement_date = '';
        $this->vighas = '';
        $this->bag_quantity = '';
        $this->selected_farmer_village = '';
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->resetInputFields();
    }

    public function render()
    {
        // dd($this->financialYears);
        $agreements = $this->agreementService->getAll(
            search: $this->search,
            sortField: $this->sortField,
            sortDirection: $this->sortDirection,
            perPage: $this->perPage,
            financialYear: $this->financialYears
        );
        
        return view('livewire.admin.agreements.index', [
            'agreements' => $agreements,
            'farmers' => Farmer::orderBy('name')->get(),
            'seedVarieties' => SeedVariety::orderBy('name')->get(),
        ]);
    }

    public function exportData()
    {
        $fileSuffix = !empty($this->selectedYear) ? str_replace(' - ', '_to_', $this->selectedYear) : '';
        
        return Excel::download(
            new AgreementsExport($this->financialYears), 
            config('app.name') . "_Agreements_{$fileSuffix}_" . now()->format('Ymd-His') . '.xlsx'
        );
    }
}
