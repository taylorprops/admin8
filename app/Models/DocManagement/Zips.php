<?php

namespace App\Models\DocManagement;

use Illuminate\Database\Eloquent\Model;

class Zips extends Model
{
    public $table = 'zips';

    public function scopeStates() {
        $states = Zips::select('state') -> groupBy('state') -> get() -> toArray();
        return $states;
    }

    public function scopeCounties() {
        $counties = Zips::select('county', 'state') -> whereIn('state', ['MD', 'VA', 'PA', 'DC']) -> orderBy('state') -> orderBy('county') -> groupBy('state') -> groupBy('county') -> get() -> toArray();
        return $counties;
    }

    public function scopeCities() {
        $cities = Zips::select('city', 'state') -> whereIn('state', ['MD', 'VA', 'PA', 'DC']) -> orderBy('state') -> orderBy('city') -> groupBy('state') -> groupBy('city') -> get() -> toArray();
        return $cities;
    }

}
