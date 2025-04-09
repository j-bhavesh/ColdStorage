<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\PackagingDistributionResource;
use App\Models\PackagingDistribution;
use Illuminate\Http\Request;
use App\Services\PackagingDistributionService;

class PackagingDistributionController extends Controller
{
    protected $packagingDistribution;

    public function __construct(PackagingDistributionService $packagingDistribution)
    {
        $this->packagingDistribution = $packagingDistribution;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.packaging-distributions.index');  
    }

    public function searchPackagingDistributionFarmer(Request $request)
    {   
        $module = $request->input('module');
        $search = $request->input('search');

        $searchResult = $this->packagingDistribution->searchPdFarmer($search);

        
        $results = $searchResult->map(fn($pd) => [
            'id' => $pd['id'],
            'text' => $pd['text']
        ]);

        return response()->json($results);
    }
} 