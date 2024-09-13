<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\Companies\CompanyRequest;
use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function store(CompanyRequest $request)
    {
        Company::create($request->validated());
        return redirect()->back();
    }

    public function update(CompanyRequest $request, Company $company)
    {
        $company->update($request->validated());
        return redirect()->back();
    }

    public function destroy(Company $company)
    {
    }
}
