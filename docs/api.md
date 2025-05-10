# Dokumentasi API - Sistem Cuti Karyawan

Dokumen ini menjelaskan Application Programming Interface (API) yang disediakan oleh Sistem Cuti Karyawan. API ini memungkinkan interaksi programatik dengan sistem, misalnya untuk integrasi dengan aplikasi lain atau untuk frontend modern (jika digunakan).

## Base URL

Semua URL API diawali dengan prefix `/api`. Jika aplikasi berjalan di `http://localhost:8000`, maka base URL API adalah `http://localhost:8000/api`.

## Autentikasi

Sebagian besar endpoint API memerlukan autentikasi menggunakan **Laravel Sanctum**. Klien yang memanggil API ini harus menyertakan token API yang valid dalam header `Authorization` sebagai Bearer token.

```
Authorization: Bearer <your-api-token>
```

Endpoint yang tidak memerlukan autentikasi akan ditandai secara eksplisit.

## Endpoints

### 1. Karyawan

#### Pencarian Karyawan

*   **Method:** `GET`
*   **URL:** `/karyawans/search`
*   **Autentikasi:** Tidak diperlukan.
*   **Query Parameters:**
    *   `term` (string, optional): Kata kunci untuk pencarian (misal: nama atau NIK).
*   **Deskripsi:** Mencari data karyawan berdasarkan kata kunci. Berguna untuk fitur autocomplete atau pencarian cepat.
*   **Contoh Response Sukses (200 OK):**
    ```json
    [
        {
            "id": 1,
            "nik": "12345",
            "nama": "Budi Santoso",
            "email": "budi.santoso@example.com",
            // ... field lain yang relevan
        },
        {
            "id": 5,
            "nik": "56789",
            "nama": "Budi Hartono",
            "email": "budi.hartono@example.com",
            // ...
        }
    ]
    ```

### 2. Cuti

Endpoint ini digunakan untuk mengelola data pengajuan cuti.

#### Mendapatkan Daftar Cuti

*   **Method:** `GET`
*   **URL:** `/cutis`
*   **Autentikasi:** Diperlukan (Sanctum).
*   **Query Parameters:** (Opsional, tergantung implementasi Controller)
    *   `karyawan_id` (int): Filter berdasarkan ID karyawan.
    *   `status` (string): Filter berdasarkan status (misal: `pending`, `approved`, `rejected`).
    *   `page` (int): Untuk pagination.
*   **Deskripsi:** Mengambil daftar pengajuan cuti.
*   **Contoh Response Sukses (200 OK):**
    ```json
    {
        "data": [
            {
                "id": 10,
                "karyawan_id": 1,
                "jenis_cuti_id": 1,
                "tanggal_mulai": "2025-05-01",
                "tanggal_selesai": "2025-05-03",
                "keterangan": "Cuti tahunan",
                "status": "approved",
                // ... field lain
            }
            // ... cuti lainnya
        ],
        "links": { /* ... pagination links ... */ },
        "meta": { /* ... pagination meta ... */ }
    }
    ```

#### Membuat Pengajuan Cuti Baru

*   **Method:** `POST`
*   **URL:** `/cutis`
*   **Autentikasi:** Diperlukan (Sanctum).
*   **Request Body (JSON):**
    ```json
    {
        "karyawan_id": 1, // ID Karyawan yang mengajukan (atau diambil dari user terautentikasi)
        "jenis_cuti_id": 1,
        "tanggal_mulai": "2025-06-10",
        "tanggal_selesai": "2025-06-11",
        "keterangan": "Keperluan keluarga"
        // ... field lain yang diperlukan
    }
    ```
*   **Deskripsi:** Membuat pengajuan cuti baru.
*   **Contoh Response Sukses (201 Created):**
    ```json
    {
        "id": 11,
        "karyawan_id": 1,
        "jenis_cuti_id": 1,
        "tanggal_mulai": "2025-06-10",
        "tanggal_selesai": "2025-06-11",
        "keterangan": "Keperluan keluarga",
        "status": "pending",
        // ... field lain
    }
    ```
*   **Response Error (422 Unprocessable Entity):** Jika validasi gagal.

#### Mendapatkan Detail Cuti

*   **Method:** `GET`
*   **URL:** `/cutis/{cuti}` (ganti `{cuti}` dengan ID cuti)
*   **Autentikasi:** Diperlukan (Sanctum).
*   **Deskripsi:** Mengambil detail satu pengajuan cuti.
*   **Contoh Response Sukses (200 OK):**
    ```json
    {
        "id": 10,
        "karyawan_id": 1,
        "jenis_cuti_id": 1,
        "tanggal_mulai": "2025-05-01",
        "tanggal_selesai": "2025-05-03",
        "keterangan": "Cuti tahunan",
        "status": "approved",
        // ... field lain, mungkin termasuk relasi seperti karyawan, jenis_cuti
    }
    ```
*   **Response Error (404 Not Found):** Jika ID cuti tidak ditemukan.

#### Memperbarui Pengajuan Cuti

*   **Method:** `PUT` atau `PATCH`
*   **URL:** `/cutis/{cuti}` (ganti `{cuti}` dengan ID cuti)
*   **Autentikasi:** Diperlukan (Sanctum).
*   **Request Body (JSON):** (Hanya field yang ingin diubah)
    ```json
    {
        "keterangan": "Perubahan rencana cuti tahunan"
        // ... field lain yang bisa diubah
    }
    ```
*   **Deskripsi:** Memperbarui data pengajuan cuti yang sudah ada. Biasanya hanya bisa dilakukan jika status masih `pending`.
*   **Contoh Response Sukses (200 OK):**
    ```json
    {
        "id": 10,
        "karyawan_id": 1,
        // ... data cuti yang sudah diperbarui
        "keterangan": "Perubahan rencana cuti tahunan",
        // ...
    }
    ```
*   **Response Error (404 Not Found, 422 Unprocessable Entity, 403 Forbidden):** Tergantung kasus.

#### Menghapus Pengajuan Cuti

*   **Method:** `DELETE`
*   **URL:** `/cutis/{cuti}` (ganti `{cuti}` dengan ID cuti)
*   **Autentikasi:** Diperlukan (Sanctum).
*   **Deskripsi:** Menghapus pengajuan cuti. Biasanya hanya bisa dilakukan jika status masih `pending`.
*   **Contoh Response Sukses (204 No Content):** Tidak ada body response.
*   **Response Error (404 Not Found, 403 Forbidden):** Tergantung kasus.

### 3. User

#### Mendapatkan User Terautentikasi

*   **Method:** `GET`
*   **URL:** `/user`
*   **Autentikasi:** Diperlukan (Sanctum).
*   **Deskripsi:** Mengambil informasi detail dari pengguna yang sedang terautentikasi berdasarkan token yang diberikan.
*   **Contoh Response Sukses (200 OK):**
    ```json
    {
        "id": 1,
        "name": "Nama User",
        "email": "user@example.com",
        "username": "namauser",
        "email_verified_at": "2025-04-25T10:00:00.000000Z",
        // ... field user lain, mungkin termasuk role atau data karyawan terkait
    }
    ```

---

*Catatan: Detail response (field yang dikembalikan) dan parameter query mungkin bervariasi tergantung implementasi spesifik di Controller.*
