<?php

namespace App\GraphQL\Scalars;

use GraphQL\Language\AST\StringValueNode;
use GraphQL\Type\Definition\ScalarType;
use GraphQL\Utils\Utils;
use GraphQL\Error\Error;

class DateScalar extends ScalarType
{
    // Tên của Scalar Type trong schema GraphQL của bạn
    public string $name = 'Date';

    // Mô tả Scalar Type, sẽ hiển thị trong các công cụ GraphQL IDE
    public ?string $description = 'A date string in YYYY-MM-DD format.';

    /**
     * Serializes an internal value to include in a response.
     * Chuyển đổi giá trị PHP thành định dạng có thể gửi qua GraphQL (string YYYY-MM-DD).
     *
     * @param mixed $value Giá trị đầu vào từ PHP (ví dụ: Carbon instance, DateTime object, hoặc string)
     * @return string
     * @throws Error
     */
    public function serialize(mixed $value): string
    {
        // Nếu giá trị là một đối tượng DateTimeInterface (ví dụ: Carbon instance), định dạng nó
        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d');
        }

        // Nếu giá trị đã là một chuỗi, kiểm tra xem nó có đúng định dạng không
        if (is_string($value)) {
            // Bạn có thể thêm validation regex tại đây nếu muốn kiểm tra chặt chẽ hơn
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
                return $value;
            }
        }

        // Nếu giá trị không thể serialize được, ném lỗi
        throw new Error('Date cannot be serialized: ' . Utils::printSafe($value));
    }

    /**
     * Parses an externally provided value (query variable) to use as an internal value.
     * Chuyển đổi giá trị nhận được từ client (biến query) thành giá trị PHP.
     *
     * @param mixed $value Giá trị đầu vào từ client (string YYYY-MM-DD)
     * @return \DateTimeImmutable
     * @throws Error
     */
    public function parseValue(mixed $value): \DateTimeImmutable
    {
        // Đảm bảo giá trị là một chuỗi
        if (!is_string($value)) {
            throw new Error('Date must be a string: ' . Utils::printSafe($value));
        }

        try {
            // Cố gắng tạo một đối tượng DateTimeImmutable từ chuỗi
            return new \DateTimeImmutable($value);
        } catch (\Exception $e) {
            // Nếu định dạng không hợp lệ, ném lỗi
            throw new Error('Invalid Date format: ' . Utils::printSafe($value) . '. Expected YYYY-MM-DD.');
        }
    }

    /**
     * Parses an externally provided literal value (hardcoded in query) to use as an internal value.
     * Chuyển đổi giá trị được viết cứng trong query (literal) thành giá trị PHP.
     *
     * @param \GraphQL\Language\AST\Node $valueNode Giá trị node từ AST của query
     * @param ?array $variables Các biến query (nếu có)
     * @return \DateTimeImmutable
     * @throws Error
     */
    public function parseLiteral(\GraphQL\Language\AST\Node $valueNode, ?array $variables = null): \DateTimeImmutable
    {
        // Chỉ chấp nhận StringValueNode cho kiểu Date
        if (!($valueNode instanceof StringValueNode)) {
            throw new Error('Query error: Can only parse strings got: ' . $valueNode->kind, $valueNode);
        }

        try {
            // Cố gắng tạo một đối tượng DateTimeImmutable từ giá trị chuỗi
            return new \DateTimeImmutable($valueNode->value);
        } catch (\Exception $e) {
            // Nếu định dạng không hợp lệ, ném lỗi
            throw new Error('Invalid Date format: ' . Utils::printSafe($valueNode->value) . '. Expected YYYY-MM-DD.', $valueNode);
        }
    }
}