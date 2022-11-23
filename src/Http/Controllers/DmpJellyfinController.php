<?php

namespace Newelement\DmpJellyfin\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Newelement\DmpJellyfin\Services\JellyfinMediaSyncService;

class DmpJellyfinController extends Controller
{
	public function install(JellyfinMediaSyncService $service)
	{
		return $service->install();
	}

	public function update()
	{
		//
	}

	public function getSettings(JellyfinMediaSyncService $service)
	{
		return $service->getSettings();
	}

	public function updateSettings(Request $request, JellyfinMediaSyncService $service)
	{
		$service->updateSettings($request);
		return redirect()->back()->with('success', 'Jellyfin settings updated');
	}

	public function getNowPlaying(JellyfinMediaSyncService $service)
	{
		return $service->nowPlaying();
	}
}
