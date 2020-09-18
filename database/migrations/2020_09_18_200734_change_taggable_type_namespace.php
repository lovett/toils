<?php

// phpcs:disable Squiz.Commenting.ClassComment.Missing

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

class ChangeTaggableTypeNamespace extends Migration
{


    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("UPDATE taggables SET taggable_type='App\Models\Time' WHERE taggable_type='App\Time'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("UPDATE taggables SET taggable_type='App\Time' WHERE taggable_type='App\Models\Time'");
    }
}
