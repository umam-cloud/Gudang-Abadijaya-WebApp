# Gudang-Abadijaya-WebApp

Aplikasi Web Manajemen Logistik Tabung Gas dengan desain modern *Glassmorphism*. Aplikasi ini dirancang khusus untuk mempermudah pemantauan stok tabung gas, pencatatan otomatis, dan pelacakan aktivitas pelanggan dengan antarmuka yang dinamis, responsif, dan elegan.

## 🌟 Fitur Utama
- **Sistem Manajemen Stok & Matriks Tabung**: Memantau ketersediaan berbagai macam tabung (Oksigen, LPG, dll) baik di gudang utama maupun yang sedang dipinjam oleh mitra.
- **Pencatatan Transaksi & Pengiriman**: Catat keluar-masuk barang secara otomatis.
- **Peringatan Evaluasi Repurchase Otomatis**: Fitur cerdas yang otomatis mendeteksi mitra/pelanggan yang tidak melakukan aktivitas pengisian (*refill*) lebih dari 30 hari.
- **Ekspor Laporan (Excel/XLS)**: Fitur pembuatan laporan log pengiriman dan stok gudang yang langsung dapat di-download dalam format Excel dengan rapi dan header berwarna.
- **Live Search**: Pencarian data mitra/relasi cerdas yang langsung memfilter secara *real-time* tanpa *loading*.
- **Dark Mode & Light Mode**: Desain tema gelap/terang yang bisa disesuaikan, bebas dari isu *flicker* berkat injeksi sistem tema asinkron.
- **Mobile Responsive**: Pengalaman *smooth touch-scrolling* dengan tata letak antarmuka yang aman dibuka di berbagai ukuran layar *smartphone* maupun tablet.

## 🛠️ Persyaratan Sistem
- **PHP 7.4** atau lebih baru (Disarankan PHP 8.x)
- **MySQL / MariaDB**
- Web Server lokal (seperti XAMPP, Laragon, WAMP, dsb)

## 🚀 Cara Instalasi

1. **Clone atau Unduh Proyek**
   Letakkan folder proyek ini di dalam folder `htdocs` (jika menggunakan XAMPP) atau `www` (jika menggunakan Laragon). Ubah nama foldernya menjadi `Gudang-Abadi-WebApp` (atau nama apapun yang Anda inginkan).

2. **Jalankan Apache & MySQL**
   Buka XAMPP/Laragon Control Panel Anda dan tekan **Start** pada modul Apache dan MySQL.

3. **Konfigurasi Database (Opsional)**
   Apabila Anda menggunakan konfigurasi password khusus pada MySQL lokal Anda (secara default biasanya kosong), Anda dapat menyesuaikannya melalui file konfigurasi:
   `app/config/Database.php`

4. **Login Ke Aplikasi**
   Buka URL aplikasi di browser Anda: `http://localhost/Gudang-Abadi-WebApp/`
   Gunakan kredensial *default* berikut untuk masuk:
   - **Username:** `admin`
   - **Password:** `admin123`

## 📂 Struktur Direktori (Native MVC Pattern)

Proyek ini dibangun menggunakan konsep **Model-View-Controller (MVC)** *native* sederhana tanpa *framework*.

```text
Gudang-Abadijaya-WebApp
├── app/
│   ├── config/          # File pengaturan koneksi ke Database
│   ├── controllers/     # Logika bisnis sistem, pengelola *request* & validasi
│   ├── core/            # Sistem routing utama (App, Controller base)
│   ├── models/          # Query, interaksi, dan manipulasi data dari database
│   └── views/           # UI aplikasi yang berinteraksi dengan pengguna (HTML/PHP)
├── public/
│   ├── css/             # Custom CSS stylesheet
│   └── js/              # Interaktivitas JS (Dark Mode handler, Live Search, Modal, dll)
├── README.md            # Dokumentasi proyek (File yang sedang Anda baca)
└── index.php            # Entry point dari aplikasi
```

## 🤝 Kontributor
Aplikasi ini dirancang dan dikembangkan dengan penuh kebanggaan oleh:
- **Fatihul Umam**
- **Fatan Ruhul Alam**

Terima kasih telah mengunjungi repository ini!
