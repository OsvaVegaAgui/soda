<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardsController;
use App\Http\Controllers\RouterController;
use App\Http\Controllers\RicardoController;
use App\Http\Controllers\ThayronController;
use App\Http\Controllers\MarcosController;
use App\Http\Controllers\OsvaldoController;
use App\Http\Controllers\LuisController;
use App\Http\Controllers\MenuAdminController;
use App\Http\Controllers\MiltonController;
use App\Http\Controllers\KeylorController;
use App\Http\Controllers\CajaController;

// ── Rutas públicas (sin autenticación) ───────────────────────────────────────
Route::prefix('usuarios')->controller(KeylorController::class)->group(function () {
    Route::match(['GET','POST'], '/{accion}/{id?}', 'resolver')->name('usuarios');
});

// Redirigir la raíz al login si no está autenticado, al index si lo está
Route::get('/', function () {
    return auth()->check() ? redirect('index') : redirect()->route('usuarios', ['accion' => 'login']);
});

// ── Rutas protegidas (requieren login) ────────────────────────────────────────
Route::middleware('auth')->group(function () {

    Route::get('index', [DashboardsController::class, 'index']);

    Route::prefix('paises')->controller(RouterController::class)->group(function () {
        Route::match(['GET','POST'], '/{accion}/{id?}', 'resolver')->name('paises.procesar');
    });

    Route::prefix('productos-cocina')->controller(RicardoController::class)->group(function () {
        Route::match(['GET','POST'], '/{accion}/{id?}', 'resolver')->name('productos-cocina');
    });

    Route::prefix('productos-soda')->controller(ThayronController::class)->group(function () {
        Route::match(['GET','POST'], '/{accion}/{id?}', 'resolver')->name('productos-soda');
    });

    Route::prefix('ticketes')->controller(MarcosController::class)->group(function () {
        Route::match(['GET','POST'], '/{accion}/{id?}', 'resolver')->name('ticketes-soda');
    });

    Route::prefix('generar-ticketes')->controller(OsvaldoController::class)->group(function () {
        Route::match(['GET','POST'], '/{accion}/{id?}', 'resolver')->name('generar-ticketes');
    });

    Route::prefix('menu-site')->controller(LuisController::class)->group(function () {
        Route::match(['GET','POST'], '/{accion}/{id?}', 'resolver')->name('menu-site');
    });

    Route::prefix('menu-admin')->controller(MenuAdminController::class)->group(function () {
        Route::match(['GET','POST'], '/{accion}/{id?}', 'resolver')->name('menu_admin');
    });

    Route::prefix('ventas')->controller(MiltonController::class)->group(function () {
        Route::match(['GET','POST'], '/{accion}/{id?}', 'resolver')->name('ventas');
    });

    Route::prefix('caja')->controller(CajaController::class)->group(function () {
        Route::match(['GET','POST'], '/{accion}/{id?}', 'resolver')->name('caja');
    });
});
