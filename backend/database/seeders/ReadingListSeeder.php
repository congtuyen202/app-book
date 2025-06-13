<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ReadingList;
use App\Models\Book;
use Faker\Factory as Faker;

class ReadingListSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();
        ReadingList::factory()->count(100)->create(); // Tạo 500 danh sách đọc

        $readingLists = ReadingList::all();
        $books = Book::all();

        // Gán ngẫu nhiên sách vào các danh sách đọc
        foreach ($readingLists as $list) {
            $numBooks = mt_rand(1, 10); // Mỗi danh sách có từ 1 đến 10 sách
            $attachedBooks = $books->random($numBooks)->pluck('id')->toArray();

            $syncData = [];
            foreach ($attachedBooks as $bookId) {
                $status = $faker->randomElement(['reading', 'completed', 'dropped']);
                $startedAt = null;
                $finishedAt = null;

                if ($status === 'reading' || $status === 'completed') {
                    $startedAt = $faker->dateTimeBetween('-1 year', 'now');
                }
                if ($status === 'completed') {
                    $finishedAt = $faker->dateTimeBetween($startedAt ?? '-6 months', 'now');
                }

                $syncData[$bookId] = [
                    'status' => $status,
                    'started_reading_at' => $startedAt,
                    'finished_reading_at' => $finishedAt,
                ];
            }
            $list->books()->sync($syncData);
        }
    }
}