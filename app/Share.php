<?php

namespace App;

use Hashids\Hashids;
use Illuminate\Database\Eloquent\Model;

class Share extends Model {
    protected $fillable = ['budget_id', 'budget_version_id', 'modifiable'];
    protected $dates = ['expires_at'];
    const HASHSALT = "This is my hash salt";
    
    public function Budget() {
        return $this->belongsTo('App\Budget');
    }
    
    public function toHash() {
        $hash = new Hashids(self::HASHSALT);
        return $hash->encode($this->id);                
    }
    
    public static function findFromHash($encodedId) {
        $hash = new Hashids(self::HASHSALT);
        return Share::findOrFail($hash->decode($encodedId))->first();
    }
    
}
