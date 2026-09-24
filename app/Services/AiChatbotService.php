<?php

namespace App\Services;

use App\Models\Department;
use App\Models\Ticket;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiChatbotService
{
    /**
     * Main entry point to get an AI answer.
     *
     * @param  array<int, array{sender: string, text: string, time: string}>  $history
     */
    public function ask(string $message, array $history = []): string
    {
        $trimmedMessage = trim($message);

        if ($trimmedMessage === '') {
            return 'Halo! Silakan ketikkan pertanyaan atau kendala yang ingin Anda konsultasikan seputar sistem tiket dan operasional PT. Asia Plastik.';
        }

        // 1. Try external AI API if configured (Gemini / OpenAI / Groq)
        $externalResponse = $this->tryExternalAi($trimmedMessage, $history);
        if ($externalResponse !== null && trim($externalResponse) !== '') {
            return $externalResponse;
        }

        // 2. Fallback to our Intelligent Local AI Knowledge Engine
        return $this->generateLocalAiResponse($trimmedMessage);
    }

    /**
     * Try querying an external AI API (Gemini, OpenAI, or Groq) if API keys are available.
     *
     * @param  array<int, array{sender: string, text: string, time: string}>  $history
     */
    protected function tryExternalAi(string $message, array $history = []): ?string
    {
        $geminiKey = env('GEMINI_API_KEY') ?: Config::get('services.ai.gemini_key');
        $openaiKey = env('OPENAI_API_KEY') ?: Config::get('services.ai.openai_key');
        $groqKey = env('GROQ_API_KEY') ?: Config::get('services.ai.groq_key');

        // Priority 1: Google Gemini (generous free tier)
        if (! empty($geminiKey)) {
            $geminiResponse = $this->callGemini($geminiKey, $message, $history);
            if ($geminiResponse !== null) {
                return $geminiResponse;
            }
        }

        // Priority 2: OpenAI API
        if (! empty($openaiKey)) {
            $openaiResponse = $this->callOpenAi($openaiKey, $message, $history);
            if ($openaiResponse !== null) {
                return $openaiResponse;
            }
        }

        // Priority 3: Groq API
        if (! empty($groqKey)) {
            $groqResponse = $this->callGroq($groqKey, $message, $history);
            if ($groqResponse !== null) {
                return $groqResponse;
            }
        }

        return null;
    }

    /**
     * Call Google Gemini API (gemini-1.5-flash).
     *
     * @param  array<int, array{sender: string, text: string, time: string}>  $history
     */
    protected function callGemini(string $apiKey, string $message, array $history = []): ?string
    {
        try {
            $model = env('GEMINI_MODEL', 'gemini-1.5-flash');
            $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

            $systemInstruction = $this->getSystemPrompt();

            $contents = [];
            // Add short history
            $recentHistory = array_slice($history, -6);
            foreach ($recentHistory as $item) {
                $role = ($item['sender'] === 'user') ? 'user' : 'model';
                $contents[] = [
                    'role' => $role,
                    'parts' => [['text' => $item['text']]],
                ];
            }

            // Add current message
            $contents[] = [
                'role' => 'user',
                'parts' => [['text' => $message]],
            ];

            $response = Http::timeout(10)->post($url, [
                'system_instruction' => [
                    'parts' => [['text' => $systemInstruction]],
                ],
                'contents' => $contents,
                'generationConfig' => [
                    'temperature' => 0.7,
                    'maxOutputTokens' => 800,
                ],
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $reply = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
                if (! empty($reply)) {
                    return trim($reply);
                }
            } else {
                Log::warning('Gemini API call returned non-200: '.$response->body());
            }
        } catch (\Throwable $e) {
            Log::warning('Gemini API call failed: '.$e->getMessage());
        }

        return null;
    }

    /**
     * Call OpenAI API.
     *
     * @param  array<int, array{sender: string, text: string, time: string}>  $history
     */
    protected function callOpenAi(string $apiKey, string $message, array $history = []): ?string
    {
        try {
            $model = env('OPENAI_MODEL', 'gpt-4o-mini');
            $messages = [
                ['role' => 'system', 'content' => $this->getSystemPrompt()],
            ];

            $recentHistory = array_slice($history, -6);
            foreach ($recentHistory as $item) {
                $role = ($item['sender'] === 'user') ? 'user' : 'assistant';
                $messages[] = ['role' => $role, 'content' => $item['text']];
            }

            $messages[] = ['role' => 'user', 'content' => $message];

            $response = Http::timeout(10)->withToken($apiKey)->post('https://api.openai.com/v1/chat/completions', [
                'model' => $model,
                'messages' => $messages,
                'temperature' => 0.7,
                'max_tokens' => 800,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $reply = $data['choices'][0]['message']['content'] ?? null;
                if (! empty($reply)) {
                    return trim($reply);
                }
            }
        } catch (\Throwable $e) {
            Log::warning('OpenAI API call failed: '.$e->getMessage());
        }

        return null;
    }

    /**
     * Call Groq API.
     *
     * @param  array<int, array{sender: string, text: string, time: string}>  $history
     */
    protected function callGroq(string $apiKey, string $message, array $history = []): ?string
    {
        try {
            $model = env('GROQ_MODEL', 'llama-3.3-70b-versatile');
            $messages = [
                ['role' => 'system', 'content' => $this->getSystemPrompt()],
            ];

            $recentHistory = array_slice($history, -6);
            foreach ($recentHistory as $item) {
                $role = ($item['sender'] === 'user') ? 'user' : 'assistant';
                $messages[] = ['role' => $role, 'content' => $item['text']];
            }

            $messages[] = ['role' => 'user', 'content' => $message];

            $response = Http::timeout(10)->withToken($apiKey)->post('https://api.groq.com/openai/v1/chat/completions', [
                'model' => $model,
                'messages' => $messages,
                'temperature' => 0.7,
                'max_tokens' => 800,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $reply = $data['choices'][0]['message']['content'] ?? null;
                if (! empty($reply)) {
                    return trim($reply);
                }
            }
        } catch (\Throwable $e) {
            Log::warning('Groq API call failed: '.$e->getMessage());
        }

        return null;
    }

    /**
     * System prompt establishing AI persona and company knowledge.
     */
    protected function getSystemPrompt(): string
    {
        $ticketCount = Ticket::count();
        $pendingCount = Ticket::where('status', 'Pending')->count();
        $inProgressCount = Ticket::where('status', 'In Progress')->count();

        return <<<PROMPT
Anda adalah "AsiaBot", asisten AI resmi dan ramah dari PT. ASIA PLASTIK (Perusahaan manufaktur terkemuka di bidang Plastic Packaging, Blow Molding, dan Injection Molding di Surabaya).
Fokus utama Anda adalah membantu karyawan, staf produksi, teknisi, dan manajemen dalam sistem Corporate Ticketing & Communication.

Informasi Internal Terkini:
- Total Tiket di Sistem: {$ticketCount} (Menunggu: {$pendingCount}, Dikerjakan: {$inProgressCount}).
- Departemen yang Tersedia: Information Technology (IT), Maintenance / Mekanik Mesin, Quality Control (QC), Produksi, Logistik & Gudang, HR & GA, Finance, Purchasing.
- Tingkat Prioritas Tiket:
  * Emergency: Menghentikan lini pabrik / kecelakaan kerja (respon < 15 menit).
  * High: Menghambat kapasitas lini > 30% (respon < 1 jam).
  * Medium: Gangguan parsial tanpa shutdown lini (respon < 4 jam).
  * Low: Permintaan operasional/administrasi rutin (respon < 24 jam).

Gaya Komunikasi:
- Ramah, profesional, solutif, ringkas, dan jelas.
- Menggunakan bahasa yang sama dengan pengguna (default: Bahasa Indonesia).
- Format jawaban dengan rapi menggunakan poin atau teks tebal bila diperlukan.
PROMPT;
    }

    /**
     * Comprehensive Local AI Knowledge & NLP Engine for PT. Asia Plastik.
     */
    protected function generateLocalAiResponse(string $query): string
    {
        $lower = strtolower($query);

        // 1. Ticket status lookup by Ticket ID (e.g. "tiket #5", "cek tiket 12", "status tiket 3")
        if (preg_match('/(?:tiket|ticket)\s*(?:nomor|no|#)?\s*(\d+)/i', $query, $matches)) {
            $ticketId = (int) $matches[1];
            $ticket = Ticket::with(['department', 'user'])->find($ticketId);

            if ($ticket) {
                $deptName = $ticket->department?->name ?? 'Belum ditentukan';
                $userName = $ticket->user?->name ?? 'Anonim';
                $dateFormatted = $ticket->created_at ? $ticket->created_at->format('d M Y, H:i') : '-';

                $badgeStatus = match (strtolower($ticket->status)) {
                    'pending' => '🟡 Menunggu Penanganan (Pending)',
                    'in progress' => '🔵 Sedang Dikerjakan (In Progress)',
                    'resolved' => '🟢 Selesai Ditangani (Resolved)',
                    default => '⚪ '.$ticket->status
                };

                return "📋 **Detail Tiket #{$ticket->id}**\n\n"
                    ."• **Judul:** {$ticket->title}\n"
                    ."• **Status:** {$badgeStatus}\n"
                    ."• **Prioritas:** {$ticket->priority}\n"
                    ."• **Kategori:** {$ticket->category}\n"
                    ."• **Departemen Tujuan:** {$deptName}\n"
                    ."• **Pelapor:** {$userName}\n"
                    ."• **Waktu Dibuat:** {$dateFormatted}\n\n"
                    ."💡 *Anda dapat memantau percakapan langsung atau menambahkan catatan melalui panel Live Chat Desk.*";
            } else {
                return "Maaf, tiket dengan **ID #{$ticketId}** tidak ditemukan dalam database sistem. Pastikan nomor tiket yang Anda masukkan sudah benar, atau Anda dapat melihat daftar semua tiket di tabel antrean.";
            }
        }

        // 2. General Ticket Queue / Status Inquiry
        if (str_contains($lower, 'antrean') || str_contains($lower, 'status tiket') || str_contains($lower, 'berapa tiket') || str_contains($lower, 'cek tiket')) {
            $total = Ticket::count();
            $pending = Ticket::where('status', 'Pending')->count();
            $inProgress = Ticket::where('status', 'In Progress')->count();
            $resolved = Ticket::where('status', 'Resolved')->count();

            return "📊 **Ringkasan Status Tiket Terkini PT. Asia Plastik:**\n\n"
                ."• **Total Tiket Terdaftar:** {$total}\n"
                ."• 🟡 **Menunggu Penanganan (Pending):** {$pending} tiket\n"
                ."• 🔵 **Sedang Dikerjakan (In Progress):** {$inProgress} tiket\n"
                ."• 🟢 **Selesai (Resolved):** {$resolved} tiket\n\n"
                ."Untuk mengecek tiket spesifik, ketik saja: *'Cek tiket #[nomor]'* (contoh: *Cek tiket #1*).";
        }

        // 3. How to create ticket
        if (str_contains($lower, 'buat tiket') || str_contains($lower, 'cara lapor') || str_contains($lower, 'ajukan komplain') || str_contains($lower, 'submit support') || str_contains($lower, 'cara buat')) {
            return "📝 **Langkah Mudah Membuat Tiket Dukungan:**\n\n"
                ."1. **Pilih Departemen Tujuan:** Tentukan divisi yang berwenang (misal: *Maintenance* untuk kendala mesin, atau *IT* untuk kendala jaringan/komputer).\n"
                ."2. **Pilih Kategori Kendala:** Dropdown kategori akan otomatis menyesuaikan dengan departemen terpilih.\n"
                ."3. **Tentukan Prioritas:**\n"
                ."   - **Emergency:** Lini pabrik terhenti total / bahaya K3.\n"
                ."   - **High:** Kapasitas produksi terganggu parah.\n"
                ."   - **Medium:** Gangguan standar tanpa henti total.\n"
                ."   - **Low:** Permintaan rutin/konsultasi.\n"
                ."4. **Tuliskan Judul & Deskripsi:** Jelaskan lokasi mesin, shift, dan kronologi kendala secara rinci.\n"
                ."5. **Unggah Bukti Foto (Opsional):** Lampirkan foto kondisi mesin atau produk cacat untuk mempercepat analisa teknisi.\n"
                ."6. Klik tombol **'Kirim Tiket Dukungan'**.\n\n"
                ."💡 *Formulir dapat Anda temukan langsung di bagian atas dashboard ('Ajukan Permintaan Dukungan').*";
        }

        // 4. Injection Molding Troubleshooting
        if (str_contains($lower, 'injection') || str_contains($lower, 'injeksi') || str_contains($lower, 'moulding') || str_contains($lower, 'cetakan')) {
            if (str_contains($lower, 'flash') || str_contains($lower, 'burr') || str_contains($lower, 'sirip')) {
                return "⚙️ **Troubleshooting: Masalah Flash / Burrs (Sirip Plastik Berlebih):**\n\n"
                    ."• **Penyebab Utama:**\n"
                    ."  1. Tekanan injeksi (Injection Pressure) atau Holding Pressure terlalu tinggi.\n"
                    ."  2. Clamping Force (daya jepit tonase mesin) kurang kuat menahan rongga mould.\n"
                    ."  3. Parting line pada mould aus, terganjal kotoran gram, atau suhu mould terlalu panas.\n"
                    ."• **Langkah Rekomendasi:**\n"
                    ."  - Turunkan holding pressure secara bertahap (5-10 bar).\n"
                    ."  - Periksa kebersihan parting line cetakan dari sisa resin.\n"
                    ."  - Buat tiket darurat ke departemen **Maintenance / Mekanik** bila mould mengalami keausan mekanis.";
            }

            if (str_contains($lower, 'short shot') || str_contains($lower, 'tidak penuh') || str_contains($lower, 'kurang isi')) {
                return "⚙️ **Troubleshooting: Masalah Short Shot (Produk Tidak Terisi Penuh):**\n\n"
                    ."• **Penyebab:**\n"
                    ."  1. Suhu barrel atau nozzle terlalu dingin sehingga viskositas lelehan resin kaku.\n"
                    ."  2. Dosis injection shot size kurang mencukupi rongga cavity.\n"
                    ."  3. Lubang venting pada cetakan tersumbat gas deposit.\n"
                    ."• **Langkah Rekomendasi:**\n"
                    ."  - Naikkan suhu barrel zona depan dan nozzle sebesar 5°C.\n"
                    ."  - Tingkatkan shot size atau switch-over position (V/P transfer point).\n"
                    ."  - Bersihkan venting groove cetakan.";
            }

            return "⚙️ **Panduan Teknis Injection Molding PT. Asia Plastik:**\n\n"
                ."Mesin injection kami beroperasi presisi tinggi untuk memproduksi tutup botol, toples, dan komponen kemasan plastik.\n\n"
                ."Kategori kendala yang sering ditangani:\n"
                ."• **Flash / Burrs:** Turunkan injection pressure atau periksa clamping force.\n"
                ."• **Short Shot:** Cek shot size, temperatur lelehan resin, dan venting mould.\n"
                ."• **Sink Marks (Cekung):** Tambah holding time dan maksimalkan cooling time.\n"
                ."• **Warpage (Melengkung):** Samakan temperatur core vs cavity mould chiller.\n\n"
                ."Silakan ajukan tiket ke **Maintenance / Mekanik** bila memerlukan pergantian sparepart atau kalibrasi servo.";
        }

        // 5. Blow Molding Troubleshooting
        if (str_contains($lower, 'blow') || str_contains($lower, 'tiup') || str_contains($lower, 'botol tipis') || str_contains($lower, 'parison')) {
            return "🍾 **Panduan Teknis Blow Molding PT. Asia Plastik:**\n\n"
                ."Mesin blow moulding kami digunakan untuk produksi jerigen HDPE, botol kosmetik, dan botol farmasi.\n\n"
                ."• **Ketebalan Botol Tidak Rata:** Periksa die gap centering head extruder dan suhu parison.\n"
                ."• **Bocor / Pinhole pada Pinch-off:** Periksa ketajaman pinch-off blade dan pastikan pendinginan pisau optimal.\n"
                ."• **Bentuk Botol Kempot / Deformasi:** Periksa tekanan udara tiup (Blowing Air Pressure minimal 6-8 bar) dan durasi exhaust venting.";
        }

        // 6. IT Department & Technical Support
        if (str_contains($lower, 'it') || str_contains($lower, 'jaringan') || str_contains($lower, 'wifi') || str_contains($lower, 'komputer') || str_contains($lower, 'internet') || str_contains($lower, 'password') || str_contains($lower, 'login')) {
            return "💻 **Bantuan Divisi Information Technology (IT):**\n\n"
                ."Divisi IT siap melayani kendala:\n"
                ."• **Jaringan & Internet:** Koneksi LAN terputus, Wi-Fi pabrik lambat, VPN kantor.\n"
                ."• **Hardware & Komputer:** PC timbangan mati, printer label barcode rusak, monitor ruang kontrol error.\n"
                ."• **Sistem & ERP:** Kendala login akun, reset password portal ticketing, modul inventori resin.\n\n"
                ."📞 **Kontak Darurat IT:** Ext. 102 / WhatsApp Helpdesk IT (08:00 - 20:00 WIB).\n"
                ."*Untuk respon tercatat dan terpantau SLA, silakan buat tiket ke Departemen 'Information Technology'.*";
        }

        // 7. HR, GA & K3 (Safety)
        if (str_contains($lower, 'hr') || str_contains($lower, 'ga') || str_contains($lower, 'k3') || str_contains($lower, 'keselamatan') || str_contains($lower, 'apd') || str_contains($lower, 'shift') || str_contains($lower, 'lembur')) {
            return "🦺 **Informasi HR, GA & K3 (Keselamatan Kerja):**\n\n"
                ."• **Keselamatan Kerja (K3) adalah Prioritas Utama:** Seluruh staf di area pabrik wajib mengenakan Safety Shoes, Earplug, dan Rompi APD.\n"
                ."• **Jadwal Shift Pabrik:**\n"
                ."  - Shift 1: 07.00 - 15.00 WIB\n"
                ."  - Shift 2: 15.00 - 23.00 WIB\n"
                ."  - Shift 3: 23.00 - 07.00 WIB\n"
                ."• **Insiden / Kecelakaan Kerja:** Wajib laporkan dengan **Prioritas EMERGENCY** agar Tim K3 & Medis langsung menuju lokasi dalam < 5 menit.";
        }

        // 8. Quality Control (QC)
        if (str_contains($lower, 'qc') || str_contains($lower, 'quality') || str_contains($lower, 'kualitas') || str_contains($lower, 'cacat') || str_contains($lower, 'defect') || str_contains($lower, 'reject')) {
            return "🔍 **Bantuan Departemen Quality Control (QC):**\n\n"
                ."Jika Anda menemukan lot produksi dengan angka reject tinggi:\n"
                ."1. Segera beri tanda isolasi / label **HOLD** pada pallet produk.\n"
                ."2. Buat tiket ke departemen **Quality Control** dengan melampirkan foto cacat fisik produk.\n"
                ."3. Tim QC akan segera datang ke lini untuk uji dimensi, ketebalan ultrasonik, dan tes kebocoran (leak test).";
        }

        // 9. About PT. Asia Plastik & Contact
        if (str_contains($lower, 'wa') || str_contains($lower, 'whatsapp') || str_contains($lower, 'telepon') || str_contains($lower, 'kontak') || str_contains($lower, 'call') || str_contains($lower, 'hubungi')) {
            return "📞 **Kontak Resmi & WhatsApp PT. ASIA PLASTIK:**\n\n"
                ."• **Phone / WhatsApp:** +6231 8433078 / 8439998\n"
                ."• **Kantor & Pabrik:** Kawasan Industri Rungkut, Surabaya, Jawa Timur\n"
                ."• **Jam Layanan:** 24 Jam Operasional Pabrik\n\n"
                ."💡 *Anda juga dapat langsung mengklik tombol hijau **WhatsApp** di sudut kanan bawah untuk terhubung langsung ke Customer Service kami.*";
        }

        if (str_contains($lower, 'asia plastik') || str_contains($lower, 'profil') || str_contains($lower, 'perusahaan') || str_contains($lower, 'pabrik') || str_contains($lower, 'alamat')) {
            return "🏭 **Tentang PT. ASIA PLASTIK:**\n\n"
                ."PT. Asia Plastik adalah perusahaan manufaktur kemasan plastik terintegrasi di Surabaya, Jawa Timur dengan spesialisasi:\n"
                ."• **Injection Molding:** Tutup botol presisi, preform, wadah kemasan kaku.\n"
                ."• **Blow Molding:** Botol HDPE, PET, botol agrokimia, dan jerigen industri.\n"
                ."• **Alamat Pabrik & Kantor:** Kawasan Industri Rungkut, Surabaya, Jawa Timur, Indonesia.\n"
                ."• **Phone / WhatsApp:** +6231 8433078 / 8439998\n"
                ."• **Operasional:** 24 Jam Non-Stop (3 Shift) dengan standar manajemen mutu bersertifikasi ISO.";
        }

        // 10. Greetings & Friendly chat
        if (str_contains($lower, 'halo') || str_contains($lower, 'hai') || str_contains($lower, 'hello') || str_contains($lower, 'selamat pagi') || str_contains($lower, 'selamat siang') || str_contains($lower, 'selamat malam')) {
            return "Halo! 👋 Selamat datang di Layanan Bantuan Asisten AI PT. Asia Plastik.\n\n"
                ."Ada yang bisa saya bantu hari ini? Anda bisa menanyakan:\n"
                ."• *'Cara buat tiket baru'*\n"
                ."• *'Cek status tiket #1'*\n"
                ."• *'Kendala mesin injection/blow'* \n"
                ."• *'Kontak bantuan departemen IT / Maintenance'*";
        }

        if (str_contains($lower, 'terima kasih') || str_contains($lower, 'makasih') || str_contains($lower, 'thanks') || str_contains($lower, 'ok') || str_contains($lower, 'siap')) {
            return "Sama-sama! Senang bisa membantu Anda. 😊\n\nJika ada pertanyaan atau kendala operasional lainnya di pabrik, jangan ragu untuk bertanya kembali kapan saja. Selamat bekerja dan selalu utamakan keselamatan kerja (K3)!";
        }

        // 11. Multi-language quick responses
        if (preg_match('/[a-zA-Z]/', $query) && (str_contains($lower, 'how to') || str_contains($lower, 'ticket') || str_contains($lower, 'help'))) {
            return "Hello! 👋 I am **AsiaBot**, the AI Support Assistant for PT. Asia Plastik.\n\n"
                ."I can assist you with:\n"
                ."• **Ticket Creation:** Guide you through submitting maintenance or IT requests.\n"
                ."• **Ticket Status:** Track your ticket by typing *'Check ticket #ID'* (e.g. *Check ticket #1*).\n"
                ."• **Factory Operations:** Technical help for Injection & Blow Molding machines.\n\n"
                ."How may I assist you today?";
        }

        // Default intelligent fallback
        return "Terima kasih atas pesan Anda! 🤖\n\n"
            ."Saya adalah **AsiaBot**, Asisten Cerdas PT. Asia Plastik. Untuk membantu Anda dengan tepat, silakan pilih topik berikut:\n\n"
            ."1. **Tiket Dukungan:** Ketik *'Cara buat tiket'* atau *'Cek tiket #[nomor]'*.\n"
            ."2. **Kendala Mesin:** Ketik *'Masalah mesin injection'* atau *'Masalah blow molding'*.\n"
            ."3. **Divisi Internal:** Ketik *'Kontak IT'*, *'Divisi Maintenance'*, atau *'Info QC'*.\n"
            ."4. **Status Antrean:** Ketik *'Cek status tiket'* untuk melihat ringkasan antrean saat ini.\n\n"
            ."Apa yang ingin Anda ketahui lebih lanjut?";
    }
}
