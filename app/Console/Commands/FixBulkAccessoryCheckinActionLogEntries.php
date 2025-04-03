<?php

namespace App\Console\Commands;

use App\Models\Actionlog;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;

class FixBulkAccessoryCheckinActionLogEntries extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'snipeit:fix-bulk-accessory-action-log-entries {--dry-run : Run the sync process but don\'t update the database}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This script attempts to fix timestamps and missing created_by values for bulk checkin entries in the log table';

    private bool $dryrun = false;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('dry-run')) {
            $this->dryrun = true;
        }

        if ($this->dryrun) {
            $this->info('This is a DRY RUN - no changes will be saved.');
            $this->newLine();
        }

        // Logs that were improperly timestamped should have created_at in the 1970s
        $logs = Actionlog::whereYear('created_at', '1970')->get();

        $this->info('Found ' . $logs->count() . ' logs with incorrect timestamps:');

        $this->table(
            ['ID', 'Created By', 'Created At', 'Updated At'],
            $logs->map(function ($log) {
                return [
                    $log->id,
                    $log->created_by,
                    $log->created_at,
                    $log->updated_at,
                ];
            })
        );

        if (!$this->dryrun && !$this->confirm('Update these logs?')) {
            return 0;
        }

        if ($this->dryrun) {
            $this->newLine();
            $this->info('DRY RUN. NOT ACTUALLY UPDATING LOGS.');
        }

        foreach ($logs as $log) {
            $this->newLine();
            $this->info('Processing log id:' . $log->id);

            // created_by was not being set for accessory bulk checkins
            // so let's see if there was another bulk checkin log
            // with the same timestamp and a created_by value we can use.
            if (is_null($log->created_by)) {
                $createdByFromSimilarLog = $this->getCreatedByAttributeFromSimilarLog($log);

                if ($createdByFromSimilarLog) {
                    $this->line(vsprintf('Updating log id:%s created_by to %s', [$log->id, $createdByFromSimilarLog]));
                    $log->created_by = $createdByFromSimilarLog;
                } else {
                    $this->warn(vsprintf('No created_by found for log id:%s', [$log->id]));
                    $this->warn('Skipping updating this log since no similar log was found to update created_by from.');

                    // If we can't find a similar log then let's skip updating it
                    continue;
                }
            }

            $this->line(vsprintf('Updating log id:%s from %s to %s', [$log->id, $log->created_at, $log->updated_at]));
            $log->created_at = $log->updated_at;

            if (!$this->dryrun) {
                Model::withoutTimestamps(function () use ($log) {
                    $log->saveQuietly();
                });
            }
        }

        $this->newLine();

        if ($this->dryrun) {
            $this->info('DRY RUN. NO CHANGES WERE ACTUALLY MADE.');
        }

        return 0;
    }

    /**
     * This method attempts to find a bulk check in log that was
     * created at the same time as the log passed in.
     */
    private function getCreatedByAttributeFromSimilarLog(Actionlog $log): null|int
    {
        $similarLog = Actionlog::query()
            ->whereNotNull('created_by')
            ->where([
                'action_type' => 'checkin from',
                'note' => 'Bulk checkin items',
                'target_id' => $log->target_id,
                'target_type' => $log->target_type,
                'created_at' => $log->updated_at,
            ])
            ->first();

        if ($similarLog) {
            return $similarLog->created_by;
        }

        return null;
    }
}
