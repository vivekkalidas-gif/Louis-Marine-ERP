<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function users()
    {
        try {
            $currentUserId = Auth::id();
            if (!$currentUserId) {
                return response()->json(['error' => 'Unauthenticated'], 401);
            }

            // Get all users except current user with unread message count
            $users = User::where('id', '!=', $currentUserId)
                ->withCount(['sentMessages as unread_count' => function($query) use ($currentUserId) {
                    $query->where('receiver_id', $currentUserId)
                        ->where('is_read', false);
                }])
                ->get(['id', 'name', 'email']);

            return response()->json($users);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Chat Users Fetch Error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load users'], 500);
        }
    }

    public function messages($userId)
    {
        // Find User Manually to avoid Binding Issues
        $user = User::find($userId);
        
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // Fetch conversation
        \Illuminate\Support\Facades\Log::info("Chat Request Stats:", [
            'my_id' => Auth::id(), 
            'target_id' => $user->id,
            'target_name' => $user->name
        ]);
        
        // Mark messages as read
        ChatMessage::where('sender_id', $user->id)
            ->where('receiver_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $messages = ChatMessage::where(function($q) use ($user) {
                $q->where('sender_id', Auth::id())
                  ->where('receiver_id', $user->id);
            })
            ->orWhere(function($q) use ($user) {
                $q->where('sender_id', $user->id)
                  ->where('receiver_id', Auth::id());
            })
            ->orderBy('created_at', 'asc')
            ->get();
            
        return response()->json($messages);
    }

    public function send(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string',
        ]);

        $message = ChatMessage::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
        ]);

        return response()->json($message);
    }
}
