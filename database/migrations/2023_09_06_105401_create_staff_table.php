<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStaffTable extends Migration
{
    private $tableName = 'staff';
    private $relations = [
        'outlet', 'manager', 'supervisor', 'staff_type'
    ];

    private $externalTableName = 'supervisor';
    private $externalRelations = ['staff'];


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
                    $table->integer($relation.'_id')->unsigned()->nullable()->after('email');
                    $table->foreign($relation.'_id')->references('id')->on($relation)
                        ->onDelete('set null')->onUpdate('cascade');
                });
            }
        }

        if (Schema::hasTable($this->externalTableName)) {
            foreach ($this->externalRelations as $relation) {
                Schema::table($this->externalTableName, function (Blueprint $table) use ($relation) {
                    $table->integer($relation.'_id')->unsigned()->nullable()->after('slug');
                    $table->foreign($relation.'_id')->references('id')->on($relation);
                });
            }
        }
    }
}
