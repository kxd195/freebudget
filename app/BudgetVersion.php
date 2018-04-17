<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Mpociot\Versionable\Version;

class BudgetVersion extends Model {
    protected $fillable = ['name'];
    private $version_nums;
    private $budget;
    
    public function User() {
        return $this->belongsTo('App\User');
    }
    
    public function init() {
        $version_nums = unserialize($this->data);
        $budget_version_id = $version_nums['budget_version_id'];
        
        $this->budget = Version::find($budget_version_id)->getModel();
        $this->budget->days = new Collection();
        
        if (isset($version_nums['days']))
            foreach ($version_nums['days'] as $day_history) {
                $day_version_id = $day_history['day_version_id'];
                $day = Version::find($day_version_id)->getModel();
                $day->people = new Collection();
                
                if (isset($day_history['people']))
                    foreach ($day_history['people'] as $person_history) {
                        $person_version_id = $person_history['person_version_id'];
                        $person = Version::find($person_version_id)->getModel();
                        $person->line_items = new Collection();

                        if (isset($person_history['line_items']))
                            foreach ($person_history['line_items'] as $item_history) {
                                $item_version_id = $item_history['line_item_version_id'];
                                $item = Version::find($item_version_id)->getModel();
                                $person->line_items->push($item);
                            }
                        
                        $day->people->push($person);
                    }
                
                $this->budget->days->push($day);
            }
        
        return $this;
    }
    
    public function getBudget() {
        return $this->budget;
    }
}
