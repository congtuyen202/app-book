<?php

namespace Modules\Community\Classes\Scalars;

use GraphQL\Type\Definition\ScalarType;
use GraphQL\Error\Error;
use DateTime as PhpDateTime;

class DateTime extends ScalarType
{
    public ?string $description = "The `DateTime` scalar type represents a date and time in ISO 8601 format (e.g., '2025-03-26T12:00:00Z').";

    /**
     * Serialize giá trị nội bộ (từ PHP) thành định dạng gửi ra client.
     */
    public function serialize($value)
    {
        \Log::info('DateTime serialize called with value: ' . json_encode($value));

        // Nếu giá trị là object DateTime, chuyển thành chuỗi ISO 8601
        if ($value instanceof PhpDateTime) {
            return $value->format('c'); // ISO 8601 format (e.g., 2025-03-26T12:00:00+00:00)
        }

        // Nếu là chuỗi, kiểm tra và chuyển đổi
        if (is_string($value)) {
            try {
                $date = new PhpDateTime($value);
                return $date->format('c');
            } catch (\Exception $e) {
                throw new Error("Cannot serialize value as DateTime: " . $e->getMessage());
            }
        }

        throw new Error("Cannot serialize value as DateTime: " . gettype($value));
    }

    /**
     * Parse giá trị từ client (thường là chuỗi) thành giá trị nội bộ.
     */
    public function parseValue($value)
    {

        if (!is_string($value)) {
            throw new Error("DateTime must be a string, got: " . gettype($value));
        }

        try {
            $date = new PhpDateTime($value);
            return $date; // Trả về object DateTime để dùng trong PHP
        } catch (\Exception $e) {
            throw new Error("Invalid DateTime format: " . $e->getMessage());
        }
    }

    /**
     * Parse literal từ query (AST node) thành giá trị nội bộ.
     */
    public function parseLiteral($valueNode, ?array $variables = null)
    {

        if (!property_exists($valueNode, 'value')) {
            throw new Error("DateTime scalar requires a string literal.");
        }

        try {
            $date = new PhpDateTime($valueNode->value);
            return $date; // Trả về object DateTime
        } catch (\Exception $e) {
            throw new Error("Invalid DateTime literal: " . $e->getMessage());
        }
    }
}
