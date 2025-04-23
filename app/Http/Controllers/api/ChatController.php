<?php

namespace App\Http\Controllers\api;

use App\Events\MessageSentEvent;
use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use App\Helpers\Response\ApiResponder;

class ChatController extends Controller
{
    // todo: sendMessage
    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $sender = auth()->user();
        $receiver = User::where('type', 'admin')->first();

        $conversation = Conversation::firstOrCreateBetween($sender->id, $receiver->id);

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'message' => $request->message,
        ]);

        $conversation->update(['last_message_at' => now()]);

        event(new MessageSentEvent($message));

        return ApiResponder::success('Message sent successfully', $message->load('sender'));
    }

    // todo: getConversations
    public function getConversations(Request $request)
    {
        $user = auth()->user();

        $conversations = Conversation::with(['messages' => function($query) {
            $query->latest()->limit(1);
        }, 'client', 'admin'])
            ->forUser($user->id)
            ->latest('last_message_at')
            ->get()
            ->map(function($conversation) use ($user) {
                $otherParticipant = ($user->id === $conversation->client_id)
                    ? $conversation->admin
                    : $conversation->client;

                return [
                    'id' => $conversation->id,
                    'other_participant' => [
                        'id' => $otherParticipant->id,
                        'name' => $otherParticipant->name,
                        'avatar' => $otherParticipant->avatar_url,
                        'type' => $otherParticipant->type,
                    ],
                    'last_message' => $conversation->messages->first(),
                    'unread_count' => $conversation->messages()
                        ->where('receiver_id', $user->id)
                        ->whereNull('seen_at')
                        ->count(),
                    'updated_at' => $conversation->last_message_at,
                ];
            });

        return ApiResponder::loaded($conversations);
    }

    // todo: getMessages
//    public function getMessages(Request $request, $conversationId)
//    {
//        $conversation = Conversation::findOrFail($conversationId);
//        $user = auth()->user();
//
//        if (!in_array($user->id, [$conversation->client_id, $conversation->admin_id])) {
//            return ApiResponder::failed('Unauthorized', 403);
//        }
//
//        Message::where('conversation_id', $conversationId)
//            ->where('receiver_id', $user->id)
//            ->whereNull('seen_at')
//            ->update(['seen_at' => now()]);
//
//        $messages = Message::with('sender')
//            ->where('conversation_id', $conversationId)
//            ->latest()
//            ->paginate(40);
//
//        return ApiResponder::loaded([
//            'conversation' => $conversation,
//            'messages' => $messages,
//        ]);
//    }


    public function getMessages(Request $request)
    {
        $user = auth()->user();

        $admin = User::where('type', 'admin')->first();

        if (!$admin) {
            return ApiResponder::failed('No admin found', 404);
        }

        $conversation = Conversation::firstBetween($user->id, $admin->id);
        if (!$conversation) {
            return ApiResponder::loaded([
                'conversation' => '',
                'messages' => [],
            ]);
        }
        if (!$conversation) {
            return ApiResponder::failed('Conversation not found', 404);
        }

        Message::where('conversation_id', $conversation->id)
            ->where('receiver_id', $user->id)
            ->whereNull('seen_at')
            ->update(['seen_at' => now()]);

        $messages = Message::with('sender')
            ->where('conversation_id', $conversation->id)
            ->latest()
            ->paginate(40);

        return ApiResponder::loaded([
            'conversation' => $conversation,
            'messages' => $messages,
        ]);
    }

    // todo: markAsRead
    public function markAsRead(Request $request, $conversationId)
    {
        $user = auth()->user();

        Message::where('conversation_id', $conversationId)
            ->where('receiver_id', $user->id)
            ->whereNull('seen_at')
            ->update(['seen_at' => now()]);
        return ApiResponder::success('Messages marked as read');
    }
}
