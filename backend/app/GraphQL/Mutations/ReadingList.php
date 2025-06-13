<?php

namespace App\GraphQL\Mutations;

use App\Models\ReadingList;
use App\Models\Book;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

final class ReadingList
{
    public function addBook($_, array $args)
    {
        $user = Auth::user();
        if (!$user) {
            throw new \Exception("Unauthenticated.");
        }

        $readingList = ReadingList::where('user_id', $user->id)->find($args['reading_list_id']);
        if (!$readingList) {
            throw new \Exception("Reading list not found or not owned by user.");
        }

        $book = Book::find($args['book_id']);
        if (!$book) {
            throw new \Exception("Book not found.");
        }

        // Kiểm tra xem sách đã có trong danh sách chưa
        if ($readingList->books->contains($book->id)) {
            throw ValidationException::withMessages([
                'book_id' => ['Book already in this reading list.'],
            ]);
        }

        $readingList->books()->attach($book->id, ['status' => $args['status'] ?? 'pending']);

        return $readingList;
    }

    public function removeBook($_, array $args)
    {
        $user = Auth::user();
        if (!$user) {
            throw new \Exception("Unauthenticated.");
        }

        $readingList = ReadingList::where('user_id', $user->id)->find($args['reading_list_id']);
        if (!$readingList) {
            throw new \Exception("Reading list not found or not owned by user.");
        }

        $book = Book::find($args['book_id']);
        if (!$book) {
            throw new \Exception("Book not found.");
        }

        // Kiểm tra xem sách có trong danh sách không
        if (!$readingList->books->contains($book->id)) {
            throw new \Exception("Book not found in this reading list.");
        }

        $readingList->books()->detach($book->id);

        return $readingList;
    }

    public function updateBookStatus($_, array $args)
    {
        $user = Auth::user();
        if (!$user) {
            throw new \Exception("Unauthenticated.");
        }

        $readingList = ReadingList::where('user_id', $user->id)->find($args['reading_list_id']);
        if (!$readingList) {
            throw new \Exception("Reading list not found or not owned by user.");
        }

        $book = Book::find($args['book_id']);
        if (!$book) {
            throw new \Exception("Book not found.");
        }

        $pivotData = [
            'status' => $args['status'],
        ];

        if (isset($args['started_reading_at'])) {
            $pivotData['started_reading_at'] = $args['started_reading_at'];
        }
        if (isset($args['finished_reading_at'])) {
            $pivotData['finished_reading_at'] = $args['finished_reading_at'];
        }

        // Cập nhật pivot table
        $readingList->books()->updateExistingPivot($book->id, $pivotData);

        return $readingList;
    }
}