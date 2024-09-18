<?php

namespace App\Http\Controllers;

use App\Exceptions\ServiceCalculatorException;
use App\Http\Requests\Dashboard\CalculateRequest;
use App\Http\Resources\CustomSelect\CustomSelectProperties;
use App\Http\Resources\CustomSelect\CustomSelectResource;
use App\Http\Resources\CustomSelect\CustomSelectResourceCollection;
use App\Http\Resources\Dashboard\CompanyResource;
use App\Models\Area;
use App\Models\AreaPrice;
use App\Models\Company;
use App\Models\DeparturePoint;
use App\Models\Service;
use App\Services\ServiceCalculator;

class DashboardController extends Controller
{
    public function index()
    {
        return inertia('Dashboard', [
            'exmail_services' => CustomSelectResourceCollection::collection(
                Company::find(Company::EXMAIL_COMPANY_ID)->services,
                new CustomSelectProperties('name', 'id')
            ),
            'companies' => CompanyResource::collection(
                Company::where('id', '!=', Company::EXMAIL_COMPANY_ID)->with('services')->get()
            ),
            'departure_points' => CustomSelectResourceCollection::collection(
                DeparturePoint::orderBy('name')->get(),
                new CustomSelectProperties('name', 'id')
            )
        ]);
    }

    public function calculate(CalculateRequest $request)
    {
        try {
            $exmailCalculator = new ServiceCalculator(
                Service::find($request->get('exmail_service_id')),
                DeparturePoint::find($request->get('where_from')),
                DeparturePoint::find($request->get('where_to')),
            );
            $extraServices = [];
            foreach ($request->get('selected_comparable_services') as $extraService) {
                if(!$extraService) continue;
                $comparingServiceCalculator = new ServiceCalculator(
                    $service = Service::find($extraService),
                    DeparturePoint::find($request->get('where_from')),
                    DeparturePoint::find($request->get('where_to')),
                );
                $extraServices[$service->company_id] = [
                    'price' => $comparingServiceCalculator->getPrice($request->get('weight')),
                    'terms' => $comparingServiceCalculator->getArea()->terms,
                ];
            }
            return redirect()->back()->with('data', [
                'exmail' => [
                    'price' => $exmailCalculator->getPrice($request->get('weight')),
                    'terms' => $exmailCalculator->getArea()->terms
                ],
                ...$extraServices
            ]);
        } catch (ServiceCalculatorException $exception) {
            return redirect()->back()->withErrors(['calculation' => $exception->getMessage()]);
        }
    }
}
