<?php

use Illuminate\Database\Seeder;
use App\Models\CRM\CRMContacts;
class AgentContacts extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $count = 100;
        factory(CRMContacts::class, $count) -> create();
    }
}
