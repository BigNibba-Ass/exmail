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
                Company::find(Company::EXMAIL_COMPANY_ID)->services()->where('name', '!=', 'Себестоимость')->get(),
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
        $services = [];
        try {
            $exmailCalculator = new ServiceCalculator(
                Service::find($request->get('exmail_service_id')),
                DeparturePoint::find($request->get('where_from')),
                DeparturePoint::find($request->get('where_to')),
                $request->get('exmail_sale'),
            );

            $markup = null;
            try {
                $service = Service::firstWhere(['name' => Service::$EXMAIL_INITIAL_SERVICE_NAME]);
                if ($service) {
                    $exmailInitialCalculator = new ServiceCalculator(
                        $service,
                        DeparturePoint::find($request->get('where_from')),
                        DeparturePoint::find($request->get('where_to')),
                        $request->get('exmail_sale'),
                    );
                    $salePrice = $exmailCalculator->getPrice($request->get('weight'));
                    $markup = (($salePrice - $exmailInitialCalculator->getPrice($request->get('weight'))) / $salePrice) * 100;
                }
            } catch (\Exception) {
            }

            $services['exmail'] = [
                'price' => $exmailCalculator->getPrice($request->get('weight')),
                'terms' => $exmailCalculator->getArea()->terms,
                'markup' => $markup,
            ];
        } catch (ServiceCalculatorException $exception) {
            return redirect()->back()->withErrors(['calculation' => $exception->getMessage()]);
        }


        foreach ($request->get('selected_comparable_services') as $extraService) {
            if (!$extraService) continue;
            $service = Service::find($extraService['service']);
            try {
                $comparingServiceCalculator = new ServiceCalculator(
                    $service,
                    DeparturePoint::find($request->get('where_from')),
                    DeparturePoint::find($request->get('where_to')),
                    $extraService['sale']
                );
                $services[$service->company_id] = [
                    'price' => $comparingServiceCalculator->getPrice($request->get('weight')),
                    'terms' => $comparingServiceCalculator->getArea()->terms,
                ];
            } catch (ServiceCalculatorException $exception) {
                $services[$service->company_id] = [
                    'price' => $exception->getMessage(),
                    'terms' => null,
                ];
            }
        }
        return redirect()->back()->with('data', $services);
    }
}
