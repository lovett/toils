<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBaseSchema extends Migration
{
    /**
     * Crate the base application schema
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->string('name', 100)->unique();
            $table->boolean('active')->index()->default(true);
            $table->string('contactName', 50)->nullable();
            $table->string('contactEmail', 50)->nullable();
            $table->string('address1', 50)->nullable();
            $table->string('address2', 50)->nullable();
            $table->string('city', 50)->nullable();
            $table->string('locality', 20)->nullable();
            $table->integer('postalCode')->nullable()->unsigned();
            $table->string('phone', 20)->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('user_id')->references('id')->on('users');
        });

        Schema::create('projects', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('client_id')->unsigned();
            $table->string('name', 100);
            $table->boolean('active')->default(true);
            $table->boolean('billable')->default(true);
            $table->boolean('taxDeducted')->default(false);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('client_id')->references('id')->on('clients');
        });

        Schema::create('estimates', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->string('name', 100);
            $table->date('submitted')->nullable();
            $table->date('closed')->nullable();
            $table->string('status', 50)->nullable();
            $table->string('recipient', 100)->nullable();
            $table->integer('client_id')->unsigned()->nullable();
            $table->integer('project_id')->unsigned()->nullable();
            $table->integer('fee')->nullable();
            $table->integer('totalHours')->nullable()->unsigned();
            $table->text('summary')->nullable();
            $table->string('submissionType', 50)->nullable();
            $table->integer('submissionSize')->nullable()->unsigned();
            $table->string('signatureType', 50)->nullable();
            $table->integer('signatureSize')->nullable()->unsigned();
            $table->text('summary')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('client_id')->references('id')->on('clients');
            $table->foreign('project_id')->references('id')->on('projects');
        });

        Schema::create('invoices', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('number')->nullable()->unsigned();
            $table->integer('user_id')->unsigned();
            $table->integer('amount')->nullable();
            $table->date('sent')->nullable();
            $table->date('due')->nullable();
            $table->date('paid')->nullable();
            $table->string('name', 75);
            $table->integer('project_id')->unsigned();
            $table->date('start')->nullable();
            $table->date('end')->nullable();
            $table->text('summary')->nullable();
            $table->string('receiptType', 20)->nullable();
            $table->integer('receiptSize')->nullable()->unsigned();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('project_id')->references('id')->on('projects');
        });

        Schema::create('times', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('estimate_id')->unsigned();
            $table->integer('project_id')->unsigned();
            $table->integer('invoice_id')->nullable()->unsigned();
            $table->date('start')->nullable();
            $table->date('minutes')->nullable()->unsigned();
            $table->text('summary')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('estimate_id')->references('id')->on('estimates');
            $table->foreign('project_id')->references('id')->on('projects');
            $table->foreign('invoice_id')->references('id')->on('invoices');
        });
    }

    /**
     * Drop all application tables
     *
     * Tables are dropped in the reverse order they were created to
     * avoid foreign key constraint violations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('times');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('estimates');
        Schema::dropIfExists('projects');
        Schema::dropIfExists('clients');
    }
}
