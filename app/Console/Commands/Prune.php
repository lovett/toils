<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Client;
use App\Estimate;
use App\Invoice;
use App\Project;
use App\Tag;
use App\Time;
use App\User;

/**
 * Permanently delete soft-deleted records.
 */
class Prune extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'maintenance:prune';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Permanent deletion of soft-deleted records';

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
     * Order is significant to ensure foreign key constraints are
     * observed.
     *
     * @return void
     */
    public function handle()
    {
        Time::prune();
        Estimate::prune();
        Invoice::prune();
        Project::prune();
        Client::prune();
        User::prune();
    }
}
