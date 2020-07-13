<?php

namespace App\Models\DocManagement\Transactions\Referrals;

use Illuminate\Database\Eloquent\Model;

class Referrals extends Model
{
    protected $connection = 'mysql';
    public $table = 'docs_transactions_referrals';
    protected $primaryKey = 'Referral_ID';
    protected $guarded = [];
}
