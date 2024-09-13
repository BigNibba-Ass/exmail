<?php

namespace App\Console\Commands;

use App\Http\Enums\ServiceTypeEnum;
use App\Models\Area;
use App\Models\AreaPrice;
use App\Models\Company;
use App\Models\DeparturePoint;
use App\Models\Service;
use Illuminate\Console\Command;
use PhpOffice\PhpSpreadsheet\Cell\CellAddress;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ExmailTableFillCommand extends Command
{
    protected $signature = 'exmail:fill-first';

    protected $description = 'todo';

    public function handle(): void
    {
       $this->fillZones();
       $this->setPrices();
    }

    public function fillZones()
    {
        $inputFileType = 'Xlsx';
        $inputFileName = storage_path('trf.xlsx');

        $reader = IOFactory::createReader($inputFileType);
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($inputFileName);

        //0, 7
        $worksheet = $spreadsheet->getSheet(7);//


//        $highestRow = $worksheet->getHighestRow(); // e.g. 10
        $highestColumn = $worksheet->getHighestColumn(); // e.g 'F'
        $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);

        $serviceId = Company::find(Company::EXMAIL_COMPANY_ID)->services()->firstOrCreate(['name' => 'Экспресс доставка по России'])->id;


        // Лист Экспресс Доставка
        for ($row = 4; $row <= 102; $row++) {
            for ($col = 3; $col <= $highestColumnIndex; $col++) {
                Area::create([
                    'area_number' => $worksheet->getCell(new CellAddress('$' . Coordinate::stringFromColumnIndex($col) . '$' . $row)),
                    'where_from' => DeparturePoint::firstOrCreate(['name' => $worksheet->getCell(new CellAddress('$' . 'B' . '$' . $row))])->id,
                    'where_to' => DeparturePoint::firstOrCreate(['name' => $worksheet->getCell(new CellAddress('$' . Coordinate::stringFromColumnIndex($col) . '$' . '3'))])->id,
                    'service_id' => $serviceId,
                    'terms' => null,
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

        //0, 7
        $worksheet = $spreadsheet->getSheet(0);//


//        $highestRow = $worksheet->getHighestRow(); // e.g. 10
        $highestColumn = $worksheet->getHighestColumn(); // e.g 'F'
        $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);

        $serviceId = Company::find(Company::EXMAIL_COMPANY_ID)->services()->firstOrCreate(['name' => 'Экспресс доставка по России'])->id;


        for ($row = 3; $row <= 18; $row++) {
            $area = $worksheet->getCell(new CellAddress('$' . Coordinate::stringFromColumnIndex(1) . '$' . $row));
            for ($col = 2; $col <= $highestColumnIndex; $col++) {
                $weightArray = explode(';', $worksheet->getCell(new CellAddress('$' . Coordinate::stringFromColumnIndex($col) . '$' . '2')));
                    try {
                        AreaPrice::create([
                            'service_id' => $serviceId,
                            'area_number' => $area,
                            'weight_min' => $weightArray[0],
                            'weight_max' => $weightArray[1],
                            'price' => $worksheet->getCell(new CellAddress('$' . Coordinate::stringFromColumnIndex($col) . '$' . $row)),
                            'price_per_extra' => $worksheet->getCell(new CellAddress('$' . Coordinate::stringFromColumnIndex($highestColumnIndex) . '$' . $row)),
                            'extra_definition' => 1,
                        ]);
                    } catch (\Exception){}

            }
        }
    }
}
