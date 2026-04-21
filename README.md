## 👤 Identitas Mahasiswa
- Nama: ARSANDY VIDYA GUPTA PRADANA
- NIM: 220103050
- **KELAS : 22TIA2
- Dosen Pengampu: Sopingi, M.Kom.

---

## 🏗️ Penjelasan Tema Kasus
Aplikasi **FrozenHub** adalah sistem manajemen operasional yang dirancang khusus untuk bisnis distribusi makanan beku (*frozen food*). Proyek ini menangani tantangan utama dalam industri *frozen food*, yaitu:
1. **Manajemen Inventaris Kompleks**: Mengelola stok barang yang memiliki unit berbeda (kardus vs pcs) dengan grouping produk yang sistematis.
2. **Digitalisasi Pemesanan**: Menyediakan portal bagi pelanggan (User) untuk melakukan pemesanan secara mandiri yang terintegrasi langsung dengan database admin.
3. **Efisiensi Administrasi**: Otomatisasi pembuatan nomor invoice, perhitungan diskon bertingkat, serta penyajian laporan transaksi dalam format PDF Landscape yang siap cetak untuk printer standar kantor.

---

## 🚀 Fitur Utama

### 🔐 Authentication & Roles
- **Admin**: Manajemen produk, customer, shipper, transaksi (diskon, status pengiriman), dan cetak invoice.
- **User (Customer)**: Katalog produk, keranjang belanja, histori pembelian, dan manajemen profil.

### 📦 Manajemen Inventaris
- Pemantauan stok real-time.
- Grouping produk (Parent-Child).
- Input stok masuk.

### 🛒 E-commerce (User Side)
- Sinkronisasi otomatis data User dengan data Pelanggan.
- Checkout otomatis dengan nomor invoice unik.
- Histori transaksi mandiri.

### 📄 Laporan & Invoice
- Export Invoice ke PDF (Format A4 Landscape).
- Penyimpanan otomatis salinan PDF ke server.
- Update status pengiriman (Undelivered/Delivered).
- Pemberian diskon (%) manual oleh Admin.

## 📂 Struktur Proyek

```
frozen_food_web/
├── assets/              # Resource pendukung (CSS & Layout)
│   ├── layout_header.php
│   ├── layout_footer.php
│   └── style.css
├── config/              # Konfigurasi Sistem
│   └── koneksi.php      # Koneksi Database
├── database/            # Database & Utilities (SQL & Migration)
│   ├── dbfrozen_food.sql
│   ├── db_inspect.php
│   └── update_db.php
├── customer/            # Manajemen Data Pelanggan (Admin)
├── produk/              # Manajemen Inventaris & Barang (Admin)
├── order/               # Proses Input Order Manual (Admin)
├── shipper/             # Manajemen Kurir/Ekspedisi (Admin)
├── transaksi/           # Cetak Invoice & Update Status (Admin)
├── user/                # Fitur Khusus Pelanggan/User
│   ├── katalog.php
│   ├── keranjang.php
│   ├── akun_saya.php
│   └── histori_pembelian.php
├── PDF/                 # Folder Penyimpanan File Invoice PDF
├── dashboard.php        # Dashboard Utama
├── login.php            # Halaman Login
├── register.php         # Halaman Registrasi
├── logout.php           # Proses Logout
├── proses_login.php
└── proses_register.php
```

## 🛠️ Instalasi

### Setup Database
1. Import file `database/dbfrozen_food.sql` ke MySQL.
2. Sesuaikan konfigurasi database di `config/koneksi.php`.

### Akses Default
- **Admin**: `admin` / `admin123`
- **User**: (Daftar melalui halaman Register)

## 🎨 Teknologi
- **Backend**: PHP Native
- **Database**: MySQL
- **Frontend**: Vanilla CSS & Bootstrap Icons
- **PDF Engine**: html2pdf.js

---
*Dikembangkan untuk efisiensi operasional FrozenHub.*
