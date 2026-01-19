<?php

namespace App\Repositories;

use App\Models\Truck;

class TruckRepository
{
    // Implement repository methods here
    public $model;

    public function __construct(Truck $model)
    {
        $this->model = $model;
    }

    // Get data by id
    public function findByID($id)
    {
        return $this->model->findorFail($id);
    }

    // Create new recoard
    public function create($params)
    {
        $data = $this->model->create($params);
        return $data;
    }

    // Update recoard
    public function update($params, $id)
    {
        $data = $this->findByID($id)->update($params);
        return $data;
    }

    //Filter data
    public function filter($params)
    {

    }
}