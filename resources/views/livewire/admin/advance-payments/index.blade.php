<div>
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div class="input-group" style="width: 250px;">
                    <input type="text" wire:model.live="search" class="form-control" placeholder="Search...">
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
                        <i class="fas fa-plus"></i> Add Advance Payment
                    </button>
                    <button wire:click="exportData" class="btn btn-success">
                        <i class="fas fa-file-excel"></i> Export
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body">
            @if (session()->has('message'))
                <div class="alert alert-success">
                    {{ session('message') }}
                </div>
            @endif

            @if (session()->has('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th wire:click="sortBy('id')" style="cursor: pointer;">
                                ID
                                @if ($sortField === 'id')
                                    <i class="bi bi-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </th>
                            <th wire:click="sortBy('farmer_id')" style="cursor: pointer;">
                                Farmer
                                @if ($sortField === 'farmer_id')
                                    <i class="bi bi-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </th>
                            <th wire:click="sortBy('amount')" style="cursor: pointer;">
                                Amount
                                @if ($sortField === 'amount')
                                    <i class="bi bi-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </th>
                            <th wire:click="sortBy('payment_date')" style="cursor: pointer;">
                                Payment Date
                                @if ($sortField === 'payment_date')
                                    <i class="bi bi-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </th>
                            <th wire:click="sortBy('taken_by')" style="cursor: pointer;">
                                Taken By
                                @if ($sortField === 'taken_by')
                                    <i class="bi bi-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </th>
                            <th wire:click="sortBy('taken_by_name')" style="cursor: pointer;">
                                Taken By Name
                                @if ($sortField === 'taken_by_name')
                                    <i class="bi bi-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </th>
                            <th>Created By</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($advancePayments as $payment)
                            @php
                                $createdBy = $payment->created_by === auth()->user()->id ? 'You' : $payment->creator->name;
                            @endphp
                            <tr>
                                <td>{{ $payment->id }}</td>
                                <td>
                                    {{ $payment->farmer->name ?? 'N/A' }}
                                    ({{ $payment->farmer->farmer_id ?? 'N/A' }})
                                </td>
                                <td>₹{{ number_format($payment->amount, 2) }}</td>
                                <td>{{ $payment->payment_date->format(env('DATE_FORMATE')) }}</td>
                                <td>{{ ucfirst($payment->taken_by) }}</td>
                                <td>{{ $payment->taken_by_name ?? 'N/A' }}</td>
                                <td>{{ $payment->creator->name }}</td>
                                <td>
                                    <button wire:click="edit({{ $payment->id }})" class="btn btn-sm btn-primary">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button type="button" 
                                        class="btn btn-sm btn-danger" 
                                        onclick="if(confirm('⚠️ WARNING: This will permanently delete the advance payment.\n\nThis action cannot be undone. Are you sure you want to proceed?')) { Livewire.find(document.querySelector('[wire\\:id]').getAttribute('wire:id')).call('delete', {{ $payment->id }}) }">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">No advance payments found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $advancePayments->links() }}
            </div>
        </div>
    </div>

    @include('livewire.admin.advance-payments.form')
</div>

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

<!-- Select2 Ajax -->
@push('scripts')
<script>
$(document).ready(function(){
    function initSelect2() {
        setTimeout(function() {
            $('#farmer_id').select2({
                placeholder: 'Search and select farmer...',
                // allowClear: true,
                width: '100%',
                dropdownParent: $('.modal-content'),
                containerCssClass: 'farmers-select2-container',
                selectionCssClass: 'farmers-select2-selection',
                dropdownCssClass: 'farmers-select2-dropdown',
                maximumSelectionLength: 2, // AJAX triggers only after 2 characters

                ajax: {
                    url: "{{ route('admin.farmers.search') }}",
                    dataType: 'json',
                    delay: 250, // wait 250ms after typing
                    data: function (params) {
                        return {
                            search: params.term, // user input
                            module: 'advance-payment'
                        };
                    },
                    processResults: function (data) {
                        // Map results to Select2 format
                        return {
                            results: data
                        };
                    },
                    cache: true
                }
            });

            // Set initial value if editing
            let selectedId = @this.farmer_id ? @this.farmer_id : null;
            let selectedText = @this.farmer_name ? @this.farmer_name : null;

            if (selectedId && selectedText) {
                let option = new Option(selectedText, selectedId, true, true);
                $('#farmer_id').append(option).trigger('change');
            }

            // Sync back to Livewire when user selects
            $('#farmer_id').off('change.select2').on('change.select2', function (e) {
                @this.set('farmer_id', $(this).val());
            });
        },100);
    }

    initSelect2();

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
        initSelect2();
        initFinancialYearPicker();
    });

    Livewire.hook('element.updated', (el, component) => {
        if ($(el).find('.farmer_id').length > 0) {
            initSelect2();
        }
        initFinancialYearPicker();
    });

    Livewire.hook('morph.updated', (el, component) => {
        initSelect2();
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