<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Genre;
use App\Models\Book;

class GenreSeeder extends Seeder
{
    public function run(): void
    {
        $genres = [
            'Fantasy', 'Science Fiction', 'Mystery', 'Thriller', 'Romance',
            'Horror', 'Historical Fiction', 'Biography', 'Autobiography', 'Poetry',
            'Young Adult', 'Children\'s', 'Self-Help', 'Business', 'Cooking',
            'Travel', 'Art', 'Science', 'History', 'Philosophy',
            'Crime', 'Adventure', 'Humor', 'Dystopian', 'Non-Fiction'
        ];

        foreach ($genres as $genreName) {
            Genre::create(['name' => $genreName]);
        }

        // Gán ngẫu nhiên thể loại cho sách
        $books = Book::all();
        $allGenres = Genre::all();

        foreach ($books as $book) {
            // Mỗi sách có từ 1 đến 3 thể loại
            $book->genres()->attach(
                $allGenres->random(mt_rand(1, 3))->pluck('id')->toArray()
            );
        }
    }
}