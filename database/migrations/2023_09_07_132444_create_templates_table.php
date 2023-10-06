<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTemplatesTable extends Migration
{
    private $tableNames = [
        'templates',
        'details'
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable($this->tableNames[0])) {
            Schema::create($this->tableNames[0], function (Blueprint $table) {
                $table->increments('id');
                $table->string('title');
                $table->string('slug');
                $table->integer('outlet_id')->nullable();
                $table->integer('supervisor_id')->nullable();
                $table->string('supervisor_duty')->nullable();
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
        }

        if (!Schema::hasTable($this->tableNames[1])) {
            Schema::create($this->tableNames[1], function (Blueprint $table) {
                $table->increments('id');
                $table->integer('templates_id');
                $table->integer('product_id');
                $table->string('product_code');
                $table->string('product_name');
                $table->json('units');
                $table->integer('receipt_tolerance')->default(0);
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
        Schema::dropIfExists($this->tableNames[0]);
        Schema::dropIfExists($this->tableNames[1]);
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
