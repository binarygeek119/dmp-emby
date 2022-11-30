<?php

namespace Newelement\DmpEmby\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use App\Interfaces\MediaSyncInterface;
use App\Traits\PosterProcess;
use Illuminate\Support\Facades\Artisan;
use Plugin;

class EmbyMediaSyncService implements MediaSyncInterface
{
	use PosterProcess;

	public $embySettings = [];

	public function __construct()
	{
		$this->setSettings();
		$this->setEmbySettings();
	}

	public function setSettings()
	{
		$this->settings = Setting::first();
	}

	/**
	 * Install the plugin
	 *
	 * @return array
	 */
	public function install(): array
	{
		Artisan::call('vendor:publish', ['--provider' => 'Newelement\DmpEmby\DmpEmbyServiceProvider', '--force' => true]);

		$plugin = [
			'type' => 'media_source',
			'plugin_key' => 'dmp-emby',
			'name' => 'Emby Now Playing',
			'description' => 'Shows now playing.',
			'url' => 'https://github.com/newelement/dmp-emby',
			'repo' => 'newelement/dmp-emby',
			'version' => '1.0.0',
		];

		Plugin::install($plugin);

		$options = [
			[
				'type' => 'string',
				'value' => '',
				'field_name' => 'emby_ip_address',
				'plugin_key' => 'dmp-emby',
			],
			[
				'type' => 'string',
				'value' => '',
				'field_name' => 'emby_token',
				'plugin_key' => 'dmp-emby',
			],
			[
				'type' => 'boolean',
				'value' => false,
				'field_name' => 'emby_use_ssl',
				'plugin_key' => 'dmp-emby',
			],
		];

		Plugin::addOptions($options);

		return ['success' => true];
	}

	public function update()
	{
		//
	}

	public function setEmbySettings()
	{
		// Can also call Plugin::getOptions('dmp-emby') to get full options array
		$this->embySettings['emby_ip_address'] = Plugin::getOptionValue('emby_ip_address');
		$this->embySettings['emby_token'] = Plugin::getOptionValue('emby_token');
		$this->embySettings['emby_use_ssl'] = Plugin::getOptionValue('emby_use_ssl');
	}

	public function getSettings()
	{
		return $this->embySettings;
	}

	public function updateSettings($request)
	{
		Plugin::updateOption('emby_ip_address', $request->emby_ip_address);
		Plugin::updateOption('emby_token', $request->emby_token);
		Plugin::updateOption('emby_use_ssl', $request->emby_use_ssl);
	}

	/**
	 * Make Emby API calls to media server
	 *
	 * @param string $path /path/resource
	 * @param string $method get|post
	 * @param array $params
	 *
	 * @return json
	 */
	public function apiCall($path, $method = 'GET', $params = [])
	{
		$protocol = $this->embySettings['emby_use_ssl'] ? 'https' : 'http';
		$response = Http::withHeaders([
			'Accept' => 'application/json',
		])->get($protocol.'://'.$this->embySettings['emby_ip_address'].':8096'.$path.'?api_key='.$this->embySettings['emby_token']);

		return $response->json();
	}

	public function syncMedia()
	{
		$json = $this->apiCall('/Items');
		$movies = $json['Items'];
		$this->processMovies($movies);
	}

	public function processMovies($movies)
	{
		$protocol = $this->embySettings['emby_use_ssl'] ? 'https' : 'http';

		foreach ($movies as $movie) {
			if ($movie['Type'] === 'Movie') {
				$imageUrl = $protocol.'://'.$this->embySettings['emby_ip_address'].':8096/emby/Items/'.$movie['Id'].'/Images/Primary';

				$savedImage = $this->saveImage($movie['Name'], $imageUrl);

				$params = [
					'name' => $movie['Name'],
					'file_name' => $savedImage['file_name'],
					'id' => $movie['Id'],
					'mpaa_rating' => isset($movie['OfficialRating']) ? $movie['OfficialRating'] : null,
					'audience_rating' => isset($movie['CommunityRating']) ? $movie['CommunityRating'] : 0,
					'runtime' => is_numeric($movie['RunTimeTicks']) ? $movie['RunTimeTicks']/1000/60 : null
				];

				$this->savePoster($params);
			}
		}
	}
}


