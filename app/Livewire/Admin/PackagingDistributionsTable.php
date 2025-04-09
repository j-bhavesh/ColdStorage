<?php

namespace App\Livewire\Admin;

use App\Models\PackagingDistribution;
use App\Services\PackagingDistributionService;
use Livewire\Component;
use Livewire\WithPagination;

use App\Exports\PackagingDistributionReportExport;
use Maatwebsite\Excel\Facades\Excel;

class PackagingDistributionsTable extends Component
{
    use WithPagination;

    public $paginationTheme = 'bootstrap';

    public $isOpen = false;
    public $packagingDistributionId;
    public $agreement_id;
    public $bag_quantity;
    public $vehicle_number;
    public $distribution_date;
    public $received_by;
    public $search = '';
    public $sortField = 'id';
    public $sortDirection = 'desc';
    public $farmers = [];
    public $agreement_name;

    public $selectedYear = '';
    public $financialYears = [];

    protected $listeners = ['refresh' => '$refresh'];

    protected $rules = [
        'agreement_id' => 'required',
        'bag_quantity' => 'required|integer|min:1',
        'vehicle_number' => 'required|string|max:50',
        'distribution_date' => 'required|date',
        'received_by' => 'required|string|max:50',
    ];

    public function mount()
    {
        $this->loadFarmers();
    }

    public function loadFarmers()
    {
        $service = app(PackagingDistributionService::class);
        $this->farmers = $service->getFarmersWithActiveAgreements();
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
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function create()
    {
        $this->resetInputFields();
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

    private function resetInputFields()
    {
        $this->packagingDistributionId = null;
        $this->agreement_id = '';
        $this->bag_quantity = '';
        $this->vehicle_number = '';
        $this->distribution_date = '';
        $this->received_by = '';
    }

    public function store()
    {
        $this->validate();

        try {
            $service = app(PackagingDistributionService::class);
            $service->create([
                'agreement_id' => $this->agreement_id,
                'bag_quantity' => $this->bag_quantity,
                'vehicle_number' => $this->vehicle_number,
                'distribution_date' => $this->distribution_date,
                'received_by' => $this->received_by,
            ]);

            session()->flash('message', 'Packaging distribution created successfully.');
            $this->closeModal();
            $this->resetInputFields();
            $this->loadFarmers();
        } catch (\Exception $e) {
            $this->addError('bag_quantity', $e->getMessage());
        }
    }

    public function edit($id)
    {
        $service = app(PackagingDistributionService::class);
        $packagingDistribution = $service->findById($id);
        
        $this->packagingDistributionId = $id;
        $this->agreement_id = $packagingDistribution->agreement_id;
        // $this->agreement_name = $packagingDistribution->agreement_id;
        $this->bag_quantity = $packagingDistribution->bag_quantity;
        $this->vehicle_number = $packagingDistribution->vehicle_number;
        $this->distribution_date = $packagingDistribution->distribution_date->format('Y-m-d');
        $this->received_by = $packagingDistribution->received_by;
        
        $this->openModal();
    }

    public function update()
    {
        $this->validate();

        try {
            $service = app(PackagingDistributionService::class);
            $service->update($this->packagingDistributionId, [
                'agreement_id' => $this->agreement_id,
                'bag_quantity' => $this->bag_quantity,
                'vehicle_number' => $this->vehicle_number,
                'distribution_date' => $this->distribution_date,
                'received_by' => $this->received_by,
            ]);

            session()->flash('message', 'Packaging distribution updated successfully.');
            $this->closeModal();
            $this->resetInputFields();
            $this->loadFarmers();
        } catch (\Exception $e) {
            $this->addError('bag_quantity', $e->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            $service = app(PackagingDistributionService::class);
            $service->delete($id);
            session()->flash('message', 'Packaging distribution permanently deleted.');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to delete packaging distribution: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $service = app(PackagingDistributionService::class);
        return view('livewire.admin.packaging-distributions.table', [
            'packagingDistributions' => $service->getAll(
                $this->search,
                $this->sortField,
                $this->sortDirection,
                financialYear: $this->financialYears
            ),
        ]);
    }

    public function exportData()
    {
        $fileSuffix = !empty($this->selectedYear) ? str_replace(' - ', '_to_', $this->selectedYear) : '';

        return Excel::download(
            new PackagingDistributionReportExport($this->financialYears), 
            config('app.name') . "_PackagingDistributions_{$fileSuffix}" . now()->format('Ymd-His') . '.xlsx'
        );
    }
} 