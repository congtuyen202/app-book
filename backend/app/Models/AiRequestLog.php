<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiRequestLog extends Model
{
    use HasFactory;

    protected $table = 'bookai_ai_requests_log';
    protected $fillable = [
        'user_id',
        'ai_model_name',
        'request_type',
        'input_text',
        'output_text',
        'tokens_used',
        'cost',
        'status',
        'error_message',
        'requested_at',
    ];

    protected $casts = [
        'requested_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}