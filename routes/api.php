<?php
use App\Http\Controllers\Api\ServerController;
use Illuminate\Support\Facades\Route;

Route::middleware(['api'])->prefix('server')->group(function () {

    Route::get('/stats', [ServerController::class, 'getSystemStats']);
    Route::get('/restart-queue/{queueName}', [ServerController::class, 'restartQueue']);

    Route::get('/nginx-sites', [ServerController::class, 'getNginxSites']);
    Route::get('/docker-containers', [ServerController::class, 'getDockerContainers']);
    Route::get('/docker-containers/stats/{containerId}', [ServerController::class, 'getDockerContainerStats']);
    Route::post('/container/{action}', [\App\Http\Controllers\Api\DockerController::class, 'actionToContainer']);
    Route::get('/docker-containers/logs/{containerId}', [\App\Http\Controllers\Api\DockerController::class, 'getDockerLogs']);

    Route::get('/uptime', [ServerController::class, 'getSystemUptime']);

    Route::get('/load', [ServerController::class, 'getSystemLoad']);

    Route::get('/nginx-status', [ServerController::class, 'getNginxStatus']);

    Route::post('/clear-cache', [ServerController::class, 'clearCache']);
});
