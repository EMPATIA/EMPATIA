<?php

use App\Helpers\HBackend;
use App\Http\Controllers\Backend\BackendController;
use App\Http\Controllers\Backend\CMS\ContentsController;
use App\Http\Controllers\Backend\CMS\LanguagesController;
use App\Http\Controllers\Backend\CMS\MenusController;
use App\Http\Controllers\Backend\CMS\TranslationsController;
use App\Http\Controllers\Backend\ConfigurationsController;
use App\Http\Controllers\Backend\Empatia\Cbs\CbsController;
use App\Http\Controllers\Backend\Empatia\Cbs\CbsNotificationsController;
use App\Http\Controllers\Backend\Empatia\Cbs\OperationSchedulesController;
use App\Http\Controllers\Backend\Empatia\Cbs\TopicsController as BackendTopicsController;
use App\Http\Controllers\Backend\FilesController;
use App\Http\Controllers\Backend\Notifications\EmailsController;
use App\Http\Controllers\Backend\Notifications\SmsController;
use App\Http\Controllers\Backend\Notifications\TemplatesController;
use App\Http\Controllers\Backend\Users\UsersController;
use App\Http\Controllers\Frontend\CMS\FrontendController;
use App\Http\Controllers\Frontend\Empatia\Cbs\TopicsController as FrontendTopicsController;
use App\Http\Controllers\Frontend\Users\UsersController as FrontendUsersController;
use App\Http\Controllers\Backend\Empatia\LoginLevelsController;
use App\Http\Controllers\Backend\Empatia\Cbs\TechnicalAnalysisController;
use Illuminate\Support\Facades\Route;

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


/********************************************************************************************
 * NO LANGUAGE URLs
 ********************************************************************************************/

// Files
Route::group([
    'prefix' => 'download',
    'as' => 'download.',
], function () {
    Route::get('file/{name}', [FilesController::class, 'downloadFile'])->name('file');
    Route::get('image/{name}', [FilesController::class, 'downloadImage'])->name('image');
});


Route::group(['prefix' => HBackend::languages_routeLang()], function () {

    /********************************************************************************************
     * BACKEND
     ********************************************************************************************/

    Route::group([
        'prefix' => 'private',
        'middleware' => ['keycloak-web', 'keycloak-web-can:laravel-bo-user'],
    ], function () {

        /**   Users   **/
        Route::group([
            'prefix' => 'users',
            'as' => 'users.',
        ], function () {
            Route::get('/', [UsersController::class, 'index'])->name('index');
            Route::get('/{id}', [UsersController::class, 'show'])->name('show');
        });

        Route::group([
            'prefix' => 'configurations',
            'as' => 'configurations.',
            'controller' => ConfigurationsController::class,
        ], function () {
            Route::get('/', 'index')->name('index');
            Route::get('/create', 'create')->name('create');
            Route::post('/', 'store')->name('store');
            Route::get('/{id}', 'show')->name('show');
            Route::get('/{id}/edit', 'edit')->name('edit');
            Route::put('/{id}', 'update')->name('update');
            Route::delete('/{id}', 'destroy')->name('delete');
            Route::patch('/{id}', 'restore')->name('restore');
        });


        /**   CMS   **/
        Route::group([
            'prefix' => 'cms',
            'as' => 'cms.',
        ], function () {

            Route::group([
                'prefix' => 'translations',
                'as' => 'translations.',
                'controller' => TranslationsController::class,
            ], function () {
                Route::get('/', 'index')->name('index');
                Route::get('/create', 'create')->name('create');
                Route::post('/', 'store')->name('store');
                Route::delete('/{id}', 'destroy')->name('delete');
                Route::patch('/{id}', 'restore')->name('restore');
            });

            // Route::resource('menu-types', \MenuTypesController::class)->parameters(['menu-types' => 'id']);
            // Route::resource('menus', \MenusController::class)->parameters(['menus' => 'id']);

            Route::group([
                'prefix' => 'menus',
                'as' => 'menus.',
                'controller' => MenusController::class,
            ], function () {
                Route::get('/', 'index')->name('index');
                Route::get('/create', 'create')->name('create');
                Route::post('/', 'store')->name('store');
                Route::get('/{id}', 'show')->name('show');
                Route::get('/{id}/edit', 'edit')->name('edit');
                Route::put('/{id}', 'update')->name('update');
                Route::delete('/{id}', 'destroy')->name('delete');
            });

            Route::group([
                'prefix' => 'languages',
                'as' => 'languages.',
                'controller' => LanguagesController::class,
            ], function () {
                Route::get('/', 'index')->name('index');
                Route::get('/create', 'create')->name('create');
                Route::post('/', 'store')->name('store');
                Route::get('/{id}', 'show')->name('show');
                Route::get('/{id}/edit', 'edit')->name('edit');
                Route::put('/{id}', 'update')->name('update');
                Route::delete('/{id}', 'destroy')->name('delete');
                Route::patch('/{id}', 'restore')->name('restore');
            });

            Route::prefix('{type}')->group(function () {
                Route::group([
                    'as' => 'content.',
                    'controller' => ContentsController::class,
                ], function () {
                    Route::get('', 'index')->name('index');
                    Route::get('/create', 'create')->name('create');
                    Route::post('/', 'store')->name('store');
                    Route::get('/{id}', 'show')->name('show');
                    Route::put('/{id}', 'update')->name('update');
                    Route::delete('/{id}', 'destroy')->name('delete');
                    Route::patch('/{id}', 'restore')->name('restore');

                });
            });

        });

        /**   Notifications   **/
        Route::group([
            'prefix' => 'notifications',
            'as' => 'notifications.',
        ], function () {

            Route::group([
                'prefix' => 'templates',
                'as' => 'templates.',
                'controller' => TemplatesController::class,
            ], function () {
                Route::get('/', 'index')->name('index');
                Route::get('/create', 'create')->name('create');
                Route::post('/', 'store')->name('store');
                Route::get('/{id}', 'show')->name('show');
                Route::get('/{id}/edit', 'edit')->name('edit');
                Route::put('/{id}', 'update')->name('update');
                Route::delete('/{id}', 'destroy')->name('delete');
                Route::patch('/{id}', 'restore')->name('restore');
            });

            Route::group([
                'prefix' => 'emails',
                'as' => 'emails.',
                'controller' => EmailsController::class,
            ], function () {
                Route::get('/', 'index')->name('index');
                Route::get('/{id}', 'show')->name('show');
                Route::delete('/{id}', 'destroy')->name('delete');
                Route::patch('/{id}', 'restore')->name('restore');
            });

            Route::group([
                'prefix' => 'sms',
                'as' => 'sms.',
                'controller' => SmsController::class,
            ], function () {
                Route::get('/', 'index')->name('index');
                Route::get('/{id}', 'show')->name('show');
                Route::delete('/{id}', 'destroy')->name('delete');
                Route::patch('/{id}', 'restore')->name('restore');
            });

        });

        Route::group([
            'prefix' => 'login-levels',
            'as' => 'login-levels.',
            'controller' => LoginLevelsController::class,
        ], function () {
            Route::get('/', 'index')->name('index');
            Route::get('/create', 'create')->name('create');
            Route::post('/', 'store')->name('store');
            Route::get('/{id}', 'show')->name('show');
            Route::get('/{id}/edit', 'edit')->name('edit');
            Route::put('/{id}', 'update')->name('update');
            Route::delete('/{id}', 'destroy')->name('delete');
            Route::patch('/{id}', 'restore')->name('restore');
        });

        /***   EMPATIA   ***/
        Route::group([
            'prefix' => 'cbs',
            'as' => 'cbs.',
        ], function () {
            Route::get('/', [CbsController::class, 'index'])->name('cbs.index');
//            Route::delete('/{id}', [BackendTopicsController::class, 'destroy']);


            Route::prefix('{type}')->group(function () {
                // CBS PRIMARY ACTIONS
                Route::get('/', [CbsController::class, 'index'])->name('index');
                Route::get('/create', [CbsController::class, 'create'])->name('create');
                Route::post('/', [CbsController::class, 'store'])->name('store');
                Route::get('/{id}', [CbsController::class, 'show'])->name('show');
                Route::get('/{id}/edit', [CbsController::class, 'edit'])->name('edit');
                Route::put('/{id}', [CbsController::class, 'update'])->name('update');
                Route::delete('/{id}', [CbsController::class, 'destroy'])->name('delete');
                Route::patch('/{id}', [CbsController::class, 'restore'])->name('restore');

                // CBS WIZARDS
                Route::get('{id}/statistics', [CbsController::class, 'statistics'])->name('cbs.type.stats');
                Route::get('{id}/parameters', [CbsController::class, 'parameters'])->name('cbs.type.parameters');

                Route::get('{id}/notifications', [CbsNotificationsController::class, 'show']);
                Route::get('{id}/notifications/edit', [CbsNotificationsController::class, 'edit']);
                Route::put('{id}/notifications', [CbsNotificationsController::class, 'update']);

                Route::get('{id}/configurations', [CbsConfigurationsController::class, 'show']);
                Route::get('{id}/configurations/edit', [CbsConfigurationsController::class, 'edit']);
                Route::put('{id}/configurations', [CbsConfigurationsController::class, 'update']);


                // CBS TOPICS PRIMARY ACTIONS
                Route::prefix('{cbId}/topics')->group(function () {
                    Route::get('/', [BackendTopicsController::class, 'index'])->name('backend.topics.index');
                    Route::get('/create', [BackendTopicsController::class, 'create'])->name('backend.topics.create');
                    Route::post('/', [BackendTopicsController::class, 'store'])->name('backend.topics.store');
                    Route::get('/{id}/edit', [BackendTopicsController::class, 'edit'])->name('backend.topics.edit');
                    Route::get('/{id}', [BackendTopicsController::class, 'show'])->name('backend.topics.show');
                    Route::put('/{id}', [BackendTopicsController::class, 'update'])->name('backend.topics.update');
                    Route::delete('/{id}', [BackendTopicsController::class, 'destroy'])->name('backend.topics.delete');
                    Route::patch('/{id}', [BackendTopicsController::class, 'restore'])->name('backend.topics.restore');
                });
                
                Route::prefix('{cbId}/technical-analysis-questions')->group(function () {
                    Route::get('/status', [TechnicalAnalysisController::class, 'getTopicAnalysisStatus'])->name('technical-analysis.status');
                    Route::post('/status/', [TechnicalAnalysisController::class, 'storeTopicAnalysisStatus'])->name('technical-analysis.status.store');
                    
                    // Technical Analysis questions routes
                    Route::get('/', [TechnicalAnalysisController::class, 'index'])->name('technical-analysis-questions.index');
                    Route::get('/create', [TechnicalAnalysisController::class, 'create'])->name('technical-analysis-questions.create');
                    Route::post('/', [TechnicalAnalysisController::class, 'store'])->name('technical-analysis-questions.store');
                    Route::get('/{code}', [TechnicalAnalysisController::class, 'show'])->name('technical-analysis-questions.show');
                    Route::get('/{code}/edit', [TechnicalAnalysisController::class, 'edit'])->name('technical-analysis-questions.edit');;
                    Route::put('/{code}', [TechnicalAnalysisController::class, 'update'])->name('technical-analysis-questions.update');
                    Route::delete('/{code}', [TechnicalAnalysisController::class, 'destroy'])->name('technical-analysis-questions.delete');
                    Route::patch('/{code}', [TechnicalAnalysisController::class, 'restore'])->name('technical-analysis-questions.restore');
                });
                
                Route::prefix('{cbId}/operation-schedules')->group(function () {
                    Route::get('/', [OperationSchedulesController::class, 'index'])->name('operation-schedules.index');
                    Route::get('/create', [OperationSchedulesController::class, 'create'])->name('operation-schedules.create');
                    Route::post('/', [OperationSchedulesController::class, 'store'])->name('operation-schedules.store');
                    Route::get('/{code}', [OperationSchedulesController::class, 'show'])->name('operation-schedules.show');
                    Route::get('/{code}/edit', [OperationSchedulesController::class, 'edit'])->name('operation-schedules.edit');
                    Route::put('/{code}', [OperationSchedulesController::class, 'update'])->name('operation-schedules.update');
                    Route::delete('/{code}', [OperationSchedulesController::class, 'delete'])->name('operation-schedules.delete');
                    Route::patch('/{code}', [OperationSchedulesController::class, 'restore'])->name('operation-schedules.restore');
                });
            });
        });


        // Default route
        Route::get('/', [BackendController::class, 'index'])->name('private');
        Route::get('/dashboard', [BackendController::class, 'index'])->name('private');
        Route::get('/statistics',[BackendController::class,'statistics'])
            ->name('statistics');
        Route::get('/statistics/{type}',[BackendController::class,'statistics'])
            ->name('statistics.extended');
    });

    /********************************************************************************************
     * FRONTEND
     ********************************************************************************************/

    /**   Empatia   **/

    Route::prefix('/{cbType}/{cbId}/')->group(function () {
        Route::post('/', [FrontendTopicsController::class, 'store'])
            ->name('frontend.topics.store');
        Route::post('/{topicId}', [FrontendTopicsController::class, 'update'])
            ->name('frontend.topics.update');
    });

    Route::group([
        'prefix' => 'cbs',
        'as' => 'cbs.',
    ], function () {


    });

    Route::group([
        'prefix' => 'profile',
        'middleware' => 'keycloak-web',
        'controller' => FrontendUsersController::class,
    ], function () {
        Route::get('/{tab?}', [FrontendUsersController::class, 'show'])->name('profile.show');
        Route::get('/{tab?}/edit', [FrontendUsersController::class, 'edit'])->name('profile.edit');
        Route::put('/' . 'generic', [FrontendUsersController::class, 'updateUserGenericData'])->name('profile.updateUserGenericData');
        Route::put('/' . 'details', [FrontendUsersController::class, 'updateUserDetails'])->name('profile.updateUserDetails');
        Route::put('/' . 'password', [FrontendUsersController::class, 'updateUserPassword'])->name('profile.updateUserPassword');

    });
    Route::get('/news/{slug}/{params?}', [FrontendController::class, 'newsBySlug'])
        ->where('params', '.*');
    Route::get('/', [FrontendController::class, 'pageBySlug']);
    Route::get('/{slug}/{params?}', [FrontendController::class, 'pageBySlug'])
        ->where('params', '.*')
        ->name('page');
});



