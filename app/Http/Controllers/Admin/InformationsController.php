<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Informations\DataRequest;
use App\Models\Area;
use App\Models\AreaPrice;
use App\Models\Company;
use App\Models\DeparturePoint;
use App\Services\ImportService;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Cell\CellAddress;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;

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
}
