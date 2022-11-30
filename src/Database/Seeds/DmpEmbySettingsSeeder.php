<?php

use Illuminate\Database\Seeder;
use Newelement\DmpEmby\Models\DmpEmbySetting;

class DmpEmbySettingsSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		DmpEmbySetting::firstOrCreate(
			[ 'setting_name' => 'enable' ],
			[ 'bool_value' => 0 ]
		);
	}
}
