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
    
    protected $fillable = ['production_id', 'name', 'episode', 'startdate', 'enddate', 'num_days'];
    protected $touches = ['production'];
    protected $dates = ['startdate', 'enddate'];
    
    public function budget_versions() {
        return $this->hasMany('App\BudgetVersion');
    }
    
    public function days() {
        return $this->hasMany('App\Day');
    }

    public function scenes() {
        return $this->hasMany('App\Scene');
    }

    public function undated_entries() {
        return $this->hasMany('App\Person')->whereNull('day_id');
    }
    
    public function Production() {
        return $this->belongsTo('App\Production');
    }
    
    public function autoCreate() {
        $currDate = $this->startdate;
        $day_counter = 0;
        $firstWorkDay = null;
        $mainUnit = Unit::where('name', 'Main Unit')->first();
        
        while ($day_counter < $this->num_days
            || (isset($this->enddate) && $currDate->lte($this->enddate))) {
                $dayOfWeek = $currDate->dayOfWeek;
                
                if ($this->production->getWorkingDays()[$dayOfWeek]) {
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
                    if ($this->assistant_rate_unit === "week") {
                        if ($dayOfWeek === Carbon::MONDAY)
                            Person::createPersonFromRateClass($this->id, $newDay->id, $mainUnit->id, 'AS', $this->production->assistant_rate);
                    } else {
                        // hourly assistant specified
                        if (($dayOfWeek === Carbon::SUNDAY && $this->production->asst_sun)
                                || ($dayOfWeek === Carbon::MONDAY && $this->production->asst_mon)
                                || ($dayOfWeek === Carbon::TUESDAY && $this->production->asst_tue)
                                || ($dayOfWeek === Carbon::WEDNESDAY && $this->production->asst_wed)
                                || ($dayOfWeek === Carbon::THURSDAY && $this->production->asst_thu)
                                || ($dayOfWeek === Carbon::FRIDAY && $this->production->asst_fri)
                                || ($dayOfWeek === Carbon::SATURDAY && $this->production->asst_sat))
                            Person::createPersonFromRateClass($this->id, $newDay->id, $mainUnit->id, 'AS', $this->production->assistant_rate, 8);

                    }

                    // ad a wrangler entry for each day
                    // Person::createPersonFromRateClass($newDay->id, $mainUnit->id, 'WR', $this->production->wrangler_rate, 8);
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
                $day_array['people'][] = $people_array;
            }

            $version_data['days'][] = $day_array;
        }

        foreach ($this->scenes as $scene)
            $version_data['scenes'][] = ['scene_version_id' => $scene->tagVersion()];
        
        $budget_version = new BudgetVersion();
        $budget_version->budget_id = $this->id;
        $budget_version->user_id = Auth::id();
        $budget_version->name = $version_name;
        $budget_version->data = serialize($version_data);
        $budget_version->save();
    }
    
    public function calcStats() {
        RateClass::all();
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
