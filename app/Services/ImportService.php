<?php

namespace App\Services;

class ImportService {

    public static function formatDeparturePoint($name)
    {
        if($name === '-') $name = false;
        if(is_numeric($name)) $name = false;
        $name = trim($name);
        return str_replace(array("\r", "\n"), ' ', $name);
    }

}
