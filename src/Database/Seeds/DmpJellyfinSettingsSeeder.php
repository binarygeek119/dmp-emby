<?php

use Illuminate\Database\Seeder;
use Newelement\DmpJellyfin\Models\DmpJellyfinSetting;

class DmpJellyfinSettingsSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		DmpJellyfinSetting::firstOrCreate(
			[ 'setting_name' => 'enable' ],
			[ 'bool_value' => 0 ]
		);
	}
}
