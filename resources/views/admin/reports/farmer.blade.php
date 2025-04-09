<x-admin-layout>
    
    @section('title','Farmer Report')

    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Farmer Report</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="{{ route('admin.reports.index') }}">Reports</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Farmer Report</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            <!-- Farmer Selection -->
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title">Select Farmer</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.reports.farmer') }}">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="farmer_id">Farmer</label>
                                <select name="farmer_id" id="farmer_id" class="form-control" onchange="this.form.submit()">
                                    <option value="">Select a Farmer</option>
                                    @foreach($farmers as $farmer)
                                        <option value="{{ $farmer->id }}" {{ request('farmer_id') == $farmer->id ? 'selected' : '' }}>
                                            {{ $farmer->name }} ({{ $farmer->village_name }}) - {{ $farmer->farmer_id }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 d-flex align-items-end">
                                <!-- <button type="submit" class="btn btn-primary me-2">Generate Report</button>
                                <button type="button" class="btn btn-success me-2" onclick="exportToExcel()">Export Excel</button>
                                <button type="button" class="btn btn-danger" onclick="exportToPdf()">Export PDF</button> -->
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            @if(isset($farmerDetails) && isset($totals))
            <!-- Farmer Information -->
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title">Farmer Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Farmer ID:</strong></td>
                                    <td>{{ $farmerDetails->farmer_id }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Name:</strong></td>
                                    <td>{{ $farmerDetails->name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Village:</strong></td>
                                    <td>{{ $farmerDetails->village_name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Phone:</strong></td>
                                    <td>{{ $farmerDetails->user->phone ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Total Agreements:</strong></td>
                                    <td>{{ $totals['total_agreements'] }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Total Advance Payments:</strong></td>
                                    <td>₹{{ number_format($totals['total_advance_payments'], 0) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Total Challans:</strong></td>
                                    <td>{{ $totals['total_challans'] }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td><span class="badge bg-success">Active</span></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Summary Statistics -->
            <div class="row">
                <div class="col-md-3">
                    <a href="{{ route('admin.reports.farmer.agreements', $farmerDetails->id) }}" class="text-decoration-none">
                        <div class="card bg-primary text-white" style="cursor: pointer;">
                            <div class="card-body">
                                <h5>Total Agreements</h5>
                                <h3>{{ $totals['total_agreements'] }}</h3>
                                <small>Click to view details</small>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('admin.reports.farmer.payments', $farmerDetails->id) }}" class="text-decoration-none">
                        <div class="card bg-success text-white" style="cursor: pointer;">
                            <div class="card-body">
                                <h5>Advance Payments</h5>
                                <h3>₹{{ number_format($totals['total_advance_payments'], 0) }}</h3>
                                <small>Click to view details</small>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('admin.reports.farmer.challans', $farmerDetails->id) }}" class="text-decoration-none">
                        <div class="card bg-info text-white" style="cursor: pointer;">
                            <div class="card-body">
                                <h5>Total Challans</h5>
                                <h3>{{ $totals['total_challans'] }}</h3>
                                <small>Click to view details</small>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('admin.reports.farmer.loadings', $farmerDetails->id) }}" class="text-decoration-none">
                        <div class="card bg-warning text-white" style="cursor: pointer;">
                            <div class="card-body">
                                <h5>Storage Loadings</h5>
                                @php
                                    $farmerLoadings = collect();
                                    foreach($agreements as $agreement) {
                                        $farmerLoadings = $farmerLoadings->merge($agreement->storageLoadings);
                                    }
                                @endphp
                                <h3>{{ $farmerLoadings->count() }}</h3>
                                <small>Click to view details</small>
                            </div>
                        </div>
                    </a>
                </div>
            </div>            @else
            <!-- No Farmer Selected Message -->
            <div class="card">
                <div class="card-body text-center">
                    <h5 class="card-title">No Farmer Selected</h5>
                    <p class="card-text">Please select a farmer from the dropdown above to generate a detailed report.</p>
                    <i class="fas fa-user-tie fa-3x text-muted"></i>
                </div>
            </div>
            @endif
        </div>
    </div>

    <script>
        function exportToExcel(){
            alert('Excel export functionality to be implemented');
        }

        function exportToPdf(){
            alert('PDF export functionality to be implemented');
        }
    </script>
    <!-- Select2 Ajax -->
    @push('scripts')
    <script>
    $(document).ready(function(){
        function initSelect2() {
            $('#farmer_id').select2({
                placeholder: 'Search and select farmer...',
                allowClear: true,
                width: '100%',
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

            
        }

        initSelect2();
        
    });
    </script>
    @endpush
</x-admin-layout>