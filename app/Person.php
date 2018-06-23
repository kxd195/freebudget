<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Mpociot\Versionable\VersionableTrait;

class Person extends Model {
    use SoftDeletes;
    use VersionableTrait;
    
    protected $fillable = ['budget_id', 'day_id', 'unit_id', 'scene_id', 'qty', 'description', 'rate_class_id', 'hours', 'cost', 'cost_overridden', 'cost_secondrate', 'cost_original'];
    protected $touches = ['day'];
    
    public function Scene() {
        return $this->belongsTo('App\Scene');
    }
    public function Unit() {
        return $this->belongsTo('App\Unit');
    }
    
    public function Day() {
        return $this->belongsTo('App\Day');
    }

    public function Budget() {
        return $this->belongsTo('App\Budget');
    }

    public function RateClass() {
        return $this->belongsTo('App\RateClass');
    }
    
    public static function createPersonFromRateClass($budget_id, $day_id, $unit_id, $code, $rate, $hours = 1) {
        $rate_class = RateClass::where('code', $code)->first();
        
        $assistant = new Person();
        $assistant->budget_id = $budget_id;
        $assistant->day_id = $day_id;
        $assistant->unit_id = $unit_id;
        $assistant->qty = 1;
        $assistant->description = $rate_class->name;
        $assistant->rate_class_id = $rate_class->id;
        $assistant->hours = $hours;
        $assistant->cost = $rate;
        $assistant->cost_overridden = true;
        $assistant->disableVersioning();
        $assistant->save();
    }
    
    public function tagVersion() {
        $this->enableVersioning();
        $this->version++;
        $this->save();
        return $this->currentVersion()['version_id'];
    }
    
    public function calcPayroll() {
        return Person::calculatePayroll($this->hours);
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
