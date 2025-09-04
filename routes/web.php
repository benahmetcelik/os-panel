<?php


use App\Http\Controllers\Api\DockerController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware(['web'])->group(function () {

    Route::get('/dashboard', function () {
        return view('dashboard.index');
    })->name('dashboard');


    Route::resource('sites',\App\Http\Controllers\SiteController::class);

    Route::get('/logs', function () {
        return view('dashboard.logs');
    })->name('logs.index');

});

Route::get('/containers/logs/{containerId}', [DockerController::class, 'logs'])->name('containers.logs');


Route::get('/docker/stats/{id}', [DockerController::class, 'stats'])->name('docker.stats');
Route::get('/docker/terminal/{containerId}', [DockerController::class, 'terminal'])->name('docker.terminal');
Route::get('/docker/stats-async/{id}', [DockerController::class, 'statsAsync']);

Route::post('/docker/terminal-input/{id}', [DockerController::class, 'terminalInput']);
Route::get('/docker/terminal-stream/{id}', [DockerController::class, 'terminalStream']);





