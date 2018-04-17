<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model {
    use SoftDeletes;
    protected $fillable = ['name'];
    
    public function rate_classes() {
        return $this->hasMany('App\RateClass');
    }
}