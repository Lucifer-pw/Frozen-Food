<?php
session_start();
require_once '../config/koneksi.php';

if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'user') {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// PROSES TAMBAH KERANJANG
if (isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];
    $qty = (int) $_POST['qty'];

    // Cek stok produk
    $q_produk = mysqli_query($conn, "SELECT stock FROM tb_products WHERE id_Unique='$product_id'");
    $d_produk = mysqli_fetch_assoc($q_produk);

    if ($qty > $d_produk['stock']) {
        echo "<script>alert('Stok tidak mencukupi!');</script>";
    } else {
        // Cek apakah produk sudah ada di keranjang
        $cek_cart = mysqli_query($conn, "SELECT id, qty FROM tb_cart WHERE user_id='$user_id' AND product_id='$product_id'");
        if (mysqli_num_rows($cek_cart) > 0) {
            $d_cart = mysqli_fetch_assoc($cek_cart);
            $new_qty = $d_cart['qty'] + $qty;
            if ($new_qty > $d_produk['stock']) {
                echo "<script>alert('Gagal! Total qty di keranjang melebihi stok.');</script>";
            } else {
                mysqli_query($conn, "UPDATE tb_cart SET qty='$new_qty' WHERE id='{$d_cart['id']}'");
                echo "<script>alert('Kuantitas produk di keranjang diperbarui!');</script>";
            }
        } else {
            mysqli_query($conn, "INSERT INTO tb_cart (user_id, product_id, qty) VALUES ('$user_id', '$product_id', '$qty')");
            echo "<script>alert('Produk ditambahkan ke keranjang!');</script>";
        }
    }
}

$page_title = 'Katalog Produk';
$active_menu = 'katalog';
$is_subfolder = true;
include '../assets/layout_header.php';
?>

<a href="<?= $base_url ?>dashboard.php" class="back-link">
    <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
</a>

<div class="card mb-24">
    <div class="card-header" style="display:flex; justify-content:space-between; align-items:center;">
        <h3 style="margin:0;"><i class="bi bi-shop" style="color:var(--accent-2)"></i> Katalog Frozen Food</h3>
        <a href="keranjang.php" class="btn btn-primary btn-sm">
            <i class="bi bi-cart3"></i> Lihat Keranjang
        </a>
    </div>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Nama Produk</th>
                    <th>Harga</th>
                    <th>Stok Tersedia</th>
                    <th style="width:200px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $produk = mysqli_query($conn, "SELECT * FROM tb_products WHERE stock > 0 ORDER BY name_product ASC");
            while ($p = mysqli_fetch_assoc($produk)) {
            ?>
                <tr>
                    <td style="font-weight:600; color:var(--text-primary);"><?= $p['name_product'] ?></td>
                    <td style="color:var(--success);">Rp <?= number_format($p['price'],0,',','.') ?></td>
                    <td><?= $p['stock'] ?></td>
                    <td>
                        <form method="POST" style="display:flex; gap:8px;">
                            <input type="hidden" name="product_id" value="<?= $p['id_Unique'] ?>">
                            <input type="number" name="qty" value="1" min="1" max="<?= $p['stock'] ?>" required style="width:70px; padding:6px; border-radius:6px; border:1px solid var(--border-color); background:var(--bg-secondary); color:var(--text-primary);">
                            <button type="submit" name="add_to_cart" class="btn btn-primary btn-sm" style="flex:1;">
                                + Keranjang
                            </button>
                        </form>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../assets/layout_footer.php'; ?>
