<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

$page_title = 'Dashboard';
$active_menu = 'dashboard';
include 'assets/layout_header.php';
?>

<!-- Welcome Card -->
<div class="welcome-card card">
    <h2><i class="bi bi-snow2"></i> Selamat Datang di FrozenHub!</h2>
    <p>Anda login sebagai <strong><?= strtoupper($_SESSION['role']); ?></strong> — Kelola bisnis frozen food Anda dengan mudah.</p>
</div>

<?php if ($_SESSION['role'] == 'admin') { ?>

<h3 class="mb-24" style="font-size:18px; font-weight:600;">
    <i class="bi bi-grid-3x3-gap-fill" style="color:var(--accent-1);"></i> Menu Admin
</h3>

<div class="menu-grid">
    <a href="order/index_order.php" class="menu-card">
        <div class="icon-wrap blue"><i class="bi bi-cart-check-fill"></i></div>
        <h4>Input Pesanan</h4>
        <p>Buat order baru</p>
    </a>
    <a href="produk/index_produk.php" class="menu-card">
        <div class="icon-wrap purple"><i class="bi bi-box-seam-fill"></i></div>
        <h4>Kelola Produk</h4>
        <p>Tambah, edit & stok</p>
    </a>
    <a href="customer/index_customer.php" class="menu-card">
        <div class="icon-wrap green"><i class="bi bi-people-fill"></i></div>
        <h4>Kelola Customer</h4>
        <p>Data pelanggan</p>
    </a>
    <a href="shipper/index_shipper.php" class="menu-card">
        <div class="icon-wrap yellow"><i class="bi bi-truck"></i></div>
        <h4>Kelola Shipper</h4>
        <p>Data pengiriman</p>
    </a>
    <a href="transaksi/index_transaksi.php" class="menu-card">
        <div class="icon-wrap red"><i class="bi bi-receipt-cutoff"></i></div>
        <h4>Data Transaksi</h4>
        <p>Cetak & kelola invoice</p>
    </a>
</div>

<?php } else { ?>

<h3 class="mb-24" style="font-size:18px; font-weight:600;">
    <i class="bi bi-grid-3x3-gap-fill" style="color:var(--accent-1);"></i> Menu User
</h3>

<div class="menu-grid">
    <a href="order/index_order.php" class="menu-card">
        <div class="icon-wrap blue"><i class="bi bi-cart-check-fill"></i></div>
        <h4>Order Produk</h4>
        <p>Pesan produk frozen food</p>
    </a>
</div>

<?php } ?>

<?php include 'assets/layout_footer.php'; ?>