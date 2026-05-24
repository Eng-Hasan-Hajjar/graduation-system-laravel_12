<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectIdeaController;
use App\Http\Controllers\CommitteeController;
use App\Http\Controllers\DefenseScheduleController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\LoginController;

// ─── Auth Routes (بدون الاعتماد على laravel/ui) ──────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',  [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

Route::post('/logout', [LoginController::class, 'logout'])
     ->middleware('auth')
     ->name('logout');

// ─── Language Switch ──────────────────────────────────────────────────────────
Route::get('/lang/{locale}', function (string $locale) {
    abort_unless(in_array($locale, ['ar', 'en']), 400);
    session(['locale' => $locale]);
    if (auth()->check()) {
        auth()->user()->update(['lang_preference' => $locale]);
    }
    return back();
})->name('lang.switch');

// ─── Authenticated Routes ─────────────────────────────────────────────────────
Route::middleware(['auth', 'set.locale'])->group(function () {

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Projects
    Route::prefix('projects')->name('projects.')->group(function () {
        Route::get('/',         [ProjectController::class, 'index'])->name('index');
        Route::get('/create',   [ProjectController::class, 'create'])->name('create');
        Route::post('/',        [ProjectController::class, 'store'])->name('store');
        Route::get('/archived', [ProjectController::class, 'archived'])->name('archived');
        Route::get('/{project}',      [ProjectController::class, 'show'])->name('show');
        Route::get('/{project}/edit', [ProjectController::class, 'edit'])->name('edit');
        Route::put('/{project}',      [ProjectController::class, 'update'])->name('update');
        Route::delete('/{project}',   [ProjectController::class, 'destroy'])->name('destroy');
        Route::patch('/{project}/approve',        [ProjectController::class, 'approve'])->name('approve');
        Route::patch('/{project}/reject',         [ProjectController::class, 'reject'])->name('reject');
        Route::patch('/{project}/mark-discussed', [ProjectController::class, 'markDiscussed'])->name('mark-discussed');
        Route::patch('/{project}/archive',        [ProjectController::class, 'archive'])->name('archive');
    });

    // Ideas
    Route::prefix('ideas')->name('ideas.')->group(function () {
        Route::get('/',             [ProjectIdeaController::class, 'index'])->name('index');
        Route::get('/create',       [ProjectIdeaController::class, 'create'])->name('create');
        Route::post('/',            [ProjectIdeaController::class, 'store'])->name('store');
        Route::get('/{idea}',       [ProjectIdeaController::class, 'show'])->name('show');
        Route::get('/{idea}/edit',  [ProjectIdeaController::class, 'edit'])->name('edit');
        Route::put('/{idea}',       [ProjectIdeaController::class, 'update'])->name('update');
        Route::delete('/{idea}',    [ProjectIdeaController::class, 'destroy'])->name('destroy');
        Route::patch('/{idea}/approve', [ProjectIdeaController::class, 'approve'])->name('approve');
        Route::patch('/{idea}/reject',  [ProjectIdeaController::class, 'reject'])->name('reject');
    });

    // Committees
    Route::prefix('committees')->name('committees.')->group(function () {
        Route::get('/',                       [CommitteeController::class, 'index'])->name('index');
        Route::get('/create',                 [CommitteeController::class, 'create'])->name('create');
        Route::post('/',                      [CommitteeController::class, 'store'])->name('store');
        Route::get('/{committee}',            [CommitteeController::class, 'show'])->name('show');
        Route::patch('/{committee}/complete', [CommitteeController::class, 'markCompleted'])->name('complete');
        Route::delete('/{committee}',         [CommitteeController::class, 'destroy'])->name('destroy');
    });

    // Schedules
    Route::prefix('schedules')->name('schedules.')->group(function () {
        Route::get('/',                      [DefenseScheduleController::class, 'index'])->name('index');
        Route::get('/create',                [DefenseScheduleController::class, 'create'])->name('create');
        Route::post('/',                     [DefenseScheduleController::class, 'store'])->name('store');
        Route::get('/{schedule}',            [DefenseScheduleController::class, 'show'])->name('show');
        Route::patch('/{schedule}/postpone', [DefenseScheduleController::class, 'postpone'])->name('postpone');
        Route::patch('/{schedule}/cancel',   [DefenseScheduleController::class, 'cancel'])->name('cancel');
        Route::patch('/{schedule}/complete', [DefenseScheduleController::class, 'markCompleted'])->name('complete');
    });

    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/',                  [ReportController::class, 'index'])->name('index');
        Route::get('/create',            [ReportController::class, 'create'])->name('create');
        Route::post('/',                 [ReportController::class, 'store'])->name('store');
        Route::get('/{report}',          [ReportController::class, 'show'])->name('show');
        Route::patch('/{report}/review', [ReportController::class, 'review'])->name('review');
    });

    // Users
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/',            [UserController::class, 'index'])->name('index');
        Route::get('/create',      [UserController::class, 'create'])->name('create');
        Route::post('/',           [UserController::class, 'store'])->name('store');
        Route::get('/{user}',      [UserController::class, 'show'])->name('show');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
        Route::put('/{user}',      [UserController::class, 'update'])->name('update');
        Route::delete('/{user}',   [UserController::class, 'destroy'])->name('destroy');
    });

    // Profile
    Route::get('/profile',           [UserController::class, 'profile'])->name('profile');
    Route::post('/profile',          [UserController::class, 'updateProfile'])->name('profile.update');
    Route::post('/profile/password', [UserController::class, 'changePassword'])->name('profile.password');
    Route::post('/user/preferences', [UserController::class, 'updatePreferences'])->name('user.preferences');

    // Admin Only
    Route::middleware('role:admin,coordinator')->group(function () {
        Route::resource('academic-years', \App\Http\Controllers\AcademicYearController::class);
        Route::resource('departments',    \App\Http\Controllers\DepartmentController::class);
        Route::resource('settings',       \App\Http\Controllers\SettingController::class)->only(['index','update']);
    });
});