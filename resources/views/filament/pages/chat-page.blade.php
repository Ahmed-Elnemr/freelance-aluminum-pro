<x-filament::page>
    <div x-data="chatUi()"
        class="conversation-container flex flex-col md:flex-row w-full h-screen md:h-[calc(100vh-120px)] text-white rounded-xl overflow-hidden border shadow-2xl"
        style="background-color: #0b141a; border-color: #0f171c;" x-init="initHooks()">

        {{-- قائمة المحادثات --}}
        <div class="conversation-sidebar w-full md:w-80 lg:w-96 flex-shrink-0 flex flex-col h-full md:relative fixed inset-y-0 left-0 z-40 md:z-0 max-h-full transition-transform duration-300 ease-in-out"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'"
            style="background-color: #111b21; border-right: 1px solid #0f171c;">

            {{-- Header للمحادثات في الموبايل --}}
            <div class="flex items-center justify-between px-4 py-3 border-b md:hidden" style="border-color: #0f171c;">
                <div class="text-gray-300 text-sm font-medium">المحادثات</div>
                <button @click="sidebarOpen = false" class="p-1 rounded-lg text-gray-400 hover:text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- عنوان الشريط في الشاشات الكبيرة --}}
            <div class="hidden md:block px-4 py-3 text-gray-300 text-xs uppercase tracking-wide border-b"
                style="border-color: #0f171c;">
                المحادثات
            </div>

            {{-- مربع البحث --}}
            <div class="px-3 py-3 border-b" style="border-color: #0f171c;">
                <div class="flex items-center gap-2 rounded-lg px-3 py-2" style="background-color: #202c33;">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400 flex-shrink-0" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 105.64 5.64a7.5 7.5 0 0010.01 10.01z" />
                    </svg>
                    <input type="text" placeholder="بحث عن عميل..." wire:model.debounce.300ms="search"
                        class="w-full bg-transparent text-sm text-gray-200 placeholder:text-gray-500 focus:outline-none" />
                </div>
            </div>

            {{-- قائمة المحادثات --}}
            <div class="flex-1 overflow-y-auto">
                @forelse ($conversations as $conversation)
                    <div
                        wire:key="conversation-{{ $conversation->id }}"
                        @click="selectConversation({{ $conversation->id }})"
                        class="flex items-center gap-3 px-3 sm:px-4 py-3 cursor-pointer transition border-b border-[#0f171c]/50 hover:bg-[#2a3942]"
                        :class="{
                            'bg-[#202c33]': {{ $selectedConversationId }} === {{ $conversation->id }},
                            'md:opacity-100': {{ $selectedConversationId }} === {{ $conversation->id }},
                            'md:opacity-90 md:hover:opacity-100': {{ $selectedConversationId }} !== {{ $conversation->id }}
                        }">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-2">
                                <span class="font-semibold text-white text-sm truncate">
                                    {{ $conversation->client?->name ?? 'غير متوفر' }}
                                </span>
                                <span class="text-[10px] sm:text-[11px] text-gray-400 whitespace-nowrap">
                                    {{ $conversation->last_message_at?->diffForHumans() }}
                                </span>
                            </div>
                            <div class="text-xs text-gray-300 mt-1 truncate">
                                المسؤول: {{ $conversation->admin?->name ?? 'غير متوفر' }}
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-gray-400 text-sm">
                        لا توجد محادثات
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Overlay للموبايل --}}
        <div x-show="sidebarOpen && !isDesktop" x-transition.opacity
            class="fixed inset-0 bg-black/70 z-30 md:hidden"
            @click="sidebarOpen = false"></div>

        {{-- زر عام لفتح/إغلاق القائمة في الموبايل --}}
        <button type="button"
            class="md:hidden fixed bottom-4 left-4 z-50 h-12 w-12 rounded-full bg-[#00a884] text-white flex items-center justify-center shadow-lg ring-2 ring-white/60"
            @click="sidebarOpen = !sidebarOpen" x-cloak x-show="!sidebarOpen">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>

        {{-- منطقة الرسائل الرئيسية --}}
        <div class="flex-1 flex flex-col h-full min-h-0" style="background-color: #0b141a;">
            @if ($selectedConversation)
                {{-- Header الرسائل --}}
                <div class="flex items-center justify-between px-4 sm:px-6 py-3 border-b"
                    style="background-color: #111b21; border-color: #0f171c;">
                    <div class="flex items-center gap-3">
                        <button type="button"
                            class="md:hidden p-2 rounded-lg bg-[#202c33] text-gray-200 hover:bg-[#2a3942]"
                            @click="sidebarOpen = true">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                        <div>
                            <div
                                class="text-base sm:text-lg font-semibold text-white truncate max-w-[150px] sm:max-w-none">
                                {{ $selectedConversation->client->name }}
                            </div>
                            <div class="text-xs text-gray-400">
                                المسؤول: {{ $selectedConversation->admin?->name ?? 'غير متوفر' }}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- منطقة الرسائل --}}
                <div class="flex-1 overflow-hidden flex flex-col">
                    <div class="flex-1 overflow-y-auto px-2 sm:px-3 md:px-4 py-3 space-y-2 scroll-smooth min-h-0"
                        style="background-color: #0b141a;" x-ref="messageContainer" x-init="initScroll()"
                        @scroll.passive="onScroll">

                        @if ($messages->count() >= $perPage)
                            <div class="flex justify-center px-2">
                                <button type="button" wire:click="loadMoreMessages" @click.prevent="prepareLoadOlder"
                                    class="text-xs text-gray-300 hover:text-white px-3 py-1.5 rounded-full transition min-w-[100px]"
                                    style="background-color: #1c2a32;"
                                    onmouseover="this.style.backgroundColor='#2a3942'"
                                    onmouseout="this.style.backgroundColor='#1c2a32'">
                                    عرض رسائل أقدم
                                </button>
                            </div>
                        @endif

                        @foreach ($messages as $message)
                            <div
                                class="flex {{ $message->sender_id === auth()->id() ? 'justify-end' : 'justify-start' }} px-1 sm:px-2">
                                <div class="max-w-[90%] sm:max-w-[80%] rounded-2xl px-3 py-2 shadow-sm relative overflow-hidden space-y-1"
                                    style="background-color: {{ $message->sender_id === auth()->id() ? '#005c4b' : '#202c33' }}; color: #ffffff;">
                                    <div class="text-[11px] sm:text-[12px] text-gray-200/90">
                                        {{ $message->sender->name }}
                                    </div>
                                    <div class="text-sm sm:text-base leading-relaxed break-words">
                                        {{ $message->message }}
                                    </div>

                                    @foreach ($message->attachments as $attachment)
                                        @if (str_starts_with($attachment->mime_type, 'image/'))
                                            <img src="{{ $attachment->file_path }}"
                                                class="mt-1 sm:mt-2 max-w-full sm:max-w-[200px] md:max-w-[250px] h-auto rounded-lg object-cover cursor-pointer border hover:opacity-90 transition"
                                                style="border-color: #1c2a32;"
                                                @click="openPreview('{{ $attachment->file_path }}')"
                                                loading="lazy" />
                                        @elseif(str_starts_with($attachment->mime_type, 'video/'))
                                            <video controls
                                                class="mt-1 sm:mt-2 w-full max-w-full sm:max-w-[300px] md:max-w-[350px] rounded-lg border bg-black"
                                                style="border-color: #1c2a32;">
                                                <source src="{{ $attachment->file_path }}"
                                                    type="{{ $attachment->mime_type }}">
                                            </video>
                                        @elseif(str_starts_with($attachment->mime_type, 'audio/'))
                                            <audio controls class="mt-1 sm:mt-2 w-full rounded-lg border"
                                                style="border-color: #1c2a32; background-color: #111b21;">
                                                <source src="{{ $attachment->file_path }}"
                                                    type="{{ $attachment->mime_type }}">
                                            </audio>
                                        @endif
                                    @endforeach

                                    <div class="text-[9px] sm:text-[10px] text-gray-200/80 mt-0.5 sm:mt-1 flex justify-end">
                                        {{ $message->created_at->diffForHumans() }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- شريط الإدخال --}}
                    <div class="px-2 sm:px-3 md:px-4 py-2 border-t sticky bottom-0"
                        style="background-color: #111b21; border-color: #0f171c;">
                        <form wire:submit.prevent="sendMessage" class="flex items-center gap-1 sm:gap-2">

                            {{-- زر المرفقات --}}
                            <label for="attachments"
                                class="flex items-center justify-center h-10 w-10 sm:h-11 sm:w-11 md:h-12 md:w-12 rounded-full text-gray-300 cursor-pointer transition flex-shrink-0"
                                style="background-color: #202c33;" onmouseover="this.style.backgroundColor='#2a3942'"
                                onmouseout="this.style.backgroundColor='#202c33'">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:h-5 sm:w-5 md:h-6 md:w-6" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M21 12.75V6.75A2.25 2.25 0 0018.75 4.5h-7.5A2.25 2.25 0 009 6.75v10.5a3.75 3.75 0 007.5 0V7.5" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M12 11.25V6.75A2.25 2.25 0 009.75 4.5H6.75A2.25 2.25 0 004.5 6.75v10.5a3.75 3.75 0 007.5 0v-4.5" />
                                </svg>
                            </label>
                            <input id="attachments" type="file" wire:model="attachments" multiple
                                class="hidden" />

                            {{-- زر الإيموجي --}}
                            <button type="button"
                                class="flex items-center justify-center h-10 w-10 sm:h-11 sm:w-11 md:h-12 md:w-12 rounded-full text-gray-300 transition flex-shrink-0"
                                style="background-color: #202c33;" onmouseover="this.style.backgroundColor='#2a3942'"
                                onmouseout="this.style.backgroundColor='#202c33'"
                                @click.prevent="emojiOpen = !emojiOpen">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:h-5 sm:w-5 md:h-6 md:w-6" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M12 21a9 9 0 100-18 9 9 0 000 18z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M9 10h.01M15 10h.01M9 15s1.5 1.5 3 1.5S15 15 15 15" />
                                </svg>
                            </button>

                            {{-- حقل الإدخال مع الإيموجي --}}
                            <div class="relative flex-1 flex items-center rounded-full px-2 sm:px-3 py-1.5 sm:py-2 border border-transparent min-w-0"
                                style="background-color: #202c33;" onfocusin="this.style.borderColor='#00a884'"
                                onfocusout="this.style.borderColor='transparent'">
                                <input x-ref="messageInput" wire:model.defer="newMessage" type="text"
                                    placeholder="اكتب رسالتك..."
                                    class="flex-1 bg-transparent text-white placeholder:text-gray-400 focus:outline-none text-sm sm:text-base min-w-0" />
                                <div x-show="emojiOpen" x-transition @click.outside="emojiOpen = false"
                                    class="absolute bottom-12 sm:bottom-14 right-0 z-20 bg-[#202c33] border border-[#0f171c] rounded-lg p-2 grid grid-cols-6 gap-1 shadow-lg max-w-[200px] sm:max-w-[250px] md:max-w-none overflow-y-auto max-h-[150px] sm:max-h-[200px]">
                                    <template x-for="emoji in emojis" :key="emoji">
                                        <button type="button"
                                            class="text-base sm:text-lg md:text-xl hover:scale-110 transition w-5 h-5 sm:w-6 sm:h-6 md:w-7 md:h-7 flex items-center justify-center"
                                            @click.prevent="appendEmoji(emoji)" x-text="emoji"></button>
                                    </template>
                                </div>
                            </div>

                            {{-- زر الإرسال --}}
                            <button type="submit"
                                class="h-10 w-10 sm:h-11 sm:w-11 md:h-12 md:w-12 rounded-full text-white flex items-center justify-center shadow-lg transition flex-shrink-0"
                                style="background-color: #00a884;" onmouseover="this.style.backgroundColor='#02926e'"
                                onmouseout="this.style.backgroundColor='#00a884'">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-4 sm:w-4 md:h-5 md:w-5"
                                    fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M2.94 2.94a.75.75 0 01.79-.18l13 5a.75.75 0 010 1.38l-13 5A.75.75 0 012 13.5V10l8-1-8-1V3.5a.75.75 0 01.94-.56z" />
                                </svg>
                            </button>
                        </form>

                        {{-- زر تسجيل الصوت للموبايل --}}
                        <div class="flex justify-center mt-2 sm:hidden">
                            <button type="button"
                                class="h-10 w-full max-w-[200px] rounded-full text-gray-100 flex items-center justify-center shadow-sm transition"
                                :class="recording || recordedUrl ? 'bg-red-600 hover:bg-red-700' : 'bg-[#202c33] hover:bg-[#2a3942]'"
                                :disabled="uploadingVoice" @click.prevent="recording ? stopRecordingManual() : startRecording()">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 2a2 2 0 00-2 2v5a2 2 0 104 0V4a2 2 0 00-2-2z" />
                                    <path fill-rule="evenodd"
                                        d="M5.5 8a.75.75 0 00-1.5 0 6 6 0 005 5.917V16.5H7a.75.75 0 000 1.5h6a.75.75 0 000-1.5h-2V13.917A6 6 0 0016 8a.75.75 0 00-1.5 0 4.5 4.5 0 11-9 0z"
                                        clip-rule="evenodd" />
                                </svg>
                                <span class="text-sm" x-text="recording ? 'جارٍ التسجيل...' : (recordedUrl ? 'معاينة' : 'تسجيل صوتي')"></span>
                            </button>
                        </div>

                        {{-- حالة التسجيل للموبايل --}}
                        <template x-if="recording && !isDesktop">
                            <div
                                class="mt-2 flex items-center justify-between text-sm text-red-200 bg-[#202c33] p-2 rounded-lg">
                                <div class="flex items-center gap-2">
                                    <span class="h-2 w-2 rounded-full bg-red-500 animate-pulse"></span>
                                    <span class="text-xs">جارٍ التسجيل...</span>
                                </div>
                                <div class="flex gap-1">
                                    <button type="button"
                                        class="px-2 py-1 rounded text-xs bg-red-600 hover:bg-red-700 text-white"
                                        @click.prevent="stopRecordingManual">
                                        إيقاف
                                    </button>
                                    <button type="button"
                                        class="px-2 py-1 rounded text-xs bg-gray-600 hover:bg-gray-700 text-white"
                                        @click.prevent="cancelRecording">
                                        إلغاء
                                    </button>
                                </div>
                            </div>
                        </template>

                        {{-- معاينة التسجيل للموبايل --}}
                        <template x-if="recordedUrl && !isDesktop">
                            <div
                                class="mt-2 flex flex-col gap-2 text-sm text-gray-200 bg-[#202c33] p-3 rounded-lg">
                                <audio controls :src="recordedUrl"
                                    class="w-full rounded border border-[#1c2a32] bg-[#111b21]"></audio>
                                <div class="flex gap-2">
                                    <button type="button"
                                        class="flex-1 px-3 py-2 rounded bg-[#00a884] hover:bg-[#02926e] text-white text-sm disabled:opacity-60 disabled:cursor-not-allowed"
                                        :disabled="uploadingVoice" @click.prevent="sendRecording">
                                        <span x-show="!uploadingVoice">إرسال</span>
                                        <span x-show="uploadingVoice">جاري الإرسال...</span>
                                    </button>
                                    <button type="button"
                                        class="px-3 py-2 rounded bg-gray-600 hover:bg-gray-700 text-white text-sm"
                                        @click.prevent="cancelRecording">
                                        حذف
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            @else
                {{-- حالة عدم وجود محادثة مختارة --}}
                <div class="flex flex-1 flex-col items-center justify-center text-gray-400 px-4 py-8 text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-14 w-14 sm:h-16 sm:w-16 mb-4 opacity-50" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                    <p class="text-base sm:text-lg mb-2">اختر محادثة لعرض الرسائل</p>
                    <p class="text-xs sm:text-sm opacity-75">من القائمة الجانبية، اختر محادثة للبدء</p>
                    <button @click="sidebarOpen = true"
                        class="mt-4 md:hidden px-4 py-2 rounded-lg bg-[#00a884] text-white text-sm">
                        عرض المحادثات
                    </button>
                </div>
            @endif
        </div>

        {{-- معاينة الصور --}}
        <div x-show="previewOpen" x-transition
            class="fixed inset-0 bg-black/90 flex items-center justify-center z-50 p-2 sm:p-4" @click="closePreview">
            <div class="relative max-w-full max-h-full">
                <button @click="closePreview" class="absolute -top-8 sm:-top-10 right-0 text-white p-1 sm:p-2 hover:text-gray-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:h-6 sm:w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                <img :src="previewSrc"
                    class="max-h-[70vh] sm:max-h-[80vh] max-w-full rounded-lg border border-[#1c2a32] object-contain" />
            </div>
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
                loadingOlder: false,
                prevHeight: 0,
                prevTop: 0,
                initialScrollNeeded: true,
                lastConversationId: null,
                currentConversationId: null,
                emojiOpen: false,
                emojis: ['😀', '😁', '😂', '🤣', '😊', '😍', '😘', '😎', '😇', '🤩', '👍', '🙏', '🎉', '❤️', '🔥', '✨',
                    '👏', '🤔', '😢', '🥳', '🤝', '✅', '⚡', '⭐', '🍀', '☕', '🚀'
                ],
                recordedUrl: null,
                recordedBlob: null,
                recordTimer: '00:00',
                timerInterval: null,
                stream: null,
                uploadingVoice: false,
                sidebarOpen: false,
                isDesktop: false,
                echoChannel: null,
                channelName: null,
                userId: {{ auth()->id() }},

                initHooks() {
                    this.currentConversationId = this.$wire.get('selectedConversationId');

                    // عند تغيير المحادثة من Livewire
                    Livewire.hook('message.processed', ({
                        component
                    }) => {
                        const container = this.$refs.messageContainer;
                        const currentId = component.get('selectedConversationId');

                        this.currentConversationId = currentId;
                        this.setupEcho();

                        if (!container) {
                            this.lastConversationId = currentId;
                            return;
                        }

                        if (this.lastConversationId !== currentId) {
                            this.lastConversationId = currentId;
                            this.initialScrollNeeded = true;
                        }

                        if (this.loadingOlder) {
                            this.loadingOlder = false;
                            requestAnimationFrame(() => {
                                const newHeight = container.scrollHeight;
                                container.scrollTop = newHeight - this.prevHeight + this.prevTop;
                            });
                            return;
                        }

                        if (this.initialScrollNeeded) {
                            this.initialScrollNeeded = false;
                            requestAnimationFrame(() => {
                                container.scrollTop = container.scrollHeight;
                            });
                        }
                    });

                    this.initResponsive();
                    this.detectTouchDevice();
                    this.setupEcho();
                },

                initResponsive() {
                    const checkDesktop = () => {
                        this.isDesktop = window.innerWidth >= 768;
                        if (this.isDesktop) {
                            this.sidebarOpen = true; // الشريط مفتوح دائمًا في الشاشات الكبيرة
                        } else {
                            this.sidebarOpen = false; // الشريط مخفي افتراضيًا في الموبايل
                        }
                    };

                    checkDesktop();
                    window.addEventListener('resize', checkDesktop);
                },

                detectTouchDevice() {
                    if ('ontouchstart' in window || navigator.maxTouchPoints) {
                        document.documentElement.classList.add('touch-device');
                    } else {
                        document.documentElement.classList.add('no-touch-device');
                    }
                },

                // الدالة الجديدة لاختيار المحادثة
                selectConversation(id) {
                    console.log('Selecting conversation:', id);

                    // حفظ ID المحادثة الحالية
                    this.currentConversationId = id;

                    // في الموبايل فقط نقوم بإغلاق الشريط
                    if (!this.isDesktop) {
                        this.sidebarOpen = false;
                    }

                    // استدعاء دالة Livewire لتحميل المحادثة
                    if (this.$wire && typeof this.$wire.call === 'function') {
                        console.log('Calling Livewire showConversation');
                        this.$wire.call('showConversation', id)
                            .then(() => {
                                console.log('Livewire call successful');
                                this.initialScrollNeeded = true;

                                // انتظر حتى يتم تحديث DOM
                                this.$nextTick(() => {
                                    setTimeout(() => {
                                        this.scrollToBottom();
                                    }, 200);
                                });
                            })
                            .catch(error => {
                                console.error('Livewire call failed:', error);
                            });
                    } else {
                        console.error('Livewire not available');
                    }
                },

                initScroll() {
                    const container = this.$refs.messageContainer;
                    if (container) {
                        setTimeout(() => {
                            container.scrollTop = container.scrollHeight;
                        }, 100);
                    }
                },

                onScroll(event) {
                    const container = event.target;
                    if (container.scrollTop <= 10 && !this.loadingOlder) {
                        this.prepareLoadOlder();
                        this.$wire.call('loadMoreMessages');
                    }
                },

                prepareLoadOlder() {
                    if (this.loadingOlder) return;
                    const container = this.$refs.messageContainer;
                    this.loadingOlder = true;
                    this.prevHeight = container.scrollHeight;
                    this.prevTop = container.scrollTop;
                },

                openPreview(src) {
                    this.previewSrc = src;
                    this.previewOpen = true;
                    document.body.style.overflow = 'hidden';
                },

                closePreview() {
                    this.previewOpen = false;
                    this.previewSrc = '';
                    document.body.style.overflow = '';
                },

                appendEmoji(emoji) {
                    const current = this.$wire.get('newMessage') ?? '';
                    this.$wire.set('newMessage', current + emoji);
                    this.emojiOpen = false;
                    this.$nextTick(() => {
                        this.$refs.messageInput?.focus();
                    });
                },

                async startRecording() {
                    if (this.recording || this.recordedUrl) {
                        return;
                    }

                    try {
                        this.stream = await navigator.mediaDevices.getUserMedia({
                            audio: {
                                echoCancellation: true,
                                noiseSuppression: true,
                                sampleRate: 44100
                            }
                        });

                        this.chunks = [];
                        this.mediaRecorder = new MediaRecorder(this.stream);
                        this.mediaRecorder.ondataavailable = e => this.chunks.push(e.data);
                        this.mediaRecorder.onstop = () => {
                            if (!this.chunks.length) return;
                            const blob = new Blob(this.chunks, {
                                type: 'audio/webm'
                            });
                            this.recordedBlob = blob;
                            this.recordedUrl = URL.createObjectURL(blob);
                            this.stopTimer();
                            this.stopStream();
                            this.recording = false;
                        };

                        this.mediaRecorder.start(100);
                        this.recording = true;
                        this.startTimer();
                    } catch (e) {
                        console.error('Microphone access denied', e);
                        alert('يرجى السماح بالوصول إلى الميكروفون');
                        this.stopStream();
                        this.stopTimer();
                        this.recording = false;
                    }
                },

                stopRecordingManual() {
                    if (this.mediaRecorder && this.recording) {
                        this.mediaRecorder.stop();
                    }
                },

                cancelRecording() {
                    if (this.recording && this.mediaRecorder?.state === 'recording') {
                        this.mediaRecorder.stop();
                    }
                    this.stopStream();
                    this.stopTimer();
                    this.recording = false;
                    this.recordedBlob = null;
                    this.recordedUrl = null;
                    this.chunks = [];
                },

                startTimer() {
                    this.recordTimer = '00:00';
                    const start = Date.now();
                    this.stopTimer();
                    this.timerInterval = setInterval(() => {
                        const diff = Math.floor((Date.now() - start) / 1000);
                        const m = String(Math.floor(diff / 60)).padStart(2, '0');
                        const s = String(diff % 60).padStart(2, '0');
                        this.recordTimer = `${m}:${s}`;
                    }, 1000);
                },

                stopTimer() {
                    if (this.timerInterval) {
                        clearInterval(this.timerInterval);
                        this.timerInterval = null;
                    }
                },

                stopStream() {
                    if (this.stream) {
                        this.stream.getTracks().forEach(t => t.stop());
                        this.stream = null;
                    }
                },

                sendRecording() {
                    if (!this.recordedBlob || this.uploadingVoice) return;

                    this.uploadingVoice = true;
                    const file = new File([this.recordedBlob], `voice-${Date.now()}.webm`, {
                        type: 'audio/webm'
                    });

                    this.$wire.upload(
                        'attachments',
                        file,
                        () => {
                            this.$wire.call('sendMessage').then(() => {
                                this.uploadingVoice = false;
                                this.cancelRecording();
                            }).catch(() => {
                                this.uploadingVoice = false;
                            });
                        },
                        () => {
                            this.uploadingVoice = false;
                        }
                    );
                },

                setupEcho() {
                    if (!window.Echo || !this.userId) {
                        return;
                    }

                    const name = `chat.${this.userId}`;

                    if (this.channelName === name && this.echoChannel) {
                        return;
                    }

                    if (this.channelName && window.Echo) {
                        window.Echo.leave(this.channelName);
                    }

                    this.channelName = name;
                    this.echoChannel = window.Echo.private(name)
                        .stopListening('.chat_message')
                        .listen('.chat_message', (payload) => {
                            if (!payload || !this.currentConversationId) {
                                return;
                            }

                            if (payload.conversation_id === this.currentConversationId) {
                                this.$wire.call('loadMessages').then(() => {
                                    this.$nextTick(() => this.scrollToBottom());
                                });
                            }
                        });
                },

                scrollToBottom() {
                    const container = this.$refs.messageContainer;
                    if (container) {
                        setTimeout(() => {
                            container.scrollTop = container.scrollHeight;
                        }, 50);
                    }
                }
            };
        }
    </script>
</x-filament::page>
