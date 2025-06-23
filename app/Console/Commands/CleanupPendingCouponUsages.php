<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CouponUsage;
use Carbon\Carbon;

class CleanupPendingCouponUsages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'coupons:cleanup-pending {--hours=24 : Hours after which pending usages should be cleaned up}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up old pending coupon usages that are likely abandoned';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $hours = $this->option('hours');
        $cutoffTime = Carbon::now()->subHours($hours);

        $pendingUsages = CouponUsage::where('status', CouponUsage::STATUS_PENDING)
            ->where('created_at', '<', $cutoffTime)
            ->get();

        $count = $pendingUsages->count();

        if ($count === 0) {
            $this->info('No pending coupon usages to clean up.');
            return;
        }

        foreach ($pendingUsages as $usage) {
            $usage->markAsFailed();
        }

        $this->info("Cleaned up {$count} pending coupon usages older than {$hours} hours.");
    }
} 