<?php
include 'config/koneksi.php';

echo "Memulai update database...\n";

// 1. Tambah kolom jenis_kelamin ke tb_customer
$q1 = mysqli_query($conn, "SHOW COLUMNS FROM tb_customer LIKE 'jenis_kelamin'");
if(mysqli_num_rows($q1) == 0) {
    mysqli_query($conn, "ALTER TABLE tb_customer ADD jenis_kelamin VARCHAR(15) NULL AFTER phone");
    echo "Kolom jenis_kelamin berhasil ditambahkan ke tb_customer.\n";
} else {
    echo "Kolom jenis_kelamin sudah ada.\n";
}

// 2. Buat tabel tb_cart
$q2 = mysqli_query($conn, "
    CREATE TABLE IF NOT EXISTS tb_cart (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        user_id INT(11) NOT NULL,
        product_id INT(11) NOT NULL,
        qty INT(11) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )
");

if($q2) {
    echo "Tabel tb_cart berhasil dibuat / sudah ada.\n";
} else {
    echo "Gagal membuat tabel tb_cart: " . mysqli_error($conn) . "\n";
}

echo "Selesai.\n";
?>
