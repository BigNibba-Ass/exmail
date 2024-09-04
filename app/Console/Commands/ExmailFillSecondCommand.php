<?php

namespace App\Console\Commands;

use App\Models\Area;
use App\Models\AreaPrice;
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
//        DeparturePoint::truncate();
        $inputFileType = 'Xlsx';
        $inputFileName = storage_path('trf.xlsx');

        $reader = IOFactory::createReader($inputFileType);
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($inputFileName);

        $worksheet = $spreadsheet->getSheet(1);//


//        $highestRow = $worksheet->getHighestRow(); // e.g. 10
        $highestColumn = $worksheet->getHighestColumn(); // e.g 'F'
        $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);

        // Города с листа экспресс доставка

//        for ($row = 4; $row <= 102; $row++) {
//            DeparturePoint::create(['name' => $worksheet->getCell(new CellAddress('$' . 'B' . '$' . $row))]);
//        }

        // /Города с листа экспресс доставка


        // Лист Экспресс Доставка
//        for ($row = 3; $row <= 18; $row++) {
//            for ($col = 2; $col <= $highestColumnIndex; $col += 2) {
//                Area::create([
//                    'area_number' => $worksheet->getCell(new CellAddress('$' . Coordinate::stringFromColumnIndex($col) . '$' . $row)),
//                    'where_from' => DeparturePoint::firstWhere(['name' => $worksheet->getCell(new CellAddress('$' . 'A' . '$' . $row))])->id,
//                    'where_to' => DeparturePoint::firstWhere(['name' => $worksheet->getCell(new CellAddress('$' . Coordinate::stringFromColumnIndex($col) . '$' . '1'))])->id,
//                    'service_id' => 2,
//                    'terms' => $worksheet->getCell(new CellAddress('$' . Coordinate::stringFromColumnIndex($col + 1) . '$' . $row)),
//                ]);
//            }
//        }
//        for ($col = 2; $col <= $highestColumnIndex; $col += 2) {
//            for ($row = 3; $row <= 18; $row++) {
//                Area::create([
//                    'area_number' => $worksheet->getCell(new CellAddress('$' . Coordinate::stringFromColumnIndex($col) . '$' . $row)),
//                    'where_from' => DeparturePoint::firstWhere(['name' => $worksheet->getCell(new CellAddress('$' . Coordinate::stringFromColumnIndex($col) . '$' . '1'))])->id,
//                    'where_to' => DeparturePoint::firstWhere(['name' => $worksheet->getCell(new CellAddress('$' . 'A' . '$' . $row))])->id,
//                    'service_id' => 2,
//                    'terms' => $worksheet->getCell(new CellAddress('$' . Coordinate::stringFromColumnIndex($col + 1) . '$' . $row)),
//                ]);
//            }
//        }
        // /Лист экспресс доставка

        // Лист экспресс доставка цены

//        for ($row = 3; $row <= 18; $row++) {
//            $area = $worksheet->getCell(new CellAddress('$' . Coordinate::stringFromColumnIndex(1) . '$' . $row));
//            for ($col = 2; $col <= $highestColumnIndex; $col++) {
//                AreaPrice::create([
//                    'service_id' => 1,
//                    'area_number' => $area,
//                    'weight_max' => $worksheet->getCell(new CellAddress('$' . Coordinate::stringFromColumnIndex($col) . '$' . '2')),
//                    'price' => $worksheet->getCell(new CellAddress('$' . Coordinate::stringFromColumnIndex($col) . '$' . $row))
//                ]);
//            }
//        }

        // /Лист экспресс доставка цены
    }
}
