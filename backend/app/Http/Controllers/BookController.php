<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Http\Response; // Import Response class for status codes

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     * Trả về danh sách sách.
     */
    public function index()
    {
        $books = Book::paginate(15); // Phân trang 15 sách mỗi trang
        return response()->json($books, Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     * Lưu một cuốn sách mới.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'original_title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'published_year' => 'nullable|integer|min:1000|max:' . date('Y'),
            'isbn' => 'nullable|string|max:20|unique:bookai_books,isbn',
            'language' => 'nullable|string|max:50',
            'page_count' => 'nullable|integer|min:1',
            'cover_image_url' => 'nullable|url|max:255',
        ]);

        $book = Book::create($validatedData);
        return response()->json($book, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     * Hiển thị thông tin chi tiết một cuốn sách.
     */
    public function show(string $id)
    {
        $book = Book::find($id);

        if (!$book) {
            return response()->json(['message' => 'Book not found'], Response::HTTP_NOT_FOUND);
        }
        return response()->json($book, Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     * Cập nhật thông tin một cuốn sách.
     */
    public function update(Request $request, string $id)
    {
        $book = Book::find($id);

        if (!$book) {
            return response()->json(['message' => 'Book not found'], Response::HTTP_NOT_FOUND);
        }

        $validatedData = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'original_title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'published_year' => 'nullable|integer|min:1000|max:' . date('Y'),
            'isbn' => 'nullable|string|max:20|unique:bookai_books,isbn,' . $id,
            'language' => 'nullable|string|max:50',
            'page_count' => 'nullable|integer|min:1',
            'cover_image_url' => 'nullable|url|max:255',
        ]);

        $book->update($validatedData);
        return response()->json($book, Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     * Xóa một cuốn sách.
     */
    public function destroy(string $id)
    {
        $book = Book::find($id);

        if (!$book) {
            return response()->json(['message' => 'Book not found'], Response::HTTP_NOT_FOUND);
        }

        $book->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}