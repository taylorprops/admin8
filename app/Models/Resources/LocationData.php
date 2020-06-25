<?php

namespace App\Models\Resources;

use Illuminate\Database\Eloquent\Model;

class LocationData extends Model
{
    protected $connection = 'mysql';
    public $table = 'docs_zips';
    protected $guarded = [];

    public function scopeActiveStates() {
        $states = config('global.vars.active_states');
        return $states;
    }
    public function scopeAllStates() {
        $states = LocationData::select('state') -> groupBy('state') -> orderBy('state') -> get();
        return $states;
    }
    public function scopeGetStateName($query, $state_abbr) {

        $state_name = LocationData::select('state_name') -> where('state', $state_abbr) -> first();
        return $state_name -> state_name;
    }
    public function scopeCounties() {
        $counties = LocationData::select('county', 'state') -> whereIn('state', config('global.vars.active_states')) -> orderBy('state') -> orderBy('county') -> groupBy('state') -> groupBy('county') -> get() -> toArray();
        return $counties;
    }
    public function scopeCountiesByState($query, $state) {
        $counties = LocationData::select('county') -> where('state', $state) -> groupBy('county') -> get();
        return $counties;
    }

    public function scopeCities() {
        $cities = LocationData::select('city', 'state') -> whereIn('state', config('global.vars.active_states')) -> orderBy('state') -> orderBy('city') -> groupBy('state') -> groupBy('city') -> get() -> toArray();
        return $cities;
    }
}
