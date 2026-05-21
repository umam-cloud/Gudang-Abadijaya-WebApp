# TabungFlow (Joki Web)
Aplikasi Web Manajemen Logistik Tabung Gas dengan fitur matriks stok, pencatatan otomatis, dan peringatan evaluasi *repurchase* (>30 hari). Dibuat menggunakan Native PHP (MVC Pattern) dan CSS Glassmorphism.

## Cara Menggunakan / Menjalankan Proyek Ini

Proyek ini dirancang agar sangat mudah dijalankan oleh siapa saja di komputer lokal mereka (menggunakan XAMPP, Laragon, dsb).

### Persyaratan Sistem
- PHP 7.4 atau lebih baru
- MySQL / MariaDB (biasanya sudah termasuk di XAMPP)

### Langkah-Langkah Instalasi

1. **Clone atau Unduh Proyek**
   Letakkan folder proyek ini di dalam folder `htdocs` (jika menggunakan XAMPP) atau `www` (jika menggunakan Laragon/WAMP). Pastikan nama foldernya sesuai (misal: `joki` atau `Gudang-Abadi-WebApp`).

2. **Jalankan Apache & MySQL**
   Buka XAMPP Control Panel dan tekan **Start** pada Apache dan MySQL.

3. **Buat Database Baru & Impor SQL**
   - Buka `http://localhost/phpmyadmin` di browser Anda.
   - Buat database baru dengan nama `joki_tabung`.
   - Pilih database tersebut, buka tab **Import**.
   - Upload file `joki_tabung_full.sql` yang ada di dalam folder proyek ini, lalu klik **Go** / **Import**.
   - *(File ini sudah memuat seluruh struktur tabel, data dummy uji coba, beserta akun admin bawaan)*.

4. **Login dan Gunakan Aplikasi**
   Buka halaman utama:
   ```
   http://localhost/joki/
   ```
   Silakan login menggunakan kredensial bawaan berikut:
   - **Username**: `admin`
   - **Password**: `admin123`

---
### Pengaturan Lanjutan (Opsional)
Jika Anda menggunakan password khusus untuk MySQL "root", Anda dapat mengubah konfigurasi koneksi pada file `app/config/Database.php` (ubah bagian `$password`). Secara default, password pada XAMPP dibiarkan kosong (`''`).