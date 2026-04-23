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
        return response()->json(['status' => 'success', 'message' => clone $message]);
    }

    // API Endpoint: Sarah's JS will GET data from here every 3 seconds
    public function fetchMessages($receiver_id)
    {
        $myId = auth()->id();

        //[TECH LEAD FIX]: Mark messages as read ONLY when the user opens the chat drawer!
        Message::where('sender_id', $receiver_id)
            ->where('receiver_id', $myId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        // Get the conversation history between these two users
        $messages = Message::where(function($query) use ($myId, $receiver_id) {
                $query->where('sender_id', $myId)->where('receiver_id', $receiver_id);
            })
            ->orWhere(function($query) use ($myId, $receiver_id) {
                $query->where('sender_id', $receiver_id)->where('receiver_id', $myId);
            })
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json(['messages' => $messages]);
    }

    // [TECH LEAD FIX]: Global Notification Poller (The Red Alarm)
    public function checkUnread()
    {
        // Simply count how many messages sent TO me have a null read_at timestamp
        $count = Message::where('receiver_id', auth()->id())
                        ->whereNull('read_at')
                        ->count();
                        
        return response()->json(['unread' => $count]);
    }

    // [TECH LEAD FEATURE]: The Duelist Comm Hub (Inbox)
    public function inbox()
    {
        $userId = auth()->id();
        
        // 1. Get all messages involving the user, ordered by latest
        $messages = Message::where('sender_id', $userId)
            ->orWhere('receiver_id', $userId)
            ->with(['sender', 'receiver'])
            ->latest()
            ->get();
            
        // 2. Extract unique partners and count their unread messages
        $conversations =[];
        foreach ($messages as $msg) {
            // Who are we talking to in this specific message?
            $partner = $msg->sender_id == $userId ? $msg->receiver : $msg->sender;
            
            if (!isset($conversations[$partner->id])) {
                $conversations[$partner->id] =[
                    'partner' => $partner,
                    'latest_message' => $msg,
                    // If they sent it to us and we haven't read it, count it as unread
                    'unread_count' => ($msg->receiver_id == $userId && is_null($msg->read_at)) ? 1 : 0 
                ];
            } else {
                if ($msg->receiver_id == $userId && is_null($msg->read_at)) {
                    $conversations[$partner->id]['unread_count']++;
                }
            }
        }
        
        return view('inbox', compact('conversations'));
    }
}