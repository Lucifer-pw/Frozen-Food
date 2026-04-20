<?php
session_start();
require_once '../config/koneksi.php';

if (!isset($_SESSION['login'])) {
    header("Location: ../login.php");
    exit;
}

if (isset($_POST['simpan'])) {
    $no_invoice = $_POST['no_invoice'];
    $customer_id = $_POST['customer_id'];
    $tanggal = $_POST['tanggal'];

    mysqli_query($conn, "
        INSERT INTO transactions (no_invoice, customer_id, tanggal)
        VALUES ('$no_invoice', '$customer_id', '$tanggal')
    ");

    header("Location: index_transaksi.php");
}

$page_title = 'Tambah Transaksi';
$active_menu = 'transaksi';
$is_subfolder = true;
include '../assets/layout_header.php';
?>

<a href="index_transaksi.php" class="back-link">
    <i class="bi bi-arrow-left"></i> Kembali ke Data Transaksi
</a>

<div class="card" style="max-width:500px;">
    <div class="card-header">
        <h3><i class="bi bi-plus-circle" style="color:var(--accent-1)"></i> Tambah Transaksi</h3>
    </div>

    <form method="POST">
        <div class="form-group">
            <label>No Invoice</label>
            <input type="text" name="no_invoice" placeholder="Masukkan no invoice" required>
        </div>
        <div class="form-group">
            <label>ID Customer</label>
            <input type="number" name="customer_id" placeholder="ID Customer" required>
        </div>
        <div class="form-group">
            <label>Tanggal</label>
            <input type="date" name="tanggal" required>
        </div>

        <button name="simpan" class="btn btn-primary full-width mt-16">
            <i class="bi bi-check-circle"></i> Simpan
        </button>
    </form>
</div>

<?php include '../assets/layout_footer.php'; ?>