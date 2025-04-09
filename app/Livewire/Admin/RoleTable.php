<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Services\RoleService;
use Livewire\WithPagination;

class RoleTable extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';
    protected $roleService;
    public $search = '';
    public $sortField = 'id';
    public $sortDirection = 'desc';
    public $perPage = 10;
    public $isOpen = false;
    public $roleId;
    public $name;
    public $guard_name = 'web';

    protected $queryString = [
        'search' => ['except' => ''],
        'sortField' => ['except' => 'id'],
        'sortDirection' => ['except' => 'desc'],
    ];

    protected $rules = [
        'name' => 'required|min:3|unique:roles,name',
        'guard_name' => 'required',
    ];

    public function boot(RoleService $roleService)
    {
        $this->roleService = $roleService;
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
        $this->roleId = null;
        $this->name = '';
        $this->guard_name = 'web';
        $this->resetValidation();
    }

    public function store()
    {
        $this->validate();

        try {
            $this->roleService->createRole([
                'name' => $this->name,
                'guard_name' => $this->guard_name,
            ]);

            session()->flash('message', 'Role created successfully.');
            $this->closeModal();
            $this->dispatch('refreshTable');
        } catch (\Exception $e) {
            session()->flash('error', 'Error creating role: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $role = $this->roleService->findRole($id);
        if (!$role) {
            session()->flash('error', 'Role not found.');
            return;
        }

        $this->roleId = $id;
        $this->name = $role->name;
        $this->guard_name = $role->guard_name;
        $this->isOpen = true;
        $this->dispatch('showModal');
    }

    public function update()
    {
        $this->validate([
            'name' => 'required|min:3|unique:roles,name,' . $this->roleId,
            'guard_name' => 'required',
        ]);

        try {
            $this->roleService->updateRole($this->roleId, [
                'name' => $this->name,
                'guard_name' => $this->guard_name,
            ]);

            session()->flash('message', 'Role updated successfully.');
            $this->closeModal();
            $this->dispatch('refreshTable');
        } catch (\Exception $e) {
            session()->flash('error', 'Error updating role: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            $this->roleService->deleteRole($id);
            session()->flash('message', 'Role deleted successfully.');
            $this->dispatch('refreshTable');
        } catch (\Exception $e) {
            session()->flash('error', 'Error deleting role: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.roles.index', [
            'roles' => $this->roleService->getAllRoles(
                $this->search,
                $this->sortField,
                $this->sortDirection,
                $this->perPage
            )
        ]);
    }
} 