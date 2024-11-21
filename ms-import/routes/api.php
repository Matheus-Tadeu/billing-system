<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ImportController;

Route::prefix('import')->group(function () {
    Route::post('/', ImportController::class);
});
