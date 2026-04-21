<?php
session_start();
require_once '../config/koneksi.php';

if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'user') {
    header("Location: ../login.php");
    exit;
}

$email_session = $_SESSION['email'];

// Ambil customer ID
$q_cust = mysqli_query($conn, "SELECT id FROM tb_customer WHERE email='$email_session'");
$d_cust = mysqli_fetch_assoc($q_cust);
$customer_id = $d_cust ? $d_cust['id'] : 0;

// Ambil Riwayat Transaksi
$query_history = mysqli_query($conn, "
    SELECT * FROM transactions 
    WHERE customer_id='$customer_id' 
    ORDER BY tanggal DESC, id DESC
");

$page_title = 'Histori Pembelian';
$active_menu = 'histori';
$is_subfolder = true;
include '../assets/layout_header.php';
?>

<a href="<?= $base_url ?>dashboard.php" class="back-link">
    <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
</a>

<div class="card">
    <div class="card-header">
        <h3><i class="bi bi-clock-history" style="color:var(--danger)"></i> Riwayat Pembelian Anda</h3>
    </div>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>No. Invoice</th>
                    <th>Tanggal Order</th>
                    <th>Tanggal Kirim</th>
                    <th>Kurir (Shipper)</th>
                    <th>Detail Barang</th>
                    <th style="text-align:right;">Total Tagihan</th>
                </tr>
            </thead>
            <tbody>
            <?php 
            if (mysqli_num_rows($query_history) == 0) {
                echo "<tr><td colspan='6' style='text-align:center; padding:32px; color:var(--text-secondary);'>Belum ada histori pembelian.</td></tr>";
            }
            
            while ($row = mysqli_fetch_assoc($query_history)) { 
                $transaction_id = $row['id'];
                
                // Ambil detail barang untuk invoice ini
                $q_detail = mysqli_query($conn, "SELECT * FROM transaction_detail WHERE transaction_id='$transaction_id'");
                
                $list_barang = [];
                $total_tagihan = 0;
                while($detail = mysqli_fetch_assoc($q_detail)){
                    $subtotal = ($detail['harga'] * $detail['qty']) - $detail['diskon_rp'];
                    $total_tagihan += $subtotal;
                    $list_barang[] = "{$detail['name_product']} (x{$detail['qty']})";
                }
            ?>
                <tr>
                    <td style="font-weight:600; color:var(--accent-1);">INV-<?= $row['no_invoice'] ?></td>
                    <td><?= date('d M Y', strtotime($row['tanggal'])) ?></td>
                    <td><?= date('d M Y', strtotime($row['tanggal_kirim'])) ?></td>
                    <td><?= $row['shipper'] ?></td>
                    <td style="font-size:13px; color:var(--text-secondary);">
                        <ul style="margin:0; padding-left:16px;">
                            <?php foreach($list_barang as $brg): ?>
                                <li><?= $brg ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </td>
                    <td style="text-align:right; font-weight:700; color:var(--text-primary);">
                        Rp <?= number_format($total_tagihan,0,',','.') ?>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../assets/layout_footer.php'; ?>
