<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailLog;
use App\Services\EmailLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmailLogController extends Controller
{
    public function index(Request $request)
    {
        $query = EmailLog::query();

        // Filter by email type
        if ($request->filled('email_type')) {
            $query->ofType($request->email_type);
        }

        // Filter by recipient type
        if ($request->filled('recipient_type')) {
            $query->toRecipientType($request->recipient_type);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->withStatus($request->status);
        }

        // Filter by recipient email
        if ($request->filled('recipient_email')) {
            $query->toEmail($request->recipient_email);
        }

        // Filter by date range
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->betweenDates($request->start_date, $request->end_date);
        }

        // Filter by order ID (from metadata)
        if ($request->filled('order_id')) {
            $query->whereJsonContains('metadata->order_id', (int) $request->order_id);
        }

        $emailLogs = $query->with(['user'])->latest()->paginate(20);

        // Get statistics
        $statistics = EmailLogService::getEmailStatistics();
        $statisticsByType = EmailLogService::getEmailStatisticsByType();

        // Get filter options
        $emailTypes = EmailLog::getEmailTypeOptions();
        $recipientTypes = EmailLog::getRecipientTypeOptions();
        $statuses = EmailLog::getStatusOptions();

        return view('admin.email-logs.index', compact(
            'emailLogs',
            'statistics',
            'statisticsByType',
            'emailTypes',
            'recipientTypes',
            'statuses'
        ));
    }

    public function show(EmailLog $emailLog)
    {
        return view('admin.email-logs.show', compact('emailLog'));
    }

    public function retry(EmailLog $emailLog)
    {
        if ($emailLog->status !== EmailLog::STATUS_FAILED) {
            return back()->with('error', 'Only failed emails can be retried.');
        }

        try {
            // Reset status to pending
            $emailLog->update([
                'status' => EmailLog::STATUS_PENDING,
                'error_message' => null,
            ]);

            // Here you would implement the actual retry logic
            // For now, we'll just mark it as pending for manual review
            
            return back()->with('success', 'Email marked for retry.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to retry email: ' . $e->getMessage());
        }
    }

    public function statistics(Request $request)
    {
        $startDate = $request->get('start_date', now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        $statistics = EmailLogService::getEmailStatistics($startDate, $endDate);
        $statisticsByType = EmailLogService::getEmailStatisticsByType($startDate, $endDate);

        // Get daily statistics for chart
        $dailyStats = EmailLog::selectRaw('
                DATE(created_at) as date,
                COUNT(*) as total,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as sent,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as failed
            ', [EmailLog::STATUS_SENT, EmailLog::STATUS_FAILED])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('admin.email-logs.statistics', compact(
            'statistics',
            'statisticsByType',
            'dailyStats',
            'startDate',
            'endDate'
        ));
    }

    public function cleanup(Request $request)
    {
        $daysToKeep = $request->get('days_to_keep', 90);
        
        try {
            $deletedCount = EmailLogService::cleanupOldLogs($daysToKeep);
            
            return back()->with('success', "Successfully cleaned up {$deletedCount} old email logs (kept last {$daysToKeep} days).");
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to cleanup email logs: ' . $e->getMessage());
        }
    }

    public function export(Request $request)
    {
        $query = EmailLog::query();

        // Apply same filters as index
        if ($request->filled('email_type')) {
            $query->ofType($request->email_type);
        }
        if ($request->filled('recipient_type')) {
            $query->toRecipientType($request->recipient_type);
        }
        if ($request->filled('status')) {
            $query->withStatus($request->status);
        }
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->betweenDates($request->start_date, $request->end_date);
        }

        $emailLogs = $query->get();

        $filename = 'email_logs_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($emailLogs) {
            $file = fopen('php://output', 'w');
            
            // Add headers
            fputcsv($file, [
                'ID',
                'Email Type',
                'Recipient Type',
                'Recipient Email',
                'Recipient Name',
                'Subject',
                'Status',
                'Error Message',
                'Sent At',
                'Delivered At',
                'Opened At',
                'Created At',
                'Updated At'
            ]);

            // Add data
            foreach ($emailLogs as $log) {
                fputcsv($file, [
                    $log->id,
                    $log->email_type,
                    $log->recipient_type,
                    $log->recipient_email,
                    $log->recipient_name,
                    $log->subject,
                    $log->status,
                    $log->error_message,
                    $log->sent_at?->format('Y-m-d H:i:s'),
                    $log->delivered_at?->format('Y-m-d H:i:s'),
                    $log->opened_at?->format('Y-m-d H:i:s'),
                    $log->created_at->format('Y-m-d H:i:s'),
                    $log->updated_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
} 