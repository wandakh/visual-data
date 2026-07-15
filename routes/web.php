<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\Auth\SessionController;
use App\Http\Controllers\DatabaseController;
use App\Http\Controllers\DiagramController;
use App\Http\Controllers\ExcelController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OvertimeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TambahDataController;
use App\Http\Controllers\UpdateController;
use App\Http\Controllers\UserActivityLogController;
use Illuminate\Support\Facades\Route;

Route::middleware(['isLogin'])->group(function () {
    // Route ini WAJIB tetap bisa diakses walau lagi di luar jam kerja,
    // supaya user gak "terkunci total" tanpa jalan keluar.
    Route::get('/lembur', [OvertimeController::class, 'blocked'])->name('overtime.blocked');
    Route::post('/lembur/minta', [OvertimeController::class, 'store'])->name('overtime.store');

    Route::middleware(['jamAkses'])->group(function () {
        Route::get('/', [HomeController::class, 'index']);

        Route::get('/database', [DatabaseController::class, 'index'])->name('database');
        Route::get('/database/trash', [DatabaseController::class, 'trash'])->name('database.trash')->middleware('can:delete-data');
        Route::post('/database/{id}/restore', [DatabaseController::class, 'restore'])->name('database.restore')->middleware('can:delete-data');
        Route::get('/database/{id}', [DatabaseController::class, 'show'])->name('show');

        Route::post('/delete/{id}', [DatabaseController::class, 'delete'])->name('delete')->middleware('can:delete-data');

        Route::get('/sales-record-options', [TambahDataController::class, 'optionsForCustomer'])->name('sales-record-options');

        Route::middleware('can:create-data')->group(function () {
            Route::get('/tambahdata', [TambahDataController::class, 'tampilkanCreateForm'])->name('tambahdata');
            Route::post('/insertdata', [TambahDataController::class, 'insertdata'])->name('insertdata');
        });

        Route::middleware('can:edit-data')->group(function () {
            Route::get('/tampilkandata/{id}', [UpdateController::class, 'tampilkandata'])->name('tampilkandata');
            Route::post('/updatedata/{id}', [UpdateController::class, 'updatedata'])->name('updatedata');
        });

        Route::get('/database-export', [ExcelController::class, 'export'])->name('database.export')->middleware('can:export-data');
        Route::post('/database-import', [ExcelController::class, 'import'])->name('database.import')->middleware('can:import-excel');

        Route::get('/activity-log', [ActivityLogController::class, 'index'])->name('activity-log')->middleware('can:delete-data');
        Route::get('/user-activity-log', [UserActivityLogController::class, 'index'])->name('user-activity-log')->middleware('can:delete-data');

        Route::get('/diagram', [DiagramController::class, 'diagram'])->name('diagram');

        Route::get('/profile', [ProfileController::class, 'profile'])->name('profile');
        Route::post('/update/profile', [ProfileController::class, 'update_profile'])->name('update_profile');
    });
});

Route::middleware(['isGuest'])->group(function () {
    Route::get('/sesi', [SessionController::class, 'index']);
    Route::post('/sesi/login', [SessionController::class, 'login'])->name('sesi.login')->middleware('throttle:5,1');
    Route::get('/sesi/register', [SessionController::class, 'register'])->name('sesi.register');
    Route::post('/sesi/create', [SessionController::class, 'create'])->name('sesi.create')->middleware('throttle:5,1');
});

Route::get('/sesi/logout', [SessionController::class, 'logout'])->name('sesi.logout');
