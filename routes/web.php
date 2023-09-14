<?php
use App\Traits\HelpersTrait;
use App\Models\ {
    Brand,
    Master,
    Manager,
    Supervisor,
    StaffType
};


Route::get('/', function() {
    return View('welcome');
});

Route::get('/test', 'IndexController@Test');

Route::get('/master', function() {
    $master = Master::get()
        ->each(function($query) {
            $query->units = HelpersTrait::sortUnitsByValue($query, 'value');

            return $query;
        });

    return response()->json($master);
});
Route::get('/hierarchy', function () {
    $items = Brand::with(['province'])->first();

    return response()->json($items);
});
