<?php

namespace App\Http\Controllers;

use App\Exceptions\ServiceCalculatorException;
use App\Http\Requests\Dashboard\CalculateRequest;
use App\Http\Requests\Dashboard\CalculateTopRequest;
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
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Shuchkin\SimpleXLSXGen;

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
        $data = [];
        foreach ($request->get('calculation_items') as $item) {
            $services = [];

            $whereFrom = DeparturePoint::find($item['where_from']);
            $whereTo = DeparturePoint::find($item['where_to']);

            $services['misc'] = [
                'where_from' => $whereFrom->name,
                'where_to' => $whereTo->name,
                'weight' => $item['weight'],
            ];
            try {
                $exmailCalculator = new ServiceCalculator(
                    Service::find($request->get('exmail_service_id')),
                    $whereFrom,
                    $whereTo,
                );
                $exmailPrice = $exmailCalculator->getPrice($item['weight'], $request->get('nds_included'));

                $markup = null;
                $initialService = Service::firstWhere(['name' => Service::$EXMAIL_INITIAL_SERVICE_NAME]);
                if ($initialService) {
                    $exmailInitialCalculator = new ServiceCalculator(
                        $initialService,
                        $whereFrom,
                        $whereTo,
                    );
                    $markup = (($exmailPrice - $exmailInitialCalculator->getPrice($item['weight'])) / $exmailPrice) * 100;
                }

                $services['exmail'] = [
                    'price' => $exmailPrice,
                    'terms' => $exmailCalculator->getArea()->terms,
                    'markup' => $markup,
                ];
                if ($sale = $item['exmail_sale']) {
                    $services['exmail']['price_with_sale'] = $exmailPrice * ((100 - $sale) / 100);
                } elseif ($item['exmail_markup'] && $initialService) {
                    $services['exmail']['price_with_markup'] = ($exmailInitialCalculator->getPrice($item['weight']) / (1 - ($item['exmail_markup'] / 100)));
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
                        $whereFrom,
                        $whereTo
                    );
                    $services[$service->company_id] = [
                        'price' => $price = $comparingServiceCalculator->getPrice($item['weight'], $request->get('nds_included')),
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
            $data[] = $services;
        }

        $top = [];
        if ($request->get('is_in_top_mode')) {
            $whereFrom = DeparturePoint::find($request->get('top_where_from'));
            foreach (CalculateRequest::$points as $key => $point) {
                $top[$key] = [
                    'where_from' => $whereFrom->name,
                    'where_to' => $point,
                ];
                $weights = [0.24, 0.49, 0.99];
                foreach ($weights as $weight) {
                    $whereTo = DeparturePoint::firstWhere(['name' => $point]);
                    try {
                        $exmailCalculator = new ServiceCalculator(
                            Service::find($request->get('exmail_service_id')),
                            $whereFrom,
                            $whereTo,
                        );
                        $exmailPrice = $exmailCalculator->getPrice($weight, $request->get('nds_included'));
                        $initialService = Service::firstWhere(['name' => Service::$EXMAIL_INITIAL_SERVICE_NAME]);
                        $exmailInitialCalculator = new ServiceCalculator(
                                $initialService,
                                $whereFrom,
                                $whereTo,
                            );
                        $price = $exmailPrice;
                        if ($sale = $request->get('top_exmail_sale')) {
                            $price = $exmailPrice * ((100 - $sale) / 100);
                        } elseif ($request->get('top_exmail_markup') && $initialService) {
                            $price = ($exmailInitialCalculator->getPrice($weight) / (1 - ($request->get('top_exmail_markup') / 100)));
                        }
                        $top[$key]['weight_'.$weight] = $price;
                        $top[$key]['additional_weight'] = $exmailCalculator->getPricePerExtra();
                    } catch (ServiceCalculatorException $exception) {
                        continue;
                    }
                }
            }
        }
        return redirect()->back()->with('data', [
            'data' => $data,
            'top' => $top
        ]);
    }
}
