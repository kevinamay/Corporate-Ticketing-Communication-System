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

    public function mount(): void
    {
        if (empty($this->messages)) {
            $this->messages[] = [
                'id' => uniqid('msg_', true),
                'sender' => 'bot',
                'text' => "Halo! 👋 Saya **AsiaBot**, Asisten AI pintar PT. Asia Plastik.\n\nAda yang bisa saya bantu terkait pembuatan tiket dukungan, status perbaikan mesin, info departemen, atau operasional pabrik?",
                'time' => now()->format('H:i'),
            ];
        }
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
                'text' => "Percakapan telah direset. Halo! 👋 Ada yang bisa saya bantu kembali terkait sistem tiket atau kendala operasional di PT. Asia Plastik?",
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
