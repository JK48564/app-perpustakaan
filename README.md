# Sistem Perpustakaan Digital Kampus (`app-perpustakaan`)

Aplikasi web manajemen perpustakaan kampus berbasis **Laravel 12**, dibangun secara bertahap dari Pertemuan 1 hingga Pertemuan 10 dengan menerapkan arsitektur MVC, Blade templating, Eloquent ORM & Relationships, autentikasi session-based, role-based middleware, REST API terstruktur, hingga konsumsi API internal melalui Laravel HTTP Client.git merge origin/main

---

## Fitur Utama

- **Dashboard Statistik Interaktif**: Menampilkan rekapitulasi jumlah buku, anggota, dan peminjaman aktif yang dikonsumsi secara real-time dari endpoint API internal (`GET /api/stats`).
- **Autentikasi & Otorisasi Berbasis Role**: Sistem login/logout tanpa starter kit, dilindungi middleware `auth` dan middleware custom `CheckAdminRole` untuk membedakan hak akses `admin` (akses penuh termasuk Kategori) dan `petugas` (operasional peminjaman).
- **CRUD Lengkap Data Master & Transaksi**:
  - **Buku (`books`)**: Pencatatan buku lengkap dengan ISBN, stok, sampul, relasi kategori, dan paginasi teks kustom.
  - **Kategori (`categories`)**: Pengelompokan buku (khusus hak akses admin).
  - **Anggota (`members`)**: Pendaftaran mahasiswa dengan validasi unik NIM dan Email kampus.
  - **Peminjaman (`loans`)**: Transaksi peminjaman multi-buku, otomatis mencatat user petugas yang sedang login, status peminjaman, serta fitur pengembalian buku.
  - **Laporan Peminjaman (`loans/report`)**: Laporan rekapitulasi peminjaman yang mengonsumsi endpoint `GET /api/loans` via Laravel HTTP Client.
- **RESTful API Siap Pakai**: 11 endpoint API terstandarisasi JSON menggunakan API Resource (`BookResource`, `MemberResource`, `LoanResource`), siap diintegrasikan dengan aplikasi mobile atau frontend modern.
- **Profil & Ganti Password**: Pengelolaan profil akun pengguna dan pembaruan password aman menggunakan bcrypt hashing.
- **Data Dummy Realistis**: Dilengkapi Seeder dan Factory kurasi bahasa Indonesia (`id_ID`) dengan domain kampus `@pens.ac.id`.

---

## Prasyarat Sistem

- **PHP**: Versi `^8.2` atau lebih baru (termasuk ekstensi `pdo_sqlite`, `mbstring`, `curl`, `openssl`)
- **Composer**: Versi `2.x`
- **Database**: SQLite (default & siap pakai) atau MySQL/MariaDB

---

## Panduan Instalasi & Menjalankan Aplikasi Secara Lokal

### 1. Clone Repository
Unduh repository ke komputer lokal:
```bash
git clone https://github.com/USERNAME/app-perpustakaan.git
cd app-perpustakaan
```

### 2. Install Dependency Composer
Pasang seluruh library dan package yang dibutuhkan project:
```bash
composer install
```

### 3. Setup File Konfigurasi Environment (`.env`)
Salin file `.env.example` menjadi `.env`:
```bash
cp .env.example .env
```
*(Di Windows PowerShell: `Copy-Item .env.example .env`)*

Buka file `.env` dan pastikan konfigurasi penting berikut telah terpasang:
```env
APP_NAME=Laravel
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

# Konfigurasi Server API Internal (Wajib port berbeda dari server utama)
INTERNAL_API_URL=http://127.0.0.1:8011

# Locale Faker Indonesia
APP_FAKER_LOCALE=id_ID

# Database (Default menggunakan SQLite)
DB_CONNECTION=sqlite
```

Generate application key:
```bash
php artisan key:generate
```

### 4. Migrasi & Seeding Database
Jalankan migrasi database beserta seeder data awal:
```bash
php artisan migrate:fresh --seed
```
Perintah di atas akan membuat seluruh tabel dan mengisinya dengan data dummy realistis:
- 1 Admin (`Bambang Sutrisno`) & 2 Petugas (`Siti Rahmawati`, `Ahmad Fauzi`)
- 5 Kategori buku
- 20 Buku dengan judul dan penerbit asli Indonesia
- 15 Data anggota mahasiswa PENS
- 10 Riwayat transaksi peminjaman

---

## Menjalankan Aplikasi: Kebutuhan DUA SERVER Lokal (Penting)

Server bawaan `php artisan serve` (PHP built-in server) bersifat **single-threaded** (hanya memproses 1 request dalam satu waktu). 

Karena halaman **Dashboard** dan **Laporan Peminjaman** mengonsumsi endpoint API-nya sendiri dari dalam kode PHP menggunakan Laravel HTTP Client (`Http::get()`), memanggil port yang sama akan menyebabkan **deadlock** (server terkunci saling menunggu).

Oleh karena itu, buka **DUA JENDELA TERMINAL** berdampingan:

### Terminal 1 — Server Utama (Trafik Browser Web)
```bash
php artisan serve --port=8000
```
> Buka browser dan akses antarmuka aplikasi di: **`http://127.0.0.1:8000`**

### Terminal 2 — Server API Internal (Khusus Konsumsi HTTP Client)
```bash
php artisan serve --port=8011
```
> Server ini khusus menerima request internal dari `DashboardController` (`/api/stats`) dan `LoanController@report` (`/api/loans`).

---

## Kredensial Login Default

Setelah database di-seed, gunakan akun berikut untuk masuk ke aplikasi (`http://127.0.0.1:8000/login`):

| Role | Nama | Email | Password | Keterangan Hak Akses |
|---|---|---|---|---|
| **Admin** | Bambang Sutrisno | `admin@pens.ac.id` | `password` | Akses penuh seluruh modul (termasuk menu Kategori) |
| **Petugas 1** | Siti Rahmawati | `petugas1@pens.ac.id` | `password` | Operasional peminjaman & buku (menu Kategori dibatasi / 403) |
| **Petugas 2** | Ahmad Fauzi | `petugas2@pens.ac.id` | `password` | Operasional peminjaman & buku (menu Kategori dibatasi / 403) |

---

## Dokumentasi & Pengujian

- **Dokumentasi REST API**: Daftar lengkap 11 endpoint beserta contoh response sungguhan dan status code dapat dilihat pada file:
   [Dokumentasi REST API (API.md)](./API.md)
- **Postman Collection Siap Pakai**: Seluruh endpoint dan skenario uji telah diekspor ke file:
   [`postman_collection.json`](./postman_collection.json) *(Bisa langsung di-import ke aplikasi Postman)*
- **Skenario Demo Live UAS**: Urutan langkah demo live dari login hingga pengujian API beserta rangkuman tanya jawab konseptual:
   [Panduan Demo Live UAS (DEMO.md)](./DEMO.md)

---

## Refleksi Aspek Arsitektur MVC

Arsitektur **Model-View-Controller (MVC)** memisahkan tanggung jawab kode aplikasi ke dalam tiga komponen utama:
1. **Model**: Bertugas mengelola data, aturan bisnis, dan berinteraksi langsung dengan database (Eloquent ORM & Relationships).
2. **View**: Bertanggung jawab menampilkan antarmuka visual kepada pengguna menggunakan templating engine (Blade).
3. **Controller**: Bertindak sebagai penghubung dan pengendali alur request dari pengguna, memvalidasi input, memproses data melalui Model, dan meneruskan hasilnya ke View atau response JSON.
