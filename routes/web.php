<?php

use Spatie\Permission\Models\Permission;

use App\Models\{
    Admin,
    Manager,
    Forms,
    Notes,
    Reports,
    Templates
};

use App\Traits\HelpersTrait;
use App\Traits\ApiResponsesTrait;
use Faker\Factory as Faker;
use Carbon\Carbon;

Route::group(['prefix' => 'api', 'middleware' => ['cors']], function() {
    Route::group(['prefix' => 'v1'], function() {
        Route::get('/test_create_notes', function() {
            $managerId = 1;
            $outletId = 1;
            $format = 'Y-m-d';
            $date = '2023-10-25';

            $forms = Forms::where('manager_id', $managerId)
                ->where('outlet_id', $outletId)
                ->orderBy('id')
                ->get();

            $faker = Faker::create();
            $dateTime = Carbon::parse($date)->format($format);

            foreach($forms as $form) {
                $notes = new Notes();
                $note = $notes->firstOrCreate([
                    'forms_id' => $form->id,
                    'staff_id' => $form->staff_id,
                    'date'     => $dateTime,
                ],  [
                    'forms_id' => $form->id,
                    'staff_id' => $form->staff_id,
                    'date'     => $dateTime,
                    'notes'    => $faker->paragraph(10)
                ]);

                $form->notes()->syncWithoutDetaching($note);
            }
        });

        // Waste
        Route::get('/fetch/{templateId}/waste', 'Api\ReportsController@fetchWasteByTemplate');
        Route::get('/forms', 'Api\FormsController@Index');

        Route::group(['prefix' => '/reports'], function() {
            Route::get('/', 'Api\ReportsController@Index');
            Route::post('/create', 'Api\ReportsController@Store');
        });

        // Superadmin or Admin only
        Route::group(['middleware' => ["route.permission"]], function() {
            Route::get('/master', 'Api\MasterDataController@Index')->name('master.index');

            Route::group(['prefix' => '/hierarchy'], function() {
                Route::get('/', 'Api\HierarchyController@fetchHierarchy');
            });
        });

        Route::group(['prefix' => '/form'], function() {
            Route::post('/position/report', 'Api\StockPositionController@CreateStockPosition');
            Route::get('/position/report', 'Api\StockPositionController@FetchStockPosition');

            Route::get('/compare/{templateId}/waste', 'Api\ReportsController@FetchWaste');
            Route::get('/final', 'Api\FinalFormController@Index');
            Route::post('/final', 'Api\FinalFormController@Create');

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