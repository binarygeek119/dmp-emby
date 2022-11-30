<?php

use Newelement\DmpEmby\Http\Controllers\DmpEmbyController;

Route::put('/dmp-emby/settings', [DmpEmbyController::class, 'updateSettings'])->name('dmp-emby.settings');
