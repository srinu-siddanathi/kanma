<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Models\ChatOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Str;

class ChatMessageController extends Controller
{
    use AuthorizesRequests;

    private function storeFile($file, $chatOrderId, $type)
    {
        // Create directory if it doesn't exist
        $uploadPath = public_path('uploads/chat-media/' . $chatOrderId);
        if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        // Generate unique filename
        $extension = $file->getClientOriginalExtension();
        $filename = Str::random(40) . '.' . $extension;
        
        // Move file to public directory
        $file->move($uploadPath, $filename);
        
        // Return relative path for database storage
        return 'uploads/chat-media/' . $chatOrderId . '/' . $filename;
    }

    private function deleteFile($path)
    {
        $fullPath = public_path($path);
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }
    }

    public function index(ChatOrder $chatOrder)
    {
        $this->authorize('view', $chatOrder);

        $messages = $chatOrder->messages()
            ->with('sender')
            ->latest()
            ->paginate(20);

        return response()->json([
            'status' => 'success',
            'data' => $messages
        ]);
    }

    public function store(Request $request, ChatOrder $chatOrder)
    {
        $this->authorize('create', [ChatMessage::class, $chatOrder]);

        $validator = Validator::make($request->all(), [
            'type' => 'required|in:text,voice,image',
            'content' => 'required_if:type,text|string',
            'media' => 'required_if:type,voice,image|file|max:10240', // 10MB max
            'duration' => 'required_if:type,voice|integer|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $messageData = [
            'chat_order_id' => $chatOrder->id,
            'sender_id' => Auth::id(),
            'type' => $request->type,
            'content' => $request->content,
            'is_read' => false
        ];

        if ($request->hasFile('media')) {
            $file = $request->file('media');
            $messageData['media_path'] = $this->storeFile($file, $chatOrder->id, $request->type);
        }

        if ($request->type === 'voice') {
            $messageData['duration'] = $request->duration;
        }

        $message = ChatMessage::create($messageData);

        // Load the sender relationship
        $message->load('sender');

        return response()->json([
            'status' => 'success',
            'message' => 'Message sent successfully',
            'data' => $message
        ], 201);
    }

    public function markAsRead(ChatMessage $message)
    {
        $this->authorize('update', $message);

        $message->update(['is_read' => true]);

        return response()->json([
            'status' => 'success',
            'message' => 'Message marked as read'
        ]);
    }

    public function markAllAsRead(ChatOrder $chatOrder)
    {
        $this->authorize('update', $chatOrder);

        $chatOrder->messages()
            ->where('is_read', false)
            ->where('sender_id', '!=', Auth::id())
            ->update(['is_read' => true]);

        return response()->json([
            'status' => 'success',
            'message' => 'All messages marked as read'
        ]);
    }

    public function destroy(ChatMessage $message)
    {
        $this->authorize('delete', $message);

        // Delete media file if exists
        if ($message->media_path) {
            $this->deleteFile($message->media_path);
        }

        $message->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Message deleted successfully'
        ]);
    }
} 