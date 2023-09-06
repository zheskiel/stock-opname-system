<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateManagerTable extends Migration
{
    private $tableName = 'manager';
    private $relations = [
        'outlet'
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable($this->tableName)) {
            foreach ($this->relations as $relation) {
                Schema::table($this->tableName, function (Blueprint $table) use ($relation) {
                    $table->integer($relation.'_id')->unsigned()->nullable()->after('password');
                    $table->foreign($relation.'_id')->references('id')->on($relation);
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::dropIfExists($this->tableName);
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
