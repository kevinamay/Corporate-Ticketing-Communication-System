<?php

namespace App\Livewire;

use App\Services\AiChatbotService;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class AiAssistant extends Component
{
    public bool $isOpen = false;

    public string $userInput = '';

    /**
     * @var array<int, array{id: string, sender: string, text: string, time: string}>
     */
    public array $messages = [];

    public bool $isTyping = false;

    /**
     * Quick suggestions displayed on the chat widget.
     *
     * @var array<int, string>
     */
    public array $suggestions = [
        '📝 Cara buat tiket dukungan',
        '📊 Cek status tiket terkini',
        '⚙️ Kendala mesin injection',
        '💻 Kontak tim IT Support',
        '🏭 Info pabrik PT Asia Plastik',
    ];

    public bool $showSettings = false;

    public string $aiProvider = 'gemini';

    public string $apiKey = '';

    public bool $isLiveConnected = false;

    public string $activeProviderName = 'Gemini';

    public function mount(AiChatbotService $aiService = new AiChatbotService): void
    {
        $info = $aiService->getActiveProviderInfo();
        $this->aiProvider = $info['provider'];
        $this->isLiveConnected = $info['is_live'];
        $this->activeProviderName = match ($this->aiProvider) {
            'openai' => 'OpenAI ChatGPT',
            'groq' => 'Groq (Llama 3.3)',
            default => 'Google Gemini',
        };

        if (empty($this->messages)) {
            $welcomeExtra = $this->isLiveConnected
                ? "\n\n✨ *AI Live Mode Aktif:* Saya terhubung langsung dengan AI API ({$this->activeProviderName}) dan siap menjawab pertanyaan apapun secara cerdas!"
                : '';

            $this->messages[] = [
                'id' => uniqid('msg_', true),
                'sender' => 'bot',
                'text' => "Halo! 👋 Saya **AsiaBot**, Asisten AI pintar PT. Asia Plastik.\n\nAda yang bisa saya bantu terkait pembuatan tiket dukungan, status perbaikan mesin, info departemen, operasional pabrik, maupun obrolan santai?{$welcomeExtra}",
                'time' => now()->format('H:i'),
            ];
        }
    }

    public function toggleSettings(): void
    {
        $this->showSettings = ! $this->showSettings;
    }

    public function saveSettings(AiChatbotService $aiService = new AiChatbotService): void
    {
        $this->validate([
            'aiProvider' => 'required|in:gemini,openai,groq',
            'apiKey' => 'nullable|string|max:255',
        ]);

        cache()->forever('ai_provider', $this->aiProvider);

        if (! empty($this->apiKey)) {
            $trimmedKey = trim($this->apiKey);
            cache()->forever("ai_{$this->aiProvider}_key", $trimmedKey);
            cache()->forever('ai_api_key', $trimmedKey);
        }

        $info = $aiService->getActiveProviderInfo();
        $this->isLiveConnected = $info['is_live'];
        $this->activeProviderName = match ($this->aiProvider) {
            'openai' => 'OpenAI ChatGPT',
            'groq' => 'Groq (Llama 3.3)',
            default => 'Google Gemini',
        };

        $this->showSettings = false;
        $this->apiKey = '';

        $statusMsg = $this->isLiveConnected
            ? "✅ **Pengaturan AI Berhasil Diaktifkan!**\n\nAsiaBot kini terhubung langsung dengan **{$this->activeProviderName}**. Silakan tanyakan hal apapun secara bebas (rekomendasi, kuliner, analisis, tips, dll), saya siap menjawab!"
            : "ℹ️ Provider disimpan ke **{$this->activeProviderName}**.\n\nMasukkan API Key untuk mengaktifkan respons AI real-time tanpa batas.";

        $this->messages[] = [
            'id' => uniqid('msg_', true),
            'sender' => 'bot',
            'text' => $statusMsg,
            'time' => now()->format('H:i'),
        ];

        $this->dispatch('chat-updated');
    }

    public function toggleChat(): void
    {
        $this->isOpen = ! $this->isOpen;

        if ($this->isOpen) {
            $this->dispatch('chat-opened');
        }
    }

    public function openChat(): void
    {
        $this->isOpen = true;
        $this->dispatch('chat-opened');
    }

    public function closeChat(): void
    {
        $this->isOpen = false;
    }

    public function sendMessage(?string $text = null, AiChatbotService $aiService = new AiChatbotService): void
    {
        $messageText = trim($text ?? $this->userInput);

        if ($messageText === '') {
            return;
        }

        // 1. Add user message
        $this->messages[] = [
            'id' => uniqid('msg_', true),
            'sender' => 'user',
            'text' => $messageText,
            'time' => now()->format('H:i'),
        ];

        $this->userInput = '';

        // 2. Fetch AI response
        $reply = $aiService->ask($messageText, $this->messages);

        // 3. Add bot message
        $this->messages[] = [
            'id' => uniqid('msg_', true),
            'sender' => 'bot',
            'text' => $reply,
            'time' => now()->format('H:i'),
        ];

        $this->dispatch('chat-updated');
    }

    public function sendSuggestion(string $prompt, AiChatbotService $aiService = new AiChatbotService): void
    {
        $this->sendMessage($prompt, $aiService);
    }

    public function resetChat(): void
    {
        $this->messages = [
            [
                'id' => uniqid('msg_', true),
                'sender' => 'bot',
                'text' => 'Percakapan telah direset. Halo! 👋 Ada yang bisa saya bantu kembali terkait sistem tiket atau kendala operasional di PT. Asia Plastik?',
                'time' => now()->format('H:i'),
            ],
        ];

        $this->dispatch('chat-updated');
    }

    public function render(): View
    {
        return view('livewire.ai-assistant');
    }
}
