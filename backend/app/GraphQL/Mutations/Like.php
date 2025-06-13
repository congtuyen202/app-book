<?php

namespace App\GraphQL\Mutations;

use App\Models\Like;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

final class Like
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function toggle($_, array $args)
    {
        $user = Auth::user(); // Lấy người dùng hiện tại

        if (!$user) {
            throw new \Exception("Unauthenticated.");
        }

        $likeableType = 'App\\Models\\' . ucfirst($args['likeable_type']); // Ví dụ: 'Post' -> 'App\Models\Post'
        $likeableId = $args['likeable_id'];

        // Kiểm tra xem Model có tồn tại không
        if (!class_exists($likeableType)) {
            throw new \Exception("Invalid likeable_type: " . $likeableType);
        }

        // Kiểm tra xem đối tượng có tồn tại không
        $likeable = app($likeableType)->find($likeableId);
        if (!$likeable) {
            throw new \Exception($args['likeable_type'] . " not found.");
        }

        $like = Like::where('user_id', $user->id)
                    ->where('likeable_type', $likeableType)
                    ->where('likeable_id', $likeableId)
                    ->first();

        if ($like) {
            // Đã like, bây giờ unlike
            $like->delete();
            // Cập nhật lại số lượng like cho đối tượng (nếu bạn muốn)
            // if (method_exists($likeable, 'likes_count')) {
            //     $likeable->decrement('likes_count');
            // }
            return $like; // Trả về like đã xóa
        } else {
            // Chưa like, bây giờ like
            $newLike = Like::create([
                'user_id' => $user->id,
                'likeable_type' => $likeableType,
                'likeable_id' => $likeableId,
            ]);
            // Cập nhật lại số lượng like cho đối tượng (nếu bạn muốn)
            // if (method_exists($likeable, 'likes_count')) {
            //     $likeable->increment('likes_count');
            // }
            return $newLike;
        }
    }
}