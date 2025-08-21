<?php

namespace App\Http\Controllers\BranchManager;

use App\Http\Controllers\Controller;
use App\Models\ChatOrder;
use App\Models\ChatMessage;
use App\Helpers\NotificationHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatOrderController extends Controller
{
    public function index()
    {
        $branch = auth()->user()->branch;
        
        $chatOrders = ChatOrder::with(['user', 'messages' => function($query) {
                $query->latest();
            }])
            ->where('branch_id', $branch->id)
            ->latest()
            ->paginate(10);

        // Get unread message counts for each chat order
        $chatOrders->getCollection()->transform(function ($chatOrder) {
            $chatOrder->unread_count = $chatOrder->messages()
                ->where('is_read', false)
                ->where('sender_id', '!=', Auth::id())
                ->count();
            return $chatOrder;
        });

        return view('branch-manager.chat-orders.index', compact('chatOrders'));
    }

    public function show(ChatOrder $chatOrder)
    {
        // Ensure the chat order belongs to the branch manager's branch
        if ($chatOrder->branch_id !== auth()->user()->branch->id) {
            abort(403);
        }

        $chatOrder->load(['user', 'messages.sender']);
        
        // Mark all messages as read
        $chatOrder->messages()
            ->where('is_read', false)
            ->where('sender_id', '!=', Auth::id())
            ->update(['is_read' => true]);

        return view('branch-manager.chat-orders.show', compact('chatOrder'));
    }

    public function storeMessage(Request $request, ChatOrder $chatOrder)
    {
        // Verify the chat order belongs to the branch manager's branch
        if ($chatOrder->branch_id !== auth()->user()->branch->id) {
            abort(403);
        }

        $validated = $request->validate([
            'type' => 'required|in:text,voice,image,schedule',
            'content' => 'required_if:type,text,schedule|string',
            'media' => 'required_if:type,voice,image|file|max:10240', // 10MB max
            'duration' => 'required_if:type,voice|integer|min:1',
        ]);

        $message = new ChatMessage();
        $message->chat_order_id = $chatOrder->id;
        $message->sender_id = auth()->id();
        
        // Use the constant from ChatMessage model
        $message->type = $validated['type'] === 'schedule' ? ChatMessage::TYPE_SCHEDULE : $validated['type'];
        
        if ($validated['type'] === 'schedule') {
            $message->content = $validated['content'];
        } elseif ($validated['type'] === 'text') {
            $message->content = $validated['content'];
        } else {
            $file = $request->file('media');
            $path = $this->storeFile($file, $chatOrder->id, $validated['type']);
            $message->content = $path;
            
            if ($validated['type'] === 'voice') {
                $message->duration = $validated['duration'];
            }
        }

        $message->save();

        return redirect()->back()->with('success', 'Message sent successfully');
    }

    private function storeFile($file, $chatOrderId, $type)
    {
        // Create directory if it doesn't exist
        $uploadPath = public_path('uploads/chat-media/' . $chatOrderId);
        if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        // Generate unique filename
        $extension = $file->getClientOriginalExtension();
        $filename = uniqid() . '.' . $extension;
        
        // Move file to public directory
        $file->move($uploadPath, $filename);
        
        // Return relative path for database storage
        return 'uploads/chat-media/' . $chatOrderId . '/' . $filename;
    }

    public function updateStatus(Request $request, ChatOrder $chatOrder)
    {
        // Ensure the chat order belongs to the branch manager's branch
        if ($chatOrder->branch_id !== auth()->user()->branch->id) {
            abort(403);
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,preparing,ready,delivered,cancelled'
        ]);

        $oldStatus = $chatOrder->status;
        $chatOrder->update($validated);

        // Send delivery update notification for delivery-related statuses
        if ($oldStatus !== $validated['status'] && in_array($validated['status'], ['ready', 'delivered'])) {
            NotificationHelper::sendDeliveryUpdate($chatOrder->user_id, $chatOrder->id, $validated['status']);
        }

        // Add a status update message to the chat
        $message = new ChatMessage();
        $message->chat_order_id = $chatOrder->id;
        $message->sender_id = auth()->id();
        $message->type = ChatMessage::TYPE_TEXT;
        $message->content = "Order status updated from {$oldStatus} to {$validated['status']}";
        $message->save();

        return back()->with('success', 'Chat order status updated successfully');
    }
} 