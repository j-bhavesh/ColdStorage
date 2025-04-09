<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Services\UserService;
use App\Models\Role;
use Livewire\WithPagination;

class UserTable extends Component
{
    use WithPagination;

    public $paginationTheme = 'bootstrap';

    protected $userService;
    public $search = '';
    public $sortField = 'id';
    public $sortDirection = 'desc';
    public $perPage = 10;
    public $isOpen = false;
    public $userId;
    public $name;
    public $email;
    public $phone;
    public $password;
    public $password_confirmation;
    public $role;
    public $status;

    protected $queryString = [
        'search' => ['except' => ''],
        'sortField' => ['except' => 'id'],
        'sortDirection' => ['except' => 'desc'],
    ];

    protected $rules = [
        'name' => 'required|min:3',
        'email' => 'required|email',
        'phone' => 'required',
        'password' => 'required|min:8|confirmed',
        'role' => 'required|exists:roles,name',
        'status' => 'required|in:pending,approved,rejected',
    ];

    public function boot(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }

        $this->sortField = $field;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function create()
    {
        $this->resetInputFields();
        $this->isOpen = true;
        $this->dispatch('showModal');
    }

    public function openModal()
    {
        $this->isOpen = true;
        $this->dispatch('showModal');
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->resetInputFields();
        $this->dispatch('hideModal');
    }

    private function resetInputFields()
    {
        $this->userId = null;
        $this->name = '';
        $this->email = '';
        $this->phone = '';
        $this->password = '';
        $this->password_confirmation = '';
        $this->role = '';
        $this->status = 'pending';
        $this->resetValidation();
    }

    public function store()
    {
        $this->validate();

        try {
            $this->userService->createUser([
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'password' => $this->password,
                'role' => $this->role,
                'status' => $this->status,
            ]);

            session()->flash('message', 'User created successfully.');
            $this->closeModal();
            $this->dispatch('refreshTable');
        } catch (\Exception $e) {
            session()->flash('error', 'Error creating user: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $user = $this->userService->findUser($id);
        if (!$user) {
            session()->flash('error', 'User not found.');
            return;
        }

        $this->userId = $id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone = $user->phone;
        $this->role = $user->roles->first()->name;
        $this->status = $user->status;
        $this->password = '';
        $this->password_confirmation = '';
        $this->isOpen = true;
        $this->dispatch('showModal');
    }

    public function update()
    {
        $this->validate([
            'name' => 'required|min:3',
            'email' => 'required|email',
            'phone' => 'required',
            'password' => 'nullable|min:8|confirmed',
            'role' => 'required|exists:roles,name',
            'status' => 'required|in:pending,approved,rejected',
        ]);

        try {
            $this->userService->updateUser($this->userId, [
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'role' => $this->role,
                'password' => $this->password,
                'status' => $this->status,
            ]);

            session()->flash('message', 'User updated successfully.');
            $this->closeModal();
            $this->dispatch('refreshTable');
        } catch (\Exception $e) {
            session()->flash('error', 'Error updating user: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            $this->userService->deleteUser($id);
            session()->flash('message', 'User deleted successfully.');
            $this->dispatch('refreshTable');
        } catch (\Exception $e) {
            session()->flash('error', 'Error deleting user: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.users.index', [
            'users' => $this->userService->getAllUsers(
                $this->search,
                $this->sortField,
                $this->sortDirection,
                $this->perPage
            ),
            'roles' => Role::all()
        ]);
    }
}
