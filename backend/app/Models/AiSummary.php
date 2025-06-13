<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiSummary extends Model
{
    use HasFactory;

    protected $table = 'bookai_ai_summaries';
    protected $fillable = [
        'book_id',
        'ai_model_name',
        'summary_type',
        'content',
        'token_count',
    ];

    public function book()
    {
        return $this->belongsTo(Book::class, 'book_id');
    }
}