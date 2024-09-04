<?php

namespace App\Console\Commands;

use App\Models\Area;
use App\Models\AreaPrice;
use App\Models\DeparturePoint;
use Illuminate\Console\Command;
use PhpOffice\PhpSpreadsheet\Cell\CellAddress;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ExmailTableFillCommand extends Command
{
    protected $signature = 'exmail-table:fill';

    protected $description = 'todo';

    public function handle(): void
    {
//        DeparturePoint::truncate();

        $inputFileType = 'Xlsx';
        $inputFileName = storage_path('trf.xlsx');

        $reader = IOFactory::createReader($inputFileType);
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($inputFileName);

        //0, 6
        $worksheet = $spreadsheet->getSheet(0);//


//        $highestRow = $worksheet->getHighestRow(); // e.g. 10
        $highestColumn = $worksheet->getHighestColumn(); // e.g 'F'
        $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);

        // Города с листа экспресс доставка

//        for ($row = 4; $row <= 102; $row++) {
//            DeparturePoint::create(['name' => $worksheet->getCell(new CellAddress('$' . 'B' . '$' . $row))]);
//        }

        // /Города с листа экспресс доставка


        // Лист Экспресс Доставка
//        for ($row = 4; $row <= 102; $row++) {
//            for ($col = 3; $col <= $highestColumnIndex; $col++) {
//                Area::create([
//                    'area_number' => $worksheet->getCell(new CellAddress('$' . Coordinate::stringFromColumnIndex($col) . '$' . $row)),
//                    'where_from' => DeparturePoint::firstWhere(['name' => $worksheet->getCell(new CellAddress('$' . 'B' . '$' . $row))])->id,
//                    'where_to' => DeparturePoint::firstWhere(['name' => $worksheet->getCell(new CellAddress('$' . Coordinate::stringFromColumnIndex($col) . '$' . '3'))])->id,
//                    'service_id' => 1,
//                    'terms' => null,
//                ]);
//            }
//        }
//        for ($col = 3; $col <= $highestColumnIndex; $col++) {
//            for ($row = 4; $row <= 102; $row++) {
//                Area::create([
//                    'area_number' => $worksheet->getCell(new CellAddress('$' . Coordinate::stringFromColumnIndex($col) . '$' . $row)),
//                    'where_from' => DeparturePoint::firstWhere(['name' => $worksheet->getCell(new CellAddress('$' . Coordinate::stringFromColumnIndex($col) . '$' . '3'))])->id,
//                    'where_to' => DeparturePoint::firstWhere(['name' => $worksheet->getCell(new CellAddress('$' . 'B' . '$' . $row))])->id,
//                    'service_id' => 1,
//                    'terms' => null,
//                ]);
//            }
//        }
        // /Лист экспресс доставка

        // Лист экспресс доставка цены

        for ($row = 3; $row <= 18; $row++) {
            $area = $worksheet->getCell(new CellAddress('$' . Coordinate::stringFromColumnIndex(1) . '$' . $row));
            for ($col = 2; $col <= $highestColumnIndex; $col++) {
                $weightArray = explode(';', $worksheet->getCell(new CellAddress('$' . Coordinate::stringFromColumnIndex($col) . '$' . '2')));
                try {
                    AreaPrice::create([
                        'service_id' => 1,
                        'area_number' => $area,
                        'weight_min' => $weightArray[0],
                        'weight_max' => $weightArray[1],
                        'price' => $worksheet->getCell(new CellAddress('$' . Coordinate::stringFromColumnIndex($col) . '$' . $row)),
                        'price_per_extra_kg' => $worksheet->getCell(new CellAddress('$' . Coordinate::stringFromColumnIndex($highestColumnIndex) . '$' . $row)),
                    ]);
                } catch (\Exception $exception) {
                }
            }
        }

        // /Лист экспресс доставка цены
    }
}
