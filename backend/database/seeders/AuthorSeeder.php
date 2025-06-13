<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Author;
use App\Models\Book;

class AuthorSeeder extends Seeder
{
    public function run(): void
    {
        Author::factory()->count(200)->create(); // Tạo 200 tác giả mẫu

        // Gán ngẫu nhiên tác giả cho sách
        $books = Book::all();
        $authors = Author::all();

        foreach ($books as $book) {
            // Mỗi sách có từ 1 đến 3 tác giả
            $book->authors()->attach(
                $authors->random(mt_rand(1, 3))->pluck('id')->toArray()
            );
        }
    }
}