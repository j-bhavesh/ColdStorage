<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <div class="input-group" style="width: 250px;">
                <input type="text" wire:model.live="search" class="form-control" placeholder="Search Unloadings...">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
            </div>
            <div class="classified-data-wrapper" wire:ignore>
                {{-- <select wire:model.live="selectedYear" class="form-select" style="width: 180px;">
                    @foreach($financialYears as $year)
                        <option value="{{ $year === 'All Years' ? '' : $year }}">{{ $year }}</option>
                    @endforeach
                </select> --}}
                 <input type="text" id="financialYearPicker" class="form-control" placeholder="Select Date" autocomplete="off" style="width: 220px; text-align: center;" />
            </div>
            <div class="tbl-header-actions-bar">
                <button wire:click="create" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add New Unloading
                </button>
                <button wire:click="exportData" class="btn btn-success">
                    <i class="fas fa-file-excel"></i> Export
                </button>
            </div>
        </div>
    </div>

    <div class="card-body p-0">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th wire:click="sortBy('id')" class="cursor-pointer">
                        ID
                        @if($sortField === 'id')
                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                        @endif
                    </th>
                    <th wire:click="sortBy('company_id')" class="cursor-pointer">
                        Company
                        @if($sortField === 'company_id')
                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                        @endif
                    </th>
                    <th wire:click="sortBy('cold_storage_id')" class="cursor-pointer">
                        Cold Storage
                        @if($sortField === 'cold_storage_id')
                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                        @endif
                    </th>
                    <th wire:click="sortBy('transporter_id')" class="cursor-pointer">
                        Transporter
                        @if($sortField === 'transporter_id')
                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                        @endif
                    </th>
                    <th wire:click="sortBy('vehicle_id')" class="cursor-pointer">
                        Vehicle
                        @if($sortField === 'vehicle_id')
                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                        @endif
                    </th>
                    <th wire:click="sortBy('seed_variety_id')" class="cursor-pointer">
                        Seed Variety
                        @if($sortField === 'seed_variety_id')
                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                        @endif
                    </th>
                    <th wire:click="sortBy('bag_quantity')" class="cursor-pointer">
                        Bag Quantity
                        @if($sortField === 'bag_quantity')
                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                        @endif
                    </th>
                    <th wire:click="sortBy('weight')" class="cursor-pointer">
                        Weight
                        @if($sortField === 'weight')
                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                        @endif
                    </th>
                    <th class="cursor-pointer">
                        Location
                    </th>
                    <th class="cursor-pointer">
                        Date
                    </th>
                    <th>Created By</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($storageUnloadings as $unloading)
                    <tr>
                        <td>{{ $unloading->id }}</td>
                        <td>{{ $unloading->unloadingCompany?->name ?? 'N/A' }}</td>
                        <td>{{ $unloading->coldStorage?->name ?? 'N/A' }}</td>
                        <td>{{ $unloading->transporter?->name ?? 'N/A' }}</td>
                        {{-- <td>{{ $unloading->vehicle?->vehicle_number ?? 'N/A' }}</td> --}}
                        <td>{{ $unloading->vehicle_number ?? 'N/A' }}</td>
                        <td>{{ $unloading->seedVariety?->name ?? 'N/A' }}</td>
                        <td>{{ $unloading->bag_quantity }}</td>
                        <td>{{ $unloading->weight }}</td>
                        <td>{{ $unloading->location }}</td>
                        <td>{{ $unloading->created_at->format(env('DATE_FORMATE')) }}</td>
                        <td>{{ $unloading->creator->name }}</td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <button wire:click="edit({{ $unloading->id }})" class="btn btn-info">
                                    <i class="text-white fas fa-edit"></i>
                                </button>
                                <button type="button" 
                                    class="btn btn-danger" 
                                    onclick="if(confirm('⚠️ WARNING: This will permanently delete the storage unloading.\n\nThis action cannot be undone. Are you sure you want to proceed?')) { Livewire.find(document.querySelector('[wire\\:id]').getAttribute('wire:id')).call('delete', {{ $unloading->id }}) }">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="12" class="text-center py-3">No unloadings found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($storageUnloadings->hasPages())
        <div class="card-footer clearfix">
            {{ $storageUnloadings->links() }}
        </div>
    @endif

    @include('livewire.admin.storage-unloadings.form')

    <!-- Success Message Toast -->
    @if (session()->has('message'))
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
        <div class="toast show align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    {{ session('message') }}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>
    @endif

    <!-- Error Message Toast -->
    @if (session()->has('error'))
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
        <div class="toast show align-items-center text-white bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    {{ session('error') }}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>
    @endif

    <style>
        .cursor-pointer {
            cursor: pointer;
        }
    </style>
</div>

<!-- Select2 Ajax -->
@push('scripts')
<script>
$(document).ready(function(){

    // Daterange picker JS
    function initFinancialYearPicker() {
        const pickerInput = $('#financialYearPicker');

        // Prevent duplicate picker initialization
        if (!pickerInput.length || pickerInput.data('daterangepicker')) {
            return;
        }

        // Get current year
        const currentYear = moment().year();
        const minYear = currentYear - 1; // Previous year
        const maxYear = currentYear + 3; // Next 3 years (total 5 years including current and previous)

        pickerInput.daterangepicker({
            autoUpdateInput: false,
            opens: 'center',
            linkedCalendars:false,
            showDropdowns: true,
            cancelButtonClasses : 'btn-danger',
            applyButtonClasses : 'btn-success',
            minYear: minYear,
            maxYear: maxYear,
            locale: {
                format: 'DD-MM-YYYY',
                cancelLabel: 'Clear'
            },
        });

        // Get the picker instance
        const picker = pickerInput.data('daterangepicker');

        // Override the updateCalendars method to customize year dropdowns
        if (picker) {
            const originalUpdateCalendars = picker.updateCalendars.bind(picker);
            
            picker.updateCalendars = function() {
                originalUpdateCalendars();
                
                // Fix both calendars to have same year range
                setTimeout(() => {
                    const $calendars = $('.daterangepicker .drp-calendar');
                    
                    $calendars.each(function() {
                        const $yearSelect = $(this).find('select.yearselect');
                        
                        if ($yearSelect.length) {
                            const currentSelectedYear = parseInt($yearSelect.val());
                            
                            // Clear and rebuild options with full range
                            $yearSelect.empty();
                            
                            // Add years from minYear to maxYear (2024-2028)
                            for (let year = minYear; year <= maxYear; year++) {
                                $yearSelect.append(`<option value="${year}">${year}</option>`);
                            }
                            
                            // Restore the selected year if it's in range
                            if (currentSelectedYear >= minYear && currentSelectedYear <= maxYear) {
                                $yearSelect.val(currentSelectedYear);
                            }
                        }
                    });
                }, 0);
            };
        }

        // Customize the date range display on show
        pickerInput.on('show.daterangepicker', function(ev, picker) {
            updateDateRangeLabel(picker);
            // fixRightCalendarYearDropdown();
        });

        // pickerInput.on('showCalendar.daterangepicker', function(ev, picker) {
        //     
        // });

        // Apply selection
        pickerInput.on('apply.daterangepicker', function (ev, pickerData) {
            const start = pickerData.startDate.format('DD-MM-YYYY');
            const end = pickerData.endDate.format('DD-MM-YYYY');

            updateDateRangeLabel(pickerData);
            $(this).val(start + ' to ' + end);
            Livewire.dispatch('yearRangeSelected', { range: start + ' - ' + end });
            // Update Livewire property
            @this.set('selectedYear', `${start} - ${end}`);
        });

        // Update on calendar changes
        $(document).on('mouseup', '.daterangepicker td.available', function() {
            setTimeout(function() {
                const picker = pickerInput.data('daterangepicker');
                if (picker) {
                    updateDateRangeLabel(picker);
                }
            }, 10);
        });

        // Clear selection
        pickerInput.on('cancel.daterangepicker', function (ev, pickerData) {
            const today = moment(); // current date

            // Clear the input box
            $(this).val('');

            // Reset picker start and end dates to today
            pickerData.setStartDate(today);
            pickerData.setEndDate(today);

            // Update the visible date range label to show today's date
            updateDateRangeLabel(pickerData);

            // Clear Livewire property and emit reset
            Livewire.dispatch('yearRangeSelected', { range: '' });
            @this.set('selectedYear', '');
        });

        console.log('✅ Daterangepicker initialized');
    }

    initFinancialYearPicker();

    // Re-init every time Livewire updates DOM
    Livewire.hook('message.processed', (message, component) => {
        initFinancialYearPicker();
    });

    Livewire.hook('element.updated', (el, component) => {
        initFinancialYearPicker();
    });

    Livewire.hook('morph.updated', (el, component) => {
        initFinancialYearPicker();
    });

    // Update the selected date lable
    function updateDateRangeLabel(picker) {
        const start = picker.startDate.format('DD-MM-YYYY');
        const end = picker.endDate.format('DD-MM-YYYY');
        
        // Find and update the date range display element
        const $dateRangeDisplay = $('.daterangepicker .drp-selected');
        
        if ($dateRangeDisplay.length) {
            $dateRangeDisplay.html(`<strong>Start Date:</strong> ${start} &nbsp; - &nbsp; <strong>End Date:</strong> ${end}`);
        }
    }

});

</script>
@endpush 