<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use App\Models\Client;
use App\Models\Estimate;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\Tag;
use App\Models\Time;
use App\Models\User;

/**
 * Permanently delete soft-deleted records.
 */
class MaintenancePrune extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'maintenance:prune { days? : Max age cutoff }';

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
        $days = (int) $this->argument('days');

        $cutoffSql = '1=1';

        if ($days > 0) {
            $cutoffSql = "deleted_at < datetime('now', 'start of day', '-{$days} days')";
        }

        // Time.
        $timeQuery = Time::onlyTrashed()->whereRaw($cutoffSql);
        $timeIds = $this->deletionIds($timeQuery);

        DB::table('taggables')
            ->whereIn('taggable_id', $timeIds)
            ->where('taggable_type', Time::class)
            ->delete();

        $timeQuery->forceDelete();

        // Estimates.
        $estimateQuery = Estimate::onlyTrashed()->whereRaw($cutoffSql);
        $estimateIds = $this->deletionIds($estimateQuery);

        DB::table('estimate_user')
            ->whereIn('estimate_id', $estimateIds)
            ->delete();

        $estimateQuery->forceDelete();

        // Invoices.
        $invoiceQuery = Invoice::onlyTrashed()->whereRaw($cutoffSql);
        $invoiceQuery->forceDelete();

        // Projects.
        $projectQuery = Project::onlyTrashed()->whereRaw($cutoffSql);
        $projectQuery->forceDelete();

        // Clients.
        $clientQuery = Client::onlyTrashed()->whereRaw($cutoffSql);
        $clientIds = $this->deletionIds($clientQuery);

        DB::table('client_user')
            ->whereIn('client_id', $clientIds)
            ->delete();

        $clientQuery->forceDelete();

        // Users.
        $userQuery = User::onlyTrashed()->whereRaw($cutoffSql);
        $userIds = $this->deletionIds($userQuery);

        DB::table('client_user')
            ->whereIn('user_id', $userIds)
            ->delete();

        DB::table('estimate_user')
            ->whereIn('user_id', $userIds)
            ->delete();

        $userQuery->forceDelete();
    }

    /**
     * Run a query and return the ids of its records.
     *
     * @param Builder $query The query to run.
     *
     * @return Array[int]
     */
    private function deletionIds(Builder $query)
    {
        return $query->select('id')->get()->map(function ($record) {
            return $record->id;
        })->toArray();
    }
}
