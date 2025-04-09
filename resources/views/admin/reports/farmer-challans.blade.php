<x-admin-layout>
    @section('title', 'Farmer Challans - ' . $farmer->name)
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Challans - {{ $farmer->name }}</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="{{ route('admin.reports.index') }}">Reports</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.reports.farmer',['farmer_id' => $farmer->id]) }}">Farmer Report</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Challans</li>
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
                            <p><strong>Total Challans:</strong> {{ $totals['total_challans'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Challans Table -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Challans Details</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Challan Number</th>
                                    <th>Vehicle</th>
                                    <th>Created Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($challans as $challan)
                                    <tr>
                                        <td>{{ $challan->challan_number }}</td>
                                        <td>{{ $challan->vehicle->vehicle_number ?? 'N/A' }}</td>
                                        <td>{{ $challan->created_at->format(env('DATE_FORMATE')) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">No challans found</td>
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