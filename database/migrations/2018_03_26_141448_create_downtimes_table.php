<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDowntimesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('downtimes', function(Blueprint $table)
		{
			$table->increments('id');

			$table->string('bd_key')->unique();
			$table->date('bd_date');
			$table->time('start');
			$table->time('finished');
			
			$table->string('decl')->nullable();
			$table->string('type')->nullable();
			$table->string('machine')->nullable();
			$table->time('total_time')->nullable();
			$table->time('wait_time')->nullable();
			$table->time('repair_time')->nullable();
			$table->string('responsible')->nullable();
			$table->string('module')->nullable();
			
			$table->integer('mechanic_id')->nullable();
			$table->string('mechanic')->nullable();
			$table->string('mechanic_comment')->nullable();

			$table->integer('leader_id')->nullable();
			$table->string('leader')->nullable();
			$table->string('bd_category_id')->nullable();
			$table->string('bd_category')->nullable();
			$table->string('style')->nullable();
			
			$table->timestamps();

		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('downtimes');
	}

}
