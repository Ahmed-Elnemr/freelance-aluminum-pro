<?php
namespace App\Filament\Pages;

use App\Events\MessageSentEvent;
use App\Models\ChatAttachment;
use App\Models\Conversation;
use App\Models\Message;
use App\Notifications\NewMessageFromAdminNotification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;

class ChatPage extends Page
{
    use WithFileUploads;
    public static function getNavigationSort(): ?int
    {
        return 4;
    }

//    protected static ?string $title = 'Chat Page';
    public static function getNavigationLabel(): string
    {
        return __('conversations');
    }
    public static function getModelLabel(): string
    {
        return __('conversations');
    }

    public static function getPluralModelLabel(): string
    {
        return __('conversations');
    }
    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    protected static string $view = 'filament.pages.chat-page';

    public $conversations;
    public $selectedConversation = null;
    public $selectedConversationId = null;
    public $newMessage = '';
    public $attachments = []; // For handling multiple files

    public function mount(): void
    {
        $this->conversations = Conversation::with('client', 'admin')->latest()->get();
    }

    public function showConversation($id): void
    {
        $this->selectedConversationId = $id;
        $this->selectedConversation = Conversation::with('messages.sender', 'client', 'admin')->findOrFail($id);
        $authId = auth()->id();

        Message::where('conversation_id', $id)
            ->where('receiver_id', $authId)
            ->whereNull('seen_at')
            ->update(['seen_at' => now()]);
    }

    public function getMessagesProperty()
    {
        return $this->selectedConversation?->messages ?? collect();
    }

    public function sendMessage(): void
    {
        if (!$this->newMessage && !$this->attachments) return; // If both message and attachments are empty, do nothing

        $senderId = Auth::guard('sanctum')->id(); // Assuming you're logged in as admin
        $receiverId = $this->selectedConversation->client_id === $senderId
            ? $this->selectedConversation->admin_id
            : $this->selectedConversation->client_id;

        // Create message record
        $message = Message::create([
            'id' => Str::uuid(),
            'message' => $this->newMessage,
            'sender_id' => $senderId,
            'receiver_id' => $receiverId,
            'conversation_id' => $this->selectedConversation->id,
        ]);

        // Handle file attachments
        if ($this->attachments) {
            foreach ($this->attachments as $attachment) {
                // Generate a unique file name
                $fileName = Str::uuid() . '.' . $attachment->getClientOriginalExtension();

                // Store the file in the appropriate folder (images or videos)
                if (str_starts_with($attachment->getMimeType(), 'image/')) {
                    $path = $attachment->storeAs('attachments/images', $fileName, 'public');
                } elseif (str_starts_with($attachment->getMimeType(), 'video/')) {
                    $path = $attachment->storeAs('attachments/videos', $fileName, 'public');
                } else {
                    // For other file types, store them in the general attachments folder
                    $path = $attachment->storeAs('attachments/others', $fileName, 'public');
                }
                $filePath = Storage::url($path);
                // Create ChatAttachment record
                ChatAttachment::create([
                    'message_id' => $message->id,
                    'file_path' => $filePath,
                    'mime_type' => $attachment->getMimeType(),
                ]);
            }
        }

        // Dispatch event to broadcast the new message
        broadcast(new MessageSentEvent($message));
        $receiver = $this->selectedConversation->client;
        $receiver?->notify(new NewMessageFromAdminNotification());

        $this->newMessage = '';
        $this->attachments = []; // Clear attachments after sending message

        // Reload conversation to include the new message
        $this->showConversation($this->selectedConversation->id);
    }
}
