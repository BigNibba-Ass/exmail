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

class MajorFillFirstCommand extends Command
{
    protected $signature = 'major:fill-first';

    protected $description = 'todo';

    public function handle(): void
    {
        $this->runAreaList(0);
        $this->runAreaList(1);
        $this->runAreaList(2);
        $this->runAreaList(3);
    }

    public function runAreaList($list)
    {
        $inputFileType = 'Xlsx';
        $inputFileName = storage_path('major_1.xlsx');

        $reader = IOFactory::createReader($inputFileType);
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($inputFileName);

        $worksheet = $spreadsheet->getSheet($list);//


        $highestRow = $worksheet->getHighestRow(); // e.g. 10
        $highestColumn = $worksheet->getHighestColumn(); // e.g 'F'
        $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);

        $serviceId = Company::firstOrCreate(['name' => 'Major Express'])->services()->firstOrCreate(['name' => 'Экспресс доставка'])->id;

        for ($row = 6; $row <= $highestRow; $row++) {
            $whereFrom = ImportService::formatDeparturePoint($worksheet->getCell(new CellAddress('$' . 'A' . '$' . $row))->getValueString());
            $whereTo = ImportService::formatDeparturePoint($worksheet->getCell(new CellAddress('$' . 'B' . '$' . $row))->getValueString());
            $areaNumber = $worksheet->getCell(new CellAddress('$' . 'C' . '$' . $row))->getValueString();
            if (!$whereTo || !$whereFrom) continue;
            $val = [
                'area_number' => $areaNumber,
                'where_from' => DeparturePoint::firstOrCreate(['name' => $whereFrom])->id,
                'where_to' => DeparturePoint::firstOrCreate(['name' => $whereTo])->id,
                'service_id' => $serviceId,
                'terms' => ImportService::formatDeparturePoint($worksheet->getCell(new CellAddress('$' . 'D' . '$' . $row))->getValueString())
            ];
            if ($val['area_number']) {
                Area::firstOrCreate($val);
            }
            AreaPrice::firstOrCreate([
                'weight_min' => 0,
                'weight_max' => 0.49,
                'area_number' => $areaNumber,
                'service_id' => $serviceId,
            ], [
                'price' => $worksheet->getCell(new CellAddress('$' . 'E' . '$' . $row))->getCalculatedValueString(),
                'price_per_extra' => $worksheet->getCell(new CellAddress('$' . 'G' . '$' . $row))->getCalculatedValueString(),
                'extra_definition' => 1,
            ]);
            AreaPrice::firstOrCreate([
                'weight_min' => 0.5,
                'weight_max' => 0.99,
                'area_number' => $areaNumber,
                'service_id' => $serviceId,
            ], [
                'price' => $worksheet->getCell(new CellAddress('$' . 'F' . '$' . $row))->getCalculatedValueString(),
                'price_per_extra' => $worksheet->getCell(new CellAddress('$' . 'G' . '$' . $row))->getCalculatedValueString(),
                'extra_definition' => 1,
            ]);
            AreaPrice::firstOrCreate([
                'weight_min' => 1,
                'weight_max' => 0,
                'area_number' => $areaNumber,
                'service_id' => $serviceId,
            ], [
                'price' => $worksheet->getCell(new CellAddress('$' . 'F' . '$' . $row))->getCalculatedValueString(),
                'price_per_extra' => $worksheet->getCell(new CellAddress('$' . 'G' . '$' . $row))->getCalculatedValueString(),
                'extra_definition' => 1,
            ]);
        }
    }
}
