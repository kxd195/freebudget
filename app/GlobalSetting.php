<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GlobalSetting extends Model {
    protected $fillable = ['hours_overtime', 'multiplier_overtime', 'hours_double', 'multiplier_double'];
    private static $global_setting;
    
    public function __invoke() {
        $global_setting = GlobalSetting::findOrFail(1);
    }
    
    public static function getHoursOvertime() :int {
        return GlobalSetting::findOrFail(1)->hours_overtime;
    }

    public static function getMultiplierOvertime() :float {
        return GlobalSetting::findOrFail(1)->multiplier_overtime;
    }

    public static function getHoursDouble() :int {
        return GlobalSetting::findOrFail(1)->hours_double;
    }

    public static function getMultiplierDouble() :float {
        return GlobalSetting::findOrFail(1)->multiplier_double;
    }
}
