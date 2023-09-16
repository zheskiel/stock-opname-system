<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMasterTable extends Migration
{
    private $tableName = 'master';

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
                $table->integer('product_id');
                $table->string('category');
                $table->string('subcategory');
                $table->string('category_type');
                $table->string('bom_name');
                $table->string('product_code');
                $table->string('product_name');
                $table->integer('base_price');
                $table->string('requestable');
                $table->integer('receipt_tolerance');
                $table->string('saleable');
                $table->string('notes');
                $table->string('vat');
                $table->string('status_uom');
                $table->string('formula');

                $table->integer('owned')->default(0)
                    ->comment("
                        0 = not both,\n\n
                        1 = own by Leader Kitchen,\n\n
                        2 = Own by Outlet Supervisor,\n\n
                        3 = Own by both
                    ");

                $table->json('units');
                $table->timestamps();
            });
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
