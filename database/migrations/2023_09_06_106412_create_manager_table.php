<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateManagerTable extends Migration
{
    private $items = [
        0 => [
            'name'   => 'manager_outlet_supervisor',
            'first'  => 'manager',
            'second' => 'outlet',
            'third'  => 'supervisor'
        ],
        1 => [
            'name'   => 'staff_supervisor_staff_type',
            'first'  => 'staff',
            'second' => 'supervisor',
            'third'  => 'staff_type'
        ]
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach($this->items as $item) {
            if (!Schema::hasTable($item['name'])) {
                Schema::create($item['name'], function (Blueprint $table) use ($item) {
                    $table->integer($item['first'] . '_id')->unsigned();
                    $table->foreign($item['first'] . '_id')
                        ->references('id')
                        ->on($item['first'])
                        ->onDelete('cascade');
                    
                    $table->integer($item['second'] . '_id')->unsigned();
                    $table->foreign($item['second'] . '_id')
                        ->references('id')
                        ->on($item['second'])
                        ->onDelete('cascade');

                    $table->integer($item['third'] . '_id')->unsigned();
                    $table->foreign($item['third'] . '_id')
                        ->references('id')
                        ->on($item['third'])
                        ->onDelete('cascade');
                });

                if (Schema::hasTable($item['name'])) {
                    DB::statement("alter table ".$item['name']." add primary key (".$item['first']."_id, ".$item['second']."_id, ".$item['third']."_id)");
                }
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

        foreach($this->items as $item) {
            Schema::dropIfExists($item['name']);
        }

        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
