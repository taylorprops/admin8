<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Fields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fields', function (Blueprint $table) {
            $table -> bigIncrements('id');
            $table -> timestamps();
            $table -> integer('upload_id');
            $table -> string('field_type', 100);
            $table -> string('field_name', 100);
            $table -> decimal('pos_top', 3, 2);
            $table -> decimal('pos_left', 3, 2);
            $table -> decimal('width', 3, 2);
            $table -> decimal('height', 3, 2);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fields');
    }
}
