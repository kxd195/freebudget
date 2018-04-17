<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Collective\Html\Eloquent\FormAccessible;

class Show extends Model {
    use FormAccessible;
    use SoftDeletes;
    
    protected $fillable = ['name', 'type', 'qty', 
        'work_sun', 'work_mon', 'work_tue', 'work_wed', 'work_thu', 'work_fri', 'work_sat',
        'assistant_rate', 'wrangler_rate', 'wrangler_addl_rate', 'num_union'
    ];
    
    const TYPE_SERIES = 'TV Series';
    const TYPE_PILOT = 'Pilot';
    const TYPE_FEATURE = 'Feature';
    const TYPE_MOVIE = 'Movie of the Week';
    
    public function budgets() {
        return $this->hasMany('App\Budget');
    }
    
    public static function getTypes() {
        return [
            self::TYPE_SERIES => self::TYPE_SERIES,
            self::TYPE_PILOT => self::TYPE_PILOT,
            self::TYPE_FEATURE => self::TYPE_FEATURE,
            self::TYPE_MOVIE => self::TYPE_MOVIE,
        ];
    }
    
    public function getWorkingDays() {
        return [
            $this->work_sun,  
            $this->work_mon,
            $this->work_tue,
            $this->work_wed,
            $this->work_thu,
            $this->work_fri,
            $this->work_sat
        ];
    }
    public function formAssistantRateAttribute($value) {
        return number_format($value, 2);
    }

    public function setAssistantRateAttribute($value) {
        return $this->attributes['assistant_rate'] = Helper::to_float($value);
    }
    
    public function formWranglerRateAttribute($value) {
        return number_format($value, 2);
    }

    public function setWranglerRateAttribute($value) {
        return $this->attributes['wrangler_rate'] = Helper::to_float($value);
    }
    
    public function formWranglerAddlRateAttribute($value) {
        return number_format($value, 2);
    }
    
    public function setWranglerAddlRateAttribute($value) {
        return $this->attributes['wrangler_addl_rate'] = Helper::to_float($value);
    }
    
}
