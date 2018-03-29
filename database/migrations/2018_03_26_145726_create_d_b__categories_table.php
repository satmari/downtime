<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDBCategoriesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('d_b__categories', function(Blueprint $table)
		{
			$table->increments('id');

			$table->string('bd_id')->unique();

			$table->string('bd_rs')->nullable();
			$table->string('bd_en')->nullable();
			$table->string('bd_it')->nullable();

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
		Schema::drop('d_b__categories');
	}

}
