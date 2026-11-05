# Panduan Skenario Demo UAS — Perpustakaan Digital Kampus

Panduan langkah demo live aplikasi `app-perpustakaan` untuk evaluasi akhir semester (UAS) melalui Browser dan API Client (Postman/Bruno).

---

## 1. Persiapan Sebelum Demo

1. **Reset & Seed Database**:
   ```bash
   php artisan migrate:fresh --seed
   ```
2. **Jalankan Dua Instance Server (Wajib)**:
   - **Terminal 1** (Aplikasi Web & Browser):
     ```bash
     php artisan serve --port=8000
     ```
   - **Terminal 2** (Internal API Consumer — port 8011):
     ```bash
     php artisan serve --port=8011
     ```
   > **Catatan Teknis**: Dua server diperlukan karena `php artisan serve` bersifat *single-threaded*. Memanggil API internal di port yang sama akan memicu *deadlock*.

---

## 2. Akun Pengujian

| Role | Email | Password | Nama Pengguna | Hak Akses |
|---|---|---|---|---|
| **Admin** | `admin@pens.ac.id` | `password` | Bambang Sutrisno | Akses penuh (Dashboard, Buku, Kategori, Anggota, Peminjaman, Laporan, Profil) |
| **Petugas** | `petugas1@pens.ac.id` | `password` | Siti Rahmawati | Operasional harian, akses menu Kategori ditolak (**403 Forbidden**) |

---

## 3. Urutan Skenario Demo Live

### Step 1: Autentikasi & Otorisasi Role
1. Buka `http://127.0.0.1:8000/books` saat belum login $\rightarrow$ Ter-redirect otomatis ke `/login` (*middleware auth*).
2. Login sebagai **Admin** (`admin@pens.ac.id` / `password`).
3. Verifikasi:
   - Diarahkan ke **Dashboard** (`/`) dengan notifikasi sukses.
   - Navbar menampilkan nama: **Bambang Sutrisno (Admin)**.
   - Menu **Kategori** muncul di navbar.
4. *(Uji Role Petugas)*: Buka jendela incognito/logout, login sebagai `petugas1@pens.ac.id`. Menu Kategori tidak ada; akses manual ke `/categories` menghasilkan **403 Forbidden** (*middleware CheckAdminRole*).

### Step 2: Dashboard (Konsumsi API Internal)
1. Periksa 3 kartu ringkasan data di Dashboard:
   - **Total Buku**: 20 (data seeder)
   - **Total Anggota**: 15
   - **Peminjaman Aktif**: 2–4 (status `dipinjam`)
2. *Poin Penjelasan*: Angka statistik diambil dari `GET /api/stats` via Laravel HTTP Client (`Http::get()`) pada port 8011, bukan query langsung ke model Eloquent di controller web.

### Step 3: Manajemen Kategori (Khusus Admin)
1. Klik menu **Kategori** (`/categories`) $\rightarrow$ **+ Tambah Kategori**.
2. Masukkan data:
   - **Nama**: `Kecerdasan Buatan`
   - **Deskripsi**: `Buku seputar machine learning, neural networks, dan AI.`
3. Simpan dan pastikan notifikasi sukses muncul serta kategori baru terdaftar di tabel.

### Step 4: Manajemen Buku (CRUD & Custom Pagination)
1. Klik menu **Buku** (`/books`).
2. Tunjukkan **Pagination** di bawah tabel: format teks bersih (`« Sebelumnya`, `1`, `2`, `Berikutnya »`). Klik halaman `2` untuk verifikasi data.
3. Klik **+ Tambah Buku**:
   - **Judul**: `Hands-On Machine Learning`
   - **Penulis**: `Aurélien Géron`
   - **Penerbit**: `O'Reilly Media`
   - **Tahun**: `2023`
   - **ISBN**: `9781098125974`
   - **Stok**: `5`
   - **Kategori**: Pilih `Kecerdasan Buatan`
4. Simpan, cek detail buku, lalu uji fitur **Edit** (ubah stok) dan simpan kembali.

### Step 5: Manajemen Anggota & Validasi Unik
1. Klik menu **Anggota** (`/members`) $\rightarrow$ **+ Tambah Anggota**.
2. Masukkan data:
   - **Nama**: `Fajar Pratama`
   - **NIM**: `3122600020`
   - **Email**: `fajar@student.pens.ac.id`
   - **Nomor Telepon**: `081234567899`
   - **Alamat**: `Jl. Raya ITS, Surabaya`
   - **Status**: `aktif`
3. Simpan dan pastikan anggota baru muncul di tabel.
4. *(Uji Validasi)*: Coba tambah lagi dengan NIM atau Email yang sama $\rightarrow$ Muncul error validasi duplikat.

### Step 6: Transaksi Peminjaman Baru
1. Klik menu **Peminjaman** (`/loans`) $\rightarrow$ **+ Tambah Peminjaman**.
2. Perhatikan: Field Petugas terisi otomatis dari sesi login user (`Bambang Sutrisno`), tanpa dropdown manual.
3. Masukkan data:
   - **Anggota**: Pilih `Fajar Pratama`
   - **Tanggal Pinjam**: Hari ini
   - **Tanggal Kembali**: 7 hari ke depan
   - **Buku**: Pilih 1–2 buku (termasuk `Hands-On Machine Learning`)
4. Simpan transaksi $\rightarrow$ Muncul di tabel dengan badge kuning **`Dipinjam`**.

### Step 7: Proses Pengembalian Buku
1. Pada baris transaksi yang baru dibuat di tabel `/loans`, klik tombol **Kembalikan**.
2. Verifikasi: Status transaksi berganti menjadi badge hijau **`Dikembalikan`** dan kolom tanggal dikembalikan otomatis terisi hari ini.

### Step 8: Laporan Peminjaman (Konsumsi API Internal)
1. Klik menu **Laporan** (`/loans/report`).
2. Tunjukkan tabel data laporan peminjaman lengkap (anggota, petugas, daftar buku, status).
3. *Poin Penjelasan*: Data diambil dari panggilan HTTP internal ke `GET /api/loans`, lalu di-render ke Blade menggunakan metadata pagination API.

### Step 9: Halaman Profil & Ganti Password
1. Klik menu **Profil** (`/profil`).
2. Tunjukkan informasi identitas user aktif (Nama, Email, Role).
3. Tunjukkan form ganti password dengan validasi password lama (`Hash::check`) dan minimal 8 karakter konfirmasi (`confirmed`).

### Step 10: Demo REST API (Postman / Bruno / Curl)
Uji endpoint publik di `http://127.0.0.1:8000/api` dengan header `Accept: application/json`:
1. `GET /api/books` $\rightarrow$ Response JSON list buku terpaginasi dengan struktur `BookResource` (nested category).
2. `GET /api/books?judul=laskar` $\rightarrow$ Filter pencarian buku berdasarkan judul.
3. `GET /api/stats` $\rightarrow$ Response JSON statistik `{ total_buku, total_anggota, peminjaman_aktif }` sinkron dengan kartu Dashboard.
4. `POST /api/loans` $\rightarrow$ Simulasikan peminjaman baru via JSON payload (status `201 Created`).
5. `GET /api/books/999` $\rightarrow$ Uji penanganan error `404 Not Found`.
6. `POST /api/books` dengan body `{}` $\rightarrow$ Uji penanganan error validasi `422 Unprocessable Content`.

---

