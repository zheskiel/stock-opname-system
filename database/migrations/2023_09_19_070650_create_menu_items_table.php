<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;


class CreateMenuItemsTable extends Migration
{
    private $tableName = 'menu_items';
    private $relations = [
        'menu_group' => 'menu_groups'
    ];
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable($this->tableName)) {
            Schema::create($this->tableName, function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->string('icon');
                $table->string('route');
                $table->boolean('status')->default(true);
                $table->string('permission_name');
                $table->integer('position');
                $table->timestamps();
            });
        };

        if (Schema::hasTable($this->tableName)) {
            foreach ($this->relations as $relationKey => $relationValue) {
                Schema::table($this->tableName, function (Blueprint $table) use ($relationKey, $relationValue) {
                    $table->integer($relationKey.'_id')->unsigned()->nullable()->after('position');
                    $table->foreign($relationKey.'_id')->references('id')->on($relationValue)
                        ->onDelete('set null')->onUpdate('cascade');
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
