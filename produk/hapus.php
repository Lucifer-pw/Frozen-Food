<?php
session_start();

// CEK LOGIN
if (!isset($_SESSION['login'])) {
    header("Location: ../login.php");
    exit;
}

// CEK ADMIN
if ($_SESSION['role'] != 'admin') {
    echo "Akses ditolak!";
    exit;
}

require_once '../config/koneksi.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Hapus data produk
    $query = mysqli_query($conn, "DELETE FROM tb_products WHERE id_Unique = '$id'");

    if ($query) {
        echo "<script>alert('Produk berhasil dihapus!'); window.location='index_produk.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus produk!'); window.location='index_produk.php';</script>";
    }
} else {
    header("Location: index_produk.php");
}
?>
