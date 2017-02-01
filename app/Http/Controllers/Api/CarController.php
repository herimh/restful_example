<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Car;

class CarController extends Controller
{
    public function index()
    {
        //TODO: validar con Roles y Permisos ejemplo:
        //Auth::user()->can('cars.list');
        //Auth::user()->hasRole('admin');

        return Car::all();
    }
}