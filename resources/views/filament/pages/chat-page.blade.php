<x-filament::page>
    <div
        x-data="chatUi()"
        class="flex h-[calc(100vh-120px)] text-white rounded-xl overflow-hidden border shadow-2xl"
        style="background-color: #0b141a; border-color: #0f171c;"
    >
        {{-- قائمة المحادثات --}}
        <div class="w-80 md:w-96 flex flex-col" style="background-color: #111b21; border-right: 1px solid #0f171c;">
            <div class="px-4 py-3 text-gray-300 text-xs uppercase tracking-wide border-b" style="border-color: #0f171c;">
                المحادثات
            </div>
            <div class="px-3 py-2 border-b" style="border-color: #0f171c;">
                <div class="flex items-center gap-2 rounded-lg px-3 py-2" style="background-color: #202c33;">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 105.64 5.64a7.5 7.5 0 0010.01 10.01z" />
                    </svg>
                    <input
                        type="text"
                        placeholder="بحث عن عميل..."
                        wire:model.debounce.300ms="search"
                        class="w-full bg-transparent text-sm text-gray-200 placeholder:text-gray-500 focus:outline-none"
                    />
                </div>
            </div>
            <div class="flex-1 overflow-y-auto">
                @foreach ($conversations as $conversation)
                    <div
                        wire:click="showConversation({{ $conversation->id }})"
                        class="flex items-center gap-3 px-4 py-3 cursor-pointer transition"
                        style="background-color: {{ $selectedConversationId === $conversation->id ? '#202c33' : 'transparent' }};"
                        onmouseover="this.style.backgroundColor='{{ $selectedConversationId === $conversation->id ? '#202c33' : '#2a3942' }}'"
                        onmouseout="this.style.backgroundColor='{{ $selectedConversationId === $conversation->id ? '#202c33' : 'transparent' }}'"
                    >
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-2">
                                <span class="font-semibold text-white truncate">
                                    {{ $conversation->client?->name ?? 'غير متوفر' }}
                                </span>
                                <span class="text-[11px] text-gray-400">
                                    {{ $conversation->last_message_at?->diffForHumans() }}
                                </span>
                            </div>
                            <div class="text-xs text-gray-300 mt-1">
                                المسؤول: {{ $conversation->admin?->name ?? 'غير متوفر' }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- منطقة الرسائل --}}
        <div class="flex-1 flex flex-col" style="background-color: #0b141a;">
            @if ($selectedConversation)
                <div class="flex items-center justify-between px-6 py-4 border-b" style="background-color: #111b21; border-color: #0f171c;">
                    <div>
                        <div class="text-lg font-semibold text-white">
                            المحادثة مع {{ $selectedConversation->client->name }}
                        </div>
                        <div class="text-xs text-gray-400 mt-1">
                            المسؤول: {{ $selectedConversation->admin?->name ?? 'غير متوفر' }}
                        </div>
                    </div>
                </div>

                <div class="flex-1 overflow-hidden flex flex-col">
                    <div
                        class="flex-1 overflow-y-auto px-4 md:px-6 py-4 space-y-3 scroll-smooth"
                        style="background-color: #0b141a;"
                        x-ref="messageContainer"
                        x-init="initScroll()"
                    >
                        @if ($messages->count() >= $perPage)
                            <div class="flex justify-center">
                                <button wire:click="loadMoreMessages"
                                    class="text-xs text-gray-300 hover:text-white px-3 py-1 rounded-full transition"
                                    style="background-color: #1c2a32;"
                                    onmouseover="this.style.backgroundColor='#2a3942'"
                                    onmouseout="this.style.backgroundColor='#1c2a32'"
                                >
                                    عرض رسائل أقدم
                                </button>
                            </div>
                        @endif

                        @foreach ($messages as $message)
                            <div class="flex {{ $message->sender_id === auth()->id() ? 'justify-end' : 'justify-start' }}">
                                <div
                                    class="max-w-[75%] rounded-2xl px-3 py-2 shadow-sm relative overflow-hidden space-y-2"
                                    style="background-color: {{ $message->sender_id === auth()->id() ? '#005c4b' : '#202c33' }}; color: #ffffff;"
                                >
                                    <div class="text-[12px] text-gray-200/90">
                                        {{ $message->sender->name }}
                                    </div>
                                    <div class="text-sm leading-relaxed">
                                        {{ $message->message }}
                                    </div>

                                    @foreach ($message->attachments as $attachment)
                                        @if (str_starts_with($attachment->mime_type, 'image/'))
                                            <img src="{{ $attachment->file_path }}"
                                                class="mt-1 max-w-[250px] h-auto rounded-lg object-cover cursor-pointer border"
                                                style="border-color: #1c2a32;"
                                                @click="openPreview('{{ $attachment->file_path }}')" />
                                        @elseif(str_starts_with($attachment->mime_type, 'video/'))
                                            <video controls
                                                class="mt-1 w-full max-w-[350px] rounded-lg border bg-black"
                                                style="border-color: #1c2a32;"
                                            >
                                                <source src="{{ $attachment->file_path }}" type="{{ $attachment->mime_type }}">
                                            </video>
                                        @elseif(str_starts_with($attachment->mime_type, 'audio/'))
                                            <audio controls
                                                class="mt-1 w-full rounded-lg border"
                                                style="border-color: #1c2a32; background-color: #111b21;"
                                            >
                                                <source src="{{ $attachment->file_path }}" type="{{ $attachment->mime_type }}">
                                            </audio>
                                        @endif
                                    @endforeach

                                    <div class="text-[10px] text-gray-200/80 mt-1 flex justify-end">
                                        {{ $message->created_at->diffForHumans() }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- شريط الإدخال --}}
                    <div class="px-4 md:px-6 py-4 border-t sticky bottom-0" style="background-color: #111b21; border-color: #0f171c;">
                        <form wire:submit.prevent="sendMessage" class="flex items-center gap-3">
                            <label for="attachments"
                                class="flex items-center justify-center h-12 w-12 rounded-full text-gray-300 cursor-pointer transition"
                                style="background-color: #202c33;"
                                onmouseover="this.style.backgroundColor='#2a3942'"
                                onmouseout="this.style.backgroundColor='#202c33'"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M21 12.75V6.75A2.25 2.25 0 0018.75 4.5h-7.5A2.25 2.25 0 009 6.75v10.5a3.75 3.75 0 007.5 0V7.5" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M12 11.25V6.75A2.25 2.25 0 009.75 4.5H6.75A2.25 2.25 0 004.5 6.75v10.5a3.75 3.75 0 007.5 0v-4.5" />
                                </svg>
                            </label>
                            <input id="attachments" type="file" wire:model="attachments" multiple class="hidden" />

                            <button type="button"
                                class="flex items-center justify-center h-12 w-12 rounded-full text-gray-300 transition"
                                style="background-color: #202c33;"
                                onmouseover="this.style.backgroundColor='#2a3942'"
                                onmouseout="this.style.backgroundColor='#202c33'"
                                @click="toggleEmojiPicker">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M12 21a9 9 0 100-18 9 9 0 000 18z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M9 10h.01M15 10h.01M9 15s1.5 1.5 3 1.5S15 15 15 15" />
                                </svg>
                            </button>

                            <div
                                class="flex-1 flex items-center rounded-full px-4 py-2 border border-transparent"
                                style="background-color: #202c33;"
                                onfocusin="this.style.borderColor='#00a884'"
                                onfocusout="this.style.borderColor='transparent'"
                            >
                                <input wire:model.defer="newMessage" type="text" placeholder="اكتب رسالتك..."
                                    class="flex-1 bg-transparent text-white placeholder:text-gray-400 focus:outline-none" />
                            </div>

                            <button type="button"
                                class="h-12 w-12 rounded-full text-gray-300 flex items-center justify-center shadow-sm transition"
                                style="background-color: #202c33;"
                                onmouseover="this.style.backgroundColor='#2a3942'"
                                onmouseout="this.style.backgroundColor='#202c33'"
                                @click="toggleRecording" :class="recording ? 'animate-pulse text-[#00a884]' : ''">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="currentColor"
                                    viewBox="0 0 20 20">
                                    <path d="M10 2a2 2 0 00-2 2v5a2 2 0 104 0V4a2 2 0 00-2-2z" />
                                    <path fill-rule="evenodd"
                                        d="M5.5 8a.75.75 0 00-1.5 0 6 6 0 005 5.917V16.5H7a.75.75 0 000 1.5h6a.75.75 0 000-1.5h-2V13.917A6 6 0 0016 8a.75.75 0 00-1.5 0 4.5 4.5 0 11-9 0z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>

                            <button type="submit"
                                class="h-12 w-12 rounded-full text-white flex items-center justify-center shadow-lg transition"
                                style="background-color: #00a884;"
                                onmouseover="this.style.backgroundColor='#02926e'"
                                onmouseout="this.style.backgroundColor='#00a884'"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor"
                                    viewBox="0 0 20 20">
                                    <path
                                        d="M2.94 2.94a.75.75 0 01.79-.18l13 5a.75.75 0 010 1.38l-13 5A.75.75 0 012 13.5V10l8-1-8-1V3.5a.75.75 0 01.94-.56z" />
                                </svg>
                            </button>
                        </div>
                    </form>
                </div>
            @else
                <div class="flex flex-1 items-center justify-center text-gray-400">
                    اختر محادثة لعرض الرسائل.
                </div>
            @endif
        </div>

        {{-- معاينة الصور --}}
        <div x-show="previewOpen" x-transition
            class="fixed inset-0 bg-black/80 flex items-center justify-center z-50" @click="closePreview">
            <img :src="previewSrc" class="max-h-[80vh] max-w-[80vw] rounded-lg border border-[#1c2a32]" />
        </div>
    </div>

    <script>
        function chatUi() {
            return {
                previewOpen: false,
                previewSrc: '',
                recording: false,
                mediaRecorder: null,
                chunks: [],
                initScroll() {
                    const container = this.$refs.messageContainer;
                    container.scrollTop = container.scrollHeight;
                    container.addEventListener('scroll', () => {
                        if (container.scrollTop <= 10) {
                            Livewire.dispatch('loadMoreMessages');
                        }
                    });
                },
                openPreview(src) {
                    this.previewSrc = src;
                    this.previewOpen = true;
                },
                closePreview() {
                    this.previewOpen = false;
                    this.previewSrc = '';
                },
                toggleEmojiPicker() {
                    // hook for emoji picker
                },
                async toggleRecording() {
                    if (this.recording) {
                        this.mediaRecorder.stop();
                        this.recording = false;
                        return;
                    }
                    try {
                        const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                        this.chunks = [];
                        this.mediaRecorder = new MediaRecorder(stream);
                        this.mediaRecorder.ondataavailable = e => this.chunks.push(e.data);
                        this.mediaRecorder.onstop = () => {
                            const blob = new Blob(this.chunks, { type: 'audio/webm' });
                            const file = new File([blob], `voice-${Date.now()}.webm`, { type: 'audio/webm' });
                            this.uploadAudio(file);
                            stream.getTracks().forEach(t => t.stop());
                        };
                        this.mediaRecorder.start();
                        this.recording = true;
                    } catch (e) {
                        console.error('Microphone access denied', e);
                    }
                },
                uploadAudio(file) {
                    if (!file) return;
                    this.$wire.upload('attachments', file, () => {}, () => {});
                },
            };
        }
    </script>
</x-filament::page>

