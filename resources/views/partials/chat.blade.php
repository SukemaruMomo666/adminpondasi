{{-- ===================== CHAT HUB PREMIUM (POTA AI & SELLER) ===================== --}}
{{-- Tombol Toggle Floating --}}
<button id="live-chat-toggle" class="fixed bottom-4 right-4 md:bottom-8 md:right-8 bg-zinc-950 text-white p-1.5 pr-5 md:pr-6 rounded-full shadow-[0_10px_40px_rgba(0,0,0,0.3)] hover:shadow-[0_15px_50px_rgba(37,99,235,0.4)] transition-all duration-300 z-[9998] flex items-center gap-2.5 md:gap-3 group outline-none hover:-translate-y-1 border border-zinc-800" onclick="toggleChatWindow()">
    <div class="bg-blue-600 w-11 h-11 md:w-12 md:h-12 rounded-full relative flex items-center justify-center shadow-inner">
        <div class="absolute inset-0 bg-blue-500 animate-pulse opacity-50 rounded-full"></div>
        <i class="fas fa-comments text-lg md:text-xl relative z-10 text-white group-hover:scale-110 transition-transform duration-300"></i>
        {{-- Dot Merah Dinamis: Muncul hanya jika ada unread_count --}}
        <div id="unread-global-dot" class="absolute top-0 right-0 w-3 h-3 bg-red-500 rounded-full border-2 border-zinc-950 animate-bounce shadow-sm z-20 hidden"></div>
    </div>
    <div class="flex flex-col text-left hidden sm:flex">
        <span class="font-black text-sm md:text-base tracking-wide text-white leading-tight">Pusat Obrolan</span>
        <span class="text-[10px] md:text-[11px] text-zinc-400 font-bold uppercase tracking-widest leading-tight">Tanya / Nego</span>
    </div>
</button>

{{-- Main Chat Window --}}
<div id="live-chat-window" class="fixed bottom-0 right-0 md:bottom-28 md:right-8 w-full h-[100dvh] md:w-[800px] md:h-[600px] bg-white md:rounded-2xl shadow-[0_20px_70px_rgba(0,0,0,0.3)] flex flex-col overflow-hidden z-[9999] transition-all duration-500 ease-[cubic-bezier(0.34,1.56,0.64,1)] opacity-0 translate-y-10 scale-95 pointer-events-none md:border md:border-zinc-200 origin-bottom-right hidden">

    {{-- Header Universal --}}
    <div class="bg-white border-b border-zinc-200 p-3 md:p-4 flex justify-between items-center shrink-0 z-30 shadow-sm relative">
        <div class="flex items-center gap-3 md:gap-4 flex-1">
            <div class="w-10 h-10 bg-zinc-950 text-white rounded-xl flex items-center justify-center shadow-md shrink-0 hidden md:flex">
                <i class="fas fa-comments text-lg"></i>
            </div>

            {{-- TAB SWITCHER --}}
            <div class="flex bg-zinc-100 rounded-xl p-1 relative z-20 w-[190px] md:w-[240px] shadow-inner">
                <div id="tab-slider" class="absolute top-1 bottom-1 left-1 w-[calc(50%-4px)] bg-white rounded-lg shadow-sm transition-transform duration-300 ease-out z-0"></div>

                <button onclick="switchChatTab('seller')" id="tab-btn-seller" class="flex-1 relative z-10 px-2 py-1.5 md:py-2 text-[10px] md:text-xs font-black uppercase tracking-widest rounded-lg transition-colors duration-300 outline-none flex items-center justify-center gap-1.5 text-zinc-900">
                    <i class="fas fa-store"></i> Penjual
                </button>
                <button onclick="switchChatTab('ai')" id="tab-btn-ai" class="flex-1 relative z-10 px-2 py-1.5 md:py-2 text-[10px] md:text-xs font-black uppercase tracking-widest rounded-lg transition-colors duration-300 outline-none flex items-center justify-center gap-1.5 text-zinc-400 hover:text-zinc-600">
                    <i class="fas fa-robot"></i> POTA AI
                </button>
            </div>
        </div>

        <div class="flex items-center gap-1">
            <button onclick="toggleFullScreen()" class="w-8 h-8 md:w-9 md:h-9 rounded-lg hover:bg-zinc-100 text-zinc-500 hover:text-zinc-900 transition-all outline-none items-center justify-center hidden sm:flex">
                <i id="icon-resize" class="fas fa-expand text-sm"></i>
            </button>
            <button onclick="toggleChatWindow()" class="w-8 h-8 md:w-9 md:h-9 rounded-lg hover:bg-red-50 hover:text-red-600 text-zinc-500 transition-all outline-none flex items-center justify-center">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>
    </div>

    {{-- KONTEN AREA (TAB CONTAINER) --}}
    <div class="relative flex-1 overflow-hidden bg-zinc-50">

        {{-- TAB 1: SELLER VIEW --}}
        <div id="view-seller" class="absolute inset-0 flex flex-row transition-all duration-500 ease-out opacity-100 translate-x-0 z-20">

            {{-- Kiri: Contact List --}}
            <div class="w-20 md:w-[280px] border-r border-zinc-200 bg-white flex flex-col shrink-0 z-10">
                <div class="p-3 border-b border-zinc-100 hidden md:block">
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-zinc-400 text-xs"></i>
                        <input type="text" id="sellerSearchInput" placeholder="Cari nama toko..." class="w-full bg-zinc-100 text-xs font-medium text-zinc-700 rounded-lg pl-8 pr-3 py-2.5 outline-none focus:ring-1 focus:ring-blue-500 transition-all">
                    </div>
                </div>

                <div id="seller-contact-list" class="flex-1 overflow-y-auto custom-scrollbar">
                    <div class="p-6 text-center text-zinc-400 flex flex-col items-center justify-center h-full">
                        <i class="fas fa-circle-notch fa-spin text-2xl mb-2"></i>
                        <span class="text-[10px] font-bold uppercase tracking-widest hidden md:block mt-2">Memuat...</span>
                    </div>
                </div>
            </div>

            {{-- Kanan: Chat Room --}}
            <div class="flex-1 bg-zinc-50/50 flex flex-col relative z-0">

                {{-- EMPTY STATE --}}
                <div id="seller-empty-state" class="absolute inset-0 flex flex-col items-center justify-center bg-white z-20 px-6 text-center transition-opacity duration-300">
                    <div class="w-32 h-32 md:w-40 md:h-40 bg-zinc-50 rounded-full flex items-center justify-center mb-6 relative">
                        <div class="absolute inset-0 bg-blue-500/5 rounded-full animate-ping"></div>
                        <i class="fas fa-comments-dollar text-5xl md:text-6xl text-zinc-300"></i>
                        <div class="absolute -bottom-2 -right-2 w-12 h-12 bg-white rounded-full flex items-center justify-center shadow-lg border border-zinc-100">
                            <i class="fas fa-store text-emerald-500 text-xl"></i>
                        </div>
                    </div>
                    <h3 class="text-xl md:text-2xl font-black text-zinc-800 mb-2 tracking-tight">Mulai Negosiasi B2B</h3>
                    <p class="text-xs md:text-sm text-zinc-500 max-w-xs font-medium leading-relaxed">Pilih kontak mitra di sebelah kiri untuk melihat pesan atau mengirim file material.</p>
                </div>

                {{-- ACTIVE CHAT --}}
                <div id="seller-active-chat" class="hidden flex-col h-full opacity-0 transition-opacity duration-300">

                    {{-- Header Chat Aktif --}}
                    <div class="bg-white px-4 py-3 border-b border-zinc-200 flex items-center justify-between shadow-sm shrink-0">
                        <div class="flex items-center gap-3">
                            <button onclick="closeActiveChatMobile()" class="md:hidden w-8 h-8 rounded-full bg-zinc-100 text-zinc-600 flex items-center justify-center outline-none">
                                <i class="fas fa-arrow-left"></i>
                            </button>
                            <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center font-black shadow-inner" id="active-store-avatar">TK</div>
                            <div>
                                <h4 class="font-black text-sm md:text-base text-zinc-900 tracking-tight" id="active-store-name">Nama Toko</h4>
                                <p class="text-[10px] text-zinc-500 flex items-center gap-1 font-bold"><span class="w-2 h-2 rounded-full bg-emerald-500 inline-block shadow-[0_0_5px_#10b981]"></span> Sedang Online</p>
                            </div>
                        </div>
                    </div>

                    {{-- Area Histori Chat --}}
                    <div id="seller-chat-messages" class="flex-1 p-4 md:p-6 overflow-y-auto custom-scrollbar flex flex-col gap-4 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] bg-zinc-50 relative">
                        {{-- Isi Chat di-render JS --}}
                    </div>

                    {{-- Animasi Typing Indicator --}}
                    <div id="typing-indicator" class="hidden px-6 py-2 pb-4 border-t border-transparent bg-zinc-50">
                        <div class="flex gap-2.5 max-w-[85%] self-start items-start origin-bottom-left animate-[scale-in_0.2s_ease-out]">
                            <div class="w-8 h-8 rounded-full bg-zinc-200 shrink-0 flex items-center justify-center text-zinc-500 text-xs mt-auto shadow-sm"><i class="fas fa-store"></i></div>
                            <div class="bg-white border border-zinc-200 p-3.5 rounded-2xl rounded-tl-sm shadow-sm flex items-center gap-1.5 h-10">
                                <span class="w-1.5 h-1.5 bg-zinc-400 rounded-full animate-bounce"></span>
                                <span class="w-1.5 h-1.5 bg-zinc-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></span>
                                <span class="w-1.5 h-1.5 bg-zinc-400 rounded-full animate-bounce" style="animation-delay: 0.4s"></span>
                            </div>
                        </div>
                    </div>

                    {{-- Alat Kirim (Teks, Gambar, File, Voice Note) --}}
                    <div class="p-3 md:p-4 bg-white border-t border-zinc-200 shrink-0 relative z-20">

                        {{-- MEDIA PREVIEW CONTAINER (Tampung sebelum kirim) --}}
                        <div id="media-preview-container" class="hidden items-center justify-between p-3 mx-1 mb-3 bg-zinc-50 border border-zinc-200 rounded-xl shadow-inner">
                            <div id="media-preview-content" class="flex items-center gap-3 w-full overflow-hidden">
                                <!-- Preview Injected Here -->
                            </div>
                            <button type="button" onclick="cancelMediaPreview()" class="w-8 h-8 rounded-full bg-red-100 text-red-500 hover:bg-red-200 flex items-center justify-center transition-colors flex-shrink-0 ml-2 shadow-sm outline-none">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>

                        {{-- Hidden File Inputs --}}
                        <input type="file" id="upload-image" accept="image/*" class="hidden" onchange="handleFileUpload(this, 'image')">
                        <input type="file" id="upload-file" accept=".pdf,.doc,.docx,.xls,.xlsx,.zip" class="hidden" onchange="handleFileUpload(this, 'file')">

                        {{-- Toolbar Atas Input --}}
                        <div class="flex items-center gap-3 mb-2 px-1">
                            <button onclick="document.getElementById('upload-image').click()" class="text-zinc-400 hover:text-blue-600 transition-colors outline-none"><i class="far fa-image text-lg"></i></button>
                            <button onclick="document.getElementById('upload-file').click()" class="text-zinc-400 hover:text-blue-600 transition-colors outline-none"><i class="far fa-folder text-lg"></i></button>
                            <button id="record-vn-btn" onclick="toggleSellerVoiceNote()" class="text-zinc-400 hover:text-red-500 transition-colors outline-none relative">
                                <i class="fas fa-microphone text-lg"></i>
                                <span class="absolute -top-1 -right-1 w-2 h-2 bg-red-500 rounded-full animate-ping hidden" id="vn-ping"></span>
                            </button>

                            {{-- Indikator Rekaman --}}
                            <div id="recording-indicator" class="hidden items-center gap-1.5 text-[10px] font-bold text-red-500 bg-red-50 px-2 py-1 rounded-md animate-pulse border border-red-100">
                                <span class="w-1.5 h-1.5 bg-red-500 rounded-full"></span> Merekam VN...
                            </div>
                        </div>

                        {{-- Teks Input --}}
                        <form id="formSendMessage" class="flex items-end gap-2 bg-zinc-50 border border-zinc-200 rounded-xl p-1.5 focus-within:border-blue-500 focus-within:ring-2 focus-within:ring-blue-500/20 transition-all">
                            <textarea id="seller-chat-input" rows="1" placeholder="Ketik pesan..." class="w-full text-sm text-zinc-700 font-medium bg-transparent px-2 py-2 outline-none resize-none max-h-[100px] min-h-[40px] custom-scrollbar"></textarea>
                            <button type="submit" id="btn-send-seller" class="w-10 h-10 rounded-lg bg-zinc-900 text-white flex items-center justify-center shrink-0 hover:bg-blue-600 hover:shadow-lg hover:-translate-y-0.5 transition-all outline-none">
                                <i class="fas fa-paper-plane text-xs"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- TAB 2: AI VIEW (POTA AI) --}}
        <div id="view-ai" class="absolute inset-0 flex flex-col transition-all duration-500 ease-out opacity-0 translate-x-10 z-10 pointer-events-none bg-white">
            {{-- Header Khusus AI --}}
            <div class="bg-gradient-to-r from-zinc-950 to-zinc-900 p-4 flex justify-between items-center shrink-0 shadow-md relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-blue-500/20 rounded-full blur-[30px]"></div>
                <div class="flex items-center gap-3 md:gap-4 relative z-10">
                    <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center border border-white/10 shadow-[0_0_20px_rgba(37,99,235,0.4)]">
                        <i class="fas fa-robot text-white text-xl"></i>
                    </div>
                    <div>
                        <h4 class="font-black text-white text-sm md:text-base tracking-wide">POTA AI Assistant</h4>
                        <p class="text-[9px] md:text-[10px] text-blue-300 font-bold uppercase tracking-widest flex items-center gap-1.5 mt-0.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-400 animate-pulse"></span> Sistem Aktif
                        </p>
                    </div>
                </div>
                <button onclick="startVoiceCallMode()" class="relative z-10 w-10 h-10 md:w-11 md:h-11 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-all outline-none border border-white/10 group">
                    <i class="fas fa-phone-alt group-hover:scale-110 transition-transform"></i>
                </button>
            </div>

            {{-- Area Pesan AI --}}
            <div class="flex-1 p-4 md:p-6 overflow-y-auto custom-scrollbar flex flex-col gap-4 bg-slate-50 bg-[radial-gradient(#e5e7eb_1px,transparent_1px)] [background-size:16px_16px]" id="ai-chat-messages">
                <div class="text-[10px] text-center text-zinc-400 font-black uppercase tracking-widest mb-2 flex items-center justify-center gap-2">
                    <span class="w-8 h-px bg-zinc-200"></span> Percakapan Cerdas <span class="w-8 h-px bg-zinc-200"></span>
                </div>
                <div class="flex gap-3 max-w-[90%] md:max-w-[85%]">
                    <div class="w-8 h-8 rounded-full bg-blue-600 flex-shrink-0 flex items-center justify-center text-white text-xs shadow-md mt-auto"><i class="fas fa-robot"></i></div>
                    <div class="bg-blue-50 border border-blue-100 text-zinc-800 p-4 rounded-2xl rounded-bl-sm text-sm shadow-sm relative font-medium leading-relaxed">
                        Halo <strong>{{ auth()->user()?->nama ?? 'Juragan' }}</strong>! Saya POTA. Ingin mencari rekomendasi material terbaik, membandingkan harga pasar, atau butuh bantuan hitung RAB proyek hari ini?
                    </div>
                </div>
            </div>

            {{-- Input Area AI --}}
            <div class="p-3 md:p-4 bg-white border-t border-zinc-200 shrink-0 relative z-20">
                <div class="flex items-center gap-2 relative bg-zinc-50 border border-zinc-200 rounded-full p-1.5 focus-within:border-blue-500 focus-within:ring-2 focus-within:ring-blue-500/20 transition-all">
                    <button id="ai-voice-btn" onclick="toggleAIVoice()" class="w-10 h-10 rounded-full text-zinc-500 hover:bg-blue-100 hover:text-blue-600 flex items-center justify-center transition-all shrink-0 outline-none">
                        <i class="fas fa-microphone"></i>
                    </button>
                    <input type="text" id="ai-chat-input" placeholder="Tanya apapun tentang material..." class="flex-1 bg-transparent text-sm font-medium px-2 py-2 outline-none" onkeypress="handleAIEnter(event)">
                    <button id="send-ai-btn" onclick="sendAIMessage()" class="w-10 h-10 rounded-full bg-blue-600 text-white hover:bg-blue-700 flex items-center justify-center shrink-0 transition-all hover:shadow-lg hover:-translate-y-0.5 outline-none">
                        <i class="fas fa-paper-plane text-xs"></i>
                    </button>
                </div>
            </div>

            {{-- Voice Call Overlay Eksklusif AI --}}
            <div id="ai-voice-overlay" class="absolute inset-0 bg-zinc-950/95 backdrop-blur-xl z-[100] hidden flex-col items-center justify-center text-white">
                <div class="text-[11px] md:text-xs font-black tracking-[0.3em] text-blue-400 uppercase mb-16 animate-pulse" id="ai-voice-status">Mendengarkan...</div>
                <div class="relative w-32 h-32 flex items-center justify-center mb-16">
                    <div class="absolute inset-0 bg-blue-600/30 rounded-full animate-ping duration-1000"></div>
                    <div class="absolute inset-[-20px] bg-blue-500/10 rounded-full animate-ping duration-1000" style="animation-delay: 0.3s"></div>
                    <div id="ai-voice-visualizer" class="w-24 h-24 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white text-3xl shadow-[0_0_50px_rgba(37,99,235,0.6)] z-10 transition-all duration-300 border border-white/20">
                        <i class="fas fa-microphone"></i>
                    </div>
                </div>
                <button onclick="endVoiceCallMode()" class="bg-red-500 text-white hover:bg-red-600 px-8 py-3.5 rounded-full font-black flex items-center gap-3 transition-all group text-xs md:text-sm outline-none shadow-[0_10px_20px_rgba(239,68,68,0.3)] hover:-translate-y-1">
                    <i class="fas fa-phone-slash"></i> Tutup Panggilan
                </button>
            </div>
        </div>

    </div>
</div>

{{-- MODAL LIGHTBOX IMAGE --}}
<div id="image-lightbox" class="fixed inset-0 z-[10000] hidden items-center justify-center bg-zinc-950/95 backdrop-blur-sm p-4" onclick="closeLightbox()">
    <button class="absolute top-6 right-6 w-12 h-12 bg-white/10 rounded-full flex items-center justify-center text-white text-xl hover:bg-red-500 transition-colors outline-none"><i class="fas fa-times"></i></button>
    <div class="relative max-w-full max-h-full flex items-center justify-center p-2" onclick="event.stopPropagation()">
        <img id="lightbox-img" src="" class="max-w-full max-h-[90dvh] object-contain rounded-xl shadow-[0_20px_50px_rgba(0,0,0,0.5)] scale-90 transition-transform duration-300">
    </div>
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 5px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    .custom-scrollbar:hover::-webkit-scrollbar-thumb { background: #94a3b8; }
    .custom-audio { height: 35px; border-radius: 20px; outline: none; }
    .custom-audio::-webkit-media-controls-panel { background-color: #f1f5f9; }

    /* Lightbox Animasi */
    #image-lightbox { transition: opacity 0.3s ease; }
    #image-lightbox.hidden { display: none; opacity: 0; pointer-events: none; }
    #image-lightbox.flex { display: flex; opacity: 1; pointer-events: auto; }
</style>

<script>
    /* === 1. GLOBAL UI & TRIGGER LOGIC === */
    const chatWindow = document.getElementById('live-chat-window');
    const viewSeller = document.getElementById('view-seller');
    const viewAI = document.getElementById('view-ai');

    const tabSellerBtn = document.getElementById('tab-btn-seller');
    const tabAIBtn = document.getElementById('tab-btn-ai');
    const tabSlider = document.getElementById('tab-slider');

    let currentStoreId = null;
    let currentMessageCount = -1;
    let pendingMedia = null;
    let globalPollingInterval = null;

    document.addEventListener('DOMContentLoaded', () => {
        const isOpen = sessionStorage.getItem('pota_chat_open') === 'true';
        const activeTab = sessionStorage.getItem('pota_chat_tab') || 'seller';

        const savedAIDom = sessionStorage.getItem('pota_ai_dom');
        if(savedAIDom && document.getElementById('ai-chat-messages')) {
            document.getElementById('ai-chat-messages').innerHTML = savedAIDom;
            document.getElementById('ai-chat-messages').scrollTop = document.getElementById('ai-chat-messages').scrollHeight;
        }

        const savedSellerDom = sessionStorage.getItem('pota_seller_dom');
        const savedStoreId = sessionStorage.getItem('pota_active_store');
        if(savedSellerDom && savedStoreId) {
            currentStoreId = savedStoreId;
            document.getElementById('seller-empty-state').classList.add('hidden');
            document.getElementById('seller-active-chat').classList.remove('hidden', 'opacity-0');
            document.getElementById('seller-active-chat').classList.add('flex');
            document.getElementById('active-store-name').innerText = sessionStorage.getItem('pota_active_name');
            document.getElementById('active-store-avatar').innerText = sessionStorage.getItem('pota_active_avatar');
            document.getElementById('seller-chat-messages').innerHTML = savedSellerDom;
            document.getElementById('seller-chat-messages').scrollTop = document.getElementById('seller-chat-messages').scrollHeight;
            openStoreChat(savedStoreId, sessionStorage.getItem('pota_active_name'), sessionStorage.getItem('pota_active_avatar'), false);
        }

        if(isOpen && chatWindow) {
            chatWindow.classList.remove('hidden');
            setTimeout(() => {
                chatWindow.classList.remove('opacity-0', 'translate-y-10', 'scale-95', 'pointer-events-none');
                chatWindow.classList.add('flex', 'opacity-100', 'translate-y-0', 'scale-100');
            }, 10);
            switchChatTab(activeTab, true);
        }

        fetchSellerContacts();
        startGlobalPolling(); // MULAI POLLING ANTI FLICKER DEWA
    });

    // --- FUNGSI GLOBAL POLLING ---
    function startGlobalPolling() {
        if(globalPollingInterval) clearInterval(globalPollingInterval);
        globalPollingInterval = setInterval(() => {
            fetchSellerContacts(false);
            if (currentStoreId && !chatWindow.classList.contains('hidden') && sessionStorage.getItem('pota_chat_tab') === 'seller') {
                loadMessages(currentStoreId, false);
            }
        }, 4000);
    }

    /* === LIGHTBOX LOGIC === */
    window.openLightbox = function(src) {
        const lightbox = document.getElementById('image-lightbox');
        const img = document.getElementById('lightbox-img');
        img.src = src;
        lightbox.classList.remove('hidden');
        lightbox.classList.add('flex');
        setTimeout(() => img.classList.replace('scale-90', 'scale-100'), 10);
    }
    window.closeLightbox = function() {
        const lightbox = document.getElementById('image-lightbox');
        const img = document.getElementById('lightbox-img');
        img.classList.replace('scale-100', 'scale-90');
        lightbox.classList.replace('flex', 'hidden');
    }

    // Mencegah enter mengirim form langsung, shift+enter untuk baris baru
    document.getElementById('seller-chat-input').addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            document.getElementById('formSendMessage').dispatchEvent(new Event('submit'));
        }
    });

    document.getElementById('formSendMessage').addEventListener('submit', function(e) {
        e.preventDefault();
        const input = document.getElementById('seller-chat-input');
        const text = input.value.trim();

        if (pendingMedia) {
            sendMediaMessage(pendingMedia.content, pendingMedia.type, pendingMedia.fileName, text);
            cancelMediaPreview();
        } else if (text !== '') {
            sendMediaMessage(text, 'text');
        }
    });

    // FUNGSI TRIGGER CHAT DARI HALAMAN PRODUK
    window.openChatWithStore = function(storeId, storeName, initials) {
        if(chatWindow.classList.contains('hidden')) {
            chatWindow.classList.remove('hidden');
            setTimeout(() => {
                chatWindow.classList.remove('opacity-0', 'translate-y-10', 'scale-95', 'pointer-events-none');
                chatWindow.classList.add('flex', 'opacity-100', 'translate-y-0', 'scale-100');
            }, 10);
            sessionStorage.setItem('pota_chat_open', 'true');
        }
        switchChatTab('seller', false);
        setTimeout(() => { openStoreChat(storeId, storeName, initials); }, 300);
    }

    function toggleChatWindow() {
        if(chatWindow.classList.contains('hidden')) {
            const lastTab = sessionStorage.getItem('pota_chat_tab') || 'seller';
            chatWindow.classList.remove('hidden');
            setTimeout(() => {
                chatWindow.classList.remove('opacity-0', 'translate-y-10', 'scale-95', 'pointer-events-none');
                chatWindow.classList.add('flex', 'opacity-100', 'translate-y-0', 'scale-100');
            }, 10);
            switchChatTab(lastTab, true);
            sessionStorage.setItem('pota_chat_open', 'true');
            fetchSellerContacts(false);
        } else {
            chatWindow.classList.add('opacity-0', 'translate-y-10', 'scale-95', 'pointer-events-none');
            chatWindow.classList.remove('opacity-100', 'translate-y-0', 'scale-100');
            setTimeout(() => {
                chatWindow.classList.add('hidden');
                viewSeller.style.display = 'none';
                viewAI.style.display = 'none';
            }, 500);
            sessionStorage.setItem('pota_chat_open', 'false');
            endVoiceCallMode();
        }
    }

    function switchChatTab(tabName, instant = false) {
        sessionStorage.setItem('pota_chat_tab', tabName);
        if(tabName === 'ai') {
            tabSlider.style.transform = 'translateX(100%)';
            tabAIBtn.classList.replace('text-zinc-400', 'text-zinc-900');
            tabSellerBtn.classList.replace('text-zinc-900', 'text-zinc-400');
            viewAI.style.display = 'flex';
            if(!instant) {
                viewSeller.classList.replace('translate-x-0', '-translate-x-10');
                viewSeller.classList.replace('opacity-100', 'opacity-0');
                viewSeller.classList.replace('pointer-events-auto', 'pointer-events-none');
            } else {
                viewSeller.classList.add('opacity-0', 'pointer-events-none', '-translate-x-10');
                viewSeller.classList.remove('opacity-100', 'pointer-events-auto', 'translate-x-0');
            }
            setTimeout(() => {
                if(!instant) viewSeller.style.display = 'none';
                viewAI.classList.replace('opacity-0', 'opacity-100');
                viewAI.classList.replace('translate-x-10', 'translate-x-0');
                viewAI.classList.replace('pointer-events-none', 'pointer-events-auto');
                document.getElementById('ai-chat-input').focus();
            }, instant ? 10 : 300);
        } else {
            tabSlider.style.transform = 'translateX(0)';
            tabSellerBtn.classList.replace('text-zinc-400', 'text-zinc-900');
            tabAIBtn.classList.replace('text-zinc-900', 'text-zinc-400');
            viewSeller.style.display = 'flex';
            if(!instant) {
                viewAI.classList.replace('translate-x-0', 'translate-x-10');
                viewAI.classList.replace('opacity-100', 'opacity-0');
                viewAI.classList.replace('pointer-events-auto', 'pointer-events-none');
            } else {
                viewAI.classList.add('opacity-0', 'pointer-events-none', 'translate-x-10');
                viewAI.classList.remove('opacity-100', 'pointer-events-auto', 'translate-x-0');
            }
            setTimeout(() => {
                if(!instant) viewAI.style.display = 'none';
                viewSeller.classList.replace('opacity-0', 'opacity-100');
                viewSeller.classList.replace('-translate-x-10', 'translate-x-0');
                viewSeller.classList.replace('pointer-events-none', 'pointer-events-auto');
            }, instant ? 10 : 300);
        }
    }

    function toggleFullScreen() {
        chatWindow.classList.toggle('md:w-[800px]');
        chatWindow.classList.toggle('md:h-[600px]');
        chatWindow.classList.toggle('w-[95vw]');
        chatWindow.classList.toggle('h-[90dvh]');
        const icon = document.getElementById('icon-resize');
        if(icon) icon.className = chatWindow.classList.contains('w-[95vw]') ? 'fas fa-compress text-sm' : 'fas fa-expand text-sm';
    }

    function closeActiveChatMobile() {
        document.getElementById('seller-active-chat').classList.replace('flex', 'hidden');
        document.getElementById('seller-empty-state').classList.replace('hidden', 'flex');
        document.getElementById('seller-empty-state').classList.remove('opacity-0');
        currentStoreId = null;
        sessionStorage.removeItem('pota_active_store');
        fetchSellerContacts();
    }


    /* ========================================================
       2. SELLER CHAT LOGIC (100% REAL API)
       ======================================================== */
    async function fetchSellerContacts(showLoading = true) {
        const contactList = document.getElementById('seller-contact-list');
        if(showLoading && contactList.innerHTML === "") contactList.innerHTML = `<div class="p-6 text-center text-zinc-400 flex flex-col items-center"><i class="fas fa-circle-notch fa-spin text-2xl mb-2"></i><span class="text-[10px] font-bold uppercase">Memuat...</span></div>`;
        try {
            const res = await fetch('/api/chat/contacts');
            if(!res.ok) throw new Error();
            const data = await res.json();
            renderContacts(data, contactList);

            // LOGIKA DOT MERAH GLOBAL
            const hasUnread = data.some(c => c.unread_count > 0);
            const dot = document.getElementById('unread-global-dot');
            if(hasUnread) dot.classList.remove('hidden');
            else dot.classList.add('hidden');

        } catch(e) {
            if(showLoading) contactList.innerHTML = `<div class="p-6 text-center text-zinc-500 text-xs font-medium">Gagal memuat kontak. Pastikan Anda sudah login.</div>`;
        }
    }

    function renderContacts(data, container) {
        let html = '';
        if(data.length === 0) {
            container.innerHTML = `<div class="p-6 text-center text-zinc-400 text-xs font-medium">Belum ada obrolan. Cari produk dan klik chat!</div>`;
            return;
        }

        data.forEach(toko => {
            const initial = toko.nama_toko.substring(0, 2).toUpperCase();
            const unreadBadge = toko.unread_count > 0 ? `<div class="bg-red-500 text-white text-[9px] font-black w-4 h-4 md:w-5 md:h-5 rounded-full flex items-center justify-center shadow-sm absolute -top-1 -right-1 z-10 animate-bounce">${toko.unread_count}</div>` : '';
            const isActive = currentStoreId == toko.store_id ? 'bg-blue-50 border-l-4 border-l-blue-600' : 'hover:bg-zinc-50 border-l-4 border-l-transparent';

            html += `
            <div onclick="openStoreChat(${toko.store_id}, '${toko.nama_toko}', '${initial}')" class="flex items-center gap-3 p-3 border-b border-zinc-100 cursor-pointer transition-all group ${isActive}">
                <div class="w-10 h-10 md:w-12 md:h-12 rounded-full bg-zinc-200 text-zinc-700 flex items-center justify-center font-black text-xs md:text-sm shrink-0 shadow-inner group-hover:bg-blue-100 group-hover:text-blue-600 transition-colors relative">
                    ${initial}
                    ${unreadBadge}
                </div>
                <div class="flex-1 min-w-0 hidden md:block">
                    <div class="flex justify-between items-center mb-1">
                        <h4 class="text-xs font-black text-zinc-900 truncate group-hover:text-blue-600 transition-colors">${toko.nama_toko}</h4>
                        <span class="text-[9px] font-bold text-zinc-400 shrink-0 ml-2">${toko.last_time || ''}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <p class="text-[10px] text-zinc-500 font-medium truncate pr-2">${toko.last_message || 'File media terkirim'}</p>
                    </div>
                </div>
                <div class="md:hidden absolute top-1 right-1">${unreadBadge}</div>
            </div>`;
        });
        container.innerHTML = html;
    }

    document.getElementById('sellerSearchInput').addEventListener('keyup', function() {
        let keyword = this.value.toLowerCase();
        let items = document.getElementById('seller-contact-list').querySelectorAll('div[onclick]');
        items.forEach(item => {
            let name = item.querySelector('h4').textContent.toLowerCase();
            if (name.includes(keyword)) { item.style.display = 'flex'; }
            else { item.style.display = 'none'; }
        });
    });

    async function openStoreChat(storeId, storeName, initials, triggerAnimation = true) {
        currentStoreId = storeId;
        sessionStorage.setItem('pota_active_store', storeId);
        sessionStorage.setItem('pota_active_name', storeName);
        sessionStorage.setItem('pota_active_avatar', initials);

        const emptyState = document.getElementById('seller-empty-state');
        const activeChat = document.getElementById('seller-active-chat');

        if(triggerAnimation) {
            emptyState.classList.add('opacity-0', 'pointer-events-none');
            setTimeout(() => { emptyState.classList.add('hidden'); }, 300);
            activeChat.classList.remove('hidden');
            setTimeout(() => { activeChat.classList.remove('opacity-0'); }, 50);
        } else {
            emptyState.classList.add('hidden');
            activeChat.classList.remove('hidden', 'opacity-0');
        }

        document.getElementById('active-store-name').innerText = storeName;
        document.getElementById('active-store-avatar').innerText = initials;

        const msgContainer = document.getElementById('seller-chat-messages');
        if(triggerAnimation) {
            msgContainer.innerHTML = `<div class="text-center text-xs font-bold text-zinc-400 my-4 flex items-center justify-center gap-2"><i class="fas fa-circle-notch fa-spin"></i> Memuat histori...</div>`;
        }

        loadMessages(storeId, true);
    }

    // FUNGSI POLLING CERDAS ANTI FLICKER (Hanya render jika ada pesan baru)
    async function loadMessages(storeId, isInitialLoad = false) {
        try {
            const res = await fetch(`/api/chat/messages/${storeId}`);
            if(!res.ok) throw new Error();
            const data = await res.json();
            const msgContainer = document.getElementById('seller-chat-messages');

            if(data.is_typing) document.getElementById('typing-indicator').classList.remove('hidden');
            else document.getElementById('typing-indicator').classList.add('hidden');

            // BUG FIX DEWA: Tangani penambahan array dinamis tanpa flicker total jika memungkinkan
            if (isInitialLoad || data.length > currentMessageCount || data.length < currentMessageCount) {
                msgContainer.innerHTML = '';
                currentMessageCount = data.length;

                if(data.length === 0) {
                     msgContainer.innerHTML = `<div class="text-center text-[10px] font-bold text-zinc-400 my-4 bg-white p-2 mx-auto rounded-full border border-zinc-200 max-w-[200px] shadow-sm">Belum ada obrolan. Mulai sapa penjual!</div>`;
                } else {
                     data.forEach(msg => appendSellerMessage(msg.content, msg.sender, msg.time, msg.type, msg.fileName, msg.is_read, isInitialLoad));
                }
                if(isInitialLoad || msgContainer.scrollHeight - msgContainer.scrollTop < 500) {
                    msgContainer.scrollTop = msgContainer.scrollHeight;
                }
            } else if (data.length === currentMessageCount) {
                // Update Centang Jika Jumlah Pesan Tetap Sama (Read Receipt Update)
                updateReadTicks(data);
            }
            fetchSellerContacts(false);
        } catch(e) {
            if(isInitialLoad) {
                document.getElementById('seller-chat-messages').innerHTML = `<div class="text-center text-xs font-bold text-red-500 my-4 bg-red-50 p-2 rounded-xl border border-red-200 mx-auto max-w-[250px]">Gagal memuat histori chat. Pastikan server terhubung.</div>`;
            }
        }
    }

    function updateReadTicks(items) {
        const msgContainer = document.getElementById('seller-chat-messages');
        const messageElements = msgContainer.querySelectorAll('.message-bubble');
        items.forEach((msg, index) => {
            let isOut = msg.sender === 'user';
            if (isOut && msg.is_read == 1 && messageElements[index]) {
                const statusContainer = messageElements[index].querySelector('.chat-status');
                if (statusContainer && statusContainer.innerHTML.includes('fa-check') && !statusContainer.innerHTML.includes('fa-check-double')) {
                    statusContainer.className = 'chat-status flex items-center gap-1 bg-white/25 text-white px-1.5 py-[2px] rounded uppercase ml-1 transition-all duration-300';
                    statusContainer.innerHTML = `<span class="text-[8px] tracking-wider font-black">SEEN</span><i class="chat-tick fas fa-check-double text-[11px] leading-none"></i>`;
                }
            }
        });
    }

    function appendSellerMessage(content, sender, time, type = 'text', fileName = '', isRead = 0, animate = true) {
        const container = document.getElementById('seller-chat-messages');
        const isOut = sender === 'user'; // Di sisi customer, 'user' adalah diri sendiri

        let wrapAlign = isOut ? 'self-end items-end' : 'self-start items-start';
        let bubbleColor = isOut ? 'bg-blue-600 text-white rounded-l-2xl rounded-tr-2xl rounded-br-sm shadow-md' : 'bg-white border border-zinc-200 text-zinc-800 rounded-r-2xl rounded-tl-2xl rounded-bl-sm shadow-sm';
        let timeColor = isOut ? 'text-blue-100' : 'text-zinc-400';
        let animClass = animate ? 'animate-[scale-in_0.2s_ease-out]' : '';
        let transformOrigin = isOut ? 'origin-bottom-right' : 'origin-bottom-left';

        // Logika Centang Customer (Pakai FontAwesome FAS)
        let tickHtml = '';
        if(isOut) {
            if(isRead == 1) {
                tickHtml = `<div class="chat-status flex items-center gap-1 bg-white/25 text-white px-1.5 py-[2px] rounded uppercase ml-1"><span class="text-[8px] tracking-wider font-black">SEEN</span><i class="chat-tick fas fa-check-double text-[11px] leading-none"></i></div>`;
            } else {
                tickHtml = `<div class="chat-status flex items-center gap-1 text-blue-200 px-1.5 py-0.5 ml-1"><i class="chat-tick fas fa-check text-[11px] leading-none"></i></div>`;
            }
        }

        let innerHTML = '';
        if(type === 'image') {
            innerHTML = `<div class="p-1"><img src="${content}" onclick="openLightbox('${content}')" class="max-w-[200px] md:max-w-[250px] rounded-xl object-cover cursor-pointer hover:opacity-90 border border-black/5 transition-all" alt="Uploaded Image"></div>`;
        }
        else if (type === 'file') {
            innerHTML = `
            <a href="${content}" download="${fileName}" class="p-2 flex items-center gap-3 hover:bg-black/5 transition-colors rounded-xl no-underline">
                <div class="w-10 h-10 rounded-lg bg-red-100 flex items-center justify-center text-red-600 shrink-0"><i class="fas fa-file-pdf text-xl"></i></div>
                <div class="flex flex-col min-w-0 pr-2">
                    <span class="text-xs font-bold truncate max-w-[150px] md:max-w-[200px] ${isOut ? 'text-white' : 'text-zinc-800'}">${fileName}</span>
                    <span class="text-[9px] ${isOut ? 'text-blue-200' : 'text-zinc-500'} uppercase tracking-widest mt-0.5"><i class="fas fa-download"></i> Unduh File</span>
                </div>
            </a>`;
        }
        else if (type === 'audio') {
            innerHTML = `<div class="p-2 w-[220px] md:w-[260px]"><audio controls src="${content}" class="w-full custom-audio"></audio></div>`;
        }
        else {
            innerHTML = `<div class="px-4 py-2.5 text-[13px] md:text-sm font-medium leading-relaxed break-words">${content}</div>`;
        }

        let html = `
            <div class="message-bubble flex flex-col max-w-[85%] md:max-w-[75%] ${wrapAlign} ${transformOrigin} ${animClass}">
                <div class="${bubbleColor}">
                    ${innerHTML}
                    <div class="text-[9px] font-bold mt-1 px-3 pb-2 text-right ${timeColor} flex items-center justify-end gap-1">
                        ${time} ${tickHtml}
                    </div>
                </div>
            </div>`;

        container.insertAdjacentHTML('beforeend', html);
        sessionStorage.setItem('pota_seller_dom', container.innerHTML);
    }

    // ========================================================
    // LOGIKA PREVIEW MEDIA & UPLOAD
    // ========================================================
    window.handleFileUpload = function(inputElement, type) {
        const file = inputElement.files[0];
        if(!file || !currentStoreId) return;

        if(file.size > 2000000) { alert('Maksimal ukuran file adalah 2MB.'); return; }

        const reader = new FileReader();
        reader.onload = async function(e) {
            showMediaPreview(type, e.target.result, file.name);
        };
        reader.readAsDataURL(file);
        inputElement.value = '';
    }

    function showMediaPreview(type, content, fileName) {
        pendingMedia = { type, content, fileName };
        const container = document.getElementById('media-preview-container');
        const contentDiv = document.getElementById('media-preview-content');

        if(type === 'image') {
            contentDiv.innerHTML = `<img src="${content}" class="h-12 w-12 object-cover rounded-lg border border-zinc-200 shadow-sm"> <div class="flex flex-col"><span class="text-xs font-black text-zinc-800 truncate">Gambar Siap Kirim</span><span class="text-[10px] text-zinc-400">Tambahkan pesan teks di bawah jika perlu...</span></div>`;
        } else if (type === 'file') {
            contentDiv.innerHTML = `<div class="h-12 w-12 bg-red-100 text-red-500 rounded-lg flex items-center justify-center text-xl shadow-sm"><i class="fas fa-file-pdf"></i></div> <div class="flex flex-col"><span class="text-xs font-black text-zinc-800 truncate max-w-[200px]">${fileName}</span><span class="text-[10px] text-zinc-400">Dokumen siap kirim</span></div>`;
        } else if (type === 'audio') {
            contentDiv.innerHTML = `<div class="h-12 w-12 bg-blue-100 text-blue-500 rounded-lg flex items-center justify-center text-xl shadow-sm"><i class="fas fa-microphone"></i></div> <div class="flex flex-col"><span class="text-xs font-black text-zinc-800">Voice Note</span><span class="text-[10px] text-zinc-400">Siap dikirim</span></div>`;
        }

        container.classList.remove('hidden'); container.classList.add('flex');
    }

    window.cancelMediaPreview = function() {
        pendingMedia = null;
        const container = document.getElementById('media-preview-container');
        container.classList.add('hidden'); container.classList.remove('flex');
    }

    // LOGIKA PENGIRIMAN FINAL KE BACKEND
    function sendMediaMessage(content, type = 'text', fileName = '', caption = '') {
        if(!content || !currentStoreId) return;

        let btn = document.getElementById('btn-send-seller');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin text-xs"></i>';

        const timeNow = new Date().toLocaleTimeString('id-ID', {hour: '2-digit', minute:'2-digit'});
        appendSellerMessage(content, 'user', timeNow, type, fileName, 0, true);

        currentMessageCount++;

        if (caption && type !== 'text') {
            setTimeout(() => appendSellerMessage(caption, 'user', timeNow, 'text', '', 0, true), 100);
            currentMessageCount++;
        }

        const msgContainer = document.getElementById('seller-chat-messages');
        msgContainer.scrollTop = msgContainer.scrollHeight;

        // BUG FIX UTAMA: Kunci (Key) yang benar dikirim adalah 'store_id'
        let payload = { store_id: currentStoreId, message: content, type: type, file_name: fileName };

        fetch('/api/chat/send', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify(payload)
        })
        .then(async res => {
            if(!res.ok) throw new Error();
            if (caption && type !== 'text') {
                fetch('/api/chat/send', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    // BUG FIX: Kunci 'store_id'
                    body: JSON.stringify({ store_id: currentStoreId, message: caption, type: 'text' })
                });
            }
            fetchSellerContacts(false);
        })
        .catch(err => { console.error(err); })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-paper-plane text-xs"></i>';
            document.getElementById('seller-chat-input').focus();
        });
    }

    let vnRecorder;
    let vnChunks = [];
    let isRecordingVN = false;

    async function toggleSellerVoiceNote() {
        if(!currentStoreId) return alert('Pilih toko terlebih dahulu di sebelah kiri!');

        const btn = document.getElementById('record-vn-btn');
        const ping = document.getElementById('vn-ping');
        const indicator = document.getElementById('recording-indicator');

        if(isRecordingVN) {
            vnRecorder.stop();
            isRecordingVN = false;
            btn.classList.remove('text-red-500'); btn.classList.add('text-zinc-400');
            ping.classList.add('hidden');
            indicator.classList.add('hidden'); indicator.classList.remove('flex');
        } else {
            try {
                const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                vnRecorder = new MediaRecorder(stream);
                vnChunks = [];

                vnRecorder.ondataavailable = e => { if (e.data.size > 0) vnChunks.push(e.data); };
                vnRecorder.onstop = () => {
                    const audioBlob = new Blob(vnChunks, { type: 'audio/webm' });
                    const reader = new FileReader();

                    reader.onload = async function(e) {
                        showMediaPreview('audio', e.target.result, 'Voice Note');
                    };
                    reader.readAsDataURL(audioBlob);
                    stream.getTracks().forEach(track => track.stop());
                };

                vnRecorder.start();
                isRecordingVN = true;

                btn.classList.add('text-red-500'); btn.classList.remove('text-zinc-400');
                ping.classList.remove('hidden');
                indicator.classList.remove('hidden'); indicator.classList.add('flex');
            } catch (err) {
                alert("Akses Mikrofon ditolak.");
            }
        }
    }


    /* ========================================================
       3. POTA AI CHAT LOGIC (Real API)
       ======================================================== */
    const aiMessagesContainer = document.getElementById('ai-chat-messages');
    const aiInput = document.getElementById('ai-chat-input');
    const aiCallOverlay = document.getElementById('ai-voice-overlay');
    let aiChatHistory = [];
    let aiRecognition = null;
    let isAiCallMode = false;
    let aiVoices = [];

    window.speechSynthesis.onvoiceschanged = () => { aiVoices = window.speechSynthesis.getVoices(); };

    if (window.SpeechRecognition || window.webkitSpeechRecognition) {
        const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
        aiRecognition = new SpeechRecognition();
        aiRecognition.lang = 'id-ID';
        aiRecognition.interimResults = false;

        aiRecognition.onresult = (event) => {
            const text = event.results[0][0].transcript;
            if(isAiCallMode) {
                document.getElementById('ai-voice-status').innerText = "Menganalisa data...";
                document.getElementById('ai-voice-visualizer').classList.remove('animate-pulse');
                sendAIMessage(text);
            } else {
                if(aiInput) aiInput.value = text;
                stopAIRecordingUI();
            }
        };
        aiRecognition.onerror = () => {
            stopAIRecordingUI();
            if(isAiCallMode) {
                document.getElementById('ai-voice-status').innerText = "Suara tidak terdengar.";
                setTimeout(() => { if(isAiCallMode) aiRecognition.start(); }, 2000);
            }
        };
        aiRecognition.onend = () => { if(!isAiCallMode) stopAIRecordingUI(); };
    }

    function toggleAIVoice() {
        if(!aiRecognition) return alert("Browser tidak mendukung mic.");
        const btn = document.getElementById('ai-voice-btn');
        if(btn.classList.contains('bg-blue-600')) {
            aiRecognition.stop(); stopAIRecordingUI();
        } else {
            aiRecognition.start(); startAIRecordingUI();
        }
    }

    function startAIRecordingUI() {
        const btn = document.getElementById('ai-voice-btn');
        if(btn) { btn.classList.add('bg-blue-600', 'text-white', 'animate-pulse'); btn.classList.remove('bg-zinc-100', 'text-zinc-500'); }
    }

    function stopAIRecordingUI() {
        const btn = document.getElementById('ai-voice-btn');
        if(btn) { btn.classList.remove('bg-blue-600', 'text-white', 'animate-pulse'); btn.classList.add('bg-zinc-100', 'text-zinc-500'); }
    }

    function startVoiceCallMode() {
        if(!aiRecognition) return alert("Browser tidak mendukung fitur suara.");
        isAiCallMode = true;
        aiCallOverlay.classList.remove('hidden');
        aiCallOverlay.classList.add('flex');
        document.getElementById('ai-voice-status').innerText = "Mandor Standby...";
        document.getElementById('ai-voice-visualizer').classList.add('animate-pulse');
        speakText("Halo Bosku! Ada yang bisa POTA bantu untuk proyek hari ini?", true);
    }

    function endVoiceCallMode() {
        isAiCallMode = false;
        aiCallOverlay.classList.add('hidden');
        aiCallOverlay.classList.remove('flex');
        window.speechSynthesis.cancel();
        if(aiRecognition) aiRecognition.stop();
    }

    function saveAIState() {
        if(aiMessagesContainer) {
            sessionStorage.setItem('pota_ai_dom', aiMessagesContainer.innerHTML);
            sessionStorage.setItem('pota_ai_history', JSON.stringify(aiChatHistory));
        }
    }

    function appendAIMessage(text, sender) {
        if(!aiMessagesContainer) return;
        const clean = text.replace(/"/g, "'").replace(/\n/g, " ").replace(/<[^>]*>?/gm, '');

        let html = '';
        if(sender === 'bot') {
            html = `
            <div class="flex gap-3 max-w-[90%] md:max-w-[85%] origin-bottom-left transition-all animate-[scale-in_0.2s_ease-out]">
                <div class="w-8 h-8 rounded-full bg-blue-600 flex-shrink-0 flex items-center justify-center text-white text-xs shadow-md mt-auto"><i class="fas fa-robot"></i></div>
                <div class="bg-blue-50 border border-blue-100 text-zinc-800 p-4 rounded-2xl rounded-bl-sm text-sm shadow-sm relative group font-medium leading-relaxed">
                    ${text}
                    <button onclick="speakText('${clean}')" class="absolute -right-8 bottom-1 w-6 h-6 rounded-full text-zinc-400 hover:text-blue-600 opacity-0 group-hover:opacity-100 transition-all outline-none"><i class="fas fa-volume-up text-xs"></i></button>
                </div>
            </div>`;
        } else {
            html = `
            <div class="flex justify-end max-w-[90%] md:max-w-[85%] self-end origin-bottom-right animate-[scale-in_0.2s_ease-out]">
                <div class="bg-zinc-900 text-white p-4 rounded-2xl rounded-br-sm text-sm font-medium shadow-md leading-relaxed break-words">${text}</div>
            </div>`;
        }

        aiMessagesContainer.insertAdjacentHTML('beforeend', html);
        aiMessagesContainer.scrollTop = aiMessagesContainer.scrollHeight;
        saveAIState();
    }

    async function sendAIMessage(textOverride = null) {
        if(!aiInput) return;
        const text = textOverride || aiInput.value.trim();
        if(!text) return;

        if(!textOverride) { appendAIMessage(text, 'user'); aiInput.value = ''; }
        aiChatHistory.push({sender:'user', text:text});

        if(!isAiCallMode) {
            const loadHtml = `
            <div id="ai-loading" class="flex gap-1.5 ml-14 items-center text-blue-500 my-2">
                <span class="w-1.5 h-1.5 bg-blue-500 rounded-full animate-bounce"></span>
                <span class="w-1.5 h-1.5 bg-blue-500 rounded-full animate-bounce" style="animation-delay: 0.2s"></span>
                <span class="w-1.5 h-1.5 bg-blue-500 rounded-full animate-bounce" style="animation-delay: 0.4s"></span>
            </div>`;
            aiMessagesContainer.insertAdjacentHTML('beforeend', loadHtml);
            aiMessagesContainer.scrollTop = aiMessagesContainer.scrollHeight;
        }

        try {
            const res = await fetch('/api/chat', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({message: text, history: aiChatHistory.slice(-6)})
            });
            if (!res.ok) throw new Error("Gagal terhubung.");
            const data = await res.json();

            if(document.getElementById('ai-loading')) document.getElementById('ai-loading').remove();
            appendAIMessage(data.reply, 'bot');
            aiChatHistory.push({sender:'bot', text: data.reply.replace(/<[^>]*>?/gm, '')});

            if(isAiCallMode) speakText(data.reply, true);

        } catch(e) {
            if(document.getElementById('ai-loading')) document.getElementById('ai-loading').remove();
            appendAIMessage("Mohon maaf, server AI POTA sedang sibuk.", 'bot');
        }
    }

    function handleAIEnter(e) { if(e.key === 'Enter') sendAIMessage(); }

    function speakText(text, autoListen = false) {
        window.speechSynthesis.cancel();
        const u = new SpeechSynthesisUtterance(text);
        u.lang = 'id-ID';
        u.rate = 1.05;

        const indoVoice = aiVoices.find(v => v.lang === 'id-ID' && (v.name.includes('Google') || v.name.includes('Online')));
        if (indoVoice) u.voice = indoVoice;

        u.onstart = () => {
            if(isAiCallMode) {
                document.getElementById('ai-voice-status').innerText = "POTA Berbicara...";
                document.getElementById('ai-voice-visualizer').classList.remove('animate-pulse');
            }
        };
        u.onend = () => {
            if(isAiCallMode && autoListen) {
                aiRecognition.start();
                document.getElementById('ai-voice-status').innerText = "Silakan bicara Bos...";
                document.getElementById('ai-voice-visualizer').classList.add('animate-pulse');
            }
        };
        window.speechSynthesis.speak(u);
    }
</script>
