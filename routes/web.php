<?php

use Newelement\DmpJellyfin\Http\Controllers\DmpJellyfinController;

Route::put('/dmp-jelllyfin/settings', [DmpJellyfinController::class, 'updateSettings'])->name('dmp-jelllyfin.settings');
