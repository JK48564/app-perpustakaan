<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Category;
use App\Models\Loan;
use App\Models\Member;
use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Pertemuan9ApiTest extends TestCase
{
    use RefreshDatabase;

    protected Category $category;
    protected User $user;
    protected Member $member;
    protected Book $book;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(UserSeeder::class);

        $this->category = Category::create([
            'nama_kategori' => 'Sastra & Fiksi',
            'deskripsi' => 'Kategori buku sastra dan novel fiksi',
        ]);

        $this->user = User::where('email', 'petugas1@pens.ac.id')->first();

        $this->member = Member::create([
            'nama' => 'Ahmad Dahlan',
            'nim' => '2103191001',
            'email' => 'ahmad@pens.ac.id',
            'nomor_telepon' => '081234567890',
            'alamat' => 'Jl. Raya ITS No. 1, Surabaya',
            'status' => 'aktif',
        ]);

        $this->book = Book::create([
            'judul' => 'Laskar Pelangi',
            'penulis' => 'Andrea Hirata',
            'penerbit' => 'Bentang Pustaka',
            'tahun_terbit' => 2005,
            'isbn' => '978-979-3062-79-2',
            'stok' => 7,
            'category_id' => $this->category->id,
        ]);
    }

    public function test_get_books_returns_paginated_resource(): void
    {
        $response = $this->getJson('/api/books');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id', 'judul', 'penulis', 'penerbit', 'tahun_terbit',
                        'isbn', 'stok', 'sampul',
                        'category' => ['id', 'nama_kategori'],
                        'created_at', 'updated_at',
                    ],
                ],
                'links',
                'meta',
            ]);
    }

    public function test_get_books_filter_by_judul(): void
    {
        Book::create([
            'judul' => 'Bumi Manusia',
            'penulis' => 'Pramoedya Ananta Toer',
            'penerbit' => 'Hasta Mitra',
            'tahun_terbit' => 1980,
            'isbn' => '978-979-973-12-0',
            'stok' => 4,
            'category_id' => $this->category->id,
        ]);

        $response = $this->getJson('/api/books?judul=laskar');
        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));
        $this->assertEquals('Laskar Pelangi', $response->json('data.0.judul'));
    }

    public function test_get_books_filter_by_category_id(): void
    {
        $cat2 = Category::create(['nama_kategori' => 'Teknologi']);
        Book::create([
            'judul' => 'Clean Code',
            'penulis' => 'Robert C. Martin',
            'penerbit' => 'Prentice Hall',
            'tahun_terbit' => 2008,
            'isbn' => '978-0132350884',
            'stok' => 3,
            'category_id' => $cat2->id,
        ]);

        $response = $this->getJson('/api/books?category_id=' . $cat2->id);
        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));
        $this->assertEquals('Clean Code', $response->json('data.0.judul'));
    }

    public function test_post_books_success(): void
    {
        $payload = [
            'judul' => 'Filosofi Teras',
            'penulis' => 'Henry Manampiring',
            'penerbit' => 'Kompas',
            'tahun_terbit' => 2018,
            'isbn' => '978-602-412-518-9',
            'stok' => 10,
            'category_id' => $this->category->id,
        ];

        $response = $this->postJson('/api/books', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('data.judul', 'Filosofi Teras')
            ->assertJsonPath('data.category.nama_kategori', 'Sastra & Fiksi');

        $this->assertDatabaseHas('books', ['judul' => 'Filosofi Teras']);
    }

    public function test_post_books_validation_fails(): void
    {
        $response = $this->postJson('/api/books', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['judul', 'penulis', 'penerbit', 'tahun_terbit', 'stok', 'category_id']);
    }

    public function test_get_single_book_success(): void
    {
        $response = $this->getJson('/api/books/' . $this->book->id);

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $this->book->id)
            ->assertJsonPath('data.judul', 'Laskar Pelangi');
    }

    public function test_get_single_book_not_found(): void
    {
        $response = $this->getJson('/api/books/99999');
        $response->assertStatus(404);
    }

    public function test_put_book_success(): void
    {
        $payload = [
            'judul' => 'Laskar Pelangi (Edisi Khusus)',
            'penulis' => 'Andrea Hirata',
            'penerbit' => 'Bentang Pustaka',
            'tahun_terbit' => 2008,
            'isbn' => '978-979-3062-79-2',
            'stok' => 12,
            'category_id' => $this->category->id,
        ];

        $response = $this->putJson('/api/books/' . $this->book->id, $payload);

        $response->assertStatus(200)
            ->assertJsonPath('data.judul', 'Laskar Pelangi (Edisi Khusus)')
            ->assertJsonPath('data.stok', 12);
    }

    public function test_delete_book_success(): void
    {
        $response = $this->deleteJson('/api/books/' . $this->book->id);

        $response->assertStatus(200)
            ->assertExactJson(['message' => 'Buku berhasil dihapus.']);

        $this->assertDatabaseMissing('books', ['id' => $this->book->id]);
    }

    public function test_get_members_returns_paginated_resource(): void
    {
        $response = $this->getJson('/api/members');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id', 'nama', 'nim', 'email', 'nomor_telepon', 'alamat',
                        'status', 'created_at', 'updated_at',
                    ],
                ],
                'links',
                'meta',
            ])
            ->assertJsonPath('data.0.nama', 'Ahmad Dahlan');
    }

    public function test_get_single_member_success(): void
    {
        $response = $this->getJson('/api/members/' . $this->member->id);

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $this->member->id)
            ->assertJsonPath('data.nama', 'Ahmad Dahlan');
    }

    public function test_get_single_member_not_found(): void
    {
        $response = $this->getJson('/api/members/99999');
        $response->assertStatus(404);
    }

    public function test_post_loans_success(): void
    {
        $payload = [
            'member_id' => $this->member->id,
            'user_id' => $this->user->id,
            'tanggal_pinjam' => now()->toDateString(),
            'tanggal_kembali' => now()->addDays(7)->toDateString(),
            'book_ids' => [$this->book->id],
        ];

        $response = $this->postJson('/api/loans', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('data.status', 'dipinjam')
            ->assertJsonPath('data.member.nama', 'Ahmad Dahlan')
            ->assertJsonPath('data.petugas.name', 'Petugas Satu')
            ->assertJsonPath('data.books.0.judul', 'Laskar Pelangi');
    }

    public function test_post_loans_validation_fails(): void
    {
        $response = $this->postJson('/api/loans', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['member_id', 'user_id', 'tanggal_pinjam', 'tanggal_kembali', 'book_ids']);
    }

    public function test_get_loans_returns_paginated_resource(): void
    {
        $loan = Loan::create([
            'member_id' => $this->member->id,
            'user_id' => $this->user->id,
            'tanggal_pinjam' => now()->toDateString(),
            'tanggal_kembali' => now()->addDays(7)->toDateString(),
        ]);
        $loan->loanItems()->create(['book_id' => $this->book->id]);

        $response = $this->getJson('/api/loans');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id', 'member', 'petugas', 'tanggal_pinjam',
                        'tanggal_kembali', 'tanggal_dikembalikan', 'status', 'books',
                    ],
                ],
                'links',
                'meta',
            ]);
    }

    public function test_put_loans_kembalikan_success(): void
    {
        $loan = Loan::create([
            'member_id' => $this->member->id,
            'user_id' => $this->user->id,
            'tanggal_pinjam' => now()->toDateString(),
            'tanggal_kembali' => now()->addDays(7)->toDateString(),
        ]);
        $loan->loanItems()->create(['book_id' => $this->book->id]);

        $response = $this->putJson('/api/loans/' . $loan->id . '/kembalikan');

        $response->assertStatus(200)
            ->assertJsonPath('data.status', 'dikembalikan')
            ->assertJsonPath('data.tanggal_dikembalikan', now()->toDateString());
    }

    public function test_get_stats_success(): void
    {
        $loan = Loan::create([
            'member_id' => $this->member->id,
            'user_id' => $this->user->id,
            'tanggal_pinjam' => now()->toDateString(),
            'tanggal_kembali' => now()->addDays(7)->toDateString(),
            'status' => 'dipinjam',
        ]);
        $loan->loanItems()->create(['book_id' => $this->book->id]);

        $response = $this->getJson('/api/stats');

        $response->assertStatus(200)
            ->assertExactJson([
                'total_buku' => 1,
                'total_anggota' => 1,
                'peminjaman_aktif' => 1,
            ]);
    }
}
