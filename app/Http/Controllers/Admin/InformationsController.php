<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Informations\DataRequest;
use App\Models\Area;
use App\Models\AreaPrice;
use App\Models\Company;
use App\Models\DeparturePoint;
use App\Models\Service;
use App\Services\ImportService;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Cell\CellAddress;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Psy\Util\Str;

class InformationsController extends Controller
{
    public function index()
    {
        return inertia('Admin/Informations/Index', [
            'companies' => Company::with('services')->get()
        ]);
    }

    public function uploadData(DataRequest $request)
    {
        $inputFileType = 'Xlsx';
        $path = $request->file('file')->store('/temp');

        $reader = IOFactory::createReader($inputFileType);
        $reader->setReadDataOnly(true);
        $file = storage_path('app/' . $path);
        $spreadsheet = $reader->load($file);

        //0, 7
        $worksheet = $spreadsheet->getSheet(1);//


        $highestRow = $worksheet->getHighestRow(); // e.g. 10
        $highestColumn = $worksheet->getHighestColumn(); // e.g 'F'
        $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);

        $serviceId = $request->get('service_id');


        // Лист Экспресс Доставка
        for ($col = 2; $col <= $highestColumnIndex; $col++) {
            for ($row = 3; $row <= $highestRow; $row++) {
                $whereFrom = ImportService::formatDeparturePoint($worksheet->getCell(new CellAddress('$' . Coordinate::stringFromColumnIndex($col) . '$' . '2')));
                $whereTo = ImportService::formatDeparturePoint($worksheet->getCell(new CellAddress('$' . 'A' . '$' . $row)));
                if (!$whereTo || !$whereFrom) continue;
                Area::updateOrCreate([
                    'area_number' => $worksheet->getCell(new CellAddress('$' . Coordinate::stringFromColumnIndex($col) . '$' . $row))->getValue(),
                    'where_from' => DeparturePoint::firstOrCreate(['name' => $whereFrom])->id,
                    'where_to' => DeparturePoint::firstOrCreate(['name' => $whereTo])->id,
                    'service_id' => $serviceId,
                    'terms' => null,
                ]);
            }
        }

        $worksheet = $spreadsheet->getSheet(0);//


        $highestRow = $worksheet->getHighestRow(); // e.g. 10
        $highestColumn = $worksheet->getHighestColumn(); // e.g 'F'
        $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);
        for ($row = 3; $row <= $highestRow; $row++) {
            $area = $worksheet->getCell(new CellAddress('$' . Coordinate::stringFromColumnIndex(1) . '$' . $row))->getValueString();
            for ($col = 2; $col <= $highestColumnIndex; $col++) {
                $weightArray = explode(';', $worksheet->getCell(new CellAddress('$' . Coordinate::stringFromColumnIndex($col) . '$' . '2')));
                try {
                    $areaPrice = AreaPrice::updateOrCreate([
                        'service_id' => $serviceId,
                        'area_number' => $area,
                        'weight_min' => $weightArray[0],
                        'weight_max' => $weightArray[1],
                    ], [
                        'price' => $worksheet->getCell(new CellAddress('$' . Coordinate::stringFromColumnIndex($col) . '$' . $row))->getValueString(),
                        'price_per_extra' => $worksheet->getCell(new CellAddress('$' . Coordinate::stringFromColumnIndex($highestColumnIndex) . '$' . $row))->getValueString(),
                        'extra_definition' => 1,
                    ]);
                } catch (\Exception $exception) {
//                    dd($exception->getMessage());
                }

            }
        }

        \Storage::delete($file);

        return redirect()->back();
    }

    public function downloadData(Request $request)
    {
        $prices = AreaPrice::where(['service_id' => $request->get('service_id')])->get();
        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet->getActiveSheet();
        $activeWorksheet->setCellValue('A1', 'Зоны');
        $activeWorksheet->mergeCells('A1:A2');

        $iVertical = 3;
        $areas = $prices->groupBy('area_number')->map(function ($row, $key) {
            $row->key = intval($key);
            return $row;
        })->sortBy('key');
        foreach ($areas as $key => $area) {
            $activeWorksheet->setCellValue('A' . $iVertical, $key);

            $iHorizontal = 2;
            foreach ($area as $item) {
                $activeWorksheet->setCellValue([$iHorizontal, $iVertical], $item['price']);
                $activeWorksheet->setCellValue([$iHorizontal, 2], $item['weight_min'] . ';' . $item['weight_max']);
                $iHorizontal++;
            }
            $activeWorksheet->setCellValue([$iHorizontal, $iVertical], $area[0]['price_per_extra']);
            $activeWorksheet->setCellValue([$iHorizontal, 2], 'послед.');
            $iVertical++;
        }

        $writer = new Xlsx($spreadsheet);
        $file = storage_path('app/' . \Illuminate\Support\Str::random(32));
        $writer->save($file);
        $service = Service::find($request->get('service_id'));
        return response()->download($file, $service->company->name . '-' . $service->name . '-' . now() . '.xlsx');
    }
}
