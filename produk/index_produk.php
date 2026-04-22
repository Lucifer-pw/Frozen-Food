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

// SEARCH KEYWORD
$keyword = $_GET['search'] ?? '';

// AMBIL DATA PRODUK
$query_sql = "SELECT * FROM tb_products";
if (!empty($keyword)) {
    $query_sql .= " WHERE name_product LIKE '%$keyword%' OR id_parent LIKE '%$keyword%'";
}
$query_sql .= " ORDER BY id_Unique DESC";

$query = mysqli_query($conn, $query_sql);

$page_title = 'Data Produk';
$active_menu = 'produk';
$is_subfolder = true;
include '../assets/layout_header.php';
?>

<a href="<?= $base_url ?>dashboard.php" class="back-link">
    <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
</a>

<div class="card">
    <div class="card-header" style="display:flex; justify-content:space-between; align-items:center;">
        <h3 style="margin:0;"><i class="bi bi-box-seam-fill" style="color:var(--accent-2)"></i> Data Produk</h3>
        <div style="display:flex; gap:12px;">
            <!-- Form Pencarian -->
            <form method="GET" style="display:flex; gap:8px;">
                <input type="text" name="search" placeholder="Cari nama atau ID Parent..." value="<?= htmlspecialchars($keyword) ?>" 
                       style="padding:6px 12px; border-radius:8px; border:1px solid var(--border-color); background:var(--bg-secondary); color:var(--text-primary); outline:none; font-size:14px; width:220px;">
                <button type="submit" class="btn btn-outline btn-sm">
                    <i class="bi bi-search"></i>
                </button>
            </form>
            <a href="tambah_produk.php" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Tambah Produk
            </a>
        </div>
    </div>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Foto</th>
                    <th>ID Parent</th>
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
                    <td>
                        <?php if($row['image']): ?>
                            <img src="../assets/img/produk/<?= $row['image'] ?>" alt="Produk" style="width:45px; height:45px; object-fit:cover; border-radius:8px; border:1px solid var(--border-color);">
                        <?php else: ?>
                            <div style="width:45px; height:45px; background:var(--bg-secondary); border-radius:8px; display:flex; align-items:center; justify-content:center; border:1px dashed var(--border-color);">
                                <i class="bi bi-image" style="color:var(--text-muted); font-size:12px;"></i>
                            </div>
                        <?php endif; ?>
                    </td>
                    <td><code style="background:var(--info-bg); color:var(--accent-1); padding:2px 8px; border-radius:4px; font-size:12px;"><?= $row['id_parent']; ?></code></td>
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