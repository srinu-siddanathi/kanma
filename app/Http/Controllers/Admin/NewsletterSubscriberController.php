<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Newsletter;
use Illuminate\Http\Request;

class NewsletterSubscriberController extends Controller
{
    public function index(Request $request)
    {
        $query = Newsletter::query();
        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%") ;
            });
        }
        $subscribers = $query->latest()->paginate(20)->appends(['search' => $search]);
        return view('admin.newsletters.index', compact('subscribers', 'search'));
    }

    public function exportCsv()
    {
        $subscribers = Newsletter::all();
        $filename = 'newsletter_subscribers_' . now()->format('Ymd_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];
        $callback = function() use ($subscribers) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Name', 'Email', 'Subscribed At']);
            foreach ($subscribers as $s) {
                fputcsv($handle, [
                    $s->id,
                    $s->name,
                    $s->email,
                    $s->created_at->format('Y-m-d H:i:s'),
                ]);
            }
            fclose($handle);
        };
        return response()->stream($callback, 200, $headers);
    }

    public function destroy($id)
    {
        $subscriber = Newsletter::findOrFail($id);
        $subscriber->delete();
        return redirect()->route('admin.newsletters.index')->with('success', 'Subscriber deleted successfully.');
    }
} 