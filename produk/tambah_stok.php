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

// AMBIL ID PRODUK
$id = $_GET['id'];

// AMBIL DATA PRODUK
$data = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT * FROM tb_products WHERE id_Unique='$id'
"));

// PROSES TAMBAH STOK
if (isset($_POST['simpan'])) {
    $tambah = $_POST['tambah_stok'];

    // stok lama
    $stok_lama = $data['stock'];

    // hitung stok baru
    $stok_baru = $stok_lama + $tambah;

    mysqli_query($conn, "
        UPDATE tb_products 
        SET stock='$stok_baru'
        WHERE id_Unique='$id'
    ");

    header("Location: index_produk.php");
}

$page_title = 'Tambah Stok';
$active_menu = 'produk';
$is_subfolder = true;
include '../assets/layout_header.php';
?>

<a href="index_produk.php" class="back-link">
    <i class="bi bi-arrow-left"></i> Kembali ke Data Produk
</a>

<div class="card" style="max-width:500px;">
    <div class="card-header">
        <h3><i class="bi bi-plus-circle" style="color:var(--success)"></i> Tambah Stok</h3>
    </div>

    <div class="info-box info">
        <i class="bi bi-box-seam"></i>
        <div>
            <strong><?= $data['name_product']; ?></strong><br>
            <span style="font-size:13px;">Stok saat ini: <strong><?= $data['stock']; ?></strong></span>
        </div>
    </div>

    <form method="POST">
        <div class="form-group">
            <label>Jumlah Tambah Stok</label>
            <input type="number" name="tambah_stok" required min="1" placeholder="Masukkan jumlah...">
        </div>

        <button name="simpan" class="btn btn-success full-width mt-16">
            <i class="bi bi-check-circle"></i> Simpan
        </button>
    </form>
</div>

<?php include '../assets/layout_footer.php'; ?>