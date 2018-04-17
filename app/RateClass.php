<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RateClass extends Model {
    use SoftDeletes;
    protected $fillable = ['category_id', 'name', 'code', 'min_hours', 'rate', 'bgcolor', 'is_addon'];
    public static $bgcolors = [
        'none' => 'None',
        'bg-red' => 'Red',
        'bg-orange' => 'Orange',
        'bg-yellow' => 'Yellow',
        'bg-green' => 'Green',
        'bg-blue' => 'Blue',
        'bg-indigo' => 'Indigo',
        'bg-violet' => 'Violet',
    ];

    public function Category() {
        return $this->belongsTo('App\Category');
    }
    
    public function getCodeAbbr() {
        return '<abbr title="' . $this->name . '" data-toggle="tooltip">' . $this->code . '</abbr>';
    }
    
    public function setCodeAttribute($code) {
        return $this->attributes['code'] = strtoupper($code);
    }
    
    public function getMinHoursAttribute($min_hours) {
        return $this->attributes['min_hours'] = number_format($min_hours, 1);
    }

    public function setMinHoursAttribute($min_hours) {
        return $this->attributes['min_hours'] = $min_hours !== null ? $min_hours : 0;
    }
    
    public function getRateAttribute($rate) {
        return $this->attributes['rate'] = number_format($rate, 2);
    }
    
    public function setRateAttribute($rate) {
        return $this->attributes['rate'] = $rate !== null ? $rate : 0;
    }
}
