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
    
    protected $fillable = ['budget_id', 'name', 'actualdate', 'location', 'crew_call', 'notes'];
    protected $touches = ['budget'];
    protected $dates = ['actualdate'];
    
    public function people() {
        return $this->hasMany('App\Person');
    }
    
    public function Budget() {
        return $this->belongsTo('App\Budget');
    }
    
    public function generateName() {
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
            foreach($person->line_items as $item)
                if ($item->rateclass->code === 'SI')
                    $total += $item->qty;
                    
        return $total;
    }
    
    public function calcGeneralExtra() {
        $total = 0;
        foreach ($this->people as $person)
            foreach($person->line_items as $item)
                if ($item->rateclass->code === 'GE')
                    $total += $item->qty;
                    
                    return $total;
    }
    
    public function calcBackground() {
        $total = 0;
        foreach ($this->people as $person)
            foreach($person->line_items as $item)
                if ($item->rateclass->category->name === 'Background')
                    $total += $item->qty;
                
        return $total;
    }
    
    public function calcTotalAmount() {
        $total = 0;
        foreach ($this->people as $person)
            foreach($person->line_items as $item)
                $total += $item->calcAmount();
            
        return $total;
    }
    
    public function formActualdateAttribute($value) {
        return Carbon::parse($value)->format('Y-m-d');
    }
}
