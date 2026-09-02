Nama aplikasi: App-Perpustakaan
Tujuan: Membantu user untuk mengakses/meminjam buku secara online
Cara menjalankan secara lokal:
- Clone repository 
Unduh project dari GitHub ke komputer, kemudian masuk ke folder project. Bisa menggunakan perintah terminal:
git clone https://github.com/USERNAME/app-perpustakaan.git
cd app-perpustakaan

- Install Composer 
Jalankan Composer untuk mengunduh seluruh library yang dibutuhkan oleh project.
composer install

- Buat file konfigurasi environment
Buka file `.env`, lalu sesuaikan bagian berikut dengan database MySQL yang telah dibuat.
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_perpustakaan
DB_USERNAME=root
DB_PASSWORD=

-  Jalankan migrasi database menggunakan
php artisan serve

- Buka browser, kemudian akses:
http://127.0.0.1:8000


Menurut saya perbedaan dari tiap aspek dalam MVC arsitektur adalah penggunaannya, Model bertugas mengelola data dan berinteraksi langsung dengan database, View digunakan sebagai menampilkan antarmuka atau halaman yang akan dilihat oleh pengguna, dan Controller menjadi penghubung yang menerima request pengguna, memproses data melalui Model, lalu mengirimkan hasil tersebut ke View agar dapat ditampilk
