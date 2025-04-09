<?php

namespace App\Services;


use App\Models\Company;
use App\Models\User;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

use Illuminate\Support\Str;

class CompanyService
{
    public function getAllCompanies(): LengthAwarePaginator
    {
        return Farmer::latest()->paginate(10);
    }

    public function getCompanyById(int $id): ?Company
    {
        return Company::findOrFail($id);
    }

    public function createCompany(array $data): Company
    {
        $data['created_by'] = auth()->user()->id;
        return Company::create($data);
    }

    public function updateCompany(Company $company, array $data): Company
    {
        $data['created_by'] = auth()->user()->id;
        $company->update($data);
        return $company;
    }

    public function deleteCompany(Company $company): bool
    {
        try {
            \DB::transaction(function () use ($company) {
                // 1. Permanently delete all associated records first
                
                // Permanently delete seed distributions
                $company->seedDistributions()->forceDelete();
                
                // Permanently delete seeds bookings
                $company->seedsBooking()->forceDelete();
                
                // 2. Permanently delete the company record
                $company->forceDelete();
            });
            
            return true;
        } catch (\Exception $e) {
            \Log::error('Error permanently deleting company: ' . $e->getMessage());
            throw new \Exception('Failed to permanently delete company and associated records: ' . $e->getMessage());
        }
    }

    public function searchAndSortCompanies(string $search = '', string $sortField = 'id', string $sortDirection = 'asc', int $perPage = 10 ): LengthAwarePaginator {
        return Company::query()
            ->when($search, function ($query) use ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('contact_person', 'like', "%{$search}%")
                      ->orWhere('contact_number', 'like', "%{$search}%")
                      ->orWhere('id', 'like', "%{$search}%");
                });
            })
            ->with(['creator'])
            ->orderBy($sortField, $sortDirection)
            ->paginate($perPage);
    }
} 