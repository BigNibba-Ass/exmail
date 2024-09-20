<?php

namespace App\Console\Commands;

use App\Models\Area;
use App\Models\AreaPrice;
use App\Models\Company;
use App\Models\DeparturePoint;
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
        ini_set('memory_limit', '-1');
        $this->runAreaList();
    }

    public function runAreaList()
    {
        $filePath = storage_path('exmail_initial_1.xlsx');
        $reader = ReaderEntityFactory::createReaderFromFile($filePath);
        $reader->open($filePath);

        $serviceId = Company::firstOrCreate(['name' => 'Exmail'])->services()->firstOrCreate(['name' => 'Себестоимость'])->id;


        foreach ($reader->getSheetIterator() as $sheetNum => $sheet) {
            foreach ($sheet->getRowIterator() as $key => $row) {
                if ($key === 1) continue;
                if ($key === 2) continue;
                if ($key === 3) continue;
                if ($key === 4) continue;
                dd($row, $key);
                if ($key === 96) continue;
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
