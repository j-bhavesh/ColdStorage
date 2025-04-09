<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <div class="input-group input-group-sm" style="width: 250px;">
                <input type="text" wire:model.live="search" class="form-control" placeholder="Search Agreements...">
                <div class="input-group-append">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                </div>
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
                <button wire:click="create" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Add New Agreement
                </button>
                <button wire:click="exportData" class="btn btn-success btn-sm">
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
                    <th wire:click="sortBy('farmer_id')" class="cursor-pointer">
                        Farmer Id
                        @if($sortField === 'farmer_id')
                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                        @endif
                    </th>

                    <th wire:click="sortBy('farmer_name')" class="cursor-pointer">
                        Farmer
                        @if($sortField === 'farmer_name')
                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                        @endif
                    </th>
                    <th wire:click="sortBy('village_name')" class="cursor-pointer">
                        Village
                        @if($sortField === 'village_name')
                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                        @endif
                    </th>
                    <th wire:click="sortBy('phone')" class="cursor-pointer">
                        Phone
                        @if($sortField === 'phone')
                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                        @endif
                    </th>
                    <th wire:click="sortBy('seed_variety_id')" class="cursor-pointer">
                        Seed Variety
                        @if($sortField === 'seed_variety_id')
                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                        @endif
                    </th>
                    <th wire:click="sortBy('rate_per_kg')" class="cursor-pointer">
                        Rate per KG
                        @if($sortField === 'rate_per_kg')
                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                        @endif
                    </th>
                    <th wire:click="sortBy('agreement_date')" class="cursor-pointer">
                        Agreement Date
                        @if($sortField === 'agreement_date')
                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                        @endif
                    </th>
                    <th wire:click="sortBy('vighas')" class="cursor-pointer">
                        Vighas
                        @if($sortField === 'vighas')
                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                        @endif
                    </th>
                    <th wire:click="sortBy('bag_quantity')" class="cursor-pointer">
                        {{-- Bag Quantity --}}
                        Total Bag
                        @if($sortField === 'bag_quantity')
                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                        @endif
                    </th>
                    <th class="cursor-pointer">
                        Received Bag
                    </th>
                    <th class="cursor-pointer">
                        Pending Bag
                    </th>
                    <th class="cursor-pointer">
                        Surplus Bag
                    </th>
                    <th>Created By</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($agreements as $agreement)
                    @php
                        $receivedBags = !empty( $agreement->received_bags ) ? $agreement->received_bags : '-';
                        $pendingBags = !empty( $agreement->pending_bags ) ? $agreement->pending_bags : '-';
                        $surplusBags = !empty( $agreement->surplus_bags ) ? $agreement->surplus_bags : '-';
                    @endphp
                    <tr>
                        <td>{{ $agreement->id }}</td>
                        <td>{{ $agreement->farmer->farmer_id }}</td>
                        <td>{{ $agreement->farmer->name }}</td>
                        <td>{{ $agreement->farmer->village_name }}</td>
                        <td>{{ $agreement->farmer->user->phone }}</td>
                        <td>{{ $agreement->seedVariety->name }}</td>
                        <td>{{ $agreement->rate_per_kg }}</td>
                        <td>{{ $agreement->agreement_date->format(env('DATE_FORMATE')) }}</td>
                        <td>{{ $agreement->vighas }}</td>
                        <td>{{ $agreement->bag_quantity }}</td>
                        <td>{{ $receivedBags }}</td>
                        <td>{{ $pendingBags }}</td>
                        <td>{{ $surplusBags }}</td>
                        <td>{{ $agreement->creator->name }}</td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <button wire:click="edit({{ $agreement->id }})" class="btn btn-info">
                                    <i class="text-white fas fa-edit"></i>
                                </button>
                                <button type="button"
                                    class="btn btn-danger"
                                    onclick="if(confirm('⚠️ WARNING: This will permanently delete the agreement and ALL associated data including:\n\n• Packaging Distributions\n• Storage Loadings\n\nThis action cannot be undone. Are you sure you want to proceed?')) { Livewire.find(document.querySelector('[wire\\:id]').getAttribute('wire:id')).call('delete', {{ $agreement->id }}) }">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="15" class="text-center py-3">No agreements found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($agreements->hasPages())
        <div class="card-footer clearfix">
            {{ $agreements->links() }}
        </div>
    @endif

    @include('livewire.admin.agreements.form', [
        'isOpen' => $isOpen,
        'agreementId' => $agreementId,
        'farmers' => $farmers,
        'seedVarieties' => $seedVarieties
    ])

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
    // Ensure jQuery is loaded
    if (typeof $ === 'undefined') {
        console.error('jQuery not loaded!');
        return;
    }

    // Select2 Ajax
    function initSelect2() {
        $('.farmer_id').select2({
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
                        module: 'potato-agreement'
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
            $('.farmer_id').append(option).trigger('change');
        }

        // Sync back to Livewire when user selects
        $('.farmer_id').off('change.select2').on('change.select2', function (e) {
            @this.set('farmer_id', $(this).val());
        });
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
