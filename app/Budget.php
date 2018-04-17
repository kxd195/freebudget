<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Mpociot\Versionable\VersionableTrait;
use Collective\Html\Eloquent\FormAccessible;
use Carbon\Carbon;

class Budget extends Model {
    use SoftDeletes;
    use VersionableTrait;
    use FormAccessible;
    
    protected $fillable = ['show_id', 'name', 'description', 'startdate', 'enddate', 'num_days', 'notes'];
    protected $touches = ['show'];
    protected $dates = ['startdate', 'enddate'];
    
    public function budget_versions() {
        return $this->hasMany('App\BudgetVersion');
    }
    
    public function days() {
        return $this->hasMany('App\Day');
    }

    public function undated_entries() {
        return $this->hasMany('App\Person')->whereNull('day_id');
    }
    
    public function Show() {
        return $this->belongsTo('App\Show');
    }
    
    public function autoCreate() {
        $currDate = $this->startdate;
        $day_counter = 0;
        $firstWorkDay = null;
        $mainUnit = Unit::where('name', 'Main Unit')->first();
        
        while ($day_counter < $this->num_days
            || (isset($this->enddate) && $currDate->lte($this->enddate))) {
                $dayOfWeek = $currDate->dayOfWeek;
                
                if ($this->show->getWorkingDays()[$dayOfWeek]) {
                    if (!isset($firstWorkDay))
                        $firstWorkDay = $dayOfWeek;
                    
                    $day_counter++;
                    $newDay = new Day();
                    $newDay->name = $day_counter;
                    $newDay->actualdate = $currDate;
                    $newDay->budget_id = $this->id;
                    $newDay->disableVersioning();
                    $newDay->save();
                    
                    // if it's the start of a new week, add the assistant entry
                    if ($dayOfWeek === Carbon::MONDAY)
                        Person::createPersonFromRateClass($newDay->id, $mainUnit->id, 'AS', $this->show->assistant_rate);

                    // ad a wrangler entry for each day
                    // Person::createPersonFromRateClass($newDay->id, $mainUnit->id, 'WR', $this->show->wrangler_rate, 8);
                }
                
                $currDate->addDay();
            }
    }

    public function tagVersion($version_name = null) {
        $this->enableVersioning();
        $this->version++;
        $this->save();
        
        $version_data = ['budget_version_id' => $this->currentVersion()['version_id']];

        foreach ($this->days as $day) {
            $day_array = ['day_version_id' => $day->tagVersion()];
            
            foreach ($day->people as $person) {
                $people_array = ['person_version_id' => $person->tagVersion()];
                
                foreach ($person->line_items as $item) {
                    $line_items_array = ['line_item_version_id' => $item->tagVersion()];
                    $people_array['line_items'][] = $line_items_array;
                }
                
                $day_array['people'][] = $people_array;
            }

            $version_data['days'][] = $day_array;
        }
        
        $budget_version = new BudgetVersion();
        $budget_version->budget_id = $this->id;
        $budget_version->user_id = Auth::id();
        $budget_version->name = $version_name;
        $budget_version->data = serialize($version_data);
        $budget_version->save();
    }
    
    public static function getScenesFromBudget($budget_id) {
        return Budget::find($budget_id)->getScenes();
    }
    
    public function getScenes() {
        $scenes = [];
        foreach ($this->days as $day) {
            foreach ($day->people as $person)
                if (isset($person->scene))
                    $scenes[$person->scene] = $person->scene;
        }
        ksort($scenes);
        
        return $scenes;
    }
    
    public function calcStandIn() {
        $total = 0;
        foreach($this->days as $day)
            $total += $day->calcStandIn();
            
            return $total;
    }
    
    public function calcGeneralExtra() {
        $total = 0;
        foreach($this->days as $day)
            $total += $day->calcGeneralExtra();
            
            return $total;
    }
    
    public function calcBackground() {
        $total = 0;
        foreach($this->days as $day)
            $total += $day->calcBackground();
            
        return $total;
    }
    
    public function calcTotalAmount() {
        $total = 0;
        foreach($this->days as $day)
            $total += $day->calcTotalAmount();
        
        return $total;
    }
    
    public function formStartdateAttribute($value) {
        return Helper::to_formatted_date($value);
    }

    public function formEnddateAttribute($value) {
        return Helper::to_formatted_date($value);
    }
}
