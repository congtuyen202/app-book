<?php

namespace Modules\Community\Classes\Scalars;

use GraphQL\Type\Definition\ScalarType;

class JSONScalar extends ScalarType
{
    public ?string $description = "The `JSON` scalar type represents JSON values as specified by ECMA-404.";

    public function serialize($value)
    {
        return json_decode(json_encode($value), true);
    }

    public function parseValue($value)
    {
        return json_decode($value, true);
    }

    public function parseLiteral($valueNode, ?array $variables = null)
    {
        return $valueNode->value;
    }
}
