<?php

namespace App\Libraries;

use GuzzleHttp\Client;

class GeminiService
{
    protected $apiKey;
    protected $client;
    // Menggunakan model default 1.5-flash (bisa diganti sesuai kebutuhan)
   // Ganti v1beta menjadi v1
    protected $apiUrl = "https://generativelanguage.googleapis.com/v1beta/models/gemini-3.5-flash:generateContent";

    public function __construct()
    {
        $this->apiKey = env('GEMINI_API_KEY');
        $this->client = new Client();
    }

    /**
     * Fungsi Universal untuk Memproses Prompt (Teks saja atau Teks + Gambar)
     * * @param string $prompt Instruksi text untuk Gemini
     * @param array|null $base64Image ['data' => '...', 'mimeType' => '...'] jika ingin menyertakan gambar
     * @param array|null $responseSchema Struktur JSON jika ingin return berupa JSON terstruktur
     * @return mixed Array (jika menggunakan schema) atau String teks biasa
     */
    public function generate(string $prompt, ?array $base64Image = null, ?array $responseSchema = null)
    {
        // var_dump($this->apiKey, $this->apiUrl . "?key=" . $this->apiKey); // Debugging: Tampilkan API Key
        // 1. Bersihkan data base64 dari prefix bawaan browser / plugin jika ada
        if ($base64Image !== null && isset($base64Image['data'])) {
            if (preg_match('/^data:image\/(\w+);base64,/', $base64Image['data'], $type)) {
                $base64Image['data'] = substr($base64Image['data'], strpos($base64Image['data'], ',') + 1);
                $base64Image['mimeType'] = 'image/' . strtolower($type[1]);
            }
        }

        // 1. Bangun komponen utama part text
        $parts = [
            ['text' => $prompt]
        ];

        // 2. Jika ada data gambar, masukkan ke dalam parts
        if ($base64Image !== null && isset($base64Image['data'], $base64Image['mimeType'])) {
            $parts[] = [
                'inlineData' => [
                    'mimeType' => $base64Image['mimeType'],
                    'data'     => $base64Image['data']
                ]
            ];
        }

        // 3. Set payload dasar
        $payload = [
            'contents' => [
                [
                    'parts' => $parts
                ]
            ],
            'generationConfig' => [
                'temperature' => 0.2
            ]
        ];

        // 4. Jika user meminta hasil berformat JSON terstruktur (mengirimkan schema)
        // 3. Modifikasi Format Schema (Wajib Huruf Kecil: object, string, array)
        if ($responseSchema !== null) {
            $payload['generationConfig']['response_mime_type'] = 'application/json';
            $payload['generationConfig']['response_schema']   = $this->formatSchemaToLowerCase($responseSchema);
        }
        // echo json_encode($payload); // Debugging: Tampilkan payload sebelum dikirim ke API
        try {
            $response = $this->client->post($this->apiUrl . "?key=" . $this->apiKey, [
                'json'    => $payload,
                'headers' => ['Content-Type' => 'application/json']
            ]);
            // var_dump($response->getBody()->getContents()); // Debugging: Tampilkan respons mentah dari API
            $result = json_decode($response->getBody()->getContents(), true);
            $outputString = $result['candidates'][0]['content']['parts'][0]['text'] ?? '';
            
            // var_dump($outputString); // Debugging: Tampilkan output string sebelum diproses lebih lanjut
            // Jika dari awal meminta schema JSON, otomatis return berupa Array PHP bersih
            if ($responseSchema !== null) {
                return json_decode($outputString, true);
            }

            // Jika tidak pakai schema, return berupa teks string biasa
            return $outputString;

        } catch (\Exception $e) {
            var_dump('error', $e->getMessage()); // Debugging: Tampilkan pesan error
            log_message('error', 'Gemini Universal Service Error: ' . $e->getMessage());
            return null;
        }
    }

    private function formatSchemaToLowerCase(array $schema): array
    {
        foreach ($schema as $key => $value) {
            if ($key === 'type' && is_string($value)) {
                $schema[$key] = strtolower($value);
            } elseif (is_array($value)) {
                $schema[$key] = $this->formatSchemaToLowerCase($value);
            }
        }
        return $schema;
    }
}