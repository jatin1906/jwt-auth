<?php

namespace Database\Seeders;

use App\Models\Book;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Book::create([

            'title' => 'Laravel API Development',

            'author' => 'Jatin',

            'cover_image' => 'laravel-book.jpg',

            'price' => 499.99,

            'published_date' => '2026-05-20'

        ]);

        Book::create([

            'title' => 'Node.js Master Guide',

            'author' => 'Kumar',

            'cover_image' => 'node-book.jpg',

            'price' => 799.00,

            'published_date' => '2026-04-15'

        ]);

        Book::create([

            'title' => 'PHP Advanced Concepts',

            'author' => 'Rahul',

            'cover_image' => null,

            'price' => 650.50,

            'published_date' => '2026-03-10'

        ]);
    }
}
