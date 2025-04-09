<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Farmer;
use App\Models\Vehicle;
use App\Models\Transporter;
use App\Models\ColdStorage;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $data = [
            'totalFarmers' => Farmer::count(),
            'totalVehicles' => Vehicle::count(),
            'totalTransporters' => Transporter::count(),
            'totalColdStorages' => ColdStorage::count(),
        ];

        return view('admin.dashboard', $data);
    }
}