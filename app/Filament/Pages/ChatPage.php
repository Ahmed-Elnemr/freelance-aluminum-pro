<?php

namespace App\Filament\Pages;

use App\Events\MessageSentEvent;
use App\Models\ChatAttachment;
use App\Models\Conversation;
use App\Models\Message;
use App\Notifications\NewMessageFromAdminNotification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\WithFileUploads;

class ChatPage extends Page
{
    use WithFileUploads;

    protected static string $view = 'filament.pages.chat-page';

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    public $conversations;
    public $selectedConversation = null;
    public $selectedConversationId = null;
    public $newMessage = '';
    public $attachments = [];

    public $messages;
    public $perPage = 20;
    public $page = 1;

    protected $listeners = ['loadMoreMessages'];

    public function mount(): void
    {
        $this->conversations = Conversation::with('client', 'admin')->latest()->get();
    }

    public function showConversation($id): void
    {
        $this->selectedConversationId = $id;
        $this->selectedConversation = Conversation::with('client', 'admin')->findOrFail($id);
        $this->page = 1;
        $this->loadMessages();

        Message::where('conversation_id', $id)
            ->where('receiver_id', auth()->id())
            ->whereNull('seen_at')
            ->update(['seen_at' => now()]);
    }

    public function loadMessages(): void
    {
        $this->messages = Message::where('conversation_id', $this->selectedConversationId)
            ->latest()
            ->skip(($this->page - 1) * $this->perPage)
            ->take($this->perPage)
            ->with('sender', 'attachments')
            ->get()
            ->reverse()
            ->values();
    }

    public function loadMoreMessages(): void
    {
        $this->page++;
        $moreMessages = Message::where('conversation_id', $this->selectedConversationId)
            ->latest()
            ->skip(($this->page - 1) * $this->perPage)
            ->take($this->perPage)
            ->with('sender', 'attachments')
            ->get()
            ->reverse()
            ->values();

        $this->messages = $moreMessages->concat($this->messages)->values();
    }

    public function sendMessage(): void
    {
        if (!$this->newMessage && !$this->attachments) return;

        $senderId = Auth::id();
        $receiverId = $this->selectedConversation->client_id === $senderId
            ? $this->selectedConversation->admin_id
            : $this->selectedConversation->client_id;

        $message = Message::create([
            'id' => Str::uuid(),
            'message' => $this->newMessage,
            'sender_id' => $senderId,
            'receiver_id' => $receiverId,
            'conversation_id' => $this->selectedConversation->id,
        ]);

        foreach ($this->attachments as $attachment) {
            $fileName = Str::uuid() . '.' . $attachment->getClientOriginalExtension();

            $path = match (true) {
                str_starts_with($attachment->getMimeType(), 'image/') => $attachment->storeAs('attachments/images', $fileName, 'public'),
                str_starts_with($attachment->getMimeType(), 'video/') => $attachment->storeAs('attachments/videos', $fileName, 'public'),
                default => $attachment->storeAs('attachments/others', $fileName, 'public'),
            };

            ChatAttachment::create([
                'message_id' => $message->id,
                'file_path' => Storage::url($path),
                'mime_type' => $attachment->getMimeType(),
            ]);
        }

        broadcast(new MessageSentEvent($message));
        $this->selectedConversation->client?->notify(new NewMessageFromAdminNotification());

        $this->newMessage = '';
        $this->attachments = [];

        $this->showConversation($this->selectedConversation->id);
    }
}
