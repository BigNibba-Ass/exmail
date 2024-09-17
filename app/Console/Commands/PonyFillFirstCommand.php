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

class PonyFillFirstCommand extends Command
{
    protected $signature = 'pony:fill-first';

    protected $description = 'todo';

    public function handle(): void
    {
        for ($i = 1; $i <= 9; $i++) {
            $this->runAreaList($i);
        }
        $this->setPrices();
    }

    public function runAreaList($sheet)
    {
        $inputFileType = 'Xlsx';
        $inputFileName = storage_path('pony_1.xlsx');

        $reader = IOFactory::createReader($inputFileType);
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($inputFileName);

        $worksheet = $spreadsheet->getSheet($sheet);//


        $highestRow = $worksheet->getHighestRow(); // e.g. 10
        $highestColumn = $worksheet->getHighestColumn(); // e.g 'F'
        $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);


        $serviceId = Company::firstOrCreate(['name' => 'Pony Express'])->services()->firstOrCreate(['name' => 'Стандарт от двери до двери'])->id;

        for ($row = 2; $row <= $highestRow; $row++) {
            for ($col = 2; $col <= $highestColumnIndex; $col++) {
                $whereFrom = ImportService::formatDeparturePoint($worksheet->getCell(new CellAddress('$' . 'A' . '$' . $row))->getValueString());
                $whereTo = ImportService::formatDeparturePoint($worksheet->getCell(new CellAddress('$' . Coordinate::stringFromColumnIndex($col) . '$' . '1'))->getValueString());
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

    public function setPrices()
    {
        $inputFileType = 'Xlsx';
        $inputFileName = storage_path('pony_1.xlsx');

        $reader = IOFactory::createReader($inputFileType);
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($inputFileName);

        $worksheet = $spreadsheet->getSheet(0);//


//        $highestRow = $worksheet->getHighestRow(); // e.g. 10
        $highestColumn = $worksheet->getHighestColumn(); // e.g 'F'
        $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);


        $serviceId = Company::firstOrCreate(['name' => 'Pony Express'])->services()->firstOrCreate(['name' => 'Стандарт от двери до двери'])->id;


        for ($row = 4; $row <= 13; $row++) {
            for ($col = 2; $col <= 13; $col += 2) {
                try {
                    $weightArray = explode(';', $worksheet->getCell(new CellAddress('$' . Coordinate::stringFromColumnIndex($col) . '$' . 3))->getValueString());
                    $val = [
                        'weight_min' => $weightArray[0],
                        'weight_max' => $weightArray[1],
                        'price' => $worksheet->getCell(new CellAddress('$' . Coordinate::stringFromColumnIndex($col) . '$' . $row))->getValueString(),
                        'price_per_extra' => $worksheet->getCell(new CellAddress('$' . Coordinate::stringFromColumnIndex($col + 1) . '$' . $row))->getValueString(),
                        'extra_definition' => $worksheet->getCell(new CellAddress('$' . Coordinate::stringFromColumnIndex($col + 1) . '$' . 3))->getValueString(),
                        'area_number' => $worksheet->getCell(new CellAddress('$' . Coordinate::stringFromColumnIndex(1) . '$' . $row))->getValueString(),
                        'service_id' => $serviceId,
                    ];
                    AreaPrice::create($val);
                } catch (\Exception $exception) {
                    continue;
                }
            }
        }
    }
}
