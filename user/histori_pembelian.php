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
                    <th>Item Belanja</th>
                    <th>Kurir (Shipper)</th>
                    <th>Delivery</th>
                    <th>Payment</th>
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
                    <td style="font-size:13px;">
                        <ul style="margin:0; padding-left:16px; color:var(--text-secondary);">
                            <?php foreach($list_barang as $brg): ?>
                                <li><?= $brg ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </td>
                    <td>
                        <div style="font-weight:500;"><?= $row['shipper'] ?: '-' ?></div>
                        <div style="font-size:12px; color:var(--text-muted);">
                            <?php if($row['tanggal_kirim'] != '0000-00-00' && $row['tanggal_kirim']): ?>
                                <i class="bi bi-calendar-event"></i> <?= date('d M Y', strtotime($row['tanggal_kirim'])) ?>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td>
                        <?php if ($row['status'] == 'Delivered'): ?>
                            <span style="padding:4px 8px; border-radius:12px; font-size:11px; font-weight:600; background:var(--success-bg); color:var(--success);">
                                Delivered
                            </span>
                        <?php else: ?>
                            <span style="padding:4px 8px; border-radius:12px; font-size:11px; font-weight:600; background:var(--danger-bg); color:var(--danger);">
                                Processing
                            </span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($row['payment_status'] == 'Paid'): ?>
                            <span style="padding:4px 8px; border-radius:12px; font-size:11px; font-weight:600; background:rgba(16, 185, 129, 0.1); color:#10b981;">
                                Paid
                            </span>
                            <div style="font-size:11px; color:var(--text-muted); margin-top:4px;">
                                <i class="bi bi-clock"></i> <?= date('d M Y', strtotime($row['tanggal_paid'])) ?>
                            </div>
                        <?php else: ?>
                            <span style="padding:4px 8px; border-radius:12px; font-size:11px; font-weight:600; background:rgba(239, 68, 68, 0.1); color:#ef4444;">
                                Unpaid
                            </span>
                        <?php endif; ?>
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
