<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Expand the project model with time budgeting.
 *
 */
class ProjectAllotedHours extends Migration
{
    /**
     * Add time budgeting fields to the projects table.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->unsignedInteger('allottedTotalMinutes')->nullable();
            $table->unsignedInteger('allottedWeeklyMinutes')->nullable();
        });
    }

    /**
     * Drop time budgeting fields from the projects table.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('allotedTotalMinutes');
            $table->dropColumn('allotedWeeklyMinutes');
        });
    }
}
