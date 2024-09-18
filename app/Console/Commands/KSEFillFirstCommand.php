<?php

namespace App\Console\Commands;

use App\Http\Enums\ServiceTypeEnum;
use App\Models\Area;
use App\Models\AreaPrice;
use App\Models\Company;
use App\Models\DeparturePoint;
use App\Models\Service;
use App\Services\ImportService;
use Illuminate\Console\Command;
use PhpOffice\PhpSpreadsheet\Cell\CellAddress;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;

class KSEFillFirstCommand extends Command
{
    protected $signature = 'kse:fill-first';

    protected $description = 'todo';

    public function handle(): void
    {
        $serviceId = Company::firstOrCreate(['name' => 'KSE'])->services()->firstOrCreate(['name' => 'Эконом доставка'])->id;
        $this->fillZones($serviceId);
        $this->setPricesForFirst($serviceId);
    }

    public function fillZones($serviceId)
    {
        $inputFileType = 'Xlsx';
        $inputFileName = storage_path('kse.xlsx');

        $reader = IOFactory::createReader($inputFileType);
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($inputFileName);

        //0, 7
        $worksheet = $spreadsheet->getSheet(2);//


        $highestRow = $worksheet->getHighestRow(); // e.g. 10
        $highestColumn = $worksheet->getHighestColumn(); // e.g 'F'
        $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);

        for ($col = 2; $col <= $highestColumnIndex; $col++) {
            for ($row = 2; $row <= $highestRow; $row++) {
                $whereFrom = ImportService::formatDeparturePoint($worksheet->getCell(new CellAddress('$' . Coordinate::stringFromColumnIndex($col) . '$' . '1')));
                $whereTo = ImportService::formatDeparturePoint($worksheet->getCell(new CellAddress('$' . 'A' . '$' . $row)));
                if (!$whereTo || !$whereFrom) continue;
                Area::create([
                    'area_number' => $worksheet->getCell(new CellAddress('$' . Coordinate::stringFromColumnIndex($col) . '$' . $row)),
                    'where_from' => DeparturePoint::firstOrCreate(['name' => $whereFrom])->id,
                    'where_to' => DeparturePoint::firstOrCreate(['name' => $whereTo])->id,
                    'service_id' => $serviceId,
                    'terms' => null,
                ]);
            }
        }
    }

    public function setTerms($serviceId)
    {
        $inputFileType = 'Xlsx';
        $inputFileName = storage_path('kse.xlsx');

        $reader = IOFactory::createReader($inputFileType);
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($inputFileName);

        //0, 7
        $worksheet = $spreadsheet->getSheet(3);//


        $highestRow = $worksheet->getHighestRow(); // e.g. 10
        $highestColumn = $worksheet->getHighestColumn(); // e.g 'F'
        $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);

        for ($col = 2; $col <= $highestColumnIndex; $col++) {
            for ($row = 2; $row <= $highestRow; $row++) {
                $whereFrom = ImportService::formatDeparturePoint($worksheet->getCell(new CellAddress('$' . Coordinate::stringFromColumnIndex($col) . '$' . '1')));
                $whereTo = ImportService::formatDeparturePoint($worksheet->getCell(new CellAddress('$' . 'A' . '$' . $row)));
                $terms = $worksheet->getCell(new CellAddress('$' . Coordinate::stringFromColumnIndex($col) . '$' . $row))->getValueString();
                if (!$whereTo || !$whereFrom || !$terms) continue;
                try {
                    \DB::table('areas')
                        ->where(['where_from' => DeparturePoint::firstWhere(['name' => $whereFrom])->id, 'where_to' => DeparturePoint::firstWhere(['name' => $whereTo])->id, 'service_id' => $serviceId])
                        ->update(['terms' => $terms]);
                } catch (\Exception $exception) {
                    continue;
//                    dd($whereFrom, $whereTo);
                }
            }
        }
    }

    public function setPricesForFirst($serviceId)
    {
        $inputFileType = 'Xlsx';
        $inputFileName = storage_path('kse.xlsx');

        $reader = IOFactory::createReader($inputFileType);
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($inputFileName);

        //0, 7
        $worksheet = $spreadsheet->getSheet(0);//


//        $highestRow = $worksheet->getHighestRow(); // e.g. 10
        $highestColumn = $worksheet->getHighestColumn(); // e.g 'F'
        $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);
        for ($col = 16; $col <= 28; $col++) {
            $area = str_replace('Зона ', '', $worksheet->getCell(new CellAddress('$' . Coordinate::stringFromColumnIndex($col) . '$' . '3')));
            for ($row = 34; $row <= 45; $row += 2) {
                $weightArray = explode(';', $worksheet->getCell(new CellAddress('$' . Coordinate::stringFromColumnIndex(15) . '$' . $row)));
                try {
                    AreaPrice::create([
                        'service_id' => $serviceId,
                        'area_number' => $area,
                        'weight_min' => $weightArray[0],
                        'weight_max' => $weightArray[1],
                        'price' => $worksheet->getCell(new CellAddress('$' . Coordinate::stringFromColumnIndex($col) . '$' . $row))->getCalculatedValueString(),
                        'price_per_extra' => $worksheet->getCell(new CellAddress('$' . Coordinate::stringFromColumnIndex($col) . '$' . $row + 1))->getCalculatedValueString(),
                        'extra_definition' => 1,
                    ]);
                } catch (\Exception) {
                }

            }
        }
    }

    public function setPricesForSecond($serviceId)
    {
        $inputFileType = 'Xlsx';
        $inputFileName = storage_path('kse.xlsx');

        $reader = IOFactory::createReader($inputFileType);
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($inputFileName);

        //0, 7
        $worksheet = $spreadsheet->getSheet(0);//


//        $highestRow = $worksheet->getHighestRow(); // e.g. 10
        $highestColumn = $worksheet->getHighestColumn(); // e.g 'F'
        $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);
        for ($col = 16; $col <= 28; $col++) {
            $area = str_replace('Зона ', '', $worksheet->getCell(new CellAddress('$' . Coordinate::stringFromColumnIndex($col) . '$' . '3')));
            for ($row = 4; $row <= 30; $row++) {
                $weightArray = explode(';', $worksheet->getCell(new CellAddress('$' . Coordinate::stringFromColumnIndex(15) . '$' . $row)));
                try {
                    AreaPrice::create([
                        'service_id' => $serviceId,
                        'area_number' => $area,
                        'weight_min' => $weightArray[0],
                        'weight_max' => $weightArray[1],
                        'price' => $worksheet->getCell(new CellAddress('$' . Coordinate::stringFromColumnIndex($col) . '$' . $row))->getCalculatedValueString(),
                        'price_per_extra' => $worksheet->getCell(new CellAddress('$' . Coordinate::stringFromColumnIndex($col) . '$' . 30))->getCalculatedValueString(),
                        'extra_definition' => 1,
                    ]);
                } catch (\Exception) {
                }

            }
        }
    }
}
