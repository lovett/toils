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
            $table->string('contact_name', 50)->nullable();
            $table->string('contact_email', 50)->nullable();
            $table->string('address1', 50)->nullable();
            $table->string('address2', 50)->nullable();
            $table->string('city', 50)->nullable();
            $table->string('locality', 20)->nullable();
            $table->integer('postal_code')->nullable()->unsigned();
            $table->string('phone', 20)->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('user_id')->references('id')->on('users');
        });

        Schema::create('projects', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->string('name', 100);
            $table->integer('client_id')->unsigned();
            $table->boolean('active')->default(true);
            $table->boolean('billable')->default(true);
            $table->boolean('tax_deducted')->default(false);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('client_id')->references('id')->on('clients');
        });

        Schema::create('estimates', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->string('name', 100);
            $table->date('submission_date')->nullable();
            $table->date('close_date')->nullable();
            $table->string('status', 50)->nullable();
            $table->string('recipient', 100)->nullable();
            $table->integer('client_id')->unsigned()->nullable();
            $table->integer('project_id')->unsigned()->nullable();
            $table->integer('fee')->nullable();
            $table->integer('total_hours')->nullable()->unsigned();
            $table->text('summary')->nullable();
            $table->string('submission_document_type', 50)->nullable();
            $table->integer('submission_document_size')->nullable()->unsigned();
            $table->string('signature_document_type', 50)->nullable();
            $table->integer('signature_document_size')->nullable()->unsigned();
            $table->text('statement_of_work')->nullable();
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
            $table->integer('amount_due')->nullable();
            $table->date('sent')->nullable();
            $table->date('due')->nullable();
            $table->date('paid')->nullable();
            $table->string('name', 75);
            $table->integer('project_id')->unsigned();
            $table->date('start')->nullable();
            $table->date('end')->nullable();
            $table->text('summary')->nullable();
            $table->string('receipt_type', 20)->nullable();
            $table->integer('receipt_size')->nullable()->unsigned();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('project_id')->references('id')->on('projects');
        });


        Schema::create('time_entries', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->date('start')->nullable();
            $table->date('end')->nullable();
            $table->text('summary')->nullable();
            $table->integer('estimated_duration')->nullable()->unsigned();
            $table->integer('project_id')->unsigned();
            $table->integer('invoice_id')->unsigned()->nullable();
            $table->timestamps();
            $table->softDeletes();
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
        Schema::dropIfExists('time_entries');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('estimates');
        Schema::dropIfExists('projects');
        Schema::dropIfExists('clients');
    }
}
