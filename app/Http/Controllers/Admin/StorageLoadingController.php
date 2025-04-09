<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\StorageLoadingService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StorageLoadingController extends Controller
{
    
    public function index()
    {
        return view('admin.storage-loadings.index');
    }

} 