<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DirectoryIterator;
use SplFileInfo;

/**
 * Delete old log files.
 */
class MaintenanceSweep extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'maintenance:sweep { days=7 : Max age cutoff }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete old files from the logs directory';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $logDir = storage_path('logs');

        $logIterator = new DirectoryIterator($logDir);

        $now = time();

        foreach ($logIterator as $fileInfo) {
            if ($fileInfo->getExtension() !== 'log') {
                continue;
            }

            $daysOld = ($now - $fileInfo->getCTime()) / 86400;

            if ($daysOld > 7) {
                unlink($fileInfo->getRealPath());
            }
        }
    }
}
