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
                DeparturePoint::find($request->get('where_to')
                ),
            );
            $exmailPrice = $exmailCalculator->getPrice($request->get('weight'), $request->get('nds_included'));

            $markup = null;
            $priceWithMarkup = null;
            $initialService = Service::firstWhere(['name' => Service::$EXMAIL_INITIAL_SERVICE_NAME]);
            if ($initialService) {
                $exmailInitialCalculator = new ServiceCalculator(
                    $initialService,
                    DeparturePoint::find($request->get('where_from')),
                    DeparturePoint::find($request->get('where_to')),
                );
                $markup = (($exmailPrice - $exmailInitialCalculator->getPrice($request->get('weight'))) / $exmailPrice) * 100;
            }

            $services['exmail'] = [
                'price' => $exmailPrice,
                'terms' => $exmailCalculator->getArea()->terms,
                'markup' => $markup,
            ];
            if ($sale = $request->get('exmail_sale')) {
                $services['exmail']['price_with_sale'] = $exmailPrice * ((100 - $sale) / 100);
            } elseif ($request->get('exmail_markup') && $initialService) {
                $services['exmail']['price_with_markup'] = ($exmailInitialCalculator->getPrice($request->get('weight')) / (1 - ($request->get('exmail_markup') / 100)));
            }
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
                );
                $services[$service->company_id] = [
                    'price' => $price = $comparingServiceCalculator->getPrice($request->get('weight'), $request->get('nds_included')),
                    'terms' => $comparingServiceCalculator->getArea()->terms,
                ];
                if ($sale = $extraService['sale']) {
                    $services[$service->company_id]['price'] = $price * ((100 - $sale) / 100);
                }
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
