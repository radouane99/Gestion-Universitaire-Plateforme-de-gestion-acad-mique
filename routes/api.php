<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AcademicApiController;

Route::post('/login', [AcademicApiController::class, 'login'])->middleware('throttle:5,1');

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/modules', [AcademicApiController::class, 'modules']);
    Route::get('/grades', [AcademicApiController::class, 'grades']);
    Route::get('/schedule', [AcademicApiController::class, 'schedule']);
    Route::get('/absences', [AcademicApiController::class, 'absences']);
});
