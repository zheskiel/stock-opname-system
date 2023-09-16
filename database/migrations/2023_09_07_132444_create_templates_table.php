<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTemplatesTable extends Migration
{
    private $tableName = 'template';

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
                $table->string('product_code');
                $table->string('product_name');
                $table->string('unit_label');
                $table->integer('unit_value');
                $table->integer('receipt_tolerance')->default(0);
                $table->integer('outlet_id')->nullable();
                $table->integer('supervisor_id')->nullable();
                $table->integer('manager_id')->default(0);
                $table->integer('owned')
                    ->default(0)
                    ->comment("
                        0 = not both,\n\n
                        1 = own by Leader Kitchen,\n\n
                        2 = Own by Outlet Supervisor,\n\n
                    ");
                $table->integer('status')
                    ->default(0)
                    ->comment("
                        0 = Draft
                        1 = Published
                    ");
                $table->timestamps();
            });
        };
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
