<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Minhducck\KeyValueDataStorage\Controllers\KeyValueController;

$getRoutes = [
    Route::get('/object/get_all_records', [KeyValueController::class, 'getAll']),
    Route::get('/object/{key}', [KeyValueController::class, 'retrieveByKey']),
];
$writeRoutes = [Route::post('/object', [KeyValueController::class, 'store'])];

if (env('KEY_VALUE_STORAGE.RESTRICT_READ_PERMISSION', 0) == 1) {
    array_walk($getRoutes, function ($route) {
        $route->middleware('auth');
    });
}

if (env('KEY_VALUE_STORAGE.RESTRICT_WRITE_PERMISSION', 0) == 1) {
    array_walk($writeRoutes, function ($route) {
        $route->middleware('auth');
    });
}
