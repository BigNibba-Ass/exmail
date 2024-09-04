<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;

class MainScreenController extends Controller
{
    public function index()
    {
        return inertia('Admin/MainScreen/Index', ['services' => Service::get()]);
    }
}
