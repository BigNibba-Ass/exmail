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
        return redirect()->back()->with('data', $data);
    }

    public function calculateTop(CalculateTopRequest $request)
    {
        //TODO: Refactor
        $data = [];
        $headers = ['Откуда', 'Куда', 'Вес', 'Exmail цена'];

        foreach (CalculateTopRequest::$points as $point) {
            foreach ([0.25, 0.5, 1] as $weight) {
                $services = [];

                $whereFrom = DeparturePoint::find($request->get('where_from'));
                $whereTo = DeparturePoint::firstWhere(['name' => $point]);

                $services[] = $whereFrom->name;
                $services[] = $whereTo->name;
                $services[] = $weight;


                try {
                    $exmailCalculator = new ServiceCalculator(
                        Service::find($request->get('exmail_service_id')),
                        $whereFrom,
                        $whereTo,
                    );
                    $exmailPrice = $exmailCalculator->getPrice($weight, $request->get('nds_included'));

                    $markup = null;
                    $initialService = Service::firstWhere(['name' => Service::$EXMAIL_INITIAL_SERVICE_NAME]);
                    if ($initialService) {
                        $exmailInitialCalculator = new ServiceCalculator(
                            $initialService,
                            $whereFrom,
                            $whereTo,
                        );
                        $markup = (($exmailPrice - $exmailInitialCalculator->getPrice($weight)) / $exmailPrice) * 100;
                    }

                    $services[] = $exmailPrice;

                    $terms = $exmailCalculator->getArea()->terms;
                    if ($terms) {
                        $headers[] = 'Exmail срок';
                        $services[] = $terms;
                    }
                    if ($markup) {
                        $headers[] = 'Exmail маржа';
                        $services[] = $markup;
                    }

                    $sale = $request->get('exmail_sale');
                    if ($sale) {
                        $headers[] = 'Exmail цена со скидкой';
                        $services[] = $exmailPrice * ((100 - $sale) / 100);
                    } elseif ($request->get('exmail_markup') && $initialService) {
                        $headers[] = 'Exmail цена при марже';
                        $services[] = ($exmailInitialCalculator->getPrice($weight) / (1 - ($request->get('exmail_markup') / 100)));
                    }
                } catch (ServiceCalculatorException $exception) {
                    continue;
//                  return redirect()->back()->withErrors(['calculation' => $exception->getMessage()]);
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
                        $headers[] = $comparingServiceCalculator->getCompanyName()." цена";
                        $price = $comparingServiceCalculator->getPrice($weight, $request->get('nds_included'));
                        $sale = $extraService['sale'];
                        if ($sale) {
                            $price = $price * ((100 - $sale) / 100);
                        }
                        $services[] = $price;
                        $headers[] = $comparingServiceCalculator->getCompanyName()." сроки";
                        $services[] = $comparingServiceCalculator->getArea()->terms || "Не указано";
                    } catch (ServiceCalculatorException $exception) {
                        $services[] = $exception->getMessage();
                    }
                }
                $data[] = $services;
            }
        }

        $array = [
            array_unique($headers),
            ...$data
        ];
        $xlsx = SimpleXLSXGen::fromArray($array);
        return $xlsx->downloadAs('data.xlsx');
//        dd($data);
//
//        return redirect()->back()->with('data', $data);
    }
}
