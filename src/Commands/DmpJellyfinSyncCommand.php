<?php

namespace Newelement\DmpJellyfin\Commands;

use Illuminate\Console\Command;
use Newelement\DmpJellyfin\Services\JellyfinMediaSyncService;

class DmpJellyfinSyncCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'dmp-jellyfin:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'DMP Jellyfin poster sync';


    public function handle()
    {
        $service = new JellyfinMediaSyncService();
        $service->syncMedia();
    }
}
