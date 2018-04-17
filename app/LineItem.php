<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Mpociot\Versionable\VersionableTrait;
use Collective\Html\Eloquent\FormAccessible;

class LineItem extends Model {
    use FormAccessible;
    use SoftDeletes;
    use VersionableTrait;
    
    protected $fillable = ['person_id', 'description', 'qty', 'rate_class_id', 'hours', 'cost', 'cost_overridden', 'cost_secondrate', 'cost_original'];
    protected $touches = ['person'];
    
    public function Person() {
        return $this->belongsTo('App\Person');
    }
    
    public function RateClass() {
        return $this->belongsTo('App\RateClass');
    }
    
    public function tagVersion() {
        $this->enableVersioning();
        $this->version++;
        $this->save();
        return $this->currentVersion()['version_id'];
    }
    
    public function calcPayroll() {
        return LineItem::calculatePayroll($this->hours);
    }
    
    public static function calculatePayroll($hours) {
        $hours_overtime = GlobalSetting::getHoursOvertime();
        $multiplier_overtime = GlobalSetting::getMultiplierOvertime();
        $hours_double = GlobalSetting::getHoursDouble();
        $multiplier_double = GlobalSetting::getMultiplierDouble();
        
        if ($hours <= $hours_overtime)
            return $hours;
        else if ($hours <= $hours_double)
            return $hours_overtime + (($hours - $hours_overtime) * $multiplier_overtime);
        
        return $hours_overtime + 
                    (($hours_double - $hours_overtime) * $multiplier_overtime) + 
                    (($hours - $hours_double) * $multiplier_double);
    }
    
    public function calcAmount() {
        return round($this->qty * $this->calcPayroll() * $this->cost, 2);
    }

    public function formCostAttribute($value) {
        return number_format($value, 2);
    }
    
    public function setCostAttribute($value) {
        return $this->attributes['cost'] = Helper::to_float($value);
    }
}
