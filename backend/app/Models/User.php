<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail; // Nếu muốn thêm tính năng xác minh email
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // Cho API Authentication (Sanctum)
use Spatie\Permission\Traits\HasRoles; // Import trait

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasRoles, Notifiable;

    protected $table = 'bookai_users';

    protected $fillable = [
        'username',
        'email',
        'password',
        'full_name',
        'avatar_url',
        'bio',
        'status',
        'email_verified_at',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'last_login_at' => 'datetime',
    ];

    // Relationships
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'bookai_user_roles', 'user_id', 'role_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'user_id');
    }

    public function quotes()
    {
        return $this->hasMany(Quote::class, 'user_id');
    }

    public function posts()
    {
        return $this->hasMany(Post::class, 'user_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'user_id');
    }

    public function likedItems() // Ví dụ cho Polymorphic relationship
    {
        return $this->hasMany(Like::class, 'user_id');
    }

    public function readingLists()
    {
        return $this->hasMany(ReadingList::class, 'user_id');
    }

    // Người dùng này đang theo dõi ai
    public function following()
    {
        return $this->belongsToMany(User::class, 'bookai_followers', 'follower_id', 'followed_id');
    }

    // Ai đang theo dõi người dùng này
    public function followers()
    {
        return $this->belongsToMany(User::class, 'bookai_followers', 'followed_id', 'follower_id');
    }
}