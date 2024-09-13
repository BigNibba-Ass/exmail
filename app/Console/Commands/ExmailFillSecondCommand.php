<?php

namespace App\Console\Commands;

use App\Models\Area;
use App\Models\AreaPrice;
use App\Models\Company;
use App\Models\DeparturePoint;
use Illuminate\Console\Command;
use PhpOffice\PhpSpreadsheet\Cell\CellAddress;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ExmailFillSecondCommand extends Command
{
    protected $signature = 'exmail:fill-second';

    protected $description = 'todo';

    public function handle(): void
    {
        $this->fillAreas();
        $this->setPrices();
    }

    public function fillAreas()
    {
        $inputFileType = 'Xlsx';
        $inputFileName = storage_path('trf.xlsx');

        $reader = IOFactory::createReader($inputFileType);
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($inputFileName);

        $worksheet = $spreadsheet->getSheet(1);//


//        $highestRow = $worksheet->getHighestRow(); // e.g. 10
        $highestColumn = $worksheet->getHighestColumn(); // e.g 'F'
        $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);


        $serviceId = Company::find(Company::EXMAIL_COMPANY_ID)->services()->firstOrCreate(['name' => 'Сборный груз'])->id;


        for ($row = 3; $row <= 18; $row++) {
            for ($col = 2; $col <= $highestColumnIndex; $col += 2) {
                Area::create([
                    'area_number' => $worksheet->getCell(new CellAddress('$' . Coordinate::stringFromColumnIndex($col) . '$' . $row)),
                    'where_from' => DeparturePoint::firstOrCreate(['name' => $worksheet->getCell(new CellAddress('$' . 'A' . '$' . $row))])->id,
                    'where_to' => DeparturePoint::firstOrCreate(['name' => $worksheet->getCell(new CellAddress('$' . Coordinate::stringFromColumnIndex($col) . '$' . '1'))])->id,
                    'service_id' => $serviceId,
                    'terms' => $worksheet->getCell(new CellAddress('$' . Coordinate::stringFromColumnIndex($col + 1) . '$' . $row)),
                ]);
            }
        }
    }

    public function setPrices()
    {
        $inputFileType = 'Xlsx';
        $inputFileName = storage_path('trf.xlsx');

        $reader = IOFactory::createReader($inputFileType);
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($inputFileName);

        $worksheet = $spreadsheet->getSheet(2);//


//        $highestRow = $worksheet->getHighestRow(); // e.g. 10
        $highestColumn = $worksheet->getHighestColumn(); // e.g 'F'
        $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);


        $serviceId = Company::find(Company::EXMAIL_COMPANY_ID)->services()->firstOrCreate(['name' => 'Сборный груз'])->id;


        for ($row = 3; $row <= 10; $row++) {
            $area = $worksheet->getCell(new CellAddress('$' . Coordinate::stringFromColumnIndex(1) . '$' . $row))->getValueString();
            for ($col = 2; $col <= $highestColumnIndex; $col += 2) {
                $weightArray = explode(';', $worksheet->getCell(new CellAddress('$' . Coordinate::stringFromColumnIndex($col) . '$' . '1'))->getValueString());
                try {
                    AreaPrice::create([
                        'service_id' => $serviceId,
                        'area_number' => $area,
                        'weight_min' => trim($weightArray[0]),
                        'weight_max' => trim($weightArray[1]),
                        'price' => $worksheet->getCell(new CellAddress('$' . Coordinate::stringFromColumnIndex($col) . '$' . $row))->getValueString(),
                        'price_per_extra' => $worksheet->getCell(new CellAddress('$' . Coordinate::stringFromColumnIndex($col + 1) . '$' . $row))->getValueString(),
                        'extra_definition' => 1
                    ]);
                } catch (\Exception) {
                    AreaPrice::create([
                        'service_id' => $serviceId,
                        'area_number' => $area,
                        'weight_min' => trim($weightArray[0]),
                        'weight_max' => trim($weightArray[1]),
                        'price' => $worksheet->getCell(new CellAddress('$' . Coordinate::stringFromColumnIndex($col) . '$' . $row))->getValueString(),
                        'price_per_extra' => $worksheet->getCell(new CellAddress('$' . Coordinate::stringFromColumnIndex($col + 1) . '$' . $row))->getCalculatedValueString(),
                        'extra_definition' => 1
                    ]);
                }
            }
        }
    }
}
