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

	public $jelllyfinSettings = [];

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
			'plugin_key' => 'dmp-jelllyfin',
			'name' => 'Jellyfin Media Sync and Now Playing',
			'description' => 'Syncs movie posters and shows now playing.',
			'url' => 'https://github.com/newelement/dmp-jelllyfin',
			'repo' => 'newelement/dmp-jelllyfin',
			'version' => '1.0.0',
		];

		Plugin::install($plugin);

		$options = [
			[
				'type' => 'string',
				'value' => '',
				'field_name' => 'jelllyfin_ip_address',
				'plugin_key' => 'dmp-jelllyfin',
			],
			[
				'type' => 'string',
				'value' => '',
				'field_name' => 'jelllyfin_token',
				'plugin_key' => 'dmp-jelllyfin',
			],
			[
				'type' => 'boolean',
				'value' => false,
				'field_name' => 'jelllyfin_use_ssl',
				'plugin_key' => 'dmp-jelllyfin',
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
		// Can also call Plugin::getOptions('dmp-jelllyfin') to get full options array
		$this->jelllyfinSettings['jelllyfin_ip_address'] = Plugin::getOptionValue('jelllyfin_ip_address');
		$this->jelllyfinSettings['jelllyfin_token'] = Plugin::getOptionValue('jelllyfin_token');
		$this->jelllyfinSettings['jelllyfin_use_ssl'] = Plugin::getOptionValue('jelllyfin_use_ssl');
	}

	public function getSettings()
	{
		return $this->jelllyfinSettings;
	}

	public function updateSettings($request)
	{
		Plugin::updateOption('jelllyfin_ip_address', $request->jelllyfin_ip_address);
		Plugin::updateOption('jelllyfin_token', $request->jelllyfin_token);
		Plugin::updateOption('jelllyfin_use_ssl', $request->jelllyfin_use_ssl);
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
		$protocol = $this->jelllyfinSettings['jelllyfin_use_ssl'] ? 'https' : 'http';
		$response = Http::withHeaders([
			'Accept' => 'application/json',
		])->get($protocol.'://'.$this->jelllyfinSettings['jelllyfin_ip_address'].':8096'.$path.'?api_key='.$this->jelllyfinSettings['jelllyfin_token']);

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
		$protocol = $this->jelllyfinSettings['jelllyfin_use_ssl'] ? 'https' : 'http';

		foreach ($movies as $movie) {
			if ($movie['Type'] === 'Movie') {
				$imageUrl = $protocol.'://'.$this->jelllyfinSettings['jelllyfin_ip_address'].':8096/Items/'.$movie['Id'].'/Images/Primary';

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
