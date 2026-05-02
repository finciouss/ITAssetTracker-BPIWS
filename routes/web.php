<?php

use App\Http\Controllers\AssetController;
use App\Http\Controllers\AllocationController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuditController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Support\Facades\Route;

// ── Health check — no auth, used by Railway uptime monitoring ───────────────
Route::get('/health', function () {
    try {
        \DB::connection()->getPdo();
        return response()->json([
            'status'    => 'ok',
            'app'       => config('app.name'),
            'env'       => config('app.env'),
            'db'        => 'connected',
            'timestamp' => now()->toIso8601String(),
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'db'     => 'unreachable',
            'error'  => $e->getMessage(),
        ], 503);
    }
})->name('health');

// ── Auth ─────────────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',  [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    // /register is disabled — accounts are created by Admin via /users/create
});


Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// ── Authenticated ────────────────────────────────────────────────────────────
Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Assets
    Route::get('/assets',               [AssetController::class, 'index'])->name('assets.index');
    Route::get('/assets/export',        [AssetController::class, 'exportPdf'])->name('assets.export');
    Route::get('/assets/export-excel',  [AssetController::class, 'exportExcel'])->name('assets.export_excel');
    Route::get('/assets/{asset}/detail',[AssetController::class, 'show'])->name('assets.show');

    // Assets CRUD (Admin + Staff)
    Route::middleware('role:Admin|Staff')->group(function () {
        Route::get('/assets/create',                   [AssetController::class, 'create'])->name('assets.create');
        Route::post('/assets',                         [AssetController::class, 'store'])->name('assets.store');
        Route::get('/assets/{asset}/edit',             [AssetController::class, 'edit'])->name('assets.edit');
        Route::post('/assets/{asset}',                 [AssetController::class, 'update'])->name('assets.update');
        Route::get('/assets/{asset}/delete',           [AssetController::class, 'delete'])->name('assets.delete');
        Route::post('/assets/{asset}/delete',          [AssetController::class, 'destroy'])->name('assets.destroy');
        Route::post('/assets/{asset}/maintenance',     [AssetController::class, 'updateMaintenance'])->name('assets.maintenance');
    });

    // Allocations (Admin + Staff)
    Route::middleware('role:Admin|Staff')->prefix('allocations')->group(function () {
        Route::get('/',                         [AllocationController::class, 'index'])->name('allocations.index');
        Route::get('/export',                   [AllocationController::class, 'exportPdf'])->name('allocations.export');
        Route::get('/export-excel',             [AllocationController::class, 'exportExcel'])->name('allocations.export_excel');
        Route::get('/create',                   [AllocationController::class, 'create'])->name('allocations.create');
        Route::post('/',                        [AllocationController::class, 'store'])->name('allocations.store');
        
        Route::get('/{allocation}/return',      [AllocationController::class, 'return'])->name('allocations.return');
        Route::post('/{allocation}/return',     [AllocationController::class, 'processReturn'])->name('allocations.processReturn');
        
        Route::get('/{allocation}/transfer',    [AllocationController::class, 'transfer'])->name('allocations.transfer');
        Route::post('/{allocation}/transfer',   [AllocationController::class, 'processTransfer'])->name('allocations.processTransfer');
    });

    // Employees (Admin + Staff)
    Route::middleware('role:Admin|Staff')->prefix('employees')->group(function () {
        Route::get('/',                     [EmployeeController::class, 'index'])->name('employees.index');
        Route::get('/create',               [EmployeeController::class, 'create'])->name('employees.create');
        Route::post('/',                    [EmployeeController::class, 'store'])->name('employees.store');
        Route::get('/{employee}/edit',      [EmployeeController::class, 'edit'])->name('employees.edit');
        Route::post('/{employee}',          [EmployeeController::class, 'update'])->name('employees.update');
    });

    // Projects (Admin + Staff)
    Route::middleware('role:Admin|Staff')->prefix('projects')->group(function () {
        Route::get('/',                     [ProjectController::class, 'index'])->name('projects.index');
        Route::get('/create',               [ProjectController::class, 'create'])->name('projects.create');
        Route::post('/',                    [ProjectController::class, 'store'])->name('projects.store');
        Route::get('/{project}',            [ProjectController::class, 'show'])->name('projects.show');
        Route::get('/{project}/edit',       [ProjectController::class, 'edit'])->name('projects.edit');
        Route::post('/{project}',           [ProjectController::class, 'update'])->name('projects.update');
        Route::get('/{project}/delete',     [ProjectController::class, 'delete'])->name('projects.delete');
        Route::post('/{project}/delete',    [ProjectController::class, 'destroy'])->name('projects.destroy');
        Route::get('/{project}/export',     [ProjectController::class, 'exportPdf'])->name('projects.export');
        Route::get('/{project}/export-excel',[ProjectController::class, 'exportExcel'])->name('projects.export_excel');
    });

    // User Management (Admin only)
    Route::middleware('role:Admin')->prefix('users')->group(function () {
        Route::get('/',                         [UserController::class, 'index'])->name('users.index');
        Route::get('/create',                   [UserController::class, 'create'])->name('users.create');
        Route::post('/',                        [UserController::class, 'store'])->name('users.store');
        Route::get('/{user}/change-password',   [UserController::class, 'changePassword'])->name('users.changePassword');
        Route::post('/{user}/change-password',  [UserController::class, 'updatePassword'])->name('users.updatePassword');
        Route::get('/{user}/delete',            [UserController::class, 'delete'])->name('users.delete');
        Route::post('/{user}/delete',           [UserController::class, 'destroy'])->name('users.destroy');
    });

    // Global Audit (Admin only)
    Route::middleware('role:Admin')->prefix('audit')->group(function () {
        Route::get('/',                         [AuditController::class, 'index'])->name('system.audit');
    });

    // Reports (Admin only)
    Route::middleware('role:Admin')->prefix('reports')->group(function () {
        Route::get('/monthly',                  [App\Http\Controllers\ReportController::class, 'monthly'])->name('reports.monthly');
        Route::get('/monthly/export-excel',     [App\Http\Controllers\ReportController::class, 'exportMonthlyExcel'])->name('reports.monthly.export_excel');
    });
});
