<x-admin-layout>
    @section('title', 'Farmer Storage Loadings - ' . $farmer->name)
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Storage Loadings - {{ $farmer->name }}</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="{{ route('admin.reports.index') }}">Reports</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.reports.farmer', ['farmer_id' => $farmer->id]) }}">Farmer Report</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Storage Loadings</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    
    <div class="app-content">
        <div class="container-fluid">
            <!-- Farmer Info -->
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title">Farmer Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Name:</strong> {{ $farmer->name }}</p>
                            <p><strong>Farmer ID:</strong> {{ $farmer->farmer_id }}</p>
                            <p><strong>Village:</strong> {{ $farmer->village_name }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Total Loadings:</strong> {{ $totals['total_loadings'] }}</p>
                            <p><strong>Total Bags:</strong> {{ $totals['total_bags'] }}</p>
                            <p><strong>Total Weight:</strong> {{ $totals['total_weight'] }} kg</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Storage Loadings Table -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Storage Loadings Details</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Cold Storage</th>
                                    <th>Seed Variety</th>
                                    <th>Bag Quantity</th>
                                    <th>Net Weight</th>
                                    <th>Transporter</th>
                                    <th>Vehicle</th>
                                    <th>RST Number</th>
                                    <th>Chamber No</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($farmerLoadings as $loading)
                                    <tr>
                                        <td>{{ $loading->created_at->format(env('DATE_FORMATE')) }}</td>
                                        <td>{{ $loading->coldStorage->name ?? 'N/A' }}</td>
                                        <td>{{ $loading->agreement->seedVariety->name ?? 'N/A' }}</td>
                                        <td>{{ $loading->bag_quantity }}</td>
                                        <td>{{ $loading->net_weight }}</td>
                                        <td>{{ $loading->transporter->name ?? 'N/A' }}</td>
                                        <td>{{ $loading->vehicle->vehicle_number ?? 'N/A' }}</td>
                                        <td>{{ $loading->rst_number }}</td>
                                        <td>{{ $loading->chamber_no }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center">No storage loadings found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>