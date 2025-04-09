<?php

namespace App\Services;

use App\Models\Farmer;
use App\Models\User;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

use Illuminate\Validation\ValidationException;
use App\Services\SmsService;

class FarmerService
{
    protected $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    public function getAllFarmers(int $perPage = 10, ?string $search = null): LengthAwarePaginator
    {
        // if ($perPage === -1) {
        //     $total = Farmer::count();
        //     return Farmer::latest()->paginate($total);
        // }
        // return Farmer::latest()->paginate($perPage);

        // $query = Farmer::query();
        $query = Farmer::with(['user', 'creator']);

        if($search){
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('farmer_id', 'like', "%{$search}%")
                    ->orWhere('village_name', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($query) use ($search) {
                        $query->where('phone', 'like', "%{$search}%");
                    });
            });
        }

        if ($perPage === -1) {
            $total = $query->count();
            return $query->latest()->paginate($total);
        }

        return $query->latest()->paginate($perPage);
    }

    public function searchFarmer($farmerSearch)
    {
        $farmerSearchResult = Farmer::with(['user'])
            ->where(function ($q) use ($farmerSearch) {
                $q->where('name', 'like', "%{$farmerSearch}%")
                    ->orWhere('farmer_id', 'like', "%{$farmerSearch}%")
                    ->orWhere('village_name', 'like', "%{$farmerSearch}%")
                    ->orWhereHas('user', function ($query) use ($farmerSearch) {
                        $query->where('phone', 'like', "%{$farmerSearch}%");
                    });
            })
            ->get();
        return $farmerSearchResult;
    }

    public function getFarmerById(int $id): ?Farmer
    {
        return Farmer::findOrFail($id);
    }

    public function createFarmer(array $data): array
    {
        // Step 1: Generate prefix from village name (first 3 characters, uppercased)
        $prefix = strtoupper(substr(Str::slug($data['village_name']), 0, 3));


        // Step 2: Check if farmer already exists
        $existingFarmer = Farmer::where('name', $data['name'])
            ->where('village_name', $data['village_name'])
            ->whereHas('user', function ($query) use ($data) {
                $query->where('phone', $data['phone']);
            })
            ->first();


        if ($existingFarmer){
            return ['farmer' => $existingFarmer, 'is_new' => false];
        }


        // ✅ Step 2.5: Ensure phone number is unique in users
        $existingUserWithPhone = User::where('phone', $data['phone'])->first();
        if ($existingUserWithPhone) {
            throw ValidationException::withMessages([
                'phone' => ['Phone number already exists.'],
            ]);
        }

        // Step 3: Get latest global farmer number (regardless of prefix)
        $latestFarmer = Farmer::orderBy('id', 'desc')->first();

        $nextNumber = $latestFarmer ? intval(substr($latestFarmer->farmer_id, 3)) + 1 : 1;
        $farmerId = $prefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

        // $email = strtolower($farmerId) . '@coldstorage.dev';

        $data['farmer_id'] = $farmerId;
        $data['created_by'] = auth()->user()->id;

        $existingUser = User::where('phone', $data['phone'])->first();


        if($existingUser){
            $user = $existingUser;
        } else {
            // Step 4: Create user
            $user = User::create([
                'name'     => $data['name'],
                // 'email'    => $email,
                'phone'    => $data['phone'],
                'status'    => 'approved',
                'password' => Hash::make(strtolower($farmerId)),
            ]);
            unset($data['phone']);
        }

        // Step 5: Attach user ID and create farmer
        $data['user_id'] = $user->id;
        // Step 5: Attach user role (assign Farmer role)
        $role = Role::where('name', 'farmer')->first();  // Assuming 'farmer' role exists
        if ($role) {
            $user->assignRole($role);
        }

        $newFarmer = Farmer::create($data);
        
        // Send registration SMS
        try {
            $phone = $user->phone;
            $templateId = config('services.sms.farmer_registration_template_id');
            $variables = [
                $user->name,
                $newFarmer->farmer_id,
                $newFarmer->created_at->format(env('DATE_FORMATE'))
            ];
            $this->smsService->sendTemplateSms($phone, $templateId, $variables);
        } catch (\Exception $e) {
            \Log::error('Failed to send registration SMS to farmer', [
                'error' => $e->getMessage(),
                'phone' => $user->phone ?? null,
                'farmer_id' => $data['farmer_id'] ?? null
            ]);
        }

        return ['farmer' => $newFarmer, 'is_new' => true];
    }

    public function updateFarmer(Farmer $farmer, array $data): Farmer
    {
        \Log::info('Updating farmer with data:', $data);

        $originalVillage = $farmer->village_name;
        $originalFarmerId = $farmer->farmer_id;

        $user = $farmer->user;

        if (isset($data['phone']) && $user) {
            if ($data['phone'] !== $user->phone) {
                $existingUser = User::where('phone', $data['phone'])->where('id', '!=', $user->id)->first();
                if ($existingUser) {
                    throw ValidationException::withMessages([
                        'phone' => ['Phone number already exists.'],
                    ]);
                }
                // Update phone later
                $user->phone = $data['phone'];
                
            }
            // Clean it from $data to avoid trying to update Farmer model with it
            unset($data['phone']);

            $user->name = $data['name'];
        }


        if(isset($data['village_name']) && $data['village_name'] !== $originalVillage){
            // Step 1: Generate new prefix
            $newPrefix = strtoupper(substr(Str::slug($data['village_name']), 0, 3));

            // Step 2: Keep numeric part from original farmer_id
            $numericSuffix = substr($originalFarmerId, 3);

            // Step 3: Create new farmer_id
            $newFarmerId = $newPrefix . $numericSuffix;
            $data['farmer_id'] = $newFarmerId;

            // Step 4: Update user email and password
            $email = strtolower($newFarmerId) . '@coldstorage.dev';

            if($user){
                $user->email = $email;
                $user->password = Hash::make(strtolower($newFarmerId));
            }
        }
        
        if($user){
            $user->save();
        }

        $data['created_by'] = auth()->user()->id;
        $farmer->update($data);
        $farmer->refresh(); // Refresh the model to get the latest data

        \Log::info('Farmer after update:', $farmer->toArray());

        // Send registration SMS
        try {
            $phone = $user->phone;
            $templateId = config('services.sms.farmer_registration_template_id');
            $variables = [
                $farmer->name,
                $farmer->farmer_id,
                $farmer->updated_at->format(env('DATE_FORMATE'))
            ];
            $this->smsService->sendTemplateSms($phone, $templateId, $variables);
        } catch (\Exception $e) {
            \Log::error('Failed to send registration SMS to farmer', [
                'error' => $e->getMessage(),
                'phone' => $user->phone ?? null,
                'farmer_id' => $data['farmer_id'] ?? null
            ]);
        }

        return $farmer;
    }

    public function deleteFarmer(Farmer $farmer): bool
    {
        try {
            \DB::transaction(function () use ($farmer) {
                // Get the user associated with the farmer
                $user = $farmer->user;

                // 1. Permanently delete all associated records first

                // Permanently delete advance payments
                $farmer->advancePayments()->forceDelete();

                // Permanently delete challans
                $farmer->challans()->forceDelete();

                // Permanently delete seed distributions
                $farmer->seedDistributions()->forceDelete();

                // Permanently delete seeds bookings
                $farmer->seedsBooking()->forceDelete();

                // Handle agreements and their related records explicitly
                $agreements = $farmer->agreements;
                foreach ($agreements as $agreement) {
                    // Permanently delete packaging distributions for this agreement
                    $agreement->packagingDistributions()->forceDelete();

                    // Permanently delete storage loadings for this agreement
                    $agreement->storageLoadings()->forceDelete();

                    // Permanently delete the agreement
                    $agreement->forceDelete();
                }

                // 2. Permanently delete the farmer record
                $farmer->forceDelete();

                // 3. Permanently delete the associated user account
                if ($user) {
                    $user->forceDelete();
                }
            });

            return true;
        } catch (\Exception $e) {
            \Log::error('Error permanently deleting farmer: ' . $e->getMessage());
            throw new \Exception('Failed to permanently delete farmer and associated records: ' . $e->getMessage());
        }
    }
}
