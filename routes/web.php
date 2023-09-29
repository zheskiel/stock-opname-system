<?php
Route::group(['middleware' => 'cors', 'prefix' => 'api'], function() {
    Route::group(['prefix' => 'v1'], function() {

        // Staffs
        Route::group(['prefix' => 'staff'], function() {
            Route::post('/login', 'Api\AuthController@StaffLogin');

            Route::group(['middleware' => 'guest:staff'], function() {
                Route::get('/test', 'Api\TestController@testStaffPage');
            });
        });

        // Managers
        Route::group(['prefix' => 'manager'], function() {
            Route::post('/login', 'Api\AuthController@ManagerLogin');

            Route::group(['middleware' => 'guest:manager'], function() {
                Route::get('/test', 'Api\TestController@testManagerPage');
            });
        });

        // Admins
        Route::group(['prefix' => 'admin'], function() {
            Route::post('/login', 'Api\AuthController@AdminLogin');

            Route::group(['middleware' => 'guest:admin'], function() {
                Route::get('/test', 'Api\TestController@testAdminPage');
            });
        });

        Route::post('/logout', 'Api\AuthController@Logout');

        Route::get('/manager', 'Api\IndexController@testManager');
        Route::get('/supervisor', 'Api\IndexController@testSupervisor');

        Route::get('/hierarchy', 'Api\IndexController@testHierarchy');
        Route::get('/master', 'Api\IndexController@testMaster');
        Route::get('/template', 'Api\IndexController@testTemplate');
    });
});

Route::get('/', function() {
    return View('welcome');
});
Route::get('/home', 'IndexController@Test');

Route::get('/test', 'IndexController@Test')->middleware('auth');

/*
Route::group(['prefix' => 'admin'], function() {
    Route::get('/', 'Auth\LoginController@showAdminLoginForm')->name('admin.login-view');
    Route::post('/', 'Auth\LoginController@adminLogin')->name('admin.login');

    Route::get('/dashboard', function() {
        return view('admin')->with(['title' => 'Admin']);
    })->middleware('auth:admin');
});

Route::group(['prefix' => 'manager'], function() {
    Route::get('/', 'Auth\LoginController@showManagerLoginForm')->name('manager.login-view');
    Route::post('/', 'Auth\LoginController@managerLogin')->name('manager.login');

    Route::get('/dashboard', function() {
        return view('manager')->with(['title' => 'manager']);
    })->middleware('auth:manager');
});
*/

// Auth::routes([
//     'register' => false,
//     'reset' => false,
//     'verify' => false,
// ]);