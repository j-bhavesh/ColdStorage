<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <div class="input-group input-group-sm" style="width: 250px;">
                <input type="text" wire:model.live="search" class="form-control" placeholder="Search Packaging Distributions...">
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
                    <i class="fas fa-plus"></i> Add New Packaging Distribution
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
                    <th wire:click="sortBy('agreement.farmer.name')" class="cursor-pointer">
                        Farmer
                        @if($sortField === 'agreement.farmer.name')
                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                        @endif
                    </th>
                    <th class="cursor-pointer">
                        Village
                    </th>
                    <th class="cursor-pointer">
                        Phone
                    </th>
                    <th wire:click="sortBy('agreement.seedVariety.name')" class="cursor-pointer">
                        Seed Variety
                        @if($sortField === 'agreement.seedVariety.name')
                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                        @endif
                    </th>
                    <th wire:click="sortBy('bag_quantity')" class="cursor-pointer">
                        {{-- Bag Quantity --}}
                        Total Bags
                        @if($sortField === 'bag_quantity')
                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                        @endif
                    </th>
                    <th class="cursor-pointer">
                        {{-- Bag Quantity --}}
                        Supplied Bags
                    </th>
                    <th class="cursor-pointer">
                        {{-- Bag Quantity --}}
                        Pending Bags
                    </th>
                    <th wire:click="sortBy('vehicle_number')" class="cursor-pointer">
                        Vehicle Number
                        @if($sortField === 'vehicle_number')
                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                        @endif
                    </th>
                    <th wire:click="sortBy('distribution_date')" class="cursor-pointer">
                        Distribution Date
                        @if($sortField === 'distribution_date')
                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                        @endif
                    </th>
                    <th wire:click="sortBy('received_by')" class="cursor-pointer">
                        Received By
                        @if($sortField === 'received_by')
                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                        @endif
                    </th>
                    <th>Created By</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                {{-- @dd($packagingDistributions); --}}
                @forelse($packagingDistributions as $distribution)
                    @php
                        // $receivedBags = !empty( $distribution->received_bags ) ? $distribution->received_bags : '-';
                        $receivedBags = !empty( $distribution->bag_quantity ) ? $distribution->bag_quantity : '-';
                        $pendingBags = !empty( $distribution->pending_bags ) ? $distribution->pending_bags : '-';
                        $disableEdit = $distribution->distribution_date->isFuture() || $distribution->distribution_date->isToday() ? '' : 'disabled';
                    @endphp
                    <tr>
                        <td>{{ $distribution->id }}</td>
                        <td>{{ $distribution->agreement->farmer->name }} ({{ $distribution->agreement->farmer->farmer_id }})</td>
                        <td>{{ $distribution->agreement->farmer->village_name }}</td>
                        <td>{{ $distribution->agreement->farmerUser->phone }}</td>
                        <td>{{ $distribution->agreement->seedVariety->name }}</td>
                        {{-- <td>{{ $distribution->bag_quantity }}</td> --}}
                        <td>{{ $distribution->agreement->bag_quantity }}</td>
                        <td>{{ $receivedBags }}</td>
                        <td>{{ $pendingBags }}</td>
                        <td>{{ $distribution->vehicle_number }}</td>
                        <td>{{ $distribution->distribution_date->format(env('DATE_FORMATE')) }}</td>
                        <td>{{ $distribution->received_by }}</td>
                        <td>{{ $distribution->creator->name }}</td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <button wire:click="edit({{ $distribution->id }})" class="btn btn-info" {{ $disableEdit }}>
                                    <i class="text-white fas fa-edit"></i>
                                </button>
                                <button wire:click="delete({{ $distribution->id }})"
                                    class="btn btn-danger"
                                    onclick="return confirm('Are you sure you want to delete this Packaging Distribution?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="13" class="text-center py-3">No Packaging Distributions found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($packagingDistributions->hasPages())
        <div class="card-footer clearfix">
            {{ $packagingDistributions->links() }}
        </div>
    @endif

    @include('livewire.admin.packaging-distributions.form')
</div>

<!-- Select2 Ajax -->
@push('scripts')
<script>
$(document).ready(function(){
    function initSelect2() {
        setTimeout(function() {
            $('#agreement_id').select2({
                placeholder: 'Search and select agreement...',
                // allowClear: true,
                width: '100%',
                dropdownParent: $('.modal-content'),
                containerCssClass: 'farmers-select2-container',
                selectionCssClass: 'farmers-select2-selection',
                dropdownCssClass: 'farmers-select2-dropdown',
                maximumSelectionLength: 2, // AJAX triggers only after 2 characters

                ajax: {
                    url: "{{ route('admin.packaging-distributions.search') }}",
                    dataType: 'json',
                    delay: 250, // wait 250ms after typing
                    data: function (params) {
                        return {
                            search: params.term, // user input
                            module: 'packaging-distributions'
                        };
                    },
                    processResults: function (data) {
                        console.log('Result : ', data);
                        // Map results to Select2 format
                        return {
                            results: data
                        };
                    },
                    cache: true
                }
            });

            // // Set initial value if editing
            // let selectedId = @this.agreement_id ? @this.agreement_id : null;
            // let selectedText = @this.agreement_name ? @this.agreement_name : null;

            // if (selectedId && selectedText) {
            //     let option = new Option(selectedText, selectedId, true, true);
            //     $('#agreement_id').append(option).trigger('change');
            // }

            // Sync back to Livewire when user selects
            $('#agreement_id').off('change.select2').on('change.select2', function (e) {
                @this.set('agreement_id', $(this).val());
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
        if ($(el).find('#agreement_id').length > 0) {
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