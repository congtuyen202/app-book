<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Author extends Model
{
    use HasFactory;

    protected $table = 'bookai_authors';
    protected $fillable = ['name', 'bio', 'birth_date', 'death_date', 'nationality'];

    public function books()
    {
        return $this->belongsToMany(Book::class, 'bookai_book_authors', 'author_id', 'book_id');
    }
}