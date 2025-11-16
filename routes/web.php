<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Manual\TestController;
use App\Http\Controllers\Admin\NewsController;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::get('/test', [TestController::class, 'index'])->name('test.index');

Route::prefix('admin')->name('admin.')->group(function () {

    // 🔹 Authentication
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // 🔒 Protected admin area
    Route::middleware('auth:admin')->group(function () {

        // 🏠 Dashboard
        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        })->name('dashboard');

        // 🛡️ Only for super_admins
        Route::middleware('can:manage-blog')->group(function () {
            Route::resource('roles', RoleController::class);
            Route::resource('admins', AdminController::class);
        });

        // 👥 Users (for super_admin + editor)
        Route::middleware('can:manage-pages')->group(function () {
            Route::get('/users', fn() => view('admin.users.index'))->name('users.index');
        });

        Route::middleware('can:manage-pages')->group(function () {
            Route::get('/api-types', fn() => view('admin.api_types.index'))->name('api-types.index');
        });

        Route::middleware('can:manage-pages')->group(function () {
            Route::get('/continents', fn() => view('admin.continents.index'))->name('continents.index');
        });


        Route::middleware('can:manage-pages')->group(function () {
            Route::view('/countries', 'admin.countries.index')->name('countries');
            Route::view('/leagues', 'admin.leagues.index')->name('leagues');
            Route::view('/seasons', 'admin.seasons.index')->name('seasons');
            Route::view('/venues', 'admin.venues.index')->name('venues');
            Route::view('/tv-stations', 'admin.tv-stations.index')->name('tv-stations');
            Route::view('/teams', 'admin.teams.index')->name('teams');
            Route::view('/season-teams', 'admin.season-teams.index')->name('season-teams');
            Route::view('/players', 'admin.players.index')->name('players');
            Route::view('/boxes-types', 'admin.boxes-types.index')->name('boxes-types');
            Route::view('/card-types', 'admin.card-types.index')->name('card-types');
            Route::view('/players-cards', 'admin.players-cards.index')->name('players-cards');

            Route::get('/news', fn() => view('admin.news.index'))->name('news.index');
            Route::get('/news/create', [NewsController::class, 'create'])->name('news.create');
            Route::post('/news', [NewsController::class, 'store'])->name('news.store');
            Route::get('/news/{news}/edit', [NewsController::class, 'edit'])->name('news.edit');
            Route::put('/news/{news}', [NewsController::class, 'update'])->name('news.update');

            Route::view('/weekmonths', 'admin.weekmonths.index')->name('admin.weekmonths');
            Route::view('/prediction-matches', 'admin.predictionmatches.index')->name('admin.predictionmatches');
            Route::view('/cards-weeks', 'admin.cardsweeks.index')->name('admin.cardsweeks');
            Route::view('/positions', 'admin.positions.index')->name('admin.positions');
            Route::view('/user-cards', 'admin.usercards.index')->name('admin.usercards');
            Route::view('/odds-bookmakers', 'admin.odds-bookmakers.index')->name('admin.odds-bookmakers');
            Route::view('/odds-markets', 'admin.odds-markets.index')->name('admin.odds-markets');
            Route::view('/users-rankings', 'admin.users-rankings.index')->name('admin.users-rankings');
            Route::view('/app-news', 'admin.app-news.index')->name('admin.app-news');

















        });


    });
});
Route::get('/login', fn() => redirect()->route('admin.login'))->name('login');
