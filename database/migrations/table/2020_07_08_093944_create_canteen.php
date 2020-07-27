<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCanteen extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('canteen_registrations', function (Blueprint $table) {
            $table->increments('id');
            $table->dateTime('date');
            $table->unsignedInteger('type');
            $table->unsignedInteger('account_id');
            $table->unsignedInteger('amount');
            $table->unsignedInteger('mode');
            $table->unsignedInteger('status')->default(1);
            $table->string('note')->nullable();
            $table->string('name_list')->nullable();
            $table->unsignedInteger('creater_id')->nullable();
            $table->unsignedInteger('updater_id')->nullable();
            $table->customSoftDeletes();
            $table->customTimestamps();

            $table->foreign('account_id')->references('id')->on('aspire.accounts');
            $table->foreign('creater_id')->references('id')->on('aspire.accounts');
            $table->foreign('updater_id')->references('id')->on('aspire.accounts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('canteen_registrations');
    }
}
