<?php

use App\Http\Controllers\Api\Tenant\TenantController;
use App\Http\Controllers\Api\User\AuthenticateUserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['as' => 'api.users', 'prefix' => 'users'], function () {
    Route::post('login', [AuthenticateUserController::class, 'auth'])
        ->name('login');

    Route::post('sign-up', [AuthenticateUserController::class, 'register'])
        ->name('sign-up');

    Route::group(['middleware' => ['auth:sanctum']], function () {
        Route::get('tenants', [TenantController::class, 'get'])
            ->name('tenants-list');

        Route::get('tenant-profile', [TenantController::class, 'show'])
            ->name('tenant-profile');

        Route::post('tenant-delete', [TenantController::class, 'delete'])
            ->name('tenant-delete');

        Route::post('tenants-import', [TenantController::class, 'importTenants'])
            ->name('tenants-import');

        Route::post('tenants-import/process/{batchId}', [TenantController::class, 'importProcess'])
            ->name('tenants-import');

        Route::get('tenants-export', [TenantController::class, 'exportTenants'])
            ->name('tenants-export');
    });
});
