<?php

namespace App\Http\Controllers\api;

use App\Enum\MessageTypeEnum;
use App\Events\MessageSentEvent;
use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Http\Request;
use App\Helpers\Response\ApiResponder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ChatController extends Controller
{
    // todo: sendMessage
    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'nullable|string',
            'file.*' => 'nullable|file|mimes:jpg,jpeg,png,gif,mp4,mp3,mov,avi,pdf,doc,docx|max:10240',
            'is_record' => 'nullable|boolean',
        ]);

        $sender = auth()->user();
        $receiver = User::where('type', 'admin')->first();

        $conversation = Conversation::firstOrCreateBetween($sender->id, $receiver->id);

        try {
            $type = match (true) {
                $request->hasFile('file') && $request->filled('message') => MessageTypeEnum::MULTIPLE,
                $request->hasFile('file') => MessageTypeEnum::FILE,
                $request->filled('message') => MessageTypeEnum::TEXT,
                default => throw new \Exception('Either message or file is required.')
            };

            DB::beginTransaction();

            $message = Message::create([
                'conversation_id' => $conversation->id,
                'sender_id' => $sender->id,
                'receiver_id' => $receiver->id,
                'message' => $request->message,
                'type' => $type->value,
            ]);

            if ($request->hasFile('file')) {
                foreach ($request->file('file') as $file) {
                    $path = $file->store('chat_attachments', 'public');
                    $filePath = Storage::url($path);

                    $mimeType = $request->boolean('is_record')
                        ? 'audio/mp3'
                        : $file->getMimeType();

                    $message->attachments()->create([
                        'file_path' => $filePath,
                        'mime_type' => $mimeType,
                    ]);
                }
            }

            $conversation->update(['last_message_at' => now()]);
            event(new MessageSentEvent($message));
            //todo: send notification
            $senderName = $sender->name;
            $previewText = '';

            if ($request->filled('message')) {
                $previewText = Str::limit(strip_tags($request->message), 100);
            }

            $notificationBody = "لديك رسالة جديدة من المستخدم: <strong>{$senderName}</strong>";
            if ($previewText) {
                $notificationBody .= "<br>{$previewText}";
            }

            Notification::make()
                ->title('رسالة جديدة')
                ->body($notificationBody)
                ->sendToDatabase($receiver);
            DB::commit();

            return ApiResponder::success('Message sent successfully', $message->load('sender', 'attachments'));
        } catch (\Exception $e) {
            DB::rollBack();
            return ApiResponder::failed($e->getMessage(), 422);
        }
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

        $messages = Message::with(['sender', 'attachments'])
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
