<?php

use Newelement\DmpEmby\Http\Controllers\DmpEmbyController;

// These are /api/* routes
Route::get('/dmp-emby-settings', [DmpEmbyController::class, 'getSettings']);
Route::get('/dmp-emby-now-playing', [DmpEmbyController::class, 'getNowPlaying']);

Route::get('/dmp-emby-install', [DmpEmbyController::class, 'install']);
Route::get('/dmp-emby-update', [DmpEmbyController::class, 'update']);
