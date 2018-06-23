<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Mpociot\Versionable\VersionableTrait;

class Scene extends Model {
	use SoftDeletes;
	use VersionableTrait;

	protected $fillable = ['budget_id', 'name', 'description', 'location', 'notes'];
	protected $touches = ['budget'];

	public function Budget() {
		return $this->belongsTo('App\Budget');
	}

	public static function removeOrphans($budget_id) {
        $active_scene_ids = Person::where('budget_id', $budget_id)->whereNotNull('scene_id')->select('scene_id')->pluck('scene_id')->all();
        $orphans = Scene::where('budget_id', $budget_id)->whereNotIn('id', $active_scene_ids)->get();

        foreach ($orphans as $entry) {
            $entry->disableVersioning();
            $entry->delete();
        }
	}

    public function tagVersion() {
        $this->enableVersioning();
        $this->version++;
        $this->save();
        return $this->currentVersion()['version_id'];
    }
	
}
