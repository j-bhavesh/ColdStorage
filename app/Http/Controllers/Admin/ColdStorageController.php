<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ColdStorageController extends Controller
{
    public function index()
    {
        return view('admin.cold-storages.index');
    }
} 