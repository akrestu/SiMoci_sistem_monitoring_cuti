<h1 align="center">Hi 👋, I'm Restu</h1>
<h3 align="center">A passionate laravel developer from Surabaya</h3>

- 📫 How to reach me **adrosrestuk@gmail.com**

<h3 align="left">Connect with me:</h3>
<p align="left">
</p>

<h3 align="left">Languages and Tools:</h3>
<p align="left"> <a href="https://getbootstrap.com" target="_blank" rel="noreferrer"> <img src="https://raw.githubusercontent.com/devicons/devicon/master/icons/bootstrap/bootstrap-plain-wordmark.svg" alt="bootstrap" width="40" height="40"/> </a> <a href="https://flutter.dev" target="_blank" rel="noreferrer"> <img src="https://www.vectorlogo.zone/logos/flutterio/flutterio-icon.svg" alt="flutter" width="40" height="40"/> </a> <a href="https://developer.mozilla.org/en-US/docs/Web/JavaScript" target="_blank" rel="noreferrer"> <img src="https://raw.githubusercontent.com/devicons/devicon/master/icons/javascript/javascript-original.svg" alt="javascript" width="40" height="40"/> </a> <a href="https://laravel.com/" target="_blank" rel="noreferrer"> <img src="https://raw.githubusercontent.com/devicons/devicon/master/icons/laravel/laravel-plain-wordmark.svg" alt="laravel" width="40" height="40"/> </a> <a href="https://www.mysql.com/" target="_blank" rel="noreferrer"> <img src="https://raw.githubusercontent.com/devicons/devicon/master/icons/mysql/mysql-original-wordmark.svg" alt="mysql" width="40" height="40"/> </a> <a href="https://www.php.net" target="_blank" rel="noreferrer"> <img src="https://raw.githubusercontent.com/devicons/devicon/master/icons/php/php-original.svg" alt="php" width="40" height="40"/> </a> <a href="https://reactjs.org/" target="_blank" rel="noreferrer"> <img src="https://raw.githubusercontent.com/devicons/devicon/master/icons/react/react-original-wordmark.svg" alt="react" width="40" height="40"/> </a> <a href="https://vuejs.org/" target="_blank" rel="noreferrer"> <img src="https://raw.githubusercontent.com/devicons/devicon/master/icons/vuejs/vuejs-original-wordmark.svg" alt="vuejs" width="40" height="40"/> </a> </p>


# Sistem Cuti Karyawan

Sistem Cuti Karyawan adalah aplikasi web berbasis Laravel yang dirancang untuk mengelola pengajuan dan persetujuan cuti karyawan, serta data terkait lainnya seperti jenis cuti, transportasi, dan detail perjalanan dinas.

## Fitur Utama

*   **Manajemen Karyawan:** CRUD (Create, Read, Update, Delete) data karyawan, termasuk import/export data via Excel dan pencarian.
*   **Manajemen Cuti:** Pengajuan cuti oleh karyawan (via API), persetujuan/penolakan cuti oleh atasan/HR, monitoring sisa cuti, export data cuti.
*   **Manajemen Jenis Cuti:** Mengelola berbagai jenis cuti yang tersedia (misal: Cuti Tahunan, Cuti Sakit).
*   **Manajemen Transportasi:** Mengelola jenis transportasi yang digunakan untuk perjalanan dinas.
*   **Manajemen Detail Transportasi:** Mencatat detail perjalanan dinas terkait cuti (tiket, akomodasi, dll.), termasuk dashboard dan pengingat deadline.
*   **Manajemen User & Role:** Mengelola pengguna sistem dan hak aksesnya.
*   **Dashboard:** Menampilkan ringkasan informasi penting (misal: status cuti, deadline transportasi).
*   **API:** Menyediakan endpoint untuk interaksi programatik (misal: pengajuan cuti, pencarian karyawan).

## Teknologi yang Digunakan

*   **Backend:**
    *   PHP 8.2+
    *   Laravel 12
    *   MySQL (atau database lain yang didukung Laravel)
    *   Composer (Manajemen dependensi PHP)
    *   Laravel Sanctum (Autentikasi API)
    *   Maatwebsite/Excel & FastExcel (Import/Export Excel)
    *   Barryvdh/laravel-dompdf (Generate PDF - *jika digunakan*)
*   **Frontend:**
    *   Vite (Build tool)
    *   Bootstrap 5
    *   Tailwind CSS (*terdeteksi di package.json, perlu konfirmasi penggunaan*)
    *   Sass
    *   Axios (HTTP Client)
    *   Node.js & NPM (Manajemen dependensi JS)

## Instalasi

1.  **Clone Repository:**
    ```bash
    git clone <url-repository-anda>
    cd sistem-cuti-karyawan
    ```
2.  **Install Dependensi PHP:**
    ```bash
    composer install
    ```
3.  **Install Dependensi Node.js:**
    ```bash
    npm install
    ```
4.  **Setup Environment:**
    *   Salin file `.env.example` menjadi `.env`:
        ```bash
        cp .env.example .env
        ```
    *   Buat kunci aplikasi:
        ```bash
        php artisan key:generate
        ```
    *   Konfigurasi koneksi database (`DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`) dan pengaturan lain yang diperlukan dalam file `.env`.
5.  **Migrasi Database:**
    Jalankan migrasi untuk membuat tabel-tabel database:
    ```bash
    php artisan migrate
    ```
6.  **Seed Database (Opsional):**
    Jika tersedia seeder untuk data awal (user, role, jenis cuti, dll.):
    ```bash
    php artisan db:seed
    ```
7.  **Build Aset Frontend:**
    ```bash
    npm run build
    ```

## Menjalankan Aplikasi

1.  **Jalankan Development Server:**
    Gunakan perintah `serve` dari Artisan:
    ```bash
    php artisan serve
    ```
    Aplikasi akan tersedia di `http://127.0.0.1:8000` (atau port lain jika 8000 sudah digunakan).

2.  **Jalankan dengan Vite (untuk Development Frontend):**
    Jika Anda ingin hot-reloading saat mengembangkan frontend:
    ```bash
    npm run dev
    ```
    Ini akan menjalankan server Vite. Pastikan juga `php artisan serve` berjalan.

## Dokumentasi Tambahan

*   **Dokumentasi Teknis:** [docs/technical.md](docs/technical.md) (Akan dibuat)
*   **Dokumentasi API:** [docs/api.md](docs/api.md) (Akan dibuat)
*   **Panduan Pengguna:** [docs/user_guide.md](docs/user_guide.md) (Akan dibuat)

---

*Catatan: Dokumentasi ini dibuat berdasarkan analisis kode per [Tanggal Pembuatan].*
