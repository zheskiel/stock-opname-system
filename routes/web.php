<?php

use Spatie\Permission\Models\Permission;

use App\Models\{
    Admin,
    Manager,
    Reports
};

Route::group(['prefix' => 'api', 'middleware' => ['cors']], function() {
    Route::group(['prefix' => 'v1'], function() {

        Route::get('/test_supervisor', function() {
            return response()->json('supervisor success');
        })
        ->name('test_supervisor')
        ->middleware(['route.permission']);

        Route::get('/test',  function() {
            $model = new Reports();
            $data = $model->first();

            $result = [
                'items' => [
                    'additional' => json_decode($data->additional),
                    'waste' => json_decode($data->waste),
                    'damage' => json_decode($data->damage),
                ],
                'notes' =>  $data->notes
            ];

            return response()->json($result);
        });

        Route::get('/reports', 'Api\ReportsController@Index');
        Route::get('/forms', 'Api\FormsController@Index');

        // Superadmin or Admin only
        Route::group(['middleware' => [
            // 'auth:admin-api',
            // 'role:admin,admin-api'
            "route.permission"
            ]], function() {
            Route::get('/master', 'Api\MasterDataController@Index')->name('master.index');

            Route::group(['prefix' => '/hierarchy'], function() {
                Route::get('/', 'Api\HierarchyController@fetchHierarchy');
            });
        });

        Route::group(['prefix' => '/form'], function() {
            Route::group(['prefix' => '/{managerId}'], function() {
                Route::group(['prefix' => '/outlet/{outletId}'], function() {
                    Route::get('/combined', 'Api\FormsController@fetchCombinedForm');
                });

                Route::group(['prefix' => '/{staffId}'], function() {
                    Route::get('/details', 'Api\FormsController@FetchFormByStaffId');
                    Route::get('/all', 'Api\FormsController@FetchAllSelected');

                    Route::post('/create-detail', 'Api\FormsController@createFormDetail');
                    Route::post('/remove-detail', 'Api\FormsController@removeFormDetail');
                    Route::post('/remove-all-detail', 'Api\FormsController@removeAllFormDetail');
                });
            });
        });

        Route::get('/templates', 'Api\TemplatesController@Index');

        Route::group(['prefix' => '/template'], function() {
            Route::group(['prefix' => '/{templateId}'], function() {
                Route::get('/view', 'Api\TemplateController@View')->name('template.view');
                Route::get('/all', 'Api\TemplateController@FetchAllSelected');

                Route::post('/create-detail', 'Api\TemplateController@createTemplateDetail');
                Route::post('/remove-detail', 'Api\TemplateController@removeTemplateDetail');
                Route::post('/remove-all-detail', 'Api\TemplateController@removeAllTemplateDetail');
            });
        });

        // Staffs
        Route::group([
            'prefix' => '{userType}',
            'where' => ['userType' => 'staff|manager|admin']
        ], function() {
            Route::post('/login', 'Api\AuthController@login');
        });

        Route::group(['prefix' => 'staff'], function() {
            Route::group(['middleware' => 'guest:staff'], function() {
                Route::get('/test', 'Api\TestController@testStaffPage');
            });
        });

        // Managers
        Route::group(['prefix' => 'manager'], function() {
            Route::group(['middleware' => 'guest:manager'], function() {
                Route::get('/test', 'Api\TestController@testManagerPage');
            });
        });

        // Admins
        Route::group(['prefix' => 'admin'], function() {
            Route::group(['middleware' => 'guest:admin'], function() {
                Route::get('/test', 'Api\TestController@testAdminPage');
            });
        });

        Route::post('/logout', 'Api\AuthController@Logout');

        Route::get('/manager', 'Api\IndexController@testManager');
        Route::get('/supervisor', 'Api\IndexController@testSupervisor');

        // Route::get('/hierarchy', 'Api\IndexController@testHierarchy');
        Route::get('/template', 'Api\IndexController@testTemplate')->name('template');
    });
});

Route::get('/', function() {
    return View('welcome');
});

Route::get('{any?}', function () {
    return redirect('/');
})->where('any', '.*');

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