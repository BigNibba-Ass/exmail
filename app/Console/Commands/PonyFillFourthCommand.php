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

class PonyFillFourthCommand extends Command
{
    protected $signature = 'pony:fill-fourth';

    protected $description = 'todo';

    public function handle(): void
    {
        $this->runAreaList();
    }

    public function runAreaList()
    {
        $inputFileType = 'Xlsx';
        $inputFileName = storage_path('pony_4.xlsx');

        $reader = IOFactory::createReader($inputFileType);
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($inputFileName);

        $worksheet = $spreadsheet->getSheet(0);//


        $highestRow = $worksheet->getHighestRow(); // e.g. 10
        $highestColumn = $worksheet->getHighestColumn(); // e.g 'F'
        $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);

        $serviceId = Company::firstOrCreate(['name' => 'Pony Express'])->services()->firstOrCreate(['name' => 'Сборный груз от двери до двери'])->id;

        for ($row = 3; $row <= $highestRow; $row++) {
            $whereFrom = ImportService::formatDeparturePoint($worksheet->getCell(new CellAddress('$' . 'A' . '$' . $row))->getValueString());
            $whereTo = ImportService::formatDeparturePoint($worksheet->getCell(new CellAddress('$' . 'B' . '$' . $row))->getValueString());
            $areaNumber = 'custom_' . $row;
            if (!$whereTo || !$whereFrom) continue;
            $val = [
                'area_number' => $areaNumber,
                'where_from' => DeparturePoint::firstOrCreate(['name' => $whereFrom])->id,
                'where_to' => DeparturePoint::firstOrCreate(['name' => $whereTo])->id,
                'service_id' => $serviceId,
            ];
            if ($val['area_number']) {
                Area::firstOrCreate($val);
            }
            for ($i = 3; $i <= 12; $i += 2) {
                $weightArray = explode(';', $worksheet->getCell(new CellAddress('$' . Coordinate::stringFromColumnIndex($i) . '$' . '2'))->getValueString());
                AreaPrice::create([
                    'weight_min' => $weightArray[0],
                    'weight_max' => $weightArray[1],
                    'price' => $worksheet->getCell(new CellAddress('$' . Coordinate::stringFromColumnIndex($i) . '$' . $row))->getValueString(),
                    'price_per_extra' => $worksheet->getCell(new CellAddress('$' . Coordinate::stringFromColumnIndex($i + 1) . '$' . $row))->getValueString(),
                    'extra_definition' => $worksheet->getCell(new CellAddress('$' . Coordinate::stringFromColumnIndex($i + 1) . '$' . 2))->getValueString(),
                    'area_number' => $areaNumber,
                    'service_id' => $serviceId,
                ]);
            }
        }
    }
}
