<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\KaryawanController;
use App\Http\Controllers\API\CutiApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// API for searching karyawan by nik
Route::get('/karyawans/search', [KaryawanController::class, 'search']);

// API for getting leave analysis data
Route::get('/karyawans/{id}/leave-analysis', [KaryawanController::class, 'getLeaveAnalysis']);

// API for validating leave period
Route::get('/cuti/validate-period', [CutiApiController::class, 'validatePeriod']);

// Default authentication route
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});