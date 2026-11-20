<!-- Progressive Web App (Web APK) Install Banner & Trigger Modal -->
<div x-data="{
        deferredPrompt: null,
        showBanner: false,
        showGuideModal: false,
        activeTab: 'android',
        isInstalled: false,
        isIos: /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream,
        isStandalone: window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true,
        init() {
            if (this.isStandalone) {
                this.isInstalled = true;
                return;
            }

            if (this.isIos) {
                this.activeTab = 'ios';
            } else if (!/Android/i.test(navigator.userAgent)) {
                this.activeTab = 'pc';
            }

            // Listen for Chromium / Android beforeinstallprompt event
            window.addEventListener('beforeinstallprompt', (e) => {
                e.preventDefault();
                this.deferredPrompt = e;
                window.pwaDeferredPrompt = e;
            });

            // Always display banner after 1.5 seconds if not installed and not dismissed (Mobile screen only)
            if (window.innerWidth < 768 && !sessionStorage.getItem('pwa_dismissed')) {
                setTimeout(() => {
                    this.showBanner = true;
                }, 1500);
            }

            // Detect when successfully installed
            window.addEventListener('appinstalled', () => {
                this.isInstalled = true;
                this.showBanner = false;
                this.showGuideModal = false;
                this.deferredPrompt = null;
                console.log('[PWA] Asia Plastik App successfully installed as Web APK.');
            });

            // Expose trigger globally for header/drawer/body buttons
            window.triggerPwaInstall = () => {
                if (this.deferredPrompt) {
                    this.installApp();
                } else {
                    this.showGuideModal = true;
                }
            };
        },
        async installApp() {
            if (this.deferredPrompt) {
                this.deferredPrompt.prompt();
                const { outcome } = await this.deferredPrompt.userChoice;
                if (outcome === 'accepted') {
                    this.showBanner = false;
                    this.showGuideModal = false;
                }
                this.deferredPrompt = null;
            } else {
                this.showGuideModal = true;
            }
        },
        dismissBanner() {
            this.showBanner = false;
            sessionStorage.setItem('pwa_dismissed', 'true');
        }
    }" 
    x-init="init()"
    class="relative z-50">

    <!-- FLOATING PWA / WEB APK INSTALL BANNER -->
    <div x-show="showBanner && !isInstalled"
         x-transition:enter="transition ease-out duration-300 transform"
         x-transition:enter-start="opacity-0 translate-y-8 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200 transform"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-8 scale-95"
         style="display: none;"
         class="md:!hidden fixed bottom-4 left-3 right-3 sm:left-auto sm:right-6 sm:max-w-md bg-slate-900/95 dark:bg-slate-900/95 text-white backdrop-blur-xl border border-blue-500/40 rounded-2xl shadow-2xl p-4 z-50 flex items-start gap-3.5 transition-colors">
        
        <!-- App Icon Thumbnail -->
        <div class="w-12 h-12 rounded-xl bg-slate-800 border border-blue-400/40 flex items-center justify-center shrink-0 shadow-md overflow-hidden p-1.5">
            <img src="{{ asset('images/icons/icon-192x192.png') }}" alt="Asia Plastik App" class="w-full h-full object-contain">
        </div>

        <div class="flex-1 min-w-0">
            <div class="flex items-center justify-between gap-1">
                <span class="text-[10px] font-black uppercase tracking-wider text-blue-300 bg-blue-950/80 px-2 py-0.5 rounded-full border border-blue-500/30">
                    {{ __('WebAPK Resmi') }}
                </span>
                <button type="button" @click="dismissBanner()" class="text-slate-400 hover:text-white p-1 rounded-lg transition" title="{{ __('Tutup') }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <h4 class="text-xs sm:text-sm font-bold text-white mt-1">
                {{ __('Pasang Aplikasi PT. Asia Plastik (WebAPK)') }}
            </h4>
            <p class="text-[11px] sm:text-xs text-slate-300 mt-0.5 line-clamp-2">
                {{ __('Pasang di layar utama HP Anda: tampilan bersih tanpa bar browser, cepat, dan terasa seperti aplikasi Android asli!') }}
            </p>

            <div class="mt-2.5 flex items-center gap-2">
                <button type="button" @click="installApp()" class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-bold text-xs shadow-md shadow-blue-500/30 active:scale-95 transition cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    <span>{{ __('Install') }}</span>
                </button>
                <button type="button" @click="showGuideModal = true; showBanner = false;" class="px-2.5 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-blue-300 font-semibold text-xs border border-slate-700 transition cursor-pointer">
                    {{ __('Petunjuk') }}
                </button>
                <button type="button" @click="dismissBanner()" class="px-2.5 py-1.5 rounded-xl bg-slate-800/60 hover:bg-slate-700 text-slate-400 font-semibold text-xs transition cursor-pointer">
                    {{ __('Nanti') }}
                </button>
            </div>
        </div>
    </div>

    <!-- PWA INSTALL GUIDE MODAL (Android, iOS, PC/Laptop) -->
    <div x-show="showGuideModal"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         style="display: none;"
         class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-slate-950/80 backdrop-blur-sm">
        
        <div class="bg-white dark:bg-slate-900 rounded-3xl max-w-lg w-full p-5 sm:p-6 shadow-2xl border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white relative"
             @click.away="showGuideModal = false">
            
            <button type="button" @click="showGuideModal = false" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1.5 rounded-full transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>

            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 rounded-2xl bg-slate-900 p-1.5 shadow-md shrink-0 border border-blue-500/30">
                    <img src="{{ asset('images/icons/icon-192x192.png') }}" alt="Asia Plastik" class="w-full h-full object-contain">
                </div>
                <div>
                    <h3 class="text-base sm:text-lg font-black leading-tight">
                        {{ __('📱 Cara Memasang (Install) Aplikasi') }}
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">
                        {{ __('PT. Asia Plastik Corporate Ticketing Desk') }}
                    </p>
                </div>
            </div>

            <!-- Tab Selection -->
            <div class="flex rounded-xl bg-slate-100 dark:bg-slate-800 p-1 gap-1 text-xs font-bold mb-4">
                <button type="button" @click="activeTab = 'android'" 
                        :class="activeTab === 'android' ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'" 
                        class="flex-1 py-1.5 rounded-lg transition text-center cursor-pointer">
                    🤖 Android (Chrome)
                </button>
                <button type="button" @click="activeTab = 'ios'" 
                        :class="activeTab === 'ios' ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'" 
                        class="flex-1 py-1.5 rounded-lg transition text-center cursor-pointer">
                    🍎 iPhone / iPad
                </button>
                <button type="button" @click="activeTab = 'pc'" 
                        :class="activeTab === 'pc' ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'" 
                        class="flex-1 py-1.5 rounded-lg transition text-center cursor-pointer">
                    💻 Laptop / PC
                </button>
            </div>

            <!-- Tab A: Android Chrome -->
            <div x-show="activeTab === 'android'" class="space-y-2.5 text-xs">
                <div class="bg-blue-50 dark:bg-blue-950/40 p-3 rounded-xl border border-blue-200/60 dark:border-blue-800/60 text-blue-900 dark:text-blue-200">
                    <p class="font-bold mb-1">A. Untuk Pengguna Android (Chrome):</p>
                    <ol class="list-decimal list-inside space-y-1.5 text-[11px] sm:text-xs">
                        <li>Buka link web ini di <strong>Google Chrome HP</strong>.</li>
                        <li>Akan muncul banner di bawah bertuliskan <strong>"Pasang Aplikasi PT. Asia Plastik (WebAPK)"</strong> &rarr; Klik tombol <strong>"Install"</strong>.</li>
                        <li><em>(Atau jika banner tidak muncul: ketuk titik tiga <strong>(⋮)</strong> di pojok kanan atas Chrome &rarr; Pilih <strong>"Instal aplikasi"</strong> atau <strong>"Tambahkan ke Layar Utama"</strong>).</em></li>
                        <li>Sistem Android akan memproses dan memasang ikon aplikasi di layar utama & daftar aplikasi HP Anda.</li>
                    </ol>
                </div>
                <p class="text-[11px] text-slate-500 dark:text-slate-400 italic">
                    ✨ Buka aplikasinya: tampilannya bersih tanpa bar browser, cepat, dan terasa seperti aplikasi Android asli!
                </p>
                <button type="button" @click="if (deferredPrompt) { installApp(); } else { alert('Silakan ketuk titik tiga (⋮) di Chrome lalu pilih \'Instal aplikasi\'.'); }" class="w-full mt-2 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs shadow-md transition cursor-pointer">
                    {{ __('Coba Munculkan Prompt Install Sekarang') }}
                </button>
            </div>

            <!-- Tab B: iPhone / iPad (Safari) -->
            <div x-show="activeTab === 'ios'" class="space-y-2.5 text-xs" style="display: none;">
                <div class="bg-slate-50 dark:bg-slate-800/60 p-3.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200">
                    <p class="font-bold mb-1.5">B. Untuk Pengguna iPhone / iPad (Safari):</p>
                    <ol class="list-decimal list-inside space-y-2 text-[11px] sm:text-xs">
                        <li>Buka link web ini di browser <strong>Safari</strong>.</li>
                        <li>Ketuk tombol <strong>Bagikan (Share) 📤</strong> di bagian bawah layar browser.</li>
                        <li>Gulir ke bawah dan pilih <strong>"Tambahkan ke Layar Utama" (Add to Home Screen) ➕</strong>.</li>
                        <li>Ketuk <strong>Tambah</strong> di pojok kanan atas. Ikon aplikasi akan langsung muncul di beranda iOS Anda.</li>
                    </ol>
                </div>
            </div>

            <!-- Tab C: Laptop / PC (Chrome / Edge) -->
            <div x-show="activeTab === 'pc'" class="space-y-2.5 text-xs" style="display: none;">
                <div class="bg-indigo-50 dark:bg-indigo-950/40 p-3.5 rounded-xl border border-indigo-200/60 dark:border-indigo-800/60 text-indigo-950 dark:text-indigo-200">
                    <p class="font-bold mb-1.5">C. Untuk Laptop / PC (Chrome / Edge):</p>
                    <ol class="list-decimal list-inside space-y-2 text-[11px] sm:text-xs">
                        <li>Buka web ini di browser <strong>Google Chrome</strong> atau <strong>Microsoft Edge</strong>.</li>
                        <li>Di ujung kanan bilah alamat URL (address bar) akan muncul ikon komputer kecil dengan tanda panah bawah <strong>"Install app"</strong> atau simbol <strong>(+)</strong>.</li>
                        <li>Klik untuk menginstalnya sebagai aplikasi desktop tersendiri di Windows / Mac.</li>
                    </ol>
                </div>
                <button type="button" @click="if (deferredPrompt) { installApp(); } else { alert('Periksa ikon Install di bilah alamat URL browser Anda.'); }" class="w-full mt-2 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shadow-md transition cursor-pointer">
                    {{ __('Install di Laptop / Desktop') }}
                </button>
            </div>

            <button type="button" @click="showGuideModal = false" class="mt-4 w-full py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold text-xs hover:bg-slate-200 dark:hover:bg-slate-700 transition cursor-pointer">
                {{ __('Tutup') }}
            </button>
        </div>
    </div>
</div>