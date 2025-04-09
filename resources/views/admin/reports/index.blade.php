<x-admin-layout>
    @section('title', 'Reports Management')
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Reports Management</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Reports</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    
    <div class="app-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <h3 class="card-title1">Farmer Report</h3>
                            <p class="card-text">Individual farmer reports with agreements and payments.</p>
                           <a href="{{ route('admin.reports.farmer') }}" class="btn btn-primary">Generate Report</a>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <h3 class="card-title1">Processing Report</h3>
                            <p class="card-text">Detailed processing report with quality parameters and calculations.</p>
                            <a href="{{ route('admin.reports.processing') }}" class="btn btn-primary">Generate Report</a>
                        </div>
                    </div>
                </div>
                {{--<div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title1">Storage Report</h5>
                            <p class="card-text">Cold storage wise loading and unloading reports.</p>
                            <a href="{{ route('admin.reports.storage') }}" class="btn btn-primary">Generate Report</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title1">Financial Report</h5>
                            <p class="card-text">Financial summaries and payment reports.</p>
                            <a href="{{ route('admin.reports.financial') }}" class="btn btn-primary">Generate Report</a>
                        </div>
                    </div>
                </div>--}}
                
            </div>
        </div>
    </div>
</x-admin-layout>