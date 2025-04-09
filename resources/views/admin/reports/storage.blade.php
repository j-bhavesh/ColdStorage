<x-admin-layout>
    @section('title', 'Storage Report')
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Storage Report</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="{{ route('admin.reports.index') }}">Reports</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Storage Report</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    
    <div class="app-content">
        <div class="container-fluid">
            <!-- Filters -->
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title">Report Filters</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.reports.storage') }}">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="cold_storage_id">Cold Storage</label>
                                <select name="cold_storage_id" id="cold_storage_id" class="form-control">
                                    <option value="">All Cold Storages</option>
                                    @foreach($coldStorages as $coldStorage)
                                        <option value="{{ $coldStorage->id }}" {{ request('cold_storage_id') == $coldStorage->id ? 'selected' : '' }}>
                                            {{ $coldStorage->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="date_from">Date From</label>
                                <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from') }}">
                            </div>
                            <div class="col-md-3">
                                <label for="date_to">Date To</label>
                                <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to') }}">
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">Generate Report</button>
                                <button type="button" class="btn btn-success me-2" onclick="exportToExcel()">Export Excel</button>
                                <button type="button" class="btn btn-danger" onclick="exportToPdf()">Export PDF</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Report Header -->
            <div class="card mb-3">
                <div class="card-body text-center">
                    <h4>KISAAN AGRO TECH (HENILBHAI)</h4>
                    <h5>KADIYADRA</h5>
                    <p class="mb-0">Storage Report - {{ request('date_from') ? Carbon\Carbon::parse(request('date_from'))->format('d/m/Y') : 'All Time' }} 
                    @if(request('date_to')) to {{ Carbon\Carbon::parse(request('date_to'))->format('d/m/Y') }} @endif</p>
                </div>
            </div>

            <!-- Storage Loading Data -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Storage Loading Details</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>DATE</th>
                                    <th>COLD STORAGE</th>
                                    <th>FARMER</th>
                                    <th>SEED VARIETY</th>
                                    <th>TRANSPORTER</th>
                                    <th>VEHICLE</th>
                                    <th>RST NUMBER</th>
                                    <th>CHAMBER NO</th>
                                    <th>BAG QUANTITY</th>
                                    <th>NET WEIGHT</th>
                                    <th>EXTRA BAGS</th>
                                    <th>REMARKS</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($storageLoadings as $loading)
                                    <tr>
                                        <td>{{ $loading->created_at->format(env('DATE_FORMATE')) }}</td>
                                        <td>{{ $loading->coldStorage->name ?? 'N/A' }}</td>
                                        <td>{{ $loading->agreement->farmer->name ?? 'N/A' }}</td>
                                        <td>{{ $loading->agreement->seedVariety->name ?? 'N/A' }}</td>
                                        <td>{{ $loading->transporter->name ?? 'N/A' }}</td>
                                        <td>{{ $loading->vehicle->vehicle_number ?? 'N/A' }}</td>
                                        <td>{{ $loading->rst_number }}</td>
                                        <td>{{ $loading->chamber_no }}</td>
                                        <td>{{ $loading->bag_quantity }}</td>
                                        <td>{{ $loading->net_weight }}</td>
                                        <td>{{ $loading->extra_bags ?? 0 }}</td>
                                        <td>{{ $loading->remarks ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="12" class="text-center">No data found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr class="table-dark">
                                    <td colspan="8"><strong>TOTAL</strong></td>
                                    <td><strong>{{ $storageLoadings->sum('bag_quantity') }}</strong></td>
                                    <td><strong>{{ $storageLoadings->sum('net_weight') }}</strong></td>
                                    <td><strong>{{ $storageLoadings->sum('extra_bags') }}</strong></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Summary Statistics -->
            <div class="row mt-3">
                <div class="col-md-4">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h5 class="card-title">Total Loadings</h5>
                            <h3>{{ $storageLoadings->count() }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h5 class="card-title">Total Bags</h5>
                            <h3>{{ $storageLoadings->sum('bag_quantity') }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <h5 class="card-title">Total Weight (kg)</h5>
                            <h3>{{ number_format($storageLoadings->sum('net_weight'), 2) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function exportToExcel() {
            alert('Excel export functionality to be implemented');
        }

        function exportToPdf() {
            alert('PDF export functionality to be implemented');
        }
    </script>
</x-admin-layout>