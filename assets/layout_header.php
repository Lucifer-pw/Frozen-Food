<?php
// Requires: $page_title (string), $active_menu (string)
// Optional: $hide_sidebar (bool) for auth pages
if (!isset($page_title)) $page_title = 'Frozen Food';
if (!isset($active_menu)) $active_menu = '';
$base_url = '';
if (isset($is_subfolder) && $is_subfolder) $base_url = '../';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?> — FrozenHub</title>
    <link rel="stylesheet" href="<?= $base_url ?>assets/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>

<div class="app-layout">

<!-- SIDEBAR -->
<aside class="sidebar">
    <div class="sidebar-brand">
        <i class="bi bi-snow2"></i>
        <span>FrozenHub</span>
    </div>

    <nav class="sidebar-nav">
        <div class="sidebar-label">Menu Utama</div>
        <a href="<?= $base_url ?>dashboard.php" class="<?= $active_menu == 'dashboard' ? 'active' : '' ?>">
            <i class="bi bi-grid-1x2-fill"></i> Dashboard
        </a>
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') { ?>
        <a href="<?= $base_url ?>order/index_order.php" class="<?= $active_menu == 'order' ? 'active' : '' ?>">
            <i class="bi bi-cart-check-fill"></i> Input Pesanan
        </a>
        <?php } ?>

        <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'user') { ?>
        <div class="sidebar-label">Menu User</div>
        <a href="<?= $base_url ?>user/katalog.php" class="<?= $active_menu == 'katalog' ? 'active' : '' ?>">
            <i class="bi bi-shop"></i> Katalog
        </a>
        <a href="<?= $base_url ?>user/keranjang.php" class="<?= $active_menu == 'keranjang' ? 'active' : '' ?>">
            <i class="bi bi-cart3"></i> Keranjang
        </a>
        <a href="<?= $base_url ?>user/akun_saya.php" class="<?= $active_menu == 'akun' ? 'active' : '' ?>">
            <i class="bi bi-person-circle"></i> Akun Saya
        </a>
        <a href="<?= $base_url ?>user/histori_pembelian.php" class="<?= $active_menu == 'histori' ? 'active' : '' ?>">
            <i class="bi bi-clock-history"></i> Histori
        </a>
        <?php } ?>

        <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') { ?>
        <div class="sidebar-label">Kelola Data</div>
        <a href="<?= $base_url ?>produk/index_produk.php" class="<?= $active_menu == 'produk' ? 'active' : '' ?>">
            <i class="bi bi-box-seam-fill"></i> Produk
        </a>
        <a href="<?= $base_url ?>customer/index_customer.php" class="<?= $active_menu == 'customer' ? 'active' : '' ?>">
            <i class="bi bi-people-fill"></i> Customer
        </a>
        <a href="<?= $base_url ?>shipper/index_shipper.php" class="<?= $active_menu == 'shipper' ? 'active' : '' ?>">
            <i class="bi bi-truck"></i> Shipper
        </a>
        <a href="<?= $base_url ?>transaksi/index_transaksi.php" class="<?= $active_menu == 'transaksi' ? 'active' : '' ?>">
            <i class="bi bi-receipt-cutoff"></i> Transaksi
        </a>
        <a href="<?= $base_url ?>member/index_member.php" class="<?= $active_menu == 'member' ? 'active' : '' ?>">
            <i class="bi bi-person-badge-fill"></i> Member
        </a>
        <?php } ?>
    </nav>

    <div class="sidebar-footer">
        <a href="<?= $base_url ?>logout.php">
            <i class="bi bi-box-arrow-left"></i> Logout
        </a>
    </div>
</aside>

<!-- MAIN CONTENT -->
<div class="main-content">
    <header class="top-header">
        <h1 class="page-title"><?= $page_title ?></h1>
        <div class="user-info">
            <span class="user-badge <?= $_SESSION['role'] ?? 'user' ?>">
                <i class="bi bi-shield-check"></i> <?= $_SESSION['role'] ?? 'user' ?>
            </span>
        </div>
    </header>

    <div class="content-area">
