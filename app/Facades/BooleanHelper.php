<?php

namespace App\Facades;

class BooleanHelper {

    public static function parse($value, $asInt = false) {
        $val = filter_var($value, FILTER_VALIDATE_BOOLEAN);

        if($asInt) {
            return $val ? 1 : 0;
        }

        return $val;
    }
    
}