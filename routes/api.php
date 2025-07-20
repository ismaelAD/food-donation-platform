<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\FoodWasteReportController;
use App\Http\Controllers\UserPreferenceController;
use App\Http\Controllers\DistributionController;
use App\Http\Controllers\PickupController;
use App\Http\Controllers\VolunteerController;
use App\Http\Controllers\PartnerController;
// 🔐 Auth public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);


// 🔒 Routes protégées par Sanctum
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user',     function (Request $request) {
        return $request->user();
    });

    Route::post('/logout',  [AuthController::class, 'logout']);
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/donations',   [DonationController::class, 'index']);
    Route::post('/donations',  [DonationController::class, 'store']);
    Route::patch('/donations/{donation}/quantity', [DonationController::class, 'updateQuantity']);
    Route::delete('/donations/{donation}',          [DonationController::class, 'destroy']);
    Route::get('/food-waste-reports', [FoodWasteReportController::class, 'index']); // Récupérer tous les rapports
    Route::post('/food-waste-reports', [FoodWasteReportController::class, 'store']); // Soumettre un rapport
    Route::get('/donations/search', [DonationController::class, 'search']);
    Route::get('/preferences', [UserPreferenceController::class, 'index']);
    Route::post('/preferences', [UserPreferenceController::class, 'store']);
    Route::patch('/preferences/{id}', [UserPreferenceController::class, 'update']);
    Route::delete('/preferences/{id}', [UserPreferenceController::class, 'destroy']);
    Route::apiResource('distributions', DistributionController::class);
    Route::apiResource('pickups', PickupController::class);
    Route::apiResource('volunteers', VolunteerController::class);
    Route::apiResource('partners', PartnerController::class);
});
