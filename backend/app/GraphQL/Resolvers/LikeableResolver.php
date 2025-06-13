<?php

namespace App\GraphQL\Resolvers;

use App\Models\Book;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Review;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Schema\TypeRegistry;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

final class LikeableResolver
{
    /**
     * Resolve the concrete Type for a polymorphic field.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $root
     * @param  array<string, mixed>  $context
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo
     * @return string|null
     */
    public function __invoke($root, array $context, ResolveInfo $resolveInfo): ?string
    {
        // $root ở đây là instance của Model được trả về từ quan hệ morphTo (ví dụ: một Post, một Review)
        if ($root instanceof Book) {
            return 'Book';
        }

        if ($root instanceof Post) {
            return 'Post';
        }

        if ($root instanceof Review) {
            return 'Review';
        }

        if ($root instanceof Comment) {
            return 'Comment';
        }

        // Thêm các kiểu khác nếu bạn có thêm các model có thể được like
        // if ($root instanceof SomeOtherModel) {
        //     return 'SomeOtherModel';
        // }

        return null; // Trả về null nếu không khớp với kiểu nào
    }
}