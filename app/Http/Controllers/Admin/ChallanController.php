<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ChallanController extends Controller
{
    public function index()
    {
        return view('admin.challans.index');
    }
} 