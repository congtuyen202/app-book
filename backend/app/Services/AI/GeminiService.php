<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log; // Để ghi log lỗi

class GeminiService
{
    protected $apiKey;
    protected $baseUrl = 'https://generativelanguage.googleapis.com/v1beta';

    public function __construct()
    {
        $this->apiKey = config('services.gemini.key');
        if (empty($this->apiKey)) {
            throw new \Exception('Gemini API Key is not set in .env or config/services.php');
        }
    }

    /**
     * Gửi một yêu cầu tạo nội dung văn bản đến Gemini Pro.
     *
     * @param string $prompt Câu lệnh/nội dung yêu cầu từ người dùng.
     * @param float $temperature Độ ngẫu nhiên của phản hồi (0.0 - 1.0).
     * @param int $maxOutputTokens Số lượng token tối đa trong phản hồi.
     * @return string Nội dung được tạo bởi Gemini.
     * @throws \Exception Nếu API request thất bại.
     */
    public function generateText(string $prompt, float $temperature = 0.7, int $maxOutputTokens = 1000): string
    {
        $response = Http::post("{$this->baseUrl}/models/gemini-2.0-flash:generateContent?key={$this->apiKey}", [
            'contents' => [
                ['parts' => [['text' => $prompt]]]
            ],
            'generationConfig' => [
                'temperature' => $temperature,
                'maxOutputTokens' => $maxOutputTokens,
            ],
        ]);

        if ($response->successful()) {
            // Kiểm tra xem có 'candidates' và 'parts' hợp lệ không
            $text = $response->json('candidates.0.content.parts.0.text');
            if (isset($text)) {
                return $text;
            }
            Log::warning('Gemini API: No text content found in successful response.', ['response' => $response->json()]);
            return 'Không thể tạo nội dung từ Gemini.';
        }

        Log::error('Gemini API Request Failed', [
            'status' => $response->status(),
            'response' => $response->json(),
            'prompt' => $prompt
        ]);
        throw new \Exception('Gemini API request failed: ' . ($response->json('error.message') ?? 'Unknown error'));
    }

    /**
     * Gửi yêu cầu phân tích hình ảnh đến Gemini Pro Vision.
     *
     * @param string $base64ImageData Dữ liệu hình ảnh dưới dạng base64 (ví dụ: PNG, JPEG).
     * @param string $mimeType Kiểu MIME của hình ảnh (ví dụ: 'image/jpeg', 'image/png').
     * @param string $prompt Câu lệnh yêu cầu phân tích hình ảnh.
     * @param float $temperature Độ ngẫu nhiên của phản hồi.
     * @return string Mô tả hình ảnh từ Gemini.
     * @throws \Exception Nếu API request thất bại.
     */
    public function describeImage(string $base64ImageData, string $mimeType, string $prompt = "Describe this image in detail and identify any books or literary elements.", float $temperature = 0.4): string
    {
        $response = Http::post("{$this->baseUrl}/models/gemini-pro-vision:generateContent?key={$this->apiKey}", [
            'contents' => [
                ['parts' => [
                    ['text' => $prompt],
                    ['inline_data' => [
                        'mime_type' => $mimeType,
                        'data' => $base64ImageData,
                    ]]
                ]]
            ],
            'generationConfig' => [
                'temperature' => $temperature,
            ],
        ]);

        if ($response->successful()) {
            $text = $response->json('candidates.0.content.parts.0.text');
            if (isset($text)) {
                return $text;
            }
            Log::warning('Gemini Vision API: No text content found in successful response.', ['response' => $response->json()]);
            return 'Không thể mô tả hình ảnh từ Gemini.';
        }

        Log::error('Gemini Vision API Request Failed', [
            'status' => $response->status(),
            'response' => $response->json(),
            'prompt' => $prompt
        ]);
        throw new \Exception('Gemini Vision API request failed: ' . ($response->json('error.message') ?? 'Unknown error'));
    }

    // Bạn có thể thêm các phương thức khác tại đây (ví dụ: chat, function calling, v.v.)
}