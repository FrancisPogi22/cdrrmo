<?php

use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\BarangayController;
use App\Http\Controllers\CdrrmoController;
use App\Http\Controllers\GuidelinesController;
use App\Http\Controllers\GuessController;
use App\Http\Controllers\DisasterController;
use App\Http\Controllers\EvacuationCenterController;
use App\Http\Controllers\RecordEvacueeController;
use Illuminate\Support\Facades\Route;

Route::controller(AuthenticationController::class)->group(function (){
    Route::post('/', 'authUser')->name('login');

    Route::group(['middleware' => 'check.login'], function(){
        Route::view('/', 'auth/authUser')->name('home');
    });
});

Route::group(['prefix' => 'resident', 'middleware' => 'guest'], function(){

    Route::group(['prefix' => 'reportAccident'], function(){
        Route::controller(ReportController::class)->group(function (){
            Route::get('/viewReport', 'displayGReport')->name('GdisplayReport');
            Route::post('/addReport', 'addReport')->name('GaddReport');
        });
    });

    Route::controller(GuessController::class)->group(function (){
        Route::get('/dashboard', 'dashboard')->name('Gdashboard');
        Route::get('/eligtasGuidelines', 'guessEligtasGuidelines')->name('Gguidelines');
        Route::get('/eligtasGuidelines/guidelines/{guideline_id}', 'guessEligtasGuide')->name('Gguide');
        Route::get('/evacuationCenter', 'guessEvacuationCenter')->name('GEvacuation');
        Route::get('/reportAccident', 'guessReportAccident')->name('Greport');
        Route::get('/hotlineNumbers', 'guessHotlineNumbers')->name('GNumbers');
        Route::get('/statistics', 'guessStatistics')->name('Gstatistics');
        Route::get('/about', 'guessAbout')->name('Gabout');
    });
});

Route::group(['prefix' => 'cdrrmo', 'middleware' => 'auth'], function(){

    Route::group(['prefix' => 'eligtasGuidelines'], function(){
        Route::controller(GuidelinesController::class)->group(function (){
            Route::post('/guide/addGuide{guideline_id}', 'addGuide')->name('Caguide');
            Route::put('/guide/updateGuide/{guide_id}', 'updateGuide')->name('Cupdateguide');
            Route::get('/guide/removeGuide/{guide_id}', 'removeGuide')->name('Cremoveguide');

            Route::post('/guidelines/addGuidelines', 'addGuidelines')->name('Caguidelines');
            Route::put('/guidelines/updateGuidelines/{guidelines_id}', 'updateGuidelines')->name('Cupdateguidelines');
            Route::get('/guidelines/removeGuidelines/{guidelines_id}', 'removeGuidelines')->name('Cremoveguidelines');
        });
    });

    Route::group(['prefix' => 'evacuation'], function(){
        Route::controller(EvacuationCenterController::class)->group(function (){
            Route::post('/registerEvacuation', 'registerEvacuation')->name('Cregisterevacuation');
            Route::put('/updateEvacuation/{evacuation_id}', 'updateEvacuation')->name('Cupdateevacuation');
            Route::delete('/removeEvacuation/{evacuation_id}', 'removeEvacuation')->name('Cremoveevacuation');
        });
    });

    Route::group(['prefix' => 'disaster'], function(){
        Route::controller(DisasterController::class)->group(function (){
            Route::post('/registerDisaster', 'registerDisaster')->name('Cregisterdisaster');
            Route::put('/updateDisaster/{disaster_id}', 'updateDisaster')->name('Cupdatedisaster');
            Route::delete('/removeDisaster/{disaster_id}', 'removeDisaster')->name('Cremovedisaster');
        });
    });

    Route::group(['prefix' => 'barangay'], function(){
        Route::controller(BarangayController::class)->group(function (){
            Route::post('/registerBarangay', 'registerBarangay')->name('Cregisterbarangay');
            Route::put('/updateBarangay/{barangay_id}', 'updateBarangay')->name('Cupdatebarangay');
            Route::delete('/removeBarangay/{barangay_id}', 'removeBarangay')->name('Cremovebarangay');
        });
    });

    Route::group(['prefix' => 'reportAccident'], function(){
        Route::controller(ReportController::class)->group(function (){
            Route::post('/addReport', 'addReport')->name('CaddReport');
            Route::get('/viewReport', 'displayCReport')->name('CdisplayReport');
            Route::post('/approveReport/{report_id}', 'approveReport')->name('CapproveReport');
            Route::delete('/removeReport/{report_id}', 'removeReport')->name('CremoveReport');
        });
    });

    Route::group(['prefix' => 'recordEvacuee'], function(){
        Route::controller(RecordEvacueeController::class)->group(function (){
            Route::post('/recordEvacueeInfo', 'recordEvacueeInfo')->name('CrecordEvacueeInfo');
        });
    });

    Route::controller(CdrrmoController::class)->group(function (){
        Route::get('/dashboard', 'dashboard')->name('Cdashboard');
        Route::get('/recordEvacuee', 'recordEvacuee')->name('CrecordEvacuee');
        Route::get('/disaster', 'disaster')->name('Cdisaster');
        Route::get('/eligtasGuidelines', 'eligtasGuidelines')->name('Cguidelines');
        Route::get('/eligtasGuidelines/guide/{guidelines_id}', 'eligtasGuide')->name('Cguide');
        Route::get('/barangay', 'barangay')->name('Cbarangay');
        Route::get('/evacuationManage', 'evacuationManage')->name('Cevacuationmanage');
        Route::get('/evacuationCenter', 'evacuationCenter')->name('Cevacuation');
        Route::get('/hotlineNumbers', 'hotlineNumbers')->name('CNumbers');
        Route::get('/statistics', 'statistics')->name('Cstatistics');
        Route::get('/reportAccident', 'reportAccident')->name('Creport');
        Route::get('/about', 'about')->name('Cabout');
        Route::get('/logout', 'logout')->name('Clogout');
    });
});



