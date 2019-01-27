<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * The initial database schema.
 */
class CreateBaseSchema extends Migration
{
    /**
     * Create the base application schema.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'clients',
            function (Blueprint $table) {
                $table->increments('id');
                $table->string('name', 100)->unique();
                $table->boolean('active')->index()->default(true);
                $table->string('contactName', 50)->nullable();
                $table->string('contactEmail', 50)->nullable();
                $table->string('address1', 50)->nullable();
                $table->string('address2', 50)->nullable();
                $table->string('city', 50)->nullable();
                $table->string('locality', 20)->nullable();
                $table->string('postalCode')->nullable();
                $table->string('phone', 20)->nullable();
                $table->timestamps();
                $table->softDeletes();
            }
        );

        Schema::create(
            'client_user',
            function (Blueprint $table) {
                $table->integer('client_id')->unsigned();
                $table->integer('user_id')->unsigned();
                $table->primary(['client_id', 'user_id']);
                $table->foreign('client_id')->references('id')->on('clients');
                $table->foreign('user_id')->references('id')->on('users');
            }
        );

        Schema::create(
            'projects',
            function (Blueprint $table) {
                $table->increments('id');
                $table->integer('client_id')->unsigned();
                $table->string('name', 100);
                $table->boolean('active')->default(true);
                $table->boolean('billable')->default(true);
                $table->boolean('taxDeducted')->default(false);
                $table->timestamps();
                $table->softDeletes();
                $table->foreign('client_id')->references('id')->on('clients');
            }
        );

        Schema::create(
            'estimates',
            function (Blueprint $table) {
                $table->increments('id');
                $table->string('name', 100);
                $table->date('submitted')->nullable();
                $table->string('status', 50);
                $table->string('recipient', 100)->nullable();
                $table->integer('client_id')->unsigned()->nullable();
                $table->integer('fee')->nullable();
                $table->integer('hours')->unsigned()->nullable();
                $table->text('summary')->nullable();
                $table->text('statement_of_work')->nullable();
                $table->timestamps();
                $table->softDeletes();
                $table->foreign('client_id')->references('id')->on('clients');
            }
        );

        Schema::create(
            'estimate_user',
            function (Blueprint $table) {
                $table->integer('estimate_id')->unsigned();
                $table->integer('user_id')->unsigned();
                $table->primary(['estimate_id', 'user_id']);
                $table->foreign('estimate_id')->references('id')->on('estimates');
                $table->foreign('user_id')->references('id')->on('users');
            }
        );

        Schema::create(
            'invoices',
            function (Blueprint $table) {
                $table->increments('id');
                $table->integer('number')->nullable()->unsigned();
                $table->integer('amount')->nullable();
                $table->date('sent')->nullable();
                $table->date('due')->nullable();
                $table->date('paid')->nullable();
                $table->string('name', 75);
                $table->integer('project_id')->unsigned();
                $table->dateTime('start')->nullable();
                $table->dateTime('end')->nullable();
                $table->text('summary')->nullable();
                $table->string('receipt', 40)->nullable();
                $table->timestamps();
                $table->softDeletes();
                $table->foreign('project_id')->references('id')->on('projects');
            }
        );

        Schema::create(
            'times',
            function (Blueprint $table) {
                $table->increments('id');
                $table->integer('user_id')->unsigned();
                $table->integer('estimatedDuration')->unsigned()->default(0);
                $table->integer('project_id')->unsigned();
                $table->integer('invoice_id')->nullable()->unsigned();
                $table->dateTime('start')->nullable();
                $table->integer('minutes')->nullable()->unsigned();
                $table->text('summary')->nullable();
                $table->timestamps();
                $table->softDeletes();
                $table->foreign('user_id')->references('id')->on('users');
                $table->foreign('project_id')->references('id')->on('projects');
                $table->foreign('invoice_id')->references('id')->on('invoices');
            }
        );
    }

    /**
     * Drop all application tables.
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
