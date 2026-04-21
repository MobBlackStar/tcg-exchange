<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;

class ChatController extends Controller
{
    // API Endpoint: Sarah's JS will POST data here silently
    public function sendMessage(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'content' => 'required|string',
            'listing_id' => 'nullable|exists:listings,id'
        ]);

        $message = Message::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $request->receiver_id,
            'listing_id' => $request->listing_id,
            'content' => $request->content,
        ]);

        // Return pure JSON so Sarah's JS can append it to the screen without refreshing
        return response()->json([
            'status' => 'success',
            'message' => clone $message // Clone to avoid relation loading issues
        ]);
    }

    // API Endpoint: Sarah's JS will GET data from here every 3 seconds
    public function fetchMessages($receiver_id)
    {
        $myId = auth()->id();

        // Get the conversation history between these two users
        $messages = Message::where(function($query) use ($myId, $receiver_id) {
                $query->where('sender_id', $myId)->where('receiver_id', $receiver_id);
            })
            ->orWhere(function($query) use ($myId, $receiver_id) {
                $query->where('sender_id', $receiver_id)->where('receiver_id', $myId);
            })
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'messages' => $messages
        ]);
    }
}