<?php

namespace App\Models\CRM;

use Illuminate\Database\Eloquent\Model;

class CRMContacts extends Model
{
    protected $connection = 'mysql';
    public $table = 'crm_contacts';
}
