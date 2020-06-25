<?php

namespace App\Models\DocManagement\Create\Fields;

use Illuminate\Database\Eloquent\Model;

use App\Models\DocManagement\Create\Fields\FieldInputs;

use App\Models\DocManagement\Transactions\EditFiles\UserFieldsInputs;
use App\Models\DocManagement\Transactions\EditFiles\UserFieldsValues;
use App\Models\DocManagement\Transactions\Listings\Listings;
use App\Models\DocManagement\Transactions\Members\Members;

class CommonFields extends Model
{
    protected $connection = 'mysql';
    public $table = 'docs_create_common_fields';
    public $timestamps = false;
    protected $guarded = [];

    public function scopeGetCommonFields() {
        $common_fields = CommonFields::select('field_name', 'field_type') -> orderBy('field_order', 'ASC') -> get();
        $common_field_types = $common_fields -> mapToGroups(function ($item, $key) {
            return [ $item['field_type'] => [ $item['field_name'] ] ];
        });
        $common_field_types -> toArray();

        $field_types = FieldTypes::select('field_type') -> get() -> toArray();

        $common = array();
        foreach($field_types as $type) {

            if($common_field_types -> get($type['field_type'])) {
                $common[$type['field_type']] = $common_field_types -> get($type['field_type']) -> all();
            }
        }
        return $common;
    }

    public function ScopeGetCommonNameValue($query, $common_name, $input_id, $field_type, $Listing_ID = null, $Contract_ID = null, $Agent_ID) {
        if($field_type == 'system') {

            $field_input = UserFieldsInputs::where('input_id', $input_id) -> where('field_type', 'common') -> get();
            $common_name_search = $this -> where('field_name', $common_name) -> first();

            $listing = Listings::where('Listing_ID', $Listing_ID) -> first();
            $members_modal = new Members();
            $members = $members_modal -> where('Listing_ID', $Listing_ID) -> orWhere('Contract_ID', $Contract_ID) -> get();

            $value = '';

            if(!empty($common_name_search) && $common_name_search -> db_column_name != '') {

                $db_column_name = $common_name_search -> db_column_name;
                if($listing -> $db_column_name != '' ) {
                    $value = $listing -> $db_column_name;
                }

            } else {

                $member = null;
                if($common_name == 'Seller or Landlord One Name') {
                    $member = $members -> where('member_type_id', $members_modal -> GetMemberTypeID('Seller'));
                    if(count($member) > 0) {
                        $member = $member -> values();
                        $member = $member[0];
                    } else {
                        $member = null;
                    }
                } else if($common_name == 'Seller or Landlord Two Name') {
                    $member = $members -> where('member_type_id', $members_modal -> GetMemberTypeID('Seller'));
                    if(count($member) > 1) {
                        $member = $member -> values();
                        $member = $member[1];
                    } else {
                        $member = null;
                    }
                } else if($common_name == 'Buyer or Renter One Name') {
                    $member = $members -> where('member_type_id', $members_modal -> GetMemberTypeID('Buyer'));
                    if(count($member) > 0) {
                        $member = $member -> values();
                        $member = $member[0];
                    } else {
                        $member = null;
                    }
                } else if($common_name == 'Buyer or Renter Two Name') {
                    $member = $members -> where('member_type_id', $members_modal -> GetMemberTypeID('Buyer'));
                    if(count($member) > 1) {
                        $member = $member -> values();
                        $member = $member[1];
                    } else {
                        $member = null;
                    }
                } else if($common_name == 'List Agent Company') {
                    $value = $listing -> ListOfficeName;
                } else if($common_name == 'Selling Agent Company') {
                    $value = $listing -> BuyerOfficeName;
                } else if($common_name == 'Full Address') {
                    $value = $listing -> FullStreetAddress . ' ' . $listing -> City . ' ' . $listing -> StateOrProvince .' '.$listing -> PostalCode;
                } else if($common_name == 'Street Address') {
                    $value = $listing -> FullStreetAddress;
                } else if($common_name == 'City') {
                    $value = $listing -> City;
                } else if($common_name == 'State') {
                    $value = $listing -> StateOrProvince;
                } else if($common_name == 'Zip Code') {
                    $value = $listing -> PostalCode;
                } else if($common_name == 'County') {
                    $value = $listing -> County;
                } else if($common_name == 'Listing Agent Name') {
                    $value = $listing -> ListAgentFirstName . ' ' . $listing -> ListAgentLastName;
                } else if($common_name == 'Buyers Agent Name') {
                    $value = $listing -> BuyerAgentFirstName . ' ' . $listing -> BuyerAgentLastName;
                }

                if($member) {
                    $value = $member -> first_name . ' ' . $member -> last_name;
                }

            }
            if($value == null) {
                $value = '';
            }
            return trim($value);


        } else if($field_type == 'user') {



        }
    }
}
