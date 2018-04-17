<?php

namespace App;

use Carbon\Carbon;

class Helper {
    public static function to_float($num_formatted_value) {
        return floatval(preg_replace('/[^\d.]/', '', $num_formatted_value));
    }

    public static function to_formatted_date($value) {
        return $value !== null ? Carbon::parse($value)->format('Y-m-d') : '';
    }
}
