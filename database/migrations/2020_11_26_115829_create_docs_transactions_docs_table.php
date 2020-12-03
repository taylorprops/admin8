<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocsTransactionsDocsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('docs_transactions_docs', function(Blueprint $table)
		{
			$table->integer(''id'', true);
			$table->integer('Agent_ID');
			$table->bigInteger('Listing_ID');
			$table->bigInteger('Contract_ID')->nullable();
			$table->bigInteger('Referral_ID')->nullable();
			$table->string(''assigned'', 5)->nullable()->default('no');
			$table->integer('checklist_item_id')->nullable();
			$table->string(''folder'', 45)->nullable();
			$table->string(''file_type'', 45)->nullable()->comment('options - system, user');
			$table->bigInteger('file_id')->nullable()->comment('file_id from docs_create_uploads');
			$table->integer('orig_file_id')->nullable();
			$table->string(''file_name'', 200);
			$table->string(''file_name_display'', 200);
			$table->integer('pages_total');
			$table->text('file_location');
			$table->text('file_location_converted')->nullable();
			$table->integer('order')->nullable();
			$table->string(''transaction_type'', 45)->nullable();
			$table->timestamps(10);
			$table->softDeletes();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('docs_transactions_docs');
	}

}
