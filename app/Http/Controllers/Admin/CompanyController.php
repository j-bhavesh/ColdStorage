<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CompanyRequest;
use App\Models\Company;
use App\Services\CompanyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CompanyController extends Controller
{
    protected $companyService;

    public function __construct(CompanyService $companyService)
    {
        $this->companyService = $companyService;
    }

    public function index()
    {
        return view('admin.companies.index');
    }

    public function getCompaniesData(Request $request)
    {
        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length");
        $search_arr = $request->get('search');
        $searchValue = $search_arr['value'];
        $totalRecords = Company::count();
        $totalRecordswithFilter = Company::count();
        
        $query = Company::query();
        
        if($searchValue) {
            $query->where(function($q) use ($searchValue) {
                $q->where('name', 'like', "%{$searchValue}%")
                  ->orWhere('contact_person', 'like', "%{$searchValue}%")
                  ->orWhere('contact_number', 'like', "%{$searchValue}%")
                  ->orWhere('id', 'like', "%{$searchValue}%");
            });
            $totalRecordswithFilter = $query->count();
        }
        
        // Handle sorting
        $orderColumn = $request->get('order')[0]['column'] ?? 0;
        $orderDir = $request->get('order')[0]['dir'] ?? 'asc';
        
        $columns = [
            0 => 'id',
            1 => 'name',
            2 => 'contact_person',
            3 => 'contact_number',
            4 => 'actions' // This column is not sortable
        ];
        
        // Apply sorting if the column is not the actions column
        if ($orderColumn != 4) {
            $columnName = $columns[$orderColumn] ?? 'id';
            $query->orderBy($columnName, $orderDir);
        } else {
            // Default sorting if actions column is selected
            $query->orderBy('id', 'asc');
        }
        
        $records = $query->skip($start)
                        ->take($rowperpage)
                        ->get();
        
        $data_arr = [];
        
        foreach($records as $record) {
            $actions = '<div class="btn-group" role="group">';
            $actions .= '<a href="'.route('admin.companies.show', $record->id).'" class="btn btn-info btn-sm"><i class="bi bi-eye-fill text-white"></i></a>';
            $actions .= '<a href="'.route('admin.companies.edit', $record->id).'" class="btn btn-primary btn-sm"><i class="bi bi-pencil-square"></i></a>';
            $actions .= '<form action="'.route('admin.companies.destroy', $record->id).'" method="POST" class="d-inline">';
            $actions .= csrf_field();
            $actions .= method_field('DELETE');
            $actions .= '<button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Are you sure you want to delete this company?\')"><i class="bi bi-trash-fill"></i></button>';
            $actions .= '</form>';
            $actions .= '</div>';
            
            $data_arr[] = [
                "id" => $record->id,
                "name" => $record->name,
                "contact_person" => $record->contact_person,
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
} 