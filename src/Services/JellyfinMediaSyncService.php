<?php

namespace Newelement\DmpJellyfin\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use App\Interfaces\MediaSyncInterface;
use App\Traits\PosterProcess;
use Illuminate\Support\Facades\Artisan;
use Plugin;

class JellyfinMediaSyncService implements MediaSyncInterface
{
	use PosterProcess;

	public $jellyfinSettings = [];

	public function __construct()
	{
		$this->setSettings();
		$this->setJellyfinSettings();
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
		Artisan::call('vendor:publish', ['--provider' => 'Newelement\DmpJellyfin\DmpJellyfinServiceProvider', '--force' => true]);

		$plugin = [
			'type' => 'media_source',
			'plugin_key' => 'dmp-jellyfin',
			'name' => 'Jellyfin Now Playing',
			'description' => 'Shows now playing.',
			'url' => 'https://github.com/newelement/dmp-jellyfin',
			'repo' => 'newelement/dmp-jellyfin',
			'version' => '1.0.0',
		];

		Plugin::install($plugin);

		$options = [
			[
				'type' => 'string',
				'value' => '',
				'field_name' => 'jellyfin_ip_address',
				'plugin_key' => 'dmp-jellyfin',
			],
			[
				'type' => 'string',
				'value' => '',
				'field_name' => 'jellyfin_token',
				'plugin_key' => 'dmp-jellyfin',
			],
			[
				'type' => 'boolean',
				'value' => false,
				'field_name' => 'jellyfin_use_ssl',
				'plugin_key' => 'dmp-jellyfin',
			],
		];

		Plugin::addOptions($options);

		return ['success' => true];
	}

	public function update()
	{
		//
	}

	public function setJellyfinSettings()
	{
		// Can also call Plugin::getOptions('dmp-jellyfin') to get full options array
		$this->jellyfinSettings['jellyfin_ip_address'] = Plugin::getOptionValue('jellyfin_ip_address');
		$this->jellyfinSettings['jellyfin_token'] = Plugin::getOptionValue('jellyfin_token');
		$this->jellyfinSettings['jellyfin_use_ssl'] = Plugin::getOptionValue('jellyfin_use_ssl');
	}

	public function getSettings()
	{
		return $this->jellyfinSettings;
	}

	public function updateSettings($request)
	{
		Plugin::updateOption('jellyfin_ip_address', $request->jellyfin_ip_address);
		Plugin::updateOption('jellyfin_token', $request->jellyfin_token);
		Plugin::updateOption('jellyfin_use_ssl', $request->jellyfin_use_ssl);
	}

	/**
	 * Make Jellyfin API calls to media server
	 *
	 * @param string $path /path/resource
	 * @param string $method get|post
	 * @param array $params
	 *
	 * @return json
	 */
	public function apiCall($path, $method = 'GET', $params = [])
	{
		$protocol = $this->jellyfinSettings['jellyfin_use_ssl'] ? 'https' : 'http';
		$response = Http::withHeaders([
			'Accept' => 'application/json',
		])->get($protocol.'://'.$this->jellyfinSettings['jellyfin_ip_address'].':8096'.$path.'?api_key='.$this->jellyfinSettings['jellyfin_token']);

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
		$protocol = $this->jellyfinSettings['jellyfin_use_ssl'] ? 'https' : 'http';

		foreach ($movies as $movie) {
			if ($movie['Type'] === 'Movie') {
				$imageUrl = $protocol.'://'.$this->jellyfinSettings['jellyfin_ip_address'].':8096/Items/'.$movie['Id'].'/Images/Primary';

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
