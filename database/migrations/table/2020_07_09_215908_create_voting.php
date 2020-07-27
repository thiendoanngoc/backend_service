<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVoting extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('voting_options', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
        });

        Schema::create('voting_topics', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('description')->nullable();
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->string('winning_option_id')->nullable();
            $table->unsignedInteger('creater_id')->nullable();
            $table->unsignedInteger('updater_id')->nullable();
            $table->customTimestamps();

            $table->foreign('creater_id')->references('id')->on('aspire.accounts');
            $table->foreign('updater_id')->references('id')->on('aspire.accounts');
        });

        Schema::create('voting_bindings', function (Blueprint $table) {
            $table->unsignedInteger('voting_topic_id');
            $table->unsignedInteger('voting_option_id');

            $table->foreign('voting_topic_id')->references('id')->on('aspire.voting_topics');
            $table->foreign('voting_option_id')->references('id')->on('aspire.voting_options');
        });

        Schema::create('voters', function (Blueprint $table) {
            $table->unsignedInteger('voter_id');
            $table->unsignedInteger('voting_option_id');
            $table->unsignedInteger('voting_topic_id');

            $table->foreign('voter_id')->references('id')->on('aspire.accounts');
            $table->foreign('voting_option_id')->references('id')->on('aspire.voting_options');
            $table->foreign('voting_topic_id')->references('id')->on('aspire.voting_topics');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('voters');
        Schema::dropIfExists('voting_bindings');
        Schema::dropIfExists('voting_topics');
        Schema::dropIfExists('voting_options');
    }
}
