<?php

namespace Newelement\DmpEmby\Commands;

use Illuminate\Console\Command;
use Newelement\DmpEmby\Services\EmbyMediaSyncService;

class DmpEmbySyncCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'dmp-emby:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'DMP Emby poster sync';


    public function handle()
    {
        $service = new EmbyMediaSyncService();
        $service->syncMedia();
    }
}
