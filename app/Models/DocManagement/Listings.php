<?php

namespace App\Models\DocManagement;

use Illuminate\Database\Eloquent\Model;

class Listings extends Model
{
    protected $connection = 'mysql_taylorproperties';
    public $table = 'listings';

    public function scopeListingSearch($query, $state, $zip, $street_number, $street_name, $unit, $street_dir_suffix, $street_dir_suffix_alt) {
        $listings = $query -> where('StateOrProvince', $state) -> where('PostalCode', $zip)
        -> where('StreetNumber', $street_number)
        -> where('StreetName', 'LIKE', $street_name . '%')
        -> where('UnitNumber', 'LIKE', '%' . $unit . '%')
        -> whereRaw('((StreetDirSuffix = \''.$street_dir_suffix.'\' or StreetDirSuffix = \''.$street_dir_suffix_alt.'\') or (StreetDirPrefix = \''.$street_dir_suffix.'\' or StreetDirPrefix = \''.$street_dir_suffix_alt.'\'))')
        -> orderBy('MLSListDate', 'DESC')
        -> get() -> toArray();

        return $listings;
    }
}