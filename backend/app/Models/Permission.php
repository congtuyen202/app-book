<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    protected $table = 'bookai_permissions';
    protected $fillable = ['name', 'description'];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'bookai_role_permissions', 'permission_id', 'role_id');
    }
}