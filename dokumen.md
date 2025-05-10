# DOKUMENTASI SISTEM CUTI KARYAWAN

## Daftar Isi
1. [Pendahuluan](#1-pendahuluan)
2. [Arsitektur Sistem](#2-arsitektur-sistem)
3. [Fitur Utama](#3-fitur-utama)
4. [Panduan Instalasi](#4-panduan-instalasi)
5. [Struktur Database](#5-struktur-database)
6. [Panduan Pengguna](#6-panduan-pengguna)
7. [API Reference](#7-api-reference)
8. [Teknologi yang Digunakan](#8-teknologi-yang-digunakan)
9. [Pemeliharaan dan Pengembangan](#9-pemeliharaan-dan-pengembangan)

## 1. Pendahuluan

Sistem Cuti Karyawan adalah aplikasi web berbasis Laravel yang dirancang untuk mengelola pengajuan dan persetujuan cuti karyawan, serta data terkait lainnya seperti jenis cuti, transportasi, dan detail perjalanan dinas. Sistem ini memudahkan proses administrasi cuti di perusahaan dengan fitur-fitur yang lengkap dan antarmuka yang user-friendly.

### 1.1 Tujuan Sistem

- Mengotomatisasi proses pengajuan dan persetujuan cuti
- Memudahkan monitoring saldo dan riwayat cuti karyawan
- Mengelola data transportasi terkait perjalanan dinas
- Menyediakan laporan komprehensif terkait cuti karyawan
- Menyediakan API untuk integrasi dengan sistem lain

## 2. Arsitektur Sistem

Sistem ini dibangun menggunakan framework Laravel 12, mengikuti pola arsitektur Model-View-Controller (MVC):

### 2.1 Model
Merepresentasikan data dan logika bisnis aplikasi. Terletak di direktori `app/Models/`. Model utama meliputi:
- `Karyawan`: Data karyawan
- `Cuti`: Data pengajuan cuti
- `CutiDetail`: Detail jenis cuti untuk setiap pengajuan
- `JenisCuti`: Jenis-jenis cuti yang tersedia
- `Transportasi`: Jenis transportasi untuk perjalanan dinas
- `TransportasiDetail`: Detail perjalanan dinas terkait cuti
- `User`: Pengguna sistem (karyawan, HR, admin)
- `Role`: Peran pengguna (untuk hak akses)

### 2.2 View
Bertanggung jawab untuk menampilkan data kepada pengguna. Terletak di direktori `resources/views/`. Menggunakan Blade templating engine. Views diorganisir berdasarkan fitur (misal: `resources/views/karyawan/`, `resources/views/cuti/`).

### 2.3 Controller
Menangani request dari pengguna, berinteraksi dengan Model untuk mengambil atau memanipulasi data, dan memilih View yang sesuai untuk ditampilkan. Controller utama:
- `KaryawanController`: Mengelola data karyawan
- `CutiController`: Mengelola pengajuan cuti
- `DashboardController`: Menampilkan dashboard dan statistik
- `JenisCutiController`: Mengelola jenis-jenis cuti
- `TransportasiController`: Mengelola data transportasi

### 2.4 Routing
Definisi URL dan bagaimana request ke URL tersebut ditangani oleh Controller. Didefinisikan dalam file:
- `routes/web.php` (untuk web interface)
- `routes/api.php` (untuk API)

### 2.5 Middleware
Digunakan untuk memfilter HTTP request, seperti autentikasi (`auth`) atau verifikasi CSRF. Didefinisikan di `app/Http/Kernel.php`.

## 3. Fitur Utama

### 3.1 Manajemen Karyawan
- CRUD (Create, Read, Update, Delete) data karyawan
- Import/export data melalui Excel
- Pencarian dan filter data karyawan
- Monitoring cuti per karyawan

### 3.2 Manajemen Cuti
- Pengajuan cuti oleh karyawan
- Persetujuan/penolakan cuti oleh atasan/HR
- Monitoring status dan sisa cuti
- Export data cuti ke Excel/CSV
- Tampilan kalender untuk monitoring cuti

### 3.3 Manajemen Jenis Cuti
- Pengelolaan berbagai jenis cuti (Tahunan, Sakit, dll.)
- Konfigurasi jenis cuti POH (Pusat/Lokal)
- Manajemen kuota cuti per jenis

### 3.4 Manajemen Transportasi
- Pengelolaan jenis transportasi (Pesawat, Kereta, dll.)
- Detail transportasi terkait cuti
- Pencatatan rute perjalanan (asal-tujuan)

### 3.5 Memo Kompensasi
- Pencatatan kebutuhan memo kompensasi
- Tracking status memo kompensasi
- Penomoran dan pencatatan tanggal memo

### 3.6 Dashboard dan Laporan
- Statistik cuti (pending, disetujui, ditolak)
- Grafik penggunaan cuti per departemen
- Monitoring karyawan yang sedang cuti
- Laporan bulanan/tahunan penggunaan cuti

## 4. Panduan Instalasi

### 4.1 Persyaratan Sistem
- PHP 8.2+
- MySQL (atau database lain yang didukung Laravel)
- Composer
- Node.js dan NPM
- Web server (Apache/Nginx)

### 4.2 Langkah Instalasi

1. **Clone Repository:**
   ```bash
   git clone <url-repository>
   cd sistem-cuti-karyawan
   ```

2. **Install Dependensi PHP:**
   ```bash
   composer install
   ```

3. **Install Dependensi Node.js:**
   ```bash
   npm install
   ```

4. **Setup Environment:**
   - Salin file `.env.example` menjadi `.env`:
     ```bash
     cp .env.example .env
     ```
   - Buat kunci aplikasi:
     ```bash
     php artisan key:generate
     ```
   - Konfigurasi koneksi database (`DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`) dan pengaturan lain dalam file `.env`.

5. **Migrasi Database:**
   ```bash
   php artisan migrate
   ```

6. **Seed Database (Opsional):**
   ```bash
   php artisan db:seed
   ```

7. **Build Aset Frontend:**
   ```bash
   npm run build
   ```

### 4.3 Menjalankan Aplikasi

1. **Development Server:**
   ```bash
   php artisan serve
   ```
   Aplikasi akan tersedia di `http://127.0.0.1:8000`

2. **Development Frontend dengan Hot Reload:**
   ```bash
   npm run dev
   ```
   Pastikan juga `php artisan serve` berjalan bersamaan.

## 5. Struktur Database

### 5.1 Tabel Utama

1. **users**
   - Menyimpan data pengguna (login)
   - Relasi dengan: `roles` (Many-to-Many), `karyawans` (One-to-One/One-to-Many)

2. **karyawans**
   - Menyimpan data detail karyawan
   - Kolom utama: id, nama, nik, departemen, jabatan, email, poh
   - Relasi dengan: `users` (One-to-One/One-to-Many), `cutis` (One-to-Many)

3. **jenis_cutis**
   - Menyimpan jenis-jenis cuti (Tahunan, Sakit, dll.)
   - Relasi dengan: `cutis` (One-to-Many), `cuti_details` (One-to-Many)

4. **cutis**
   - Menyimpan data pengajuan cuti
   - Kolom utama: karyawan_id, jenis_cuti_id, tanggal_mulai, tanggal_selesai, lama_hari, alasan, status_cuti, memo_kompensasi_status, memo_kompensasi_nomor, memo_kompensasi_tanggal
   - Relasi dengan: `karyawans`, `jenis_cutis`, `cuti_details`, `transportasi_details`

5. **cuti_details**
   - Menyimpan detail jenis cuti untuk setiap pengajuan
   - Relasi dengan: `cutis` (Many-to-One), `jenis_cutis` (Many-to-One)

6. **transportasis**
   - Menyimpan jenis transportasi (Pesawat, Kereta, dll.)
   - Relasi dengan: `transportasi_details` (One-to-Many)

7. **transportasi_details**
   - Menyimpan detail perjalanan terkait cuti
   - Relasi dengan: `cutis` (Many-to-One), `transportasis` (Many-to-One)

8. **roles**
   - Menyimpan peran pengguna (Admin, HR, Karyawan)

## 6. Panduan Pengguna

### 6.1 Login
- Buka aplikasi melalui browser
- Masukkan Email/Username dan Password
- Klik tombol "Login"

### 6.2 Dashboard
- Menampilkan statistik dan ringkasan informasi penting
- Grafik penggunaan cuti per departemen
- Status cuti karyawan (pending, disetujui, ditolak)
- Daftar pengajuan cuti terbaru

### 6.3 Manajemen Karyawan
- **Melihat Daftar Karyawan:** Menu "Karyawan"
- **Menambah Karyawan:** Tombol "Tambah Karyawan", isi formulir, klik "Simpan"
- **Mengedit Karyawan:** Klik "Edit" pada daftar karyawan
- **Menghapus Karyawan:** Klik "Hapus" pada daftar karyawan
- **Import/Export Data:** Tombol "Import"/"Export" pada daftar karyawan
- **Monitoring Cuti Karyawan:** Klik pada nama karyawan untuk melihat detail dan monitoring cuti

### 6.4 Manajemen Cuti
- **Melihat Daftar Cuti:** Menu "Cuti"
- **Mengajukan Cuti:** Tombol "Ajukan Cuti", isi formulir (karyawan, jenis cuti, tanggal, alasan), klik "Simpan"
- **Detail Cuti:** Klik pada baris cuti untuk melihat detail
- **Edit/Hapus Cuti:** Tombol aksi pada daftar cuti
- **Batch Process:** Pilih beberapa cuti dengan checkbox untuk persetujuan massal

### 6.5 Persetujuan Cuti
- **Menyetujui/Menolak:** Klik "Approve" atau "Reject" pada daftar cuti
- **Batch Approve:** Pilih beberapa cuti, klik "Batch Approve"

### 6.6 Manajemen Jenis Cuti
- **CRUD Jenis Cuti:** Menu "Jenis Cuti"

### 6.7 Manajemen Transportasi
- **CRUD Transportasi:** Menu "Transportasi"
- **Detail Transportasi:** Terkait dengan pengajuan cuti

### 6.8 Import/Export Data
- Template tersedia untuk download sebelum import
- Format tanggal harus DD/MM/YYYY
- NIK karyawan harus sudah terdaftar dalam sistem
- Jenis cuti harus sesuai dengan yang tersedia

## 7. API Reference

Sistem menyediakan API untuk integrasi dengan aplikasi lain.

### 7.1 Base URL
- URL API diawali dengan prefix `/api`
- Contoh: `http://localhost:8000/api`

### 7.2 Autentikasi
- Menggunakan Laravel Sanctum
- Include token di header: `Authorization: Bearer <your-api-token>`

### 7.3 Endpoints Utama

#### Karyawan
- `GET /karyawans/search`: Mencari data karyawan

#### Cuti
- `GET /cutis`: Mendapatkan daftar cuti
- `POST /cutis`: Membuat pengajuan cuti
- `GET /cutis/{id}`: Detail cuti
- `PUT/PATCH /cutis/{id}`: Update cuti
- `DELETE /cutis/{id}`: Hapus cuti

#### User
- `GET /user`: Mendapatkan info user yang terautentikasi

## 8. Teknologi yang Digunakan

### 8.1 Backend
- PHP 8.2+
- Laravel 12
- MySQL
- Composer (Manajemen dependensi PHP)
- Laravel Sanctum (Autentikasi API)
- Maatwebsite/Excel & FastExcel (Import/Export Excel)
- Barryvdh/laravel-dompdf (Generate PDF)

### 8.2 Frontend
- Vite (Build tool)
- Bootstrap 5
- Tailwind CSS
- Sass
- Axios (HTTP Client)
- Chart.js (Visualisasi data)

## 9. Pemeliharaan dan Pengembangan

### 9.1 Backup Database
Lakukan backup database secara berkala untuk menghindari kehilangan data.

### 9.2 Update Sistem
- Update dependensi secara berkala: `composer update` dan `npm update`
- Periksa keamanan dengan: `composer audit`

### 9.3 Pengembangan Lanjutan
Beberapa ide pengembangan lanjutan:
- Integrasi notifikasi email untuk status cuti
- Pengembangan mobile app dengan memanfaatkan API
- Integrasi dengan sistem payroll
- Fitur upload dokumen pendukung cuti

---

Dokumentasi ini merupakan panduan komprehensif untuk memahami, menggunakan, dan mengembangkan Sistem Cuti Karyawan. Untuk informasi lebih detail, silakan merujuk ke kode sumber atau hubungi tim pengembang.