<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\SeedDistributionService;

class SeedDistributionController extends Controller
{
    protected $seedDistributionService;

    public function __construct(SeedDistributionService $seedDistributionService)
    {
        $this->seedDistributionService = $seedDistributionService;
    }

    public function index()
    {
        return view('admin.seed-distributions.index');
    }

    public function searchSeedDistributions(Request $request)
    {
        $module = $request->input('module');
        $search = $request->input('search');

        $searchResult = $this->seedDistributionService->searchSeedDistributions($search);
        
        $results = $searchResult->map(fn($sd) => [
            'id' => $sd['id'],
            'text' => $sd['text']
        ]);

        return response()->json($results);
    }
} 