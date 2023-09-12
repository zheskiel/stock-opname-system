<?php

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
            $units = json_decode($query->units, true);

            uasort($units, function ($item1, $item2) {
                return $item2['value'] <=> $item1['value'];
            });

            $query->units = $units;

            return $query;
        });

    return response()->json($master);
});
Route::get('/hierarchy', function () {
    $items = Brand::with(['province'])->first();
    // $items = StaffType::get();

    // foreach ($items as $item) {
    //     $staffs[] = $item->staffs($item->id);
    // }

    // $items = $staffs;

    // $data = $items->staffsSameTypeOnly()->get();

    // dd( $items->staffs()->get() );

    return response()->json($items);

    // return view('welcome');
});
