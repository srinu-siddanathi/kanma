<?php

namespace App\Console\Commands;

use App\Models\EmailLog;
use App\Services\EmailLogService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RetryFailedEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'emails:retry-failed 
                            {--limit=50 : Number of emails to retry}
                            {--dry-run : Show what would be retried without actually doing it}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Retry failed email sends';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $limit = $this->option('limit');
        $dryRun = $this->option('dry-run');

        $this->info("Finding failed emails to retry (limit: {$limit})...");

        $failedEmails = EmailLogService::getFailedEmailsForRetry($limit);

        if ($failedEmails->isEmpty()) {
            $this->info('No failed emails found to retry.');
            return 0;
        }

        $this->info("Found {$failedEmails->count()} failed emails to retry.");

        if ($dryRun) {
            $this->warn('DRY RUN MODE - No emails will actually be retried.');
            $this->table(
                ['ID', 'Email Type', 'Recipient', 'Subject', 'Error'],
                $failedEmails->map(function ($email) {
                    return [
                        $email->id,
                        $email->email_type,
                        $email->recipient_email,
                        $email->subject,
                        $email->error_message,
                    ];
                })
            );
            return 0;
        }

        $bar = $this->output->createProgressBar($failedEmails->count());
        $bar->start();

        $successCount = 0;
        $errorCount = 0;

        foreach ($failedEmails as $emailLog) {
            try {
                // Reset status to pending for retry
                $emailLog->update([
                    'status' => EmailLog::STATUS_PENDING,
                    'error_message' => null,
                ]);

                // Here you would implement the actual retry logic
                // For now, we'll just mark it as pending
                // In a real implementation, you would:
                // 1. Re-send the email based on email_type and metadata
                // 2. Mark as sent if successful
                // 3. Mark as failed with new error if unsuccessful

                $successCount++;
                
                Log::info('Email marked for retry', [
                    'email_log_id' => $emailLog->id,
                    'recipient_email' => $emailLog->recipient_email,
                    'email_type' => $emailLog->email_type,
                ]);

            } catch (\Exception $e) {
                $errorCount++;
                
                Log::error('Failed to retry email', [
                    'email_log_id' => $emailLog->id,
                    'error' => $e->getMessage(),
                ]);
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        $this->info("Retry process completed:");
        $this->info("- Successfully marked for retry: {$successCount}");
        $this->info("- Errors: {$errorCount}");

        return 0;
    }
} 