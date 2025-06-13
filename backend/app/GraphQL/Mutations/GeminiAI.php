<?php

namespace App\GraphQL\Mutations;

use App\Services\AI\GeminiService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth; // Để lấy thông tin user cho log/ghi vào DB
use App\Models\AiRequestLog; // Để lưu nhật ký request AI

final class GeminiAI
{
    protected $geminiService;

    public function __construct(GeminiService $geminiService)
    {
        $this->geminiService = $geminiService;
    }

    /**
     * Xử lý yêu cầu tạo nội dung văn bản bằng Gemini.
     *
     * @param  null  $_
     * @param  array<string, mixed>  $args 'prompt', 'usage_type'
     * @return string
     * @throws \Exception
     */
    public function generateTextContent($_, array $args): string
    {
        $user = Auth::user(); // Lấy người dùng hiện tại
        $prompt = $args['prompt'];
        $usageType = $args['usage_type']; // Ví dụ: 'book_idea', 'summary_chapter', 'character_analysis'

        $aiOutput = '';
        $status = 'success';
        $errorMessage = null;

        try {
            // Tùy chỉnh prompt dựa trên usage_type (nếu cần)
            $fullPrompt = match($usageType) {
                'book_idea' => "Generate a creative book idea based on this theme: {$prompt}",
                'summary_chapter' => "Summarize the following chapter content: {$prompt}",
                'character_analysis' => "Analyze the character based on this description: {$prompt}",
                default => $prompt // Sử dụng prompt trực tiếp nếu không khớp loại nào
            };

            $aiOutput = $this->geminiService->generateText($fullPrompt);

        } catch (\Exception $e) {
            $status = 'failed';
            $errorMessage = $e->getMessage();
            Log::error('Gemini Text Generation Error', ['user_id' => $user->id ?? null, 'prompt' => $prompt, 'error' => $errorMessage]);
            throw $e; // Ném lại lỗi để GraphQL trả về cho client
        } finally {
            // Ghi nhật ký yêu cầu AI vào database
            AiRequestLog::create([
                'user_id' => $user->id ?? null,
                'ai_model_name' => 'Gemini-Pro',
                'request_type' => 'generate_text_' . $usageType,
                'input_text' => $prompt,
                'output_text' => $aiOutput,
                'tokens_used' => ceil(strlen($fullPrompt) / 4) + ceil(strlen($aiOutput) / 4), // Ước tính token
                'cost' => null, // Bạn sẽ cần tính toán chi phí thực tế dựa trên API docs của Gemini
                'status' => $status,
                'error_message' => $errorMessage,
                'requested_at' => now(),
            ]);
        }

        return $aiOutput;
    }

    public function describeImageContent($_, array $args): string
    {
        $user = Auth::user();
        $base64ImageData = $args['imageData'];
        $mimeType = $args['mimeType'];

        $aiOutput = '';
        $status = 'success';
        $errorMessage = null;

        try {
            // Loại bỏ phần header "data:image/jpeg;base64," nếu có
            $dataParts = explode(',', $base64ImageData);
            $cleanBase64Data = count($dataParts) > 1 ? $dataParts[1] : $dataParts[0];

            $aiOutput = $this->geminiService->describeImage($cleanBase64Data, $mimeType);

        } catch (\Exception $e) {
            $status = 'failed';
            $errorMessage = $e->getMessage();
            Log::error('Gemini Image Description Error', ['user_id' => $user->id ?? null, 'error' => $errorMessage]);
            throw $e;
        } finally {
            // Ghi nhật ký yêu cầu AI vào database
            AiRequestLog::create([
                'user_id' => $user->id ?? null,
                'ai_model_name' => 'Gemini-Pro-Vision',
                'request_type' => 'describe_image',
                'input_text' => 'Image (Base64) - ' . $mimeType, // Không lưu toàn bộ base64 vào log
                'output_text' => $aiOutput,
                'tokens_used' => null, // Gemini Vision có thể không trả về token count trực tiếp, cần kiểm tra docs
                'cost' => null, // Tính toán chi phí thực tế
                'status' => $status,
                'error_message' => $errorMessage,
                'requested_at' => now(),
            ]);
        }

        return $aiOutput;
    }
}