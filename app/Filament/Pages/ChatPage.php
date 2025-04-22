<?php

namespace App\Filament\Pages;

use App\Events\MessageSentEvent;
use App\Models\Conversation;
use App\Models\Message;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ChatPage extends Page
{
    protected static ?string $title = 'Chat Page';
    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static string $view = 'filament.pages.chat-page';

    public $conversations;
    public $selectedConversation = null;
    public $selectedConversationId = null;
    public $newMessage = '';

    public function mount(): void
    {
        $this->conversations = Conversation::with('client', 'admin')->latest()->get();
    }

    public function showConversation($id): void
    {
        $this->selectedConversationId = $id;
        $this->selectedConversation = Conversation::with('messages.sender', 'client', 'admin')->findOrFail($id);
    }

    public function getMessagesProperty()
    {
        return $this->selectedConversation?->messages ?? collect();
    }


    public function sendMessage(): void
    {
        if (!$this->newMessage || !$this->selectedConversation) return;

        $senderId = Auth::guard('sanctum')->id(); // Assuming you're logged in as admin

        // Determine the receiver
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

        // Dispatch event to broadcast the new message
        broadcast(new MessageSentEvent($message));

        $this->newMessage = '';

        // Reload conversation to include the new message
        $this->showConversation($this->selectedConversation->id);
    }
}
