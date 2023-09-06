<?php

use App\Models\ {
    Brand,
    Province,
    Manager
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

Route::get('/', function () {
    $item = Brand::with(['province'])->first();
    // $item = Manager::with(['supervisor'])->where('slug', 'manager-1')->first();
    // $item = Province::with(['regency'])
    //     ->where('id', 1)
    //     ->first();

    return response()->json($item);

    return view('welcome');
});
