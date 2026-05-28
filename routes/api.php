<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContentController;
use App\Http\Controllers\ExperienceController;
use App\Http\Controllers\ProjectController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public routes — no authentication required
|--------------------------------------------------------------------------
*/
Route::post('/auth/login', [AuthController::class, 'login']);

Route::get('/content', [ContentController::class, 'index']);
Route::get('/projects', [ProjectController::class, 'index']);
Route::get('/experiences', [ExperienceController::class, 'index']);

/*
|--------------------------------------------------------------------------
| Protected routes — requires Sanctum bearer token
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me',     [AuthController::class, 'me']);

    // Admin credentials
    Route::put('/admin/username', [AdminController::class, 'updateUsername']);
    Route::put('/admin/password', [AdminController::class, 'updatePassword']);

    // Content singletons
    Route::put('/content/hero',    [ContentController::class, 'updateHero']);
    Route::put('/content/about',   [ContentController::class, 'updateAbout']);
    Route::put('/content/contact', [ContentController::class, 'updateContact']);

    // Projects — reorder MUST come before /{id} to avoid routing conflicts
    Route::put('/projects/reorder',              [ProjectController::class, 'reorder']);
    Route::post('/projects',                     [ProjectController::class, 'store']);
    Route::match(['PUT', 'POST'], '/projects/{id}', [ProjectController::class, 'update']);
    Route::delete('/projects/{id}',              [ProjectController::class, 'destroy']);

    // Experiences — reorder MUST come before /{id}
    Route::put('/experiences/reorder',                              [ExperienceController::class, 'reorder']);
    Route::post('/experiences',                                     [ExperienceController::class, 'store']);
    Route::put('/experiences/{id}',                                 [ExperienceController::class, 'update']);
    Route::delete('/experiences/{id}',                              [ExperienceController::class, 'destroy']);
    Route::post('/experiences/{id}/companies',                      [ExperienceController::class, 'addCompany']);
    Route::put('/experiences/{roleId}/companies/{companyId}',       [ExperienceController::class, 'updateCompany']);
    Route::delete('/experiences/{roleId}/companies/{companyId}',    [ExperienceController::class, 'destroyCompany']);
});
