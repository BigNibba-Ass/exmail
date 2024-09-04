<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Services\ServiceRequest;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function store(ServiceRequest $request)
    {
        Service::create($request->validated());
        return redirect()->back();
    }

    public function update(ServiceRequest $request, Service $service)
    {
        $service->update($request->validated());
        return redirect()->back();
    }

    public function destroy(Service $service)
    {
        $service->delete();
        return redirect()->back();
    }
}
