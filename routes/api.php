<?php

use Newelement\DmpJellyfin\Http\Controllers\DmpJellyfinController;

// These are /api/* routes
Route::get('/dmp-jellyfin-settings', [DmpJellyfinController::class, 'getSettings']);
Route::get('/dmp-jellyfin-now-playing', [DmpJellyfinController::class, 'getNowPlaying']);

Route::get('/dmp-jellyfin-install', [DmpJellyfinController::class, 'install']);
Route::get('/dmp-jellyfin-update', [DmpJellyfinController::class, 'update']);
