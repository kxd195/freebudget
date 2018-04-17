<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Mpociot\Versionable\VersionableTrait;

class Person extends Model {
    use SoftDeletes;
    use VersionableTrait;
    
    protected $fillable = ['day_id', 'unit_id', 'description', 'scene'];
    protected $touches = ['day'];
    
    public function line_items() {
        return $this->hasMany('App\LineItem');
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
    
    public static function createPersonFromRateClass($day_id, $unit_id, $code, $rate, $hours = 1) {
        $rate_class = RateClass::where('code', $code)->first();
        
        $assistant = new Person();
        $assistant->day_id = $day_id;
        $assistant->unit_id = $unit_id;
        $assistant->description = $rate_class->name;
        $assistant->disableVersioning();
        $assistant->save();
        
        $assistant_lineitem = new LineItem();
        $assistant_lineitem->person_id = $assistant->id;
        $assistant_lineitem->qty = 1;
        $assistant_lineitem->rate_class_id = $rate_class->id;
        $assistant_lineitem->hours = $hours;
        $assistant_lineitem->cost = $rate;
        $assistant_lineitem->cost_overridden = true;
        $assistant_lineitem->disableVersioning();
        $assistant_lineitem->save();
    }
    
    public function tagVersion() {
        $this->enableVersioning();
        $this->version++;
        $this->save();
        return $this->currentVersion()['version_id'];
    }
    
    public function calcPayroll() {
        $total = 0;
        foreach($this->line_items as $item)
            $total += $item->calcPayroll();
        
        return $total;
    }

    public function calcAmount() {
        $total = 0;
        foreach($this->line_items as $item)
            $total += $item->calcAmount();
            
        return $total;
    }
}
