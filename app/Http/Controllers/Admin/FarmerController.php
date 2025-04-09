<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\FarmerRequest;
use App\Models\Farmer;
use App\Services\FarmerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FarmerController extends Controller
{
    protected $farmerService;

    public function __construct(FarmerService $farmerService)
    {
        $this->farmerService = $farmerService;
    }

    public function index()
    {
        return view('admin.farmers.index');
    }

    public function searchFarmer(Request $request)
    {   
        $module = $request->input('module');
        $search = $request->input('search');

        $searchResult = $this->farmerService->searchFarmer($search);

        if( $module === 'potato-agreement' || $module === 'advance-payment' || $module === 'storage-loading' || $module === 'challan') {
            $results = $searchResult->map(fn($farmer) => [
                'id' => $farmer->id,
                'text' => "{$farmer->name} ({$farmer->farmer_id})"
            ]);
        } elseif ( $module === 'seed-booking' ) {
            $results = $searchResult->map(fn($farmer) => [
                'id' => $farmer->id,
                'text' => "{$farmer->name} ({$farmer->farmer_id}) - {$farmer->village_name}"
            ]);
        }
        

        return response()->json($results);
    }

    /*
    public function getFarmersData(Request $request)
    {
        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length");
        $search_arr = $request->get('search');
        $searchValue = $search_arr['value'];
        $totalRecords = Farmer::count();
        $totalRecordswithFilter = Farmer::count();

        $query = Farmer::query();

        if($searchValue) {
            $query->where(function($q) use ($searchValue) {
                $q->where('name', 'like', "%{$searchValue}%")
                  ->orWhere('village_name', 'like', "%{$searchValue}%")
                  ->orWhere('contact_number', 'like', "%{$searchValue}%")
                  ->orWhere('farmer_id', 'like', "%{$searchValue}%");
            });
            $totalRecordswithFilter = $query->count();
        }

        // Handle sorting
        $orderColumn = $request->get('order')[0]['column'] ?? 0;
        $orderDir = $request->get('order')[0]['dir'] ?? 'asc';

        $columns = [
            0 => 'farmer_id',
            1 => 'name',
            2 => 'village_name',
            3 => 'contact_number',
            4 => 'actions' // This column is not sortable
        ];

        // Apply sorting if the column is not the actions column
        if ($orderColumn != 4) {
            $columnName = $columns[$orderColumn] ?? 'farmer_id';
            $query->orderBy($columnName, $orderDir);
        } else {
            // Default sorting if actions column is selected
            $query->orderBy('farmer_id', 'asc');
        }

        $records = $query->skip($start)
                        ->take($rowperpage)
                        ->get();

        $data_arr = [];

        foreach($records as $record) {
            $actions = '<div class="btn-group" role="group">';
            $actions .= '<a href="'.route('admin.farmers.show', $record->id).'" class="btn btn-info btn-sm"><i class="bi bi-eye-fill text-white"></i></a>';
            $actions .= '<a href="'.route('admin.farmers.edit', $record->id).'" class="btn btn-primary btn-sm"><i class="bi bi-pencil-square"></i></a>';
            $actions .= '<form action="'.route('admin.farmers.destroy', $record->id).'" method="POST" class="d-inline">';
            $actions .= csrf_field();
            $actions .= method_field('DELETE');
            $actions .= '<button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Are you sure you want to delete this farmer?\')"><i class="bi bi-trash-fill"></i></button>';
            $actions .= '</form>';
            $actions .= '</div>';

            $data_arr[] = [
                "farmer_id" => $record->farmer_id,
                "name" => $record->name,
                "village_name" => $record->village_name,
                "contact_number" => $record->contact_number,
                "actions" => $actions
            ];
        }

        $response = [
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordswithFilter,
            "aaData" => $data_arr
        ];

        return json_encode($response);
    }

    public function create()
    {
        return view('admin.farmers.form');
    }

    public function store(FarmerRequest $request)
    {

        $this->farmerService->createFarmer($request->validated());

        return redirect()->route('admin.farmers.index') ->with('success', 'Farmer created successfully');
    }

    public function show(Farmer $farmer)
    {
        return view('admin.farmers.show', compact('farmer'));
    }

    public function edit(Farmer $farmer)
    {
        return view('admin.farmers.form', compact('farmer'));
    }

    public function update(FarmerRequest $request, Farmer $farmer)
    {
        $this->farmerService->updateFarmer($farmer, $request->validated());
        return redirect()->route('admin.farmers.index')
            ->with('success', 'Farmer updated successfully');
    }

    public function destroy(Farmer $farmer)
    {
        $this->farmerService->deleteFarmer($farmer);
        return redirect()->route('admin.farmers.index')
            ->with('success', 'Farmer deleted successfully');
    }
            */
}
