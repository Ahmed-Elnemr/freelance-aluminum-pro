<x-filament::page>
    <div>
        <div class="flex">
            {{-- Conversations List --}}
            <div class="space-y-2 border p-4 rounded-md w-1/3 flex-shrink-0">
                <h3 class="text-lg font-bold mb-2">Conversations</h3>
                @foreach($conversations as $conversation)
                    <div
                        wire:click="showConversation({{ $conversation->id }})"
                        class="cursor-pointer p-3 border rounded-md transition hover:bg-gray-100
                            {{ $selectedConversationId === $conversation->id ? 'bg-blue-100 border-blue-400' : '' }}"
                    >
                        <div><strong>Client:</strong> {{ $conversation->client->name }}</div>
                        <div><strong>Admin:</strong> {{ $conversation->admin->name }}</div>
                        <div class="text-xs text-gray-500">
                            {{ $conversation->last_message_at?->diffForHumans() }}
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Messages --}}
            <div class="flex-1 space-y-4">
                @if($selectedConversation)
                    <h2 class="text-xl font-bold">
                        Conversation with {{ $selectedConversation->client->name }}
                    </h2>

                    <div class="border p-4 rounded-md max-h-[400px] h-[400px] overflow-y-auto bg-gray-50 flex flex-col-reverse space-y-2 space-y-reverse message-container">
                        @foreach($this->messages as $message)
                            <div class="{{ $message->sender_id === auth()->id() ? 'text-right' : 'text-left' }}">
                                <div class="inline-block bg-white px-4 py-2 rounded shadow">
                                    <strong>{{ $message->sender->name }}</strong>: {{ $message->message }}
                                    <div class="text-xs text-gray-500">
                                        {{ $message->created_at->diffForHumans() }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <form wire:submit.prevent="sendMessage" class="mt-4 flex items-center gap-2">
                        <input
                            wire:model.defer="newMessage"
                            type="text"
                            placeholder="Type your message..."
                            class="w-full rounded border-gray-300 focus:ring focus:ring-blue-200"
                        />
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                            Send
                        </button>
                    </form>
                @else
                    <div class="text-center text-gray-500 mt-4">
                        Select a conversation to view messages.
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-filament::page>
