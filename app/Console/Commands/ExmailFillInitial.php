<?php

namespace App\Console\Commands;

use App\Models\Area;
use App\Models\AreaPrice;
use App\Models\Company;
use App\Models\DeparturePoint;
use App\Models\Service;
use App\Services\ImportService;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use Illuminate\Console\Command;
use PhpOffice\PhpSpreadsheet\Cell\CellAddress;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ExmailFillInitial extends Command
{
    protected $signature = 'exmail:fill-initial';

    protected $description = 'todo';

    public function handle(): void
    {
        $files = [storage_path('exmail_initial_1.xlsx'), storage_path('exmail_initial_2.xlsx'), storage_path('exmail_initial_3.xlsx')];
        foreach ($files as $file) {
            $this->runAreaList($file);
        }
    }

    public function runAreaList($filePath)
    {
        $reader = ReaderEntityFactory::createReaderFromFile($filePath);
        $reader->open($filePath);

        $serviceId = Company::firstOrCreate(['name' => 'Exmail'])->services()->firstOrCreate(['name' => Service::$EXMAIL_INITIAL_SERVICE_NAME])->id;


        foreach ($reader->getSheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $key => $row) {
                if ($key === 1) continue;
                $rowArray = $row->toArray();
                $areaNumber = 'exmail_init_custom_' . $rowArray[1] . "__" . $rowArray[4];
                Area::firstOrCreate([
                    'area_number' => $areaNumber,
                    'where_from' => DeparturePoint::firstOrCreate(['name' => $rowArray[1]])->id,
                    'where_to' => DeparturePoint::firstOrCreate(['name' => $rowArray[4]])->id,
                    'service_id' => $serviceId,
                ]);
                AreaPrice::firstOrCreate([
                    'weight_min' => 0,
                    'weight_max' => 0.24,
                    'area_number' => $areaNumber,
                    'service_id' => $serviceId,
                ], [
                    'price' => $rowArray[5],
                    'price_per_extra' => $rowArray[8],
                    'extra_definition' => 1,
                ]);
                AreaPrice::firstOrCreate([
                    'weight_min' => 0.25,
                    'weight_max' => 0.49,
                    'area_number' => $areaNumber,
                    'service_id' => $serviceId,
                ], [
                    'price' => $rowArray[6],
                    'price_per_extra' => $rowArray[8],
                    'extra_definition' => 1,
                ]);
                AreaPrice::firstOrCreate([
                    'weight_min' => 0.5,
                    'weight_max' => 0.99,
                    'area_number' => $areaNumber,
                    'service_id' => $serviceId,
                ], [
                    'price' => $rowArray[7],
                    'price_per_extra' => $rowArray[8],
                    'extra_definition' => 1,
                ]);
                AreaPrice::firstOrCreate([
                    'weight_min' => 1,
                    'weight_max' => 0,
                    'area_number' => $areaNumber,
                    'service_id' => $serviceId,
                ], [
                    'price' => $rowArray[7],
                    'price_per_extra' => $rowArray[8],
                    'extra_definition' => 1,
                ]);
            }
        }

        $reader->close();
    }
}
