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
    
    // Handle Image Upload
    $image_name = $data['image']; // default ke gambar lama
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../assets/img/produk/";
        $extension = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
        $new_image_name = time() . "_" . bin2hex(random_bytes(4)) . "." . $extension;
        $target_file = $target_dir . $new_image_name;
        
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            // Hapus gambar lama jika ada
            if ($image_name && file_exists($target_dir . $image_name)) {
                unlink($target_dir . $image_name);
            }
            $image_name = $new_image_name;
        }
    }

    mysqli_query($conn, "
        UPDATE tb_products SET 
        id_parent = '$id_parent',
        name_product = '$nama',
        image = '$image_name',
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

    <form method="POST" enctype="multipart/form-data">
        <div class="form-group" style="text-align:center; margin-bottom:20px;">
            <label style="display:block; text-align:left;">Foto Produk</label>
            <?php if($data['image']): ?>
                <img src="../assets/img/produk/<?= $data['image'] ?>" alt="Produk" style="width:150px; height:150px; object-fit:cover; border-radius:12px; margin-bottom:10px; border:2px solid var(--border-color);">
            <?php else: ?>
                <div style="width:150px; height:150px; background:var(--bg-secondary); border-radius:12px; display:inline-flex; align-items:center; justify-content:center; margin-bottom:10px; border:2px dashed var(--border-color);">
                    <i class="bi bi-image" style="font-size:40px; color:var(--text-muted);"></i>
                </div>
            <?php endif; ?>
            <input type="file" name="image" accept="image/*" style="display:block; margin: 0 auto; padding:10px;">
            <small style="color:var(--text-muted);">Biarkan kosong jika tidak ingin mengubah foto</small>
        </div>

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
