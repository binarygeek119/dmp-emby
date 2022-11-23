<?php

use Newelement\DmpJellyfin\Http\Controllers\DmpJellyfinController;

Route::put('/dmp-jellyfin/settings', [DmpJellyfinController::class, 'updateSettings'])->name('dmp-jellyfin.settings');
