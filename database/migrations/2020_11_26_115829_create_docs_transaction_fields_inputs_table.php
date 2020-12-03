<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocsTransactionFieldsInputsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('docs_transaction_fields_inputs', function(Blueprint $table)
		{
			$table->bigInteger(''id'', true);
			$table->integer('file_id')->nullable();
			$table->string(''file_type'', 15)->nullable();
			$table->bigInteger('field_id')->nullable();
			$table->bigInteger('input_id')->nullable();
			$table->string(''input_name'', 85)->nullable();
			$table->string(''field_type'', 8)->nullable();
			$table->text('input_helper_text')->nullable();
			$table->integer('Agent_ID')->nullable();
			$table->bigInteger('Listing_ID')->nullable();
			$table->bigInteger('Contract_ID')->nullable();
			$table->bigInteger('Referral_ID')->nullable();
			$table->string(''transaction_type'', 45)->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('docs_transaction_fields_inputs');
	}

}
