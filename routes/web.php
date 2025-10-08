<?php

use App\Http\Controllers;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\Settings\TwoFactor;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

//Route::get('/', function () {
//    return view('welcome');
//})->name('home');

Route::get('/', function () {
    return Auth::check()
        ? redirect()->route('reports.summary')
        : view('welcome');
})->name('home');

//Route::view('dashboard', 'dashboard')
//    ->middleware(['auth', 'verified'])
//    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::get('test', [Controllers\TestController::class, 'execute'])->name('test');

    Route::redirect('settings', 'settings/profile');
    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');
    Route::get('settings/two-factor', TwoFactor::class)
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');

    Route::get('/reports/summary', [Controllers\ReportController::class, 'summary'])->name('reports.summary');
    Route::get('/reports/detail/{id}', [Controllers\ReportController::class, 'detail'])->name('reports.detail');
    Route::get('/reports/export', [Controllers\ReportController::class, 'export'])->name('reports.export');
//    Route::post('/upload/excel', [Controllers\UploadController::class, 'uploadExcel'])->name('upload.excel');

    Route::get('employees', [Controllers\EmployeeController::class, 'index'])->name('employees.index');

    Route::resource('departments', Controllers\DepartmentController::class)->only([
        'index', 'store', 'destroy', 'update'
    ]);


});

require __DIR__ . '/auth.php';
