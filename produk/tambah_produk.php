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

// PROSES SIMPAN
if (isset($_POST['simpan'])) {
    $id_parent = $_POST['id_parent'];
    $nama      = $_POST['nama'];
    $harga     = $_POST['harga'];
    $stok      = $_POST['stok'];
    $qty_box   = $_POST['qty_box'];
    
    // Upload Gambar
    $image_name = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../assets/img/produk/";
        $extension = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
        $image_name = time() . "_" . bin2hex(random_bytes(4)) . "." . $extension;
        $target_file = $target_dir . $image_name;
        
        move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
    }

    mysqli_query($conn, "
        INSERT INTO tb_products 
        (id_parent, name_product, image, price, stock, qty_cardboard)
        VALUES 
        ('$id_parent', '$nama', '$image_name', '$harga', '$stok', '$qty_box')
    ");

    header("Location: index_produk.php");
}

$page_title = 'Tambah Produk';
$active_menu = 'produk';
$is_subfolder = true;
include '../assets/layout_header.php';
?>

<a href="index_produk.php" class="back-link">
    <i class="bi bi-arrow-left"></i> Kembali ke Data Produk
</a>

<div class="card" style="max-width:600px;">
    <div class="card-header">
        <h3><i class="bi bi-plus-circle" style="color:var(--accent-1)"></i> Tambah Produk Baru</h3>
    </div>

    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label>Foto Produk (Opsional)</label>
            <input type="file" name="image" accept="image/*" style="padding:10px;">
        </div>
        <div class="form-group">
            <label>ID Parent</label>
            <input type="text" name="id_parent" required>
        </div>
        <div class="form-group">
            <label>Nama Produk</label>
            <input type="text" name="nama" required>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Harga</label>
                <input type="number" name="harga" required>
            </div>
            <div class="form-group">
                <label>Stok</label>
                <input type="number" name="stok" required>
            </div>
        </div>
        <div class="form-group">
            <label>Qty per Kardus</label>
            <input type="number" name="qty_box" required>
        </div>

        <button name="simpan" class="btn btn-primary full-width mt-16">
            <i class="bi bi-check-circle"></i> Simpan Produk
        </button>
    </form>
</div>

<?php include '../assets/layout_footer.php'; ?>