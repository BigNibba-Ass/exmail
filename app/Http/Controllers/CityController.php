<?php

namespace App\Http\Controllers;

use App\Models\DeparturePoint;
use Illuminate\Http\Request;

class CityController extends Controller
{
    public function index()
    {
        return inertia("Admin/Cities/Index", [
            'cities' => DeparturePoint::get()
        ]);
    }

    public function destroy(DeparturePoint $city)
    {
        $city->delete();
        return redirect()->back();
    }
}
