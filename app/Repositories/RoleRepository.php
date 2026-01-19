<?php


namespace App\Repositories;

use App\Models\Role;
use http\Env\Request;


class RoleRepository
{
    private $model;

    /**
     * RoleRepository constructor.
     */
    public function __construct(Role $model)
    {
        $this->model = $model;
    }

    public function findByID($id)
    {
        return $this->model->findById($id);
    }

    // Create new recoard
    public function create($params)
    {

        $role = $this->model->create($params->only('name'));

        $permissions = $params->permission;

        $role->syncPermissions($permissions);

        return $role;
    }

    // Update recoard
    public function update($params, $id)
    {
        $permissions = $params->permission;

        $role = $this->findByID($id);
        $role->name = $params->name;
        $role->save();

        $role->syncPermissions($permissions);

        return $role;
    }

    public function filter($params = [])
    {

        $this->model = $this->model->whereNot('name', config('constants.SUPER_ADMIN'));

        $this->model = $this->model->when(!empty($params['role_id']), function ($query) use ($params) {
            $query->where('id', $params['role_id']);
        });


        if (isset($params['return_type']) && $params['return_type'] == 'model') {
            return $this->model;
        }

        return $this->model
            ->latest()
            ->paginate(config('constants.PER_PAGE'), ['*'], 'page', !empty($params['page']) ? $params['page'] : '')
            ->setPath($params['path']);
    }

    public function dropDownList()
    {
        return Role::where('show_while_creating_user', 'YES')->orderBy('name', 'asc')->whereNot('name', config('constants.SUPER_ADMIN'))->pluck('name', 'id')->toArray();
    }
}
