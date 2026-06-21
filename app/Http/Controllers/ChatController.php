<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Conversation;
use App\Models\Message;
use App\Events\MessageSent;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function getConversations()
    {
        $user = Auth::user();
        if ($user->hasAnyRole(['cskh', 'Admin', 'Super Admin', 'Staff'])) {
            $conversations = Conversation::with(['user', 'cskh', 'messages' => function($q) {
                $q->latest()->limit(1);
            }])->orderBy('updated_at', 'desc')->get();
        } else {
            $conversations = Conversation::with(['cskh', 'messages' => function($q) {
                $q->latest()->limit(1);
            }])->where('user_id', $user->id)->orderBy('updated_at', 'desc')->get();
        }
        return response()->json($conversations);
    }

    public function getMessages($id)
    {
        $conversation = Conversation::with('messages.sender')->findOrFail($id);
        
        $user = Auth::user();
        if (!$user->hasAnyRole(['cskh', 'Admin', 'Super Admin', 'Staff']) && $conversation->user_id !== $user->id) {
            abort(403);
        }

        return response()->json($conversation->messages);
    }

    public function sendMessage(Request $request, $id)
    {
        $request->validate([
            'message' => 'nullable|string',
            'attachment' => 'nullable|file|max:10240' // max 10MB
        ]);

        $conversation = Conversation::findOrFail($id);
        
        $user = Auth::user();
        if (!$user->hasAnyRole(['cskh', 'Admin', 'Super Admin', 'Staff']) && $conversation->user_id !== $user->id) {
            abort(403);
        }

        // If admin/cskh replies, assign conversation to them
        if ($user->hasAnyRole(['cskh', 'Admin', 'Super Admin', 'Staff']) && $conversation->cskh_id === null) {
            $conversation->cskh_id = $user->id;
            $conversation->save();
        }

        $path = null;
        $originalName = null;
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $originalName = $file->getClientOriginalName();
            $path = $file->store('chat_attachments', 'public');
        }

        if (!$request->message && !$path) {
            return response()->json(['error' => 'Message or attachment is required'], 422);
        }

        $message = $conversation->messages()->create([
            'sender_id' => $user->id,
            'message' => \App\Services\ProfanityFilter::filter($request->message),
            'attachment_path' => $path ? '/storage/' . $path : null,
            'attachment_name' => $originalName,
        ]);

        $conversation->touch(); // Update updated_at

        broadcast(new MessageSent($message))->toOthers();

        return response()->json($message->load('sender'));
    }

    public function startConversation(Request $request)
    {
        $user = Auth::user();
        
        // Find existing open conversation or create new
        $conversation = Conversation::firstOrCreate(
            ['user_id' => $user->id, 'status' => 'open'],
            ['booking_id' => $request->booking_id ?? null]
        );

        return response()->json($conversation);
    }
}
