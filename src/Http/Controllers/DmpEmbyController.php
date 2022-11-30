<?php

namespace Newelement\DmpEmby\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Newelement\DmpEmby\Services\EmbyMediaSyncService;

class DmpEmbyController extends Controller
{
	public function install(EmbyMediaSyncService $service)
	{
		return $service->install();
	}

	public function update()
	{
		//
	}

	public function getSettings(EmbyMediaSyncService $service)
	{
		return $service->getSettings();
	}

	public function updateSettings(Request $request, EmbyMediaSyncService $service)
	{
		$service->updateSettings($request);
		return redirect()->back()->with('success', 'Emby settings updated');
	}

	public function getNowPlaying(EmbyMediaSyncService $service)
	{
		return $service->nowPlaying();
	}
}
