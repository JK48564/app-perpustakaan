# Dokumentasi REST API - Perpustakaan Digital Kampus

Dokumentasi lengkap 11 endpoint REST API yang dibangun pada **Pertemuan 9**. Seluruh endpoint bersifat stateless dan mengembalikan data dalam format JSON menggunakan Laravel API Resource.

- **Base URL:** `http://127.0.0.1:8000/api`
- **Default Headers:**
  - `Accept: application/json`
  - `Content-Type: application/json` (untuk request dengan body: `POST`, `PUT`)

---

## Tabel Ringkasan Endpoint

| No | Method | URL | Body (jika ada) | Status Sukses | Status Gagal | Keterangan |
|---|---|---|---|---|---|---|
| 1 | `GET` | `/api/books` | - | `200 OK` | - | Mengambil daftar buku (terpaginasi 10 item), mendukung filter `?judul=` dan `?category_id=` |
| 2 | `GET` | `/api/books/{id}` | - | `200 OK` | `404 Not Found` | Mengambil detail 1 buku beserta relasi kategorinya |
| 3 | `POST` | `/api/books` | JSON data buku | `201 Created` | `422 Unprocessable Content` | Menambahkan buku baru menggunakan `StoreBookRequest` |
| 4 | `PUT` | `/api/books/{id}` | JSON data buku | `200 OK` | `404 Not Found`, `422 Unprocessable Content` | Memperbarui data buku berdasarkan ID |
| 5 | `DELETE` | `/api/books/{id}` | - | `200 OK` | `404 Not Found` | Menghapus data buku berdasarkan ID |
| 6 | `GET` | `/api/members` | - | `200 OK` | - | Mengambil daftar seluruh anggota perpustakaan (terpaginasi 10 item) |
| 7 | `GET` | `/api/members/{id}` | - | `200 OK` | `404 Not Found` | Mengambil detail 1 anggota perpustakaan |
| 8 | `GET` | `/api/loans` | - | `200 OK` | - | Mengambil daftar peminjaman beserta relasi `member`, `petugas`, dan `books` |
| 9 | `POST` | `/api/loans` | JSON data transaksi | `201 Created` | `422 Unprocessable Content` | Mencatat transaksi peminjaman baru beserta buku-buku yang dipinjam |
| 10 | `PUT` | `/api/loans/{id}/kembalikan` | - | `200 OK` | `404 Not Found` | Mengubah status peminjaman menjadi `dikembalikan` dan mencatat tanggal pengembalian |
| 11 | `GET` | `/api/stats` | - | `200 OK` | - | Mengambil ringkasan statistik `total_buku`, `total_anggota`, dan `peminjaman_aktif` |

---

## Rincian dan Contoh Request / Response Setiap Endpoint

### 1. `GET /api/books`
Mengambil daftar buku dengan format pagination API Resource. Mendukung query filter:
- `?judul=laskar` (pencarian sebagian judul dengan `LIKE`)
- `?category_id=1` (pencocokan kategori buku)

**Request:**
- **Method:** `GET`
- **URL:** `http://127.0.0.1:8000/api/books`
- **Headers:** `Accept: application/json`

**Contoh Response Sukses (`200 OK`):**
```json
{
  "data": [
    {
      "id": 1,
      "judul": "Clean Code",
      "penulis": "Robert C. Martin",
      "penerbit": "Prentice Hall",
      "tahun_terbit": 2008,
      "isbn": "978-0132350884",
      "stok": 5,
      "sampul": null,
      "category": {
        "id": 1,
        "nama_kategori": "Teknologi & Komputer"
      },
      "created_at": "2026-11-03T21:39:15.000000Z",
      "updated_at": "2026-11-03T21:39:15.000000Z"
    },
    {
      "id": 2,
      "judul": "Laskar Pelangi",
      "penulis": "Andrea Hirata",
      "penerbit": "Bentang Pustaka",
      "tahun_terbit": 2005,
      "isbn": "978-979-3062-79-2",
      "stok": 8,
      "sampul": null,
      "category": {
        "id": 2,
        "nama_kategori": "Sastra & Fiksi"
      },
      "created_at": "2026-11-03T21:39:15.000000Z",
      "updated_at": "2026-11-03T21:39:15.000000Z"
    }
  ],
  "links": {
    "first": "http://127.0.0.1:8000/api/books?page=1",
    "last": "http://127.0.0.1:8000/api/books?page=1",
    "prev": null,
    "next": null
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 1,
    "links": [
      {
        "url": null,
        "label": "&laquo; Previous",
        "active": false
      },
      {
        "url": "http://127.0.0.1:8000/api/books?page=1",
        "label": "1",
        "active": true
      },
      {
        "url": null,
        "label": "Next &raquo;",
        "active": false
      }
    ],
    "path": "http://127.0.0.1:8000/api/books",
    "per_page": 10,
    "to": 2,
    "total": 2
  }
}
```

---

### 2. `GET /api/books/{id}`
Mengambil detail informasi satu buku berdasarkan ID.

**Request:**
- **Method:** `GET`
- **URL:** `http://127.0.0.1:8000/api/books/1`
- **Headers:** `Accept: application/json`

**Contoh Response Sukses (`200 OK`):**
```json
{
  "data": {
    "id": 1,
    "judul": "Clean Code",
    "penulis": "Robert C. Martin",
    "penerbit": "Prentice Hall",
    "tahun_terbit": 2008,
    "isbn": "978-0132350884",
    "stok": 5,
    "sampul": null,
    "category": {
      "id": 1,
      "nama_kategori": "Teknologi & Komputer"
    },
    "created_at": "2026-11-03T21:39:15.000000Z",
    "updated_at": "2026-11-03T21:39:15.000000Z"
  }
}
```

**Contoh Response Gagal (`404 Not Found`) - Jika ID tidak ditemukan (`/api/books/999`):**
```json
{
  "message": "No query results for model [App\\Models\\Book] 999"
}
```

---

### 3. `POST /api/books`
Menambahkan buku baru ke database.

**Request:**
- **Method:** `POST`
- **URL:** `http://127.0.0.1:8000/api/books`
- **Headers:**
  - `Accept: application/json`
  - `Content-Type: application/json`
- **Body (raw JSON):**
```json
{
  "judul": "Filosofi Teras",
  "penulis": "Henry Manampiring",
  "penerbit": "Kompas",
  "tahun_terbit": 2018,
  "isbn": "978-602-412-518-9",
  "stok": 10,
  "category_id": 2
}
```

**Contoh Response Sukses (`201 Created`):**
```json
{
  "data": {
    "id": 3,
    "judul": "Filosofi Teras",
    "penulis": "Henry Manampiring",
    "penerbit": "Kompas",
    "tahun_terbit": 2018,
    "isbn": "978-602-412-518-9",
    "stok": 10,
    "sampul": null,
    "category": {
      "id": 2,
      "nama_kategori": "Sastra & Fiksi"
    },
    "created_at": "2026-11-03T21:39:36.000000Z",
    "updated_at": "2026-11-03T21:39:36.000000Z"
  }
}
```

**Contoh Response Gagal (`422 Unprocessable Content`) - Body kosong / data tidak valid:**
```json
{
  "message": "Judul buku wajib diisi (and 5 more errors)",
  "errors": {
    "judul": [
      "Judul buku wajib diisi"
    ],
    "penulis": [
      "Nama penulis wajib diisi"
    ],
    "penerbit": [
      "Nama penerbit wajib diisi"
    ],
    "tahun_terbit": [
      "Tahun buku diterbitkan wajib diisi"
    ],
    "stok": [
      "Stok wajib diisi."
    ],
    "category_id": [
      "Kategori wajib dipilih."
    ]
  }
}
```

---

### 4. `PUT /api/books/{id}`
Memperbarui informasi buku yang sudah ada.

**Request:**
- **Method:** `PUT`
- **URL:** `http://127.0.0.1:8000/api/books/1`
- **Headers:**
  - `Accept: application/json`
  - `Content-Type: application/json`
- **Body (raw JSON):**
```json
{
  "judul": "Clean Code (Edisi Revisi)",
  "penulis": "Robert C. Martin",
  "penerbit": "Prentice Hall",
  "tahun_terbit": 2008,
  "isbn": "978-0132350884",
  "stok": 7,
  "category_id": 1
}
```

**Contoh Response Sukses (`200 OK`):**
```json
{
  "data": {
    "id": 1,
    "judul": "Clean Code (Edisi Revisi)",
    "penulis": "Robert C. Martin",
    "penerbit": "Prentice Hall",
    "tahun_terbit": 2008,
    "isbn": "978-0132350884",
    "stok": 7,
    "sampul": null,
    "category": {
      "id": 1,
      "nama_kategori": "Teknologi & Komputer"
    },
    "created_at": "2026-11-03T21:39:15.000000Z",
    "updated_at": "2026-11-03T21:39:36.000000Z"
  }
}
```

**Contoh Response Gagal (`404 Not Found` / `422 Unprocessable Content`):**
- Jika ID tidak ditemukan:
```json
{
  "message": "No query results for model [App\\Models\\Book] 999"
}
```
- Jika data tidak valid:
```json
{
  "message": "The judul field is required.",
  "errors": {
    "judul": [
      "The judul field is required."
    ]
  }
}
```

---

### 5. `DELETE /api/books/{id}`
Menghapus data buku dari database berdasarkan ID.

**Request:**
- **Method:** `DELETE`
- **URL:** `http://127.0.0.1:8000/api/books/3`
- **Headers:** `Accept: application/json`

**Contoh Response Sukses (`200 OK`):**
```json
{
  "message": "Buku berhasil dihapus."
}
```

**Contoh Response Gagal (`404 Not Found`) - Jika ID tidak ditemukan:**
```json
{
  "message": "No query results for model [App\\Models\\Book] 999"
}
```

---

### 6. `GET /api/members`
Mengambil daftar anggota perpustakaan terpaginasi (10 per halaman).

**Request:**
- **Method:** `GET`
- **URL:** `http://127.0.0.1:8000/api/members`
- **Headers:** `Accept: application/json`

**Contoh Response Sukses (`200 OK`):**
```json
{
  "data": [
    {
      "id": 1,
      "nama": "Ahmad Dahlan",
      "nim": "2103191001",
      "email": "ahmad@pens.ac.id",
      "nomor_telepon": "081234567890",
      "alamat": "Jl. Raya ITS No. 1, Surabaya",
      "status": "aktif",
      "created_at": "2026-11-03T21:39:15.000000Z",
      "updated_at": "2026-11-03T21:39:15.000000Z"
    },
    {
      "id": 2,
      "nama": "Siti Aminah",
      "nim": "2103191002",
      "email": "siti@pens.ac.id",
      "nomor_telepon": "081234567891",
      "alamat": "Jl. Menur No. 12, Surabaya",
      "status": "aktif",
      "created_at": "2026-11-03T21:39:15.000000Z",
      "updated_at": "2026-11-03T21:39:15.000000Z"
    }
  ],
  "links": {
    "first": "http://127.0.0.1:8000/api/members?page=1",
    "last": "http://127.0.0.1:8000/api/members?page=1",
    "prev": null,
    "next": null
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 1,
    "links": [
      {
        "url": null,
        "label": "&laquo; Previous",
        "active": false
      },
      {
        "url": "http://127.0.0.1:8000/api/members?page=1",
        "label": "1",
        "active": true
      },
      {
        "url": null,
        "label": "Next &raquo;",
        "active": false
      }
    ],
    "path": "http://127.0.0.1:8000/api/members",
    "per_page": 10,
    "to": 2,
    "total": 2
  }
}
```

---

### 7. `GET /api/members/{id}`
Mengambil detail satu anggota berdasarkan ID.

**Request:**
- **Method:** `GET`
- **URL:** `http://127.0.0.1:8000/api/members/1`
- **Headers:** `Accept: application/json`

**Contoh Response Sukses (`200 OK`):**
```json
{
  "data": {
    "id": 1,
    "nama": "Ahmad Dahlan",
    "nim": "2103191001",
    "email": "ahmad@pens.ac.id",
    "nomor_telepon": "081234567890",
    "alamat": "Jl. Raya ITS No. 1, Surabaya",
    "status": "aktif",
    "created_at": "2026-11-03T21:39:15.000000Z",
    "updated_at": "2026-11-03T21:39:15.000000Z"
  }
}
```

**Contoh Response Gagal (`404 Not Found`) - Jika ID tidak ditemukan:**
```json
{
  "message": "No query results for model [App\\Models\\Member] 999"
}
```

---

### 8. `GET /api/loans`
Mengambil daftar riwayat peminjaman beserta detail anggota, petugas, dan buku yang dipinjam.

**Request:**
- **Method:** `GET`
- **URL:** `http://127.0.0.1:8000/api/loans`
- **Headers:** `Accept: application/json`

**Contoh Response Sukses (`200 OK`):**
```json
{
  "data": [
    {
      "id": 1,
      "member": {
        "id": 1,
        "nama": "Ahmad Dahlan"
      },
      "petugas": {
        "id": 2,
        "name": "Petugas Satu"
      },
      "tanggal_pinjam": "2026-11-01",
      "tanggal_kembali": "2026-11-08",
      "tanggal_dikembalikan": null,
      "status": "dipinjam",
      "books": [
        {
          "id": 1,
          "judul": "Clean Code"
        }
      ]
    }
  ],
  "links": {
    "first": "http://127.0.0.1:8000/api/loans?page=1",
    "last": "http://127.0.0.1:8000/api/loans?page=1",
    "prev": null,
    "next": null
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 1,
    "links": [
      {
        "url": null,
        "label": "&laquo; Previous",
        "active": false
      },
      {
        "url": "http://127.0.0.1:8000/api/loans?page=1",
        "label": "1",
        "active": true
      },
      {
        "url": null,
        "label": "Next &raquo;",
        "active": false
      }
    ],
    "path": "http://127.0.0.1:8000/api/loans",
    "per_page": 10,
    "to": 1,
    "total": 1
  }
}
```

---

### 9. `POST /api/loans`
Mencatat transaksi peminjaman baru beserta daftar `book_ids`.

**Request:**
- **Method:** `POST`
- **URL:** `http://127.0.0.1:8000/api/loans`
- **Headers:**
  - `Accept: application/json`
  - `Content-Type: application/json`
- **Body (raw JSON):**
```json
{
  "member_id": 2,
  "user_id": 2,
  "tanggal_pinjam": "2026-11-04",
  "tanggal_kembali": "2026-11-11",
  "book_ids": [1, 2]
}
```

**Contoh Response Sukses (`201 Created`):**
```json
{
  "data": {
    "id": 2,
    "member": {
      "id": 2,
      "nama": "Siti Aminah"
    },
    "petugas": {
      "id": 2,
      "name": "Petugas Satu"
    },
    "tanggal_pinjam": "2026-11-04",
    "tanggal_kembali": "2026-11-11",
    "tanggal_dikembalikan": null,
    "status": "dipinjam",
    "books": [
      {
        "id": 1,
        "judul": "Clean Code"
      },
      {
        "id": 2,
        "judul": "Laskar Pelangi"
      }
    ]
  }
}
```

**Contoh Response Gagal (`422 Unprocessable Content`) - Validasi error:**
```json
{
  "message": "Anggota wajib dipilih. (and 4 more errors)",
  "errors": {
    "member_id": [
      "Anggota wajib dipilih."
    ],
    "user_id": [
      "Petugas (user_id) wajib diisi."
    ],
    "tanggal_pinjam": [
      "Tanggal pinjam wajib diisi."
    ],
    "tanggal_kembali": [
      "Tanggal kembali wajib diisi."
    ],
    "book_ids": [
      "Pilih minimal satu buku."
    ]
  }
}
```

---

### 10. `PUT /api/loans/{id}/kembalikan`
Mengembalikan buku dari status `dipinjam` menjadi `dikembalikan`, serta otomatis mengisi `tanggal_dikembalikan` dengan tanggal hari ini.

**Request:**
- **Method:** `PUT`
- **URL:** `http://127.0.0.1:8000/api/loans/1/kembalikan`
- **Headers:** `Accept: application/json`

**Contoh Response Sukses (`200 OK`):**
```json
{
  "data": {
    "id": 1,
    "member": {
      "id": 1,
      "nama": "Ahmad Dahlan"
    },
    "petugas": {
      "id": 2,
      "name": "Petugas Satu"
    },
    "tanggal_pinjam": "2026-11-01",
    "tanggal_kembali": "2026-11-08",
    "tanggal_dikembalikan": "2026-11-04",
    "status": "dikembalikan",
    "books": [
      {
        "id": 1,
        "judul": "Clean Code"
      }
    ]
  }
}
```

**Contoh Response Gagal (`404 Not Found`) - Jika ID pinjam tidak ditemukan:**
```json
{
  "message": "No query results for model [App\\Models\\Loan] 999"
}
```

---

### 11. `GET /api/stats`
Mengambil rekapitulasi data statistik perpustakaan untuk widget/dashboard.

**Request:**
- **Method:** `GET`
- **URL:** `http://127.0.0.1:8000/api/stats`
- **Headers:** `Accept: application/json`

**Contoh Response Sukses (`200 OK`):**
```json
{
  "total_buku": 3,
  "total_anggota": 2,
  "peminjaman_aktif": 1
}
```
