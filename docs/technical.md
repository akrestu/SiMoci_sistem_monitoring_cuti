# Dokumentasi Teknis - Sistem Cuti Karyawan

Dokumen ini menjelaskan aspek teknis dari aplikasi Sistem Cuti Karyawan.

## 1. Arsitektur Aplikasi

Aplikasi ini dibangun menggunakan framework **Laravel 12**, mengikuti pola arsitektur **Model-View-Controller (MVC)**:

*   **Model:** Merepresentasikan data dan logika bisnis aplikasi. Terletak di direktori `app/Models/`. Model berinteraksi dengan database menggunakan Eloquent ORM. Model utama meliputi:
    *   `Karyawan`: Data karyawan.
    *   `Cuti`: Data pengajuan cuti.
    *   `CutiDetail`: Detail tanggal untuk setiap pengajuan cuti.
    *   `JenisCuti`: Jenis-jenis cuti yang tersedia.
    *   `Transportasi`: Jenis transportasi untuk perjalanan dinas.
    *   `TransportasiDetail`: Detail perjalanan dinas terkait cuti.
    *   `User`: Pengguna sistem (bisa karyawan, HR, admin).
    *   `Role`: Peran pengguna (untuk hak akses).
*   **View:** Bertanggung jawab untuk menampilkan data kepada pengguna. Terletak di direktori `resources/views/`. Menggunakan Blade templating engine. Views diorganisir berdasarkan fitur (misal: `resources/views/karyawan/`, `resources/views/cuti/`).
*   **Controller:** Menangani request dari pengguna, berinteraksi dengan Model untuk mengambil atau memanipulasi data, dan kemudian memilih View yang sesuai untuk ditampilkan. Terletak di direktori `app/Http/Controllers/`. Terdapat controller untuk setiap fitur utama (misal: `KaryawanController`, `CutiController`) dan juga untuk API (`app/Http/Controllers/Api/`).
*   **Routing:** Definisi URL dan bagaimana request ke URL tersebut ditangani oleh Controller. Didefinisikan dalam file `routes/web.php` (untuk web) dan `routes/api.php` (untuk API).
*   **Middleware:** Digunakan untuk memfilter HTTP request, seperti autentikasi (`auth`) atau verifikasi CSRF. Didefinisikan di `app/Http/Kernel.php`.

## 2. Struktur Database

Skema database didefinisikan melalui file migrasi di `database/migrations/`. Tabel-tabel utama dan relasinya (berdasarkan nama model dan foreign key yang umum digunakan di Laravel):

*   `users`: Menyimpan data pengguna (login). Relasi: `roles` (Many-to-Many, asumsi ada pivot table), `karyawans` (One-to-One/One-to-Many, perlu dicek).
*   `roles`: Menyimpan daftar peran pengguna.
*   `karyawans`: Menyimpan data detail karyawan. Relasi: `users` (One-to-One/One-to-Many), `cutis` (One-to-Many).
*   `jenis_cutis`: Menyimpan jenis-jenis cuti (misal: Tahunan, Sakit). Relasi: `cutis` (One-to-Many).
*   `cutis`: Menyimpan data pengajuan cuti. Relasi: `karyawans` (Many-to-One), `jenis_cutis` (Many-to-One, nullable), `users` (Many-to-One untuk approval), `cuti_details` (One-to-Many), `transportasi_details` (One-to-Many).
*   `cuti_details`: Menyimpan detail tanggal spesifik untuk setiap pengajuan cuti. Relasi: `cutis` (Many-to-One).
*   `transportasis`: Menyimpan jenis transportasi (misal: Pesawat, Kereta). Relasi: `transportasi_details` (One-to-Many).
*   `transportasi_details`: Menyimpan detail perjalanan dinas (tiket, akomodasi, dll.) yang terkait dengan suatu cuti. Relasi: `cutis` (Many-to-One), `transportasis` (Many-to-One).

*Catatan: Relasi spesifik mungkin perlu diverifikasi lebih lanjut dari kode Model dan Migrasi.*

## 3. Dependensi Utama

### Backend (PHP - via Composer)

*   `laravel/framework`: Core framework Laravel.
*   `laravel/ui`: Untuk scaffolding autentikasi dasar (login, register).
*   `laravel/sanctum`: Untuk autentikasi API (token-based).
*   `maatwebsite/excel` & `rap2hpoutre/fast-excel`: Untuk import dan export data ke format Excel.
*   `barryvdh/laravel-dompdf`: Untuk generate file PDF (jika fitur report PDF diimplementasikan).
*   `doctrine/dbal`: Diperlukan untuk modifikasi kolom tabel melalui migrasi.

### Frontend (JavaScript - via NPM)

*   `vite`: Modern frontend build tool.
*   `laravel-vite-plugin`: Integrasi Vite dengan Laravel.
*   `bootstrap`: Framework CSS/JS untuk UI.
*   `@tailwindcss/vite` & `tailwindcss`: Utility-first CSS framework (penggunaan perlu dikonfirmasi).
*   `sass`: Preprocessor CSS.
*   `axios`: Library untuk membuat HTTP request (digunakan untuk interaksi API dari frontend).
*   `@popperjs/core`: Dependency untuk komponen Bootstrap seperti dropdown/tooltip.

## 4. Konfigurasi Environment (.env)

File `.env` digunakan untuk menyimpan konfigurasi spesifik environment. Beberapa variabel penting:

*   `APP_NAME`: Nama aplikasi (muncul di title bar, dll.).
*   `APP_ENV`: Environment aplikasi (`local`, `production`, `testing`).
*   `APP_KEY`: Kunci enkripsi unik untuk aplikasi (dihasilkan oleh `php artisan key:generate`).
*   `APP_DEBUG`: Mengaktifkan/menonaktifkan mode debug (`true` di local, `false` di production).
*   `APP_URL`: URL utama aplikasi (penting untuk link generation).
*   `DB_CONNECTION`: Jenis database (`mysql`, `pgsql`, `sqlite`).
*   `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`: Kredensial koneksi database.
*   `MAIL_MAILER`, `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`, `MAIL_ENCRYPTION`, `MAIL_FROM_ADDRESS`, `MAIL_FROM_NAME`: Konfigurasi untuk pengiriman email (jika fitur notifikasi email digunakan).

Pastikan konfigurasi ini sesuai dengan environment tempat aplikasi dijalankan.
