<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DepartmentController;
use App\Http\Controllers\Api\HrdController;
use App\Http\Controllers\Api\TicketController;
use App\Http\Middleware\AuthenticateApiToken;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Mobile Applications & External Client API Routes
|--------------------------------------------------------------------------
|
| These endpoints serve both the Flutter Employee App and Flutter HRD App,
| operating against the shared corporate database.
|
*/

// 1. Public Authentication & Metadata Endpoints
Route::prefix('auth')->group(function () {
    Route::post('/login-employee', [AuthController::class, 'loginEmployee']);
    Route::post('/login-hrd', [AuthController::class, 'loginHrd']);
    Route::post('/register', [AuthController::class, 'register']);
});

Route::get('/departments', [DepartmentController::class, 'index']);
Route::get('/categories', [DepartmentController::class, 'categories']);

// 2. Shared Authenticated Routes
Route::middleware([AuthenticateApiToken::class])->group(function () {
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/tickets/{id}', [TicketController::class, 'show']);
    Route::post('/tickets/{id}/messages', [TicketController::class, 'addMessage']);
});

// 3. Employee Specific Mobile API Routes (Guarded: Non-HRD)
Route::middleware([AuthenticateApiToken::class.':employee'])->prefix('employee')->group(function () {
    Route::get('/tickets', [TicketController::class, 'employeeTickets']);
    Route::post('/tickets', [TicketController::class, 'store']);
    Route::get('/kpi', [TicketController::class, 'employeeKpi']);
});

// 4. HRD & Admin Specific Mobile API Routes (Guarded: HRD/Admin only)
Route::middleware([AuthenticateApiToken::class.':hrd'])->prefix('hrd')->group(function () {
    Route::get('/metrics', [HrdController::class, 'metrics']);
    Route::get('/daily-chart', [HrdController::class, 'dailyChart']);
    Route::get('/tickets', [HrdController::class, 'tickets']);
    Route::patch('/tickets/{id}/status', [HrdController::class, 'updateTicketStatus']);
    Route::get('/employees', [HrdController::class, 'employees']);
    Route::post('/employees/{id}/toggle-approval', [HrdController::class, 'toggleEmployeeApproval']);
});
