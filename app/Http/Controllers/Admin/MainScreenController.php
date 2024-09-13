<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\CustomSelect\CustomSelectProperties;
use App\Http\Resources\CustomSelect\CustomSelectResourceCollection;
use App\Models\Company;
use App\Models\Service;
use Illuminate\Http\Request;

class MainScreenController extends Controller
{
    public function index()
    {
        return inertia('Admin/MainScreen/Index', [
            'companies_select' => CustomSelectResourceCollection::collection(
                Company::get(),
                new CustomSelectProperties('name', 'id')
            ),
            'companies' => Company::get(),
            'services' => Service::get(),
        ]);
    }
}
