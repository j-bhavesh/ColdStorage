<?php

namespace App\Livewire\Admin;

use App\Http\Requests\AdvancePaymentRequest;
use App\Models\AdvancePayment;
use App\Models\Agreement;
use App\Models\Farmer;
use App\Services\AdvancePaymentService;
use Livewire\Component;
use Livewire\WithPagination;

use App\Exports\AdvancePaymentsReportExport;
use Maatwebsite\Excel\Facades\Excel;

class AdvancePaymentsTable extends Component
{
    use WithPagination;

    protected $advancePaymentService;

    public $paginationTheme = 'bootstrap';

    public $search = '';
    public $perPage = 10;
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    
    // Form properties
    public $isOpen = false;
    public $advancePaymentId;
    public $farmer_id;
    public $farmer_name;
    public $amount;
    public $payment_date;
    public $taken_by = 'self';
    public $taken_by_name;

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
        return (new AdvancePaymentRequest())->rules();
    }

    public function boot()
    {
        $this->advancePaymentService = app(AdvancePaymentService::class);
    }

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
        $advancePayment = $this->advancePaymentService->findById($id);
        if ($advancePayment) {
            $this->advancePaymentId = $advancePayment->id;
            $this->farmer_id = $advancePayment->farmer_id;
            $this->farmer_name  = $advancePayment->farmer->name. " ({$advancePayment->farmer->farmer_id})";
            $this->amount = $advancePayment->amount;
            $this->payment_date = $advancePayment->payment_date->format('Y-m-d');
            $this->taken_by = $advancePayment->taken_by;
            $this->taken_by_name = $advancePayment->taken_by_name;

            $this->isOpen = true;
        }
    }

    public function store()
    {
        $this->validate();

        try {
            $this->advancePaymentService->create([
                'farmer_id' => $this->farmer_id,
                'amount' => $this->amount,
                'payment_date' => $this->payment_date,
                'taken_by' => $this->taken_by,
                'taken_by_name' => $this->taken_by_name,
            ]);

            session()->flash('message', 'Advance payment created successfully.');
            $this->closeModal();
            $this->resetInputFields();
        } catch (\Exception $e) {
            $this->addError('amount', $e->getMessage());
        }
    }

    public function update()
    {
        $this->validate();

        try {
            $this->advancePaymentService->update($this->advancePaymentId, [
                'farmer_id' => $this->farmer_id,
                'amount' => $this->amount,
                'payment_date' => $this->payment_date,
                'taken_by' => $this->taken_by,
                'taken_by_name' => $this->taken_by_name,
            ]);

            session()->flash('message', 'Advance payment updated successfully.');
            $this->closeModal();
            $this->resetInputFields();
        } catch (\Exception $e) {
            $this->addError('amount', $e->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            $this->advancePaymentService->delete($id);
            session()->flash('message', 'Advance payment permanently deleted.');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to delete advance payment: ' . $e->getMessage());
        }
    }

    private function resetInputFields()
    {
        $this->advancePaymentId = null;
        $this->farmer_id = '';
        $this->amount = '';
        $this->payment_date = '';
        $this->taken_by = 'self';
        $this->taken_by_name = '';
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->resetInputFields();
    }

    public function render()
    {
        $advancePayments = $this->advancePaymentService->getAll(
            search: $this->search,
            sortField: $this->sortField,
            sortDirection: $this->sortDirection,
            perPage: $this->perPage,
            financialYear: $this->financialYears
        );

        return view('livewire.admin.advance-payments.index', [
            'advancePayments' => $advancePayments,
            'farmers' => Farmer::orderBy('name')->get(),
        ]);
    }

    public function exportData()
    {
        $fileSuffix = !empty($this->selectedYear) ? str_replace(' - ', '_to_', $this->selectedYear) : '';

        return Excel::download(
            new AdvancePaymentsReportExport($this->financialYears), 
            config('app.name') . "_AdvancePayments_{$fileSuffix}" . now()->format('Ymd-His') . '.xlsx'
        );
    }
} 