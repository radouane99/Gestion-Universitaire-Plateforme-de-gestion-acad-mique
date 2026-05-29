<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        
        $conversations = Conversation::where('user_one_id', $userId)
            ->orWhere('user_two_id', $userId)
            ->with(['userOne', 'userTwo', 'messages' => function ($query) {
                $query->latest()->limit(1);
            }])
            ->get()
            ->sortByDesc(function ($conv) {
                return $conv->messages->first() ? $conv->messages->first()->created_at : $conv->created_at;
            });

        return view('messages.index', compact('conversations'));
    }

    public function show(Conversation $conversation)
    {
        $userId = Auth::id();
        
        if ($conversation->user_one_id !== $userId && $conversation->user_two_id !== $userId) {
            abort(403);
        }

        // Mark unread messages as read
        $conversation->messages()
            ->where('user_id', '!=', $userId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $conversation->load(['messages.sender', 'userOne', 'userTwo']);

        $otherUser = $conversation->user_one_id === $userId ? $conversation->userTwo : $conversation->userOne;

        return view('messages.show', compact('conversation', 'otherUser'));
    }

    public function store(Request $request, Conversation $conversation)
    {
        $userId = Auth::id();
        
        if ($conversation->user_one_id !== $userId && $conversation->user_two_id !== $userId) {
            abort(403);
        }

        $validated = $request->validate([
            'content' => 'required_without:attachment|nullable|string|max:2000',
            'attachment' => 'nullable|file|mimes:pdf,doc,docx,jpg,png,zip|max:5120'
        ]);

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'user_id' => $userId,
            'content' => $validated['content'] ?? ''
        ]);

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $path = $file->store('attachments', 'public');
            $message->attachment_path = $path;
            $message->attachment_name = $file->getClientOriginalName();
            $message->save();
        }

        return back();
    }

    public function startConversation(Request $request, User $user)
    {
        $currentUserId = Auth::id();

        if ($currentUserId === $user->id) {
            return back()->with('error', 'Vous ne pouvez pas vous envoyer de message à vous-même.');
        }

        // Check if conversation already exists
        $conversation = Conversation::where(function ($query) use ($currentUserId, $user) {
            $query->where('user_one_id', $currentUserId)->where('user_two_id', $user->id);
        })->orWhere(function ($query) use ($currentUserId, $user) {
            $query->where('user_one_id', $user->id)->where('user_two_id', $currentUserId);
        })->first();

        if (!$conversation) {
            $conversation = Conversation::create([
                'user_one_id' => $currentUserId,
                'user_two_id' => $user->id
            ]);
        }

        return redirect()->route('chat.show', $conversation);
    }
}
