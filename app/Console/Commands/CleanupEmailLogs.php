<?php

namespace App\Console\Commands;

use App\Services\EmailLogService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CleanupEmailLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'emails:cleanup 
                            {--days=90 : Number of days to keep email logs}
                            {--dry-run : Show what would be deleted without actually doing it}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up old email logs';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $daysToKeep = $this->option('days');
        $dryRun = $this->option('dry-run');

        $this->info("Cleaning up email logs older than {$daysToKeep} days...");

        if ($dryRun) {
            $this->warn('DRY RUN MODE - No logs will actually be deleted.');
            
            // Count what would be deleted
            $cutoffDate = now()->subDays($daysToKeep);
            $countToDelete = \App\Models\EmailLog::where('created_at', '<', $cutoffDate)
                ->where('status', '!=', \App\Models\EmailLog::STATUS_PENDING)
                ->count();
                
            $this->info("Would delete {$countToDelete} email logs older than {$cutoffDate->format('Y-m-d')}");
            return 0;
        }

        try {
            $deletedCount = EmailLogService::cleanupOldLogs($daysToKeep);
            
            $this->info("Successfully cleaned up {$deletedCount} old email logs (kept last {$daysToKeep} days).");
            
            Log::info('Email logs cleanup completed', [
                'deleted_count' => $deletedCount,
                'days_kept' => $daysToKeep,
            ]);

            return 0;
        } catch (\Exception $e) {
            $this->error("Failed to cleanup email logs: {$e->getMessage()}");
            
            Log::error('Email logs cleanup failed', [
                'error' => $e->getMessage(),
                'days_kept' => $daysToKeep,
            ]);

            return 1;
        }
    }
} 