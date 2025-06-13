<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Book; // Import Book model để kiểm tra sách
use App\Models\User; // Import User model để kiểm tra user (nếu cần)
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     * Trả về danh sách review (có thể lọc theo book_id hoặc user_id).
     */
    public function index(Request $request)
    {
        $query = Review::query();

        if ($request->has('book_id')) {
            $query->where('book_id', $request->input('book_id'));
        }

        if ($request->has('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }

        $reviews = $query->paginate(15);
        return response()->json($reviews, Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     * Lưu một review mới.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'book_id' => 'required|exists:bookai_books,id', // Đảm bảo book_id tồn tại trong bảng books
            'user_id' => 'required|exists:bookai_users,id', // Đảm bảo user_id tồn tại trong bảng users
            'rating' => 'required|integer|min:1|max:5',
            'review_text' => 'nullable|string',
        ]);

        $review = Review::create($validatedData);

        // Cập nhật avg_rating và rating_count cho cuốn sách
        $book = Book::find($validatedData['book_id']);
        if ($book) {
            $book->avg_rating = $book->reviews()->avg('rating');
            $book->rating_count = $book->reviews()->count();
            $book->save();
        }

        return response()->json($review, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     * Hiển thị thông tin chi tiết một review.
     */
    public function show(string $id)
    {
        $review = Review::find($id);

        if (!$review) {
            return response()->json(['message' => 'Review not found'], Response::HTTP_NOT_FOUND);
        }
        return response()->json($review, Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     * Cập nhật thông tin một review.
     */
    public function update(Request $request, string $id)
    {
        $review = Review::find($id);

        if (!$review) {
            return response()->json(['message' => 'Review not found'], Response::HTTP_NOT_FOUND);
        }

        $validatedData = $request->validate([
            'rating' => 'sometimes|required|integer|min:1|max:5',
            'review_text' => 'nullable|string',
            'status' => 'sometimes|required|string|in:pending,approved,rejected',
        ]);

        $review->update($validatedData);

        // Cập nhật lại avg_rating và rating_count cho cuốn sách
        $book = $review->book;
        if ($book) {
            $book->avg_rating = $book->reviews()->avg('rating');
            $book->rating_count = $book->reviews()->count();
            $book->save();
        }

        return response()->json($review, Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     * Xóa một review.
     */
    public function destroy(string $id)
    {
        $review = Review::find($id);

        if (!$review) {
            return response()->json(['message' => 'Review not found'], Response::HTTP_NOT_FOUND);
        }

        $review->delete();

        // Cập nhật lại avg_rating và rating_count cho cuốn sách sau khi xóa review
        $book = $review->book;
        if ($book) {
            $book->avg_rating = $book->reviews()->avg('rating');
            $book->rating_count = $book->reviews()->count();
            $book->save();
        }

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}