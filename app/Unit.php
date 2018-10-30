<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model {
    protected $fillable = ['name'];
    
    public function people($days) {
        return $this->hasMany('App\Person')
            ->select('people.*', 'rate_classes.code', 'scenes.name')
            ->leftJoin('rate_classes', 'people.rate_class_id', '=', 'rate_classes.id')
            ->leftJoin('scenes', 'people.scene_id', '=', 'scenes.id')
            ->where('day_id', '=', $days)
            ->orderByRaw("CASE 
                WHEN rate_classes.code='SI' THEN CONCAT('AAA', rate_classes.code)
                WHEN rate_classes.code='AS' THEN CONCAT('BBB', rate_classes.code)
                WHEN rate_classes.code='WR' THEN CONCAT('ZZZ', rate_classes.code)
                ELSE CONCAT(scenes.name, rate_classes.code) END")
            ->get();
    }
    
    public function calcTotalAmount($days) {
        return $this->people($days)->sum(function($i) { 
                return $i->calcAmount(); 
                });
    }
}
