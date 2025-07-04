<?php

use App\Http\Controllers\PartidoController;
use App\Http\Controllers\DashboardController;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\TeamsController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});



Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    
    Route::get('no_team', function (Request $request) {
        return Inertia::render('NoTeam');
    })->name('no-team');

    //Route::get('/partidos',[PartidoController::class,'index'])->name('partidos');

    Route::get('/partido/new',[PartidoController::class,'create'])->name('partido.create');
    Route::post('/partido',[PartidoController::class,'store'])->name('partido.store');
    Route::get('/partido/{partido}',[PartidoController::class,'show'])->name('partido.show');
    Route::put('/partidos/{partido}',[PartidoController::class,'update'])->name('partido.update');
    Route::delete('/partido/{partido}', [PartidoController::class, 'destroy'])->name('partido.destroy');

    Route::get('/partidos/list', [PartidoController::class, 'list'])->name('partidos.list');

Route::post('/partido/{partido}/disponibilidad', [PartidoController::class, 'marcarDisponibilidad'])->name('partido.disponibilidad');
Route::post('/partido/{partido}/completar', [PartidoController::class, 'completar'])->name('partido.cerrar');
Route::post('/partido/{partido}/convocar', [PartidoController::class, 'convocar'])->name('partido.convocar');
Route::get('/partido/{partido}/disponibles-con-estadisticas', [PartidoController::class, 'disponiblesConEstadisticas'])->name('partido.disponibles-estadisticas');

Route::post('/teams/{team}/add-users-excel', [TeamsController::class, 'addUsers'])->name('teams.add-users-excel');
Route::get('/teams/{team}/putoexcel', [TeamsController::class, 'addUsers']);
Route::get('/estadisticas', [PartidoController::class, 'estadisticas'])->name('estadisticas');
});
