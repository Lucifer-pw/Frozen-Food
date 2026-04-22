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

// AMBIL DATA BERDASARKAN ID
if (!isset($_GET['id'])) {
    header("Location: index_produk.php");
    exit;
}

$id = $_GET['id'];
$query = mysqli_query($conn, "SELECT * FROM tb_products WHERE id_Unique = '$id'");
$data = mysqli_fetch_assoc($query);

if (!$data) {
    echo "Data tidak ditemukan!";
    exit;
}

// PROSES UPDATE
if (isset($_POST['update'])) {
    $id_parent = $_POST['id_parent'];
    $nama      = $_POST['nama'];
    $harga     = $_POST['harga'];
    $stok      = $_POST['stok'];
    $qty_box   = $_POST['qty_box'];

    mysqli_query($conn, "
        UPDATE tb_products SET 
        id_parent = '$id_parent',
        name_product = '$nama',
        price = '$harga',
        stock = '$stok',
        qty_cardboard = '$qty_box'
        WHERE id_Unique = '$id'
    ");

    echo "<script>alert('Produk berhasil diupdate!'); window.location='index_produk.php';</script>";
}

$page_title = 'Edit Produk';
$active_menu = 'produk';
$is_subfolder = true;
include '../assets/layout_header.php';
?>

<a href="index_produk.php" class="back-link">
    <i class="bi bi-arrow-left"></i> Kembali ke Data Produk
</a>

<div class="card" style="max-width:600px;">
    <div class="card-header">
        <h3><i class="bi bi-pencil-square" style="color:var(--warning)"></i> Edit Produk</h3>
    </div>

    <form method="POST">
        <div class="form-group">
            <label>ID Parent</label>
            <input type="text" name="id_parent" value="<?= $data['id_parent'] ?>" required>
        </div>
        <div class="form-group">
            <label>Nama Produk</label>
            <input type="text" name="nama" value="<?= $data['name_product'] ?>" required>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Harga</label>
                <input type="number" name="harga" value="<?= $data['price'] ?>" required>
            </div>
            <div class="form-group">
                <label>Stok</label>
                <input type="number" name="stok" value="<?= $data['stock'] ?>" required>
            </div>
        </div>
        <div class="form-group">
            <label>Qty per Kardus</label>
            <input type="number" name="qty_box" value="<?= $data['qty_cardboard'] ?>" required>
        </div>

        <button name="update" class="btn btn-primary full-width mt-16" style="background:var(--warning); color:#0f172a; border:none;">
            <i class="bi bi-save"></i> Update Produk
        </button>
    </form>
</div>

<?php include '../assets/layout_footer.php'; ?>
