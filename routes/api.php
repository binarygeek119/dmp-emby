<?php

use Newelement\DmpJellyfin\Http\Controllers\DmpJellyfinController;

// These are /api/* routes
Route::get('/dmp-jelllyfin-settings', [DmpJellyfinController::class, 'getSettings']);
Route::get('/dmp-jelllyfin-now-playing', [DmpJellyfinController::class, 'getNowPlaying']);

Route::get('/dmp-jelllyfin-install', [DmpJellyfinController::class, 'install']);
Route::get('/dmp-jelllyfin-update', [DmpJellyfinController::class, 'update']);
