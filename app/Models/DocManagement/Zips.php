<?php

namespace App\Models\DocManagement;

use Illuminate\Database\Eloquent\Model;

class Zips extends Model
{
    public $table = 'docs_zips';

    public function scopeActiveStates() {
        $states = config('global.vars.active_states');
        return $states;
    }
    public function scopeAllStates() {
        $states = Zips::select('state') -> groupBy('state') -> orderBy('state') -> get();
        return $states;
    }

    public function scopeCounties() {
        $counties = Zips::select('county', 'state') -> whereIn('state', config('global.vars.active_states')) -> orderBy('state') -> orderBy('county') -> groupBy('state') -> groupBy('county') -> get() -> toArray();
        return $counties;
    }

    public function scopeCities() {
        $cities = Zips::select('city', 'state') -> whereIn('state', config('global.vars.active_states')) -> orderBy('state') -> orderBy('city') -> groupBy('state') -> groupBy('city') -> get() -> toArray();
        return $cities;
    }

}
