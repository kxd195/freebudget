<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Mpociot\Versionable\VersionableTrait;
use Carbon\Carbon;
use Collective\Html\Eloquent\FormAccessible;

class Day extends Model {
    use SoftDeletes;
    use VersionableTrait;
    use FormAccessible;
    
    protected $fillable = ['budget_id', 'name', 'actualdate', 'crew_call', 'notes'];
    protected $touches = ['budget'];
    protected $dates = ['actualdate'];
    
    public function people() {
        return $this->hasMany('App\Person');
    }
    
    public function Budget() {
        return $this->belongsTo('App\Budget');
    }
    
    public function generateName() {
        if ($this->id === 0)
            return $this->name;

        return "Day {$this->name} ({$this->actualdate->format('D, M j, Y')})"; 
    }

    public function tagVersion() {
        $this->enableVersioning();
        $this->version++;
        $this->save();
        return $this->currentVersion()['version_id'];
    }
    
    public function calcStandIn() {
        $total = 0;
        foreach ($this->people as $person)
            if ($person->rateclass->code === 'SI')
                $total += $person->qty;
                    
        return $total;
    }
    
    public function calcGeneralExtra() {
        $total = 0;
        foreach ($this->people as $person)
            if ($person->rateclass->code === 'GE')
                $total += $person->qty;
                    
        return $total;
    }
    
    public function calcBackground() {
        $total = 0;
        foreach ($this->people as $person)
            if ($person->rateclass->category->name === 'Background')
                $total += $person->qty;
                
        return $total;
    }
    
    public function calcTotalAmount() {
        $total = 0;
        foreach ($this->people as $person)
            $total += $person->calcAmount();
            
        return $total;
    }
    
    public function formActualdateAttribute($value) {
        return Carbon::parse($value)->format('Y-m-d');
    }
}
