<?php

use App\Models\ {
    Brand,
    Province,
    Manager,
    Master
};

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', 'IndexController@Index');
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
    $item = Brand::with(['province'])->first();
    // $item = Manager::with(['supervisor'])->where('slug', 'manager-1')->first();
    // $item = Province::with(['regency'])
    //     ->where('id', 1)
    //     ->first();

    return response()->json($item);

    // return view('welcome');
});
