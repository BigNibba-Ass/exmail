<?php


namespace App\Services;

use App\Exceptions\ServiceCalculatorException;
use App\Http\Requests\Dashboard\CalculateRequest;
use App\Models\Area;
use App\Models\AreaPrice;
use App\Models\DeparturePoint;
use App\Models\Service;
use Illuminate\Database\Eloquent\Builder;
use function Psy\debug;

class ServiceCalculator
{

    protected Service $comparableService;

    protected Area $area;

    /**
     * @throws ServiceCalculatorException
     */
    public function __construct(Service $comparableService, DeparturePoint $whereFrom, DeparturePoint $whereTo)
    {
        $this->comparableService = $comparableService;
        $areaQuery = $this->comparableService->areas()->where(['where_from' => $whereFrom->id, 'where_to' => $whereTo->id]);
        if (!$areaQuery->exists()) {
            throw new ServiceCalculatorException("Зона не найдена (" . $this->getCompanyName() . ")");
        }
        $this->area = $areaQuery->first();
    }

    public function getCompanyName()
    {
        return $this->comparableService->company()->first()->name;
    }

    /**
     * @throws ServiceCalculatorException
     */
    public function getPrice(float $weight, bool|null $includeNDS = false)
    {
        $priceQuery = $this->comparableService
            ->areaPrices()
            ->limit(1)
            ->where(['area_number' => $this->area->area_number])
            ->where(function (Builder $query) use ($weight) {
                return $query
                    ->where(function (Builder $query) use ($weight) {
                        $query
                            ->where('weight_min', '<=', $weight)
                            ->where('weight_max', '>=', $weight);
                    })->orWhere(function (Builder $query) use ($weight) {
                        $query
                            ->where('weight_min', '<=', $weight)
                            ->where(['weight_max' => 0]);
                    });
            });
        if (!$priceQuery->exists()) {
            throw new ServiceCalculatorException("Не указан тариф (" . $this->getCompanyName() . ")");
        }
        $priceObj = $priceQuery->first();
        if ($priceObj->price_per_extra) {
            $priceToWorkWith = $priceObj->price * 100;
            $timesToMultiplyBy = 0;
            for ($i = $priceObj->weight_min; $i < $weight; $i += $priceObj->extra_definition) {
                if ($priceObj->weight_max === 0.0) {
                    $timesToMultiplyBy++;
                } else if ($i + $priceObj->extra_definition < $priceObj->weight_max) {
                    $timesToMultiplyBy++;
                }
            }
            $priceToWorkWith += $priceObj->price_per_extra * $timesToMultiplyBy * 100;
            $priceObj->price = $priceToWorkWith / 100;
        }
        if($includeNDS) {
            $priceObj->price += 20 / 100 * $priceObj->price;
        }
        return $priceObj->price;
    }

    public function getPricePerExtra()
    {
        $priceQuery = $this->comparableService
            ->areaPrices()
            ->limit(1)
            ->where(['area_number' => $this->area->area_number])
            ->first();
        if (!$priceQuery->exists()) {
            throw new ServiceCalculatorException("Не указан тариф (" . $this->getCompanyName() . ")");
        }
        $priceObj = $priceQuery->first();
        return $priceObj->price_per_extra;
    }

    /**
     * @return Service
     */
    public function getComparableService(): Service
    {
        return $this->comparableService;
    }

    /**
     * @return Area
     */
    public function getArea(): Area
    {
        return $this->area;
    }
}
