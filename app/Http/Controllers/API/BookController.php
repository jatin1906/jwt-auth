<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;


class BookController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;

        $books = Book::when($search, function ($query) use ($search) {
            $query->where('title', 'LIKE', "%$search%");
        })->paginate(10);

        $books->getCollection()->transform(function ($book) {
            $book->cover_image = $book->cover_image
                ? asset('storage/' . $book->cover_image)
                : null;
            return $book;
        });


        return response()->json($books);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title'        => 'required|string|min:2|max:255',
            'author'       => 'required|string|min:2|max:255',
            'price'        => 'required|numeric|min:0',
            'cover_image'  => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation failed.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $imagePath = null;
        if ($request->hasFile('cover_image')) {
            $image     = $request->file('cover_image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $imagePath = $image->storeAs('books', $imageName, 'public');
        }

        $book = Book::create([
            'title'       => $request->title,
            'author'      => $request->author,
            'price'       => $request->price,
            'cover_image' => $imagePath,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Book added successfully.',
            'book'    => [
                'id'          => $book->id,
                'title'       => $book->title,
                'author'      => $book->author,
                'price'       => $book->price,
                'cover_image' => $book->cover_image
                    ? asset('storage/' . $book->cover_image)
                    : null,
                'created_at'  => $book->created_at,
            ],
        ], 201);
    }

    public function show($id)
    {
        $book = Book::find($id);

        if (!$book) {
            return response()->json([
                'status'  => false,
                'message' => 'Record not found.',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'book'   => $book,
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $book = Book::find($id);

        if (!$book) {
            return response()->json([
                'status'  => false,
                'message' => 'Record not found.',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'title'        => 'sometimes|required|string|min:2|max:255',
            'author'       => 'sometimes|required|string|min:2|max:255',
            'price'        => 'sometimes|required|numeric|min:0',
            'cover_image'  => 'sometimes|nullable|image|mimes:jpg,jpeg,png,webp|max:2048', // ← add
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation failed.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        // update text fields
        $book->update($request->only([
            'title',
            'author',
            'price',
        ]));

        // update image only if new file is uploaded
        if ($request->hasFile('cover_image')) {

            // delete old image if exists
            if ($book->cover_image && Storage::disk('public')->exists($book->cover_image)) {
                Storage::disk('public')->delete($book->cover_image);
            }

            // upload new image
            $image     = $request->file('cover_image');
            $imageName = time() . '_' . str_replace([' ', '(', ')'], '_', $image->getClientOriginalName());
            $imagePath = $image->storeAs('books', $imageName, 'public');

            $book->update(['cover_image' => $imagePath]);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Record updated successfully.',
            'book'    => $book->fresh(),
        ], 200);
    }

    public function destroy($id)
    {
        $book = Book::find($id);

        if (!$book) {
            return response()->json([
                'status'  => false,
                'message' => 'Record not found.',
            ], 404);
        }

        $book->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Record deleted successfully.',
        ], 200);
    }
}
