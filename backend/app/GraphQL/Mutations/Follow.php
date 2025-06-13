<?php

namespace App\GraphQL\Mutations;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

final class Follow
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function toggle($_, array $args)
    {
        $currentUser = Auth::user();

        if (!$currentUser) {
            throw new \Exception("Unauthenticated.");
        }

        $followedUser = User::find($args['followed_id']);

        if (!$followedUser) {
            throw new \Exception("User to follow/unfollow not found.");
        }

        if ($currentUser->id === $followedUser->id) {
            throw new \Exception("Cannot follow/unfollow yourself.");
        }

        // Kiểm tra xem đã theo dõi chưa
        if ($currentUser->following->contains($followedUser->id)) {
            // Đã theo dõi, bây giờ bỏ theo dõi
            $currentUser->following()->detach($followedUser->id);
            return $followedUser; // Trả về người dùng đã bỏ theo dõi
        } else {
            // Chưa theo dõi, bây giờ theo dõi
            $currentUser->following()->attach($followedUser->id);
            return $followedUser; // Trả về người dùng đã theo dõi
        }
    }
}