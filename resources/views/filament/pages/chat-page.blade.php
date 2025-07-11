<x-filament::page>
    <div class="flex flex-col md:flex-row gap-4">
        {{-- قائمة المحادثات --}}
        <div class="w-full md:w-1/4 space-y-2 border p-4 rounded-md flex-shrink-0 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 border-gray-300 dark:border-gray-700 max-h-screen overflow-y-auto">
            <h3 class="text-lg font-bold mb-2">المحادثات</h3>
            @foreach($conversations as $conversation)
                <div
                    wire:click="showConversation({{ $conversation->id }})"
                    class="cursor-pointer p-3 border rounded-md transition hover:bg-gray-100 dark:hover:bg-gray-700
                        {{ $selectedConversationId === $conversation->id ? 'bg-blue-100 dark:bg-blue-900 border-blue-400' : 'border-gray-300 dark:border-gray-700' }}"
                >
                    <div><strong>العميل:</strong> {{ $conversation->client?->name ?? 'غير متوفر' }}</div>
                    <div><strong>المسؤول:</strong> {{ $conversation->admin?->name ?? 'غير متوفر' }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">
                        {{ $conversation->last_message_at?->diffForHumans() }}
                    </div>
                </div>
            @endforeach
        </div>

        {{-- الرسائل --}}
        <div class="w-full md:w-3/4 flex flex-col space-y-4">
            @if($selectedConversation)
                <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100">
                    المحادثة مع {{ $selectedConversation->client->name }}
                </h2>

                <div
                    class="border p-4 rounded-md overflow-y-auto bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 border-gray-300 dark:border-gray-700 max-h-[500px] flex flex-col space-y-2"
                    x-data
                    x-init="
                        const container = $el;
                        container.scrollTop = container.scrollHeight;

                        container.addEventListener('scroll', function () {
                            if (container.scrollTop <= 10) {
                                Livewire.dispatch('loadMoreMessages');
                            }
                        });
                    "
                >
                    {{-- زر عرض المزيد فوق الرسائل --}}
                    @if($messages->count() >= $perPage)
                        <div class="text-center mb-2">
                            <button wire:click="loadMoreMessages" class="text-sm text-blue-600 hover:underline">
                                عرض المزيد من الرسائل
                            </button>
                        </div>
                    @endif

                    {{-- الرسائل --}}
                    @foreach($messages as $message)
                        <div class="{{ $message->sender_id === auth()->id() ? 'text-right' : 'text-left' }}">
                            <div class="inline-block bg-white dark:bg-gray-700 text-black dark:text-white px-4 py-2 rounded shadow">
                                <strong>{{ $message->sender->name }}</strong>: {{ $message->message }}
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $message->created_at->diffForHumans() }}
                                </div>

                                {{-- المرفقات --}}
                                @foreach($message->attachments as $attachment)
                                    @if(str_starts_with($attachment->mime_type, 'image/'))
                                        <img src="{{ $attachment->file_path }}" class="mt-2 w-32 h-32 object-cover rounded-md" />
                                    @elseif(str_starts_with($attachment->mime_type, 'video/'))
                                        <video controls class="mt-2 w-full rounded-md">
                                            <source src="{{ $attachment->file_path }}" type="{{ $attachment->mime_type }}">
                                        </video>
                                    @elseif(str_starts_with($attachment->mime_type, 'audio/'))
                                        <audio controls class="mt-2 w-full rounded-md">
                                            <source src="{{ $attachment->file_path }}" type="{{ $attachment->mime_type }}">
                                        </audio>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- إدخال الرسائل --}}
                <form wire:submit.prevent="sendMessage" class="mt-4 flex items-center gap-2 w-full">
                    <input
                        wire:model.defer="newMessage"
                        type="text"
                        placeholder="اكتب رسالتك..."
                        class="flex-1 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-black dark:text-white focus:ring-2 focus:ring-blue-200 focus:outline-none py-2 px-4"
                    />
                    <input
                        type="file"
                        wire:model="attachments"
                        multiple
                        class="py-2 px-4 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-black dark:text-white focus:ring-2 focus:ring-blue-200 focus:outline-none"
                    />
                    <button
                        type="submit"
                        class="bg-blue-600 dark:bg-blue-500 text-white px-6 py-3 rounded-lg shadow-md hover:bg-blue-700 dark:hover:bg-blue-600 focus:ring-2 focus:ring-blue-400"
                    >
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
</x-filament::page>
