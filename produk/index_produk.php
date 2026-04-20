<?php
session_start();

// CEK LOGIN
if (!isset($_SESSION['login'])) {
    header("Location: ../login.php");
    exit;
}

// CEK ROLE ADMIN
if ($_SESSION['role'] != 'admin') {
    echo "Akses ditolak!";
    exit;
}

// KONEKSI
require_once '../config/koneksi.php';

// AMBIL DATA PRODUK
$query = mysqli_query($conn, "SELECT * FROM tb_products ORDER BY id_Unique DESC");

$page_title = 'Data Produk';
$active_menu = 'produk';
$is_subfolder = true;
include '../assets/layout_header.php';
?>

<a href="<?= $base_url ?>dashboard.php" class="back-link">
    <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
</a>

<div class="card">
    <div class="card-header">
        <h3><i class="bi bi-box-seam-fill" style="color:var(--accent-2)"></i> Data Produk</h3>
        <a href="tambah_produk.php" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Tambah Produk
        </a>
    </div>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Produk</th>
                    <th>Harga</th>
                    <th>Stok</th>
                    <th>QTY/Karton</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php 
            $no = 1;
            while($row = mysqli_fetch_assoc($query)) { 
            ?>
                <tr>
                    <td><?= $no++; ?></td>
                    <td style="color:var(--text-primary); font-weight:500;"><?= $row['name_product']; ?></td>
                    <td>Rp <?= number_format($row['price'], 0, ',', '.'); ?></td>
                    <td>
                        <span style="padding:4px 10px; border-radius:12px; font-size:12px; font-weight:600;
                            background:<?= $row['stock'] > 0 ? 'var(--success-bg)' : 'var(--danger-bg)' ?>;
                            color:<?= $row['stock'] > 0 ? 'var(--success)' : 'var(--danger)' ?>;">
                            <?= $row['stock']; ?>
                        </span>
                    </td>
                    <td><?= $row['qty_cardboard']; ?></td>
                    <td>
                        <div class="action-btns">
                            <a href="tambah_stok.php?id=<?= $row['id_Unique']; ?>" class="btn btn-success btn-sm">
                                <i class="bi bi-plus"></i> Stok
                            </a>
                            <a href="edit.php?id=<?= $row['id_Unique']; ?>" class="btn btn-warning btn-sm">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <a href="hapus.php?id=<?= $row['id_Unique']; ?>" class="btn btn-danger btn-sm"
                               onclick="return confirm('Yakin ingin hapus?')">
                                <i class="bi bi-trash"></i>
                            </a>
                        </div>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../assets/layout_footer.php'; ?>