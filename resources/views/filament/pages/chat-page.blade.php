<x-filament::page>
    <div>
        <div class="flex">
            {{-- قائمة المحادثات --}}
            <div class="space-y-2 border p-4 rounded-md w-1/3 flex-shrink-0 mr-4 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 border-gray-300 dark:border-gray-700">
                <h3 class="text-lg font-bold mb-2">المحادثات</h3>
                @foreach($conversations as $conversation)
                    <div
                        wire:click="showConversation({{ $conversation->id }})"
                        class="cursor-pointer p-3 border rounded-md transition hover:bg-gray-100 dark:hover:bg-gray-700
                            {{ $selectedConversationId === $conversation->id ? 'bg-blue-100 dark:bg-blue-900 border-blue-400' : 'border-gray-300 dark:border-gray-700' }}"
                    >
                        <div>
                            <strong>العميل:</strong>
                            {{ $conversation->client?->name ?? 'غير متوفر' }}
                        </div>
                        <div>
                            <strong>المسؤول:</strong>
                            {{ $conversation->admin?->name ?? 'غير متوفر' }}
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            {{ $conversation->last_message_at?->diffForHumans() }}
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- الرسائل --}}
            <div class="flex-1 flex flex-col space-y-4">
                @if($selectedConversation)
                    <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100">
                        المحادثة مع {{ $selectedConversation->client->name }}
                    </h2>

                    <div class="border p-4 rounded-md flex-1 overflow-y-auto bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 flex flex-col-reverse space-y-2 space-y-reverse message-container border-gray-300 dark:border-gray-700">
                        @foreach($this->messages as $message)
                            <div class="{{ $message->sender_id === auth()->id() ? 'text-right' : 'text-left' }}">
                                <div class="inline-block bg-white dark:bg-gray-700 text-black dark:text-white px-4 py-2 rounded shadow">
                                    <strong>{{ $message->sender->name }}</strong>: {{ $message->message }}
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $message->created_at->diffForHumans() }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <form wire:submit.prevent="sendMessage" class="mt-4 flex items-center gap-2 w-full">
                        <input
                            wire:model.defer="newMessage"
                            type="text"
                            placeholder="اكتب رسالتك..."
                            class="flex-1 rounded border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-black dark:text-white focus:ring focus:ring-blue-200"
                        />
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                            إرسال
                        </button>
                    </form>
                @else
                    <div class="text-center text-gray-500 dark:text-gray-400 mt-4">
                        اختر محادثة لعرض الرسائل.
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-filament::page>
