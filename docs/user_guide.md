# Panduan Pengguna - Sistem Cuti Karyawan

Panduan ini menjelaskan cara menggunakan fitur-fitur utama dalam aplikasi Sistem Cuti Karyawan. Target pengguna bisa bervariasi (Karyawan, HR, Admin) tergantung pada hak akses (role) yang dimiliki.

## 1. Login

*   Buka aplikasi melalui browser Anda (biasanya `http://localhost:8000` jika dijalankan secara lokal).
*   Anda akan diarahkan ke halaman login.
*   Masukkan **Email** atau **Username** dan **Password** Anda yang terdaftar.
*   Klik tombol "Login".
*   Jika berhasil, Anda akan diarahkan ke halaman Dashboard.

## 2. Dashboard

Halaman Dashboard menampilkan ringkasan informasi penting dan navigasi utama. Konten dashboard bisa berbeda tergantung role pengguna. Umumnya berisi:

*   Statistik singkat (misal: jumlah karyawan, jumlah cuti pending).
*   Shortcut ke fitur-fitur utama.
*   Informasi relevan seperti deadline transportasi (jika ada).
*   Menu navigasi utama (biasanya di sidebar atau header) untuk mengakses modul lain.

## 3. Manajemen Karyawan (Umumnya untuk Admin/HR)

Modul ini digunakan untuk mengelola data karyawan.

*   **Melihat Daftar Karyawan:**
    *   Akses menu "Karyawan" atau sejenisnya.
    *   Akan tampil tabel berisi daftar karyawan dengan informasi dasar (NIK, Nama, Jabatan, dll.).
    *   Fitur pencarian dan pagination biasanya tersedia.
*   **Menambah Karyawan Baru:**
    *   Klik tombol "Tambah Karyawan" atau ikon "+".
    *   Isi formulir data karyawan (NIK, Nama, Email, Jabatan, Tanggal Masuk, dll.).
    *   Klik "Simpan".
*   **Mengedit Data Karyawan:**
    *   Pada daftar karyawan, cari karyawan yang ingin diubah.
    *   Klik tombol "Edit" (biasanya ikon pensil) di baris karyawan tersebut.
    *   Ubah data pada formulir yang muncul.
    *   Klik "Simpan" atau "Update".
*   **Menghapus Karyawan:**
    *   Pada daftar karyawan, cari karyawan yang ingin dihapus.
    *   Klik tombol "Hapus" (biasanya ikon tong sampah).
    *   Konfirmasi penghapusan jika diminta.
    *   Terdapat juga fitur "Mass Delete" untuk menghapus beberapa karyawan sekaligus dengan mencentang checkbox.
*   **Import/Export Data Karyawan:**
    *   Biasanya terdapat tombol "Import" dan "Export" di halaman daftar karyawan.
    *   **Export:** Klik "Export" untuk mengunduh data karyawan dalam format Excel. Mungkin ada opsi template ("Export Template") untuk import.
    *   **Import:** Klik "Import", pilih file Excel yang sesuai template, lalu unggah. Sistem akan memproses data dari file tersebut.
*   **Melihat Monitoring Cuti Karyawan:**
    *   Pada detail karyawan atau melalui menu terpisah, mungkin ada opsi "Monitoring Cuti".
    *   Ini akan menampilkan riwayat cuti dan sisa kuota cuti karyawan tersebut.

## 4. Manajemen Cuti (Semua User, dengan cakupan berbeda)

Modul ini untuk mengelola pengajuan cuti.

*   **Melihat Daftar Cuti:**
    *   Akses menu "Cuti" atau sejenisnya.
    *   Karyawan biasa mungkin hanya melihat cuti miliknya. Atasan/HR mungkin melihat semua cuti atau cuti bawahannya.
    *   Tabel menampilkan detail pengajuan (Tanggal, Jenis, Status, dll.).
    *   Filter berdasarkan status (Pending, Approved, Rejected) biasanya tersedia.
*   **Mengajukan Cuti Baru:**
    *   Klik tombol "Ajukan Cuti" atau sejenisnya.
    *   Isi formulir pengajuan:
        *   Pilih Jenis Cuti.
        *   Pilih Tanggal Mulai dan Tanggal Selesai.
        *   Masukkan Keterangan/Alasan.
    *   Klik "Simpan" atau "Ajukan".
    *   *Catatan: Berdasarkan analisis API, pengajuan cuti utama mungkin dilakukan via API, bukan form web.*
*   **Melihat Detail Cuti:**
    *   Klik pada salah satu baris pengajuan cuti di daftar untuk melihat detail lengkapnya.

## 5. Persetujuan Cuti (Umumnya untuk Atasan/HR)

Fitur ini digunakan untuk memproses pengajuan cuti dari karyawan/bawahan.

*   **Melihat Daftar Cuti Pending:**
    *   Akses menu "Cuti" dan filter berdasarkan status "Pending", atau mungkin ada menu khusus "Persetujuan Cuti".
*   **Menyetujui/Menolak Cuti:**
    *   Pada daftar cuti pending, klik tombol "Approve" (Setujui) atau "Reject" (Tolak) pada baris pengajuan yang relevan.
    *   Mungkin ada opsi untuk menambahkan catatan saat menolak.
*   **Operasi Batch:**
    *   Terdapat fitur untuk memilih beberapa pengajuan cuti (via checkbox) dan melakukan persetujuan ("Batch Approve") atau penghapusan ("Batch Delete") secara bersamaan.

## 6. Manajemen Jenis Cuti (Umumnya untuk Admin/HR)

Mengelola tipe-tipe cuti yang bisa diajukan.

*   Akses menu "Jenis Cuti".
*   Terdapat fungsi standar CRUD (Tambah, Lihat, Edit, Hapus) untuk jenis cuti.
*   Saat menambah/mengedit, biasanya mengisi Nama Jenis Cuti dan mungkin properti lain (misal: apakah mengurangi kuota tahunan).

## 7. Manajemen Transportasi (Umumnya untuk Admin/HR)

Mengelola jenis-jenis transportasi yang digunakan untuk perjalanan dinas terkait cuti.

*   Akses menu "Transportasi".
*   Terdapat fungsi standar CRUD (Tambah, Lihat, Edit, Hapus) untuk jenis transportasi (misal: Pesawat, Kereta Api, Mobil Dinas).

## 8. Manajemen Detail Transportasi (User terkait/Admin/HR)

Mencatat detail logistik perjalanan dinas yang terkait dengan pengajuan cuti yang sudah disetujui.

*   **Melihat Daftar Detail Transportasi:**
    *   Akses menu "Detail Transportasi" atau sejenisnya.
    *   Menampilkan daftar perjalanan yang sudah atau akan dilakukan.
*   **Menambah Detail Transportasi:**
    *   Biasanya diakses dari halaman detail Cuti yang sudah disetujui.
    *   Klik tombol "Tambah Detail Transportasi".
    *   Isi formulir: Pilih Jenis Transportasi, Jenis Perjalanan (Pulang/Pergi), Kota Asal/Tujuan, Tanggal, Nomor Tiket/Referensi, Biaya, dll.
    *   Klik "Simpan".
*   **Mengedit/Menghapus Detail Transportasi:**
    *   Pada daftar detail transportasi, klik tombol "Edit" atau "Hapus".
*   **Dashboard Transportasi & Deadline:**
    *   Mungkin ada halaman dashboard khusus (`/transportasi-details/dashboard`) atau bagian di dashboard utama yang menampilkan ringkasan perjalanan dan pengingat deadline (misal: tanggal keberangkatan).

## 9. Manajemen User (Umumnya untuk Admin)

Mengelola akun pengguna yang dapat mengakses sistem.

*   Akses menu "Users" atau "Pengguna".
*   Terdapat fungsi standar CRUD (Tambah, Lihat, Edit, Hapus) untuk akun pengguna.
*   Saat menambah/mengedit, biasanya mengisi Nama, Email, Username, Password, dan memilih Role (Hak Akses).
*   Terdapat juga fitur "Mass Delete" untuk menghapus beberapa user sekaligus.

---

*Panduan ini bersifat umum berdasarkan fitur yang teridentifikasi. Tampilan dan alur kerja spesifik mungkin sedikit berbeda.*
