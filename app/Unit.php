<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model {
    protected $fillable = ['name'];
    
    public function people($days) {
        return is_int($days) 
                ? $this->hasMany('App\Person')->where('day_id', '=', $days)->get()
                : $this->hasMany('App\Person')->whereIn('day_id', $days->pluck('id'))->get();
    }
    
    public function calcTotalAmount($days) {
        return $this->people($days)->sum(function($i) { 
                return $i->calcAmount(); 
                });
    }
}
