<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Farmer;
use App\Services\FarmerService;
use App\Http\Requests\FarmerRequest;

use App\Exports\FarmersRegistrationsExport;
use Maatwebsite\Excel\Facades\Excel;

class FarmersTable extends Component
{
    use WithPagination;

    protected $farmerService;

    public $paginationTheme = 'bootstrap';

    public $search = '';
    public $sortField = 'id';
    public $sortDirection = 'desc';

    public $id;
    public $farmer_id;
    public $name;
    public $village_name;
    public $phone;
    public $isOpen = false;

    public $selectedYear = '';
    public $financialYears = [];

    protected function rules()
    {
        // Get rules from FarmerRequest
        $rules = (new FarmerRequest())->rules();

        // Remove user_id rule as it's handled by the service
        unset($rules['user_id']);

        return $rules;
    }

    public function mount(FarmerService $farmerService)
    {
        $this->farmerService = $farmerService;
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

    // Reset form
    public function resetForm()
    {
        $this->reset(['name', 'village_name', 'phone']);
        $this->resetErrorBag();
    }

    // Create new farmer
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

    public function store(FarmerService $farmerService)
    {
        $validatedData = $this->validate($this->rules());
        $result = $farmerService->createFarmer($validatedData);

        $message = $result['is_new']
            ? 'Farmer created successfully.'
            : 'Farmer already exists.';

        session()->flash('message', $message);

        $this->closeModal();
        $this->resetForm();
    }

    public function edit(FarmerService $farmerService, $id)
    {
        $farmer = $farmerService->getFarmerById($id);
        // $this->farmer_id = $id;
        $this->id = $farmer->id;
        $this->name = $farmer->name;
        $this->farmer_id = $farmer->farmer_id;
        $this->village_name = $farmer->village_name;
        $this->phone = $farmer->user->phone;
        $this->openModal();
    }

    public function update(FarmerService $farmerService)
    {
        $validatedData = $this->validate($this->rules());

        if ($this->id) {
            $farmer = $farmerService->getFarmerById($this->id);
            $farmerService->updateFarmer($farmer, $validatedData);
            session()->flash('message', 'Farmer updated successfully.');
        }

        $this->closeModal();
        $this->resetForm();
    }

    public function delete(FarmerService $farmerService, $id)
    {
        try {
            $farmer = $farmerService->getFarmerById($id);
            $farmerService->deleteFarmer($farmer);
            session()->flash('message', 'Farmer and all associated records permanently deleted.');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to delete farmer: ' . $e->getMessage());
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
        $farmers = Farmer::with(['user', 'creator'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('village_name', 'like', '%' . $this->search . '%')
                        ->orWhere('farmer_id', 'like', '%' . $this->search . '%')
                        ->orWhereHas('user', function($q) {
                            $q->where('phone', 'like', '%' . $this->search . '%');
                        });
                });
            })
            // ✅ Add date filter (based on $this->financialYears)
            ->when(!empty($this->financialYears), function ($query) {
                $startDate = $this->financialYears['startDate'] ?? null;
                $endDate = $this->financialYears['endDate'] ?? null;

                if ($startDate && $endDate) {
                    $query->whereBetween('created_at', [
                        \Carbon\Carbon::parse($startDate)->startOfDay(),
                        \Carbon\Carbon::parse($endDate)->endOfDay(),
                    ]);
                }
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        return view('livewire.admin.farmers.index', [
            'farmers' => $farmers
        ]);
    }

    public function exportData()
    {
        $fileSuffix = !empty($this->selectedYear) ? str_replace(' - ', '_to_', $this->selectedYear) : '';

        return Excel::download(
            new FarmersRegistrationsExport($this->financialYears), 
            config('app.name') . "_Farmers_Rgistrations_{$fileSuffix}_" . now()->format('Ymd-His') . '.xlsx'
        );
    }
}
