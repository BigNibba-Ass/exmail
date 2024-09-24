<?php

namespace App\Console\Commands;

use App\Models\Area;
use App\Models\AreaPrice;
use App\Models\Company;
use App\Models\DeparturePoint;
use App\Services\ImportService;
use Illuminate\Console\Command;
use PhpOffice\PhpSpreadsheet\Cell\CellAddress;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;

class PonyFillThirdCommand extends Command
{
    protected $signature = 'pony:fill-third';

    protected $description = 'todo';

    public function handle(): void
    {
        $services = [
//            Company::firstOrCreate(['name' => 'Pony Express'])->services()->firstOrCreate(['name' => 'Экспресс от двери до двери'])->id,
            Company::firstOrCreate(['name' => 'Pony Express'])->services()->firstOrCreate(['name' => 'Экспресс от экспресс-центра до двери'])->id,
        ];
        foreach ($services as $service) {
            for ($i = 2; $i <= 10; $i++) {
                $this->runAreaList($i, $service);
            }
            $this->setPrices($service);
        }
    }

    public function runAreaList($sheet, $serviceId)
    {
        $inputFileType = 'Xlsx';
        $inputFileName = storage_path('pony_2.xlsx');

        $reader = IOFactory::createReader($inputFileType);
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($inputFileName);

        $worksheet = $spreadsheet->getSheet($sheet);//


        $highestRow = $worksheet->getHighestRow(); // e.g. 10
        $highestColumn = $worksheet->getHighestColumn(); // e.g 'F'
        $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);



        for ($row = 3; $row <= $highestRow; $row++) {
            for ($col = 2; $col <= $highestColumnIndex; $col++) {
                $whereFrom = ImportService::formatDeparturePoint($worksheet->getCell(new CellAddress('$' . 'A' . '$' . $row))->getValueString());
                $whereTo = ImportService::formatDeparturePoint($worksheet->getCell(new CellAddress('$' . Coordinate::stringFromColumnIndex($col) . '$' . '2'))->getValueString());
                if (!$whereTo || !$whereFrom) continue;
                $val = [
                    'area_number' => $worksheet->getCell(new CellAddress('$' . Coordinate::stringFromColumnIndex($col) . '$' . $row))->getValueString(),
                    'where_from' => DeparturePoint::firstOrCreate(['name' => $whereFrom])->id,
                    'where_to' => DeparturePoint::firstOrCreate(['name' => $whereTo])->id,
                    'service_id' => $serviceId,
                ];
                if ($val['area_number']) {
                    Area::create($val);
                }
            }
        }
    }

    public function setPrices($serviceId)
    {
        $inputFileType = 'Xlsx';
        $inputFileName = storage_path('pony_2.xlsx');

        $reader = IOFactory::createReader($inputFileType);
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($inputFileName);

        $worksheet = $spreadsheet->getSheet(1);//


        $highestRow = $worksheet->getHighestRow(); // e.g. 10
        $highestColumn = $worksheet->getHighestColumn(); // e.g 'F'
        $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);

        for ($row = 2; $row <= $highestRow; $row++) {
            for ($col = 2; $col <= $highestColumnIndex; $col += 2) {
//                try {
                    $weightArray = explode(';', $worksheet->getCell(new CellAddress('$' . Coordinate::stringFromColumnIndex($col) . '$' . 1))->getValueString());
                    $price = $worksheet->getCell(new CellAddress('$' . Coordinate::stringFromColumnIndex($col) . '$' . $row))->getValueString();
                    $pricePerExtra = $worksheet->getCell(new CellAddress('$' . Coordinate::stringFromColumnIndex($col + 1) . '$' . $row))->getValueString();
//                    if(!$price || !$pricePerExtra) continue;
                    $val = [
                        'weight_min' => $weightArray[0],
                        'weight_max' => $weightArray[1],
                        'price' => $price,
                        'price_per_extra' => $pricePerExtra,
                        'extra_definition' => $worksheet->getCell(new CellAddress('$' . Coordinate::stringFromColumnIndex($col + 1) . '$' . 1))->getValueString(),
                        'area_number' => $worksheet->getCell(new CellAddress('$' . Coordinate::stringFromColumnIndex(1) . '$' . $row))->getValueString(),
                        'service_id' => $serviceId,
                    ];
                    AreaPrice::create($val);
//                } catch (\Exception $exception) {
//                    continue;
//                }
            }
        }
    }
}
