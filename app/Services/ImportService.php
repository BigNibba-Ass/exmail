<?php

namespace App\Services;

class ImportService
{

    public static function formatDeparturePoint($name)
    {
        if ($name === '-') $name = false;
        if (is_numeric($name)) $name = false;
        $name = preg_replace("/\([^)]+\)/", "", $name);
        $name = trim($name);
        $name = str_replace(
            ['a', 'b', 'c', 'e', 'h', 'k', 'm', 'n', 'o', 'p', 'r', 't', 'x', 'y'],
            ['а', 'б', 'с', 'е', 'н', 'к', 'м', 'н', 'о', 'р', 'я', 'т', 'х', 'у'],
            $name
        );
        return str_replace(array("\r", "\n"), ' ', $name);
    }

}
