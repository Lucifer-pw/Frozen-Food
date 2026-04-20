<?php
session_start();
require_once '../config/koneksi.php';

if ($_SESSION['role'] != 'admin') {
    echo "Akses ditolak!";
    exit;
}

$data_header = null;
$data_detail = null;
$grand_total = 0;
$mode = ''; // 'search' or 'print'

// =====================
// STEP 1: CARI INVOICE
// =====================
if (isset($_POST['cari'])) {

    $no_invoice = $_POST['no_invoice'] ?? '';
    $mode = 'search';

    // ambil header invoice
    $query_header = mysqli_query($conn, "
        SELECT t.*, c.nama_toko, c.alamat, c.provinsi, c.negara
        FROM transactions t
        JOIN tb_customer c ON t.customer_id = c.id
        WHERE t.no_invoice='$no_invoice'
        LIMIT 1
    ");

    $data_header = mysqli_fetch_assoc($query_header);

    if ($data_header) {
        // ambil detail transaksi
        $data_detail = mysqli_query($conn, "
            SELECT * FROM transaction_detail
            WHERE transaction_id='{$data_header['id']}'
        ");
    }
}

// =====================
// STEP 2: UPDATE & CETAK
// =====================
if (isset($_POST['cetak'])) {

    $no_invoice    = $_POST['no_invoice'];
    $tanggal_kirim = $_POST['tanggal_kirim'];
    $shipper       = $_POST['shipper'];
    $mode = 'print';

    // update tanggal kirim dan shipper
    mysqli_query($conn, "
        UPDATE transactions 
        SET tanggal_kirim='$tanggal_kirim', shipper='$shipper'
        WHERE no_invoice='$no_invoice'
    ");

    // ambil header invoice (sudah di-update)
    $query_header = mysqli_query($conn, "
        SELECT t.*, c.nama_toko, c.alamat, c.provinsi, c.negara
        FROM transactions t
        JOIN tb_customer c ON t.customer_id = c.id
        WHERE t.no_invoice='$no_invoice'
        LIMIT 1
    ");

    $data_header = mysqli_fetch_assoc($query_header);

    if ($data_header) {
        $data_detail = mysqli_query($conn, "
            SELECT * FROM transaction_detail
            WHERE transaction_id='{$data_header['id']}'
        ");
    }
}

// Ambil daftar shipper untuk dropdown
$shipper_list = mysqli_query($conn, "SELECT * FROM tb_shipper ORDER BY nama_shipper ASC");

// Ambil daftar semua transaksi untuk preview
$all_transactions = mysqli_query($conn, "
    SELECT t.no_invoice, t.tanggal, t.tanggal_kirim, t.shipper, c.nama_toko
    FROM transactions t
    LEFT JOIN tb_customer c ON t.customer_id = c.id
    ORDER BY t.id DESC
");

$page_title = 'Cetak Invoice';
$active_menu = 'transaksi';
$is_subfolder = true;
include '../assets/layout_header.php';
?>

<a href="<?= $base_url ?>dashboard.php" class="back-link">
    <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
</a>

<!-- ======================== -->
<!-- DAFTAR TRANSAKSI (scrollable) -->
<!-- ======================== -->
<?php if ($mode !== 'print'): ?>
<div class="card mb-24">
    <div class="card-header">
        <h3><i class="bi bi-list-ul" style="color:var(--accent-2)"></i> Daftar Transaksi</h3>
        <span class="text-muted" style="font-size:13px;">Klik nomor invoice untuk mencari</span>
    </div>
    <div class="table-wrapper" style="max-height: 300px; overflow-y: auto;">
        <table>
            <thead style="position:sticky; top:0; z-index:1;">
                <tr>
                    <th>No Invoice</th>
                    <th>Customer</th>
                    <th>Tanggal</th>
                    <th>Tgl Kirim</th>
                    <th>Shipper</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
            <?php while($t = mysqli_fetch_assoc($all_transactions)) { 
                $has_shipping = !empty($t['tanggal_kirim']) && !empty($t['shipper']);
            ?>
                <tr>
                    <td>
                        <a href="#" onclick="isiInvoice('<?= $t['no_invoice'] ?>')" 
                           style="color:var(--accent-1); font-weight:600; cursor:pointer;">
                            <?= $t['no_invoice'] ?>
                        </a>
                    </td>
                    <td style="color:var(--text-primary);"><?= $t['nama_toko'] ?? '-' ?></td>
                    <td><?= $t['tanggal'] ?></td>
                    <td><?= $t['tanggal_kirim'] ?: '-' ?></td>
                    <td><?= $t['shipper'] ?: '-' ?></td>
                    <td>
                        <?php if ($has_shipping): ?>
                            <span style="padding:4px 10px; border-radius:12px; font-size:11px; font-weight:600; background:var(--success-bg); color:var(--success);">
                                <i class="bi bi-check-circle"></i> Lengkap
                            </span>
                        <?php else: ?>
                            <span style="padding:4px 10px; border-radius:12px; font-size:11px; font-weight:600; background:var(--warning-bg); color:var(--warning);">
                                <i class="bi bi-clock"></i> Pending
                            </span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function isiInvoice(noInvoice) {
    document.getElementById('input-invoice').value = noInvoice;
    document.getElementById('input-invoice').focus();
}
</script>
<?php endif; ?>

<!-- ======================== -->
<!-- STEP 1: FORM CARI INVOICE -->
<!-- ======================== -->
<?php if ($mode !== 'print'): ?>
<div class="card mb-24">
    <div class="card-header">
        <h3><i class="bi bi-search" style="color:var(--accent-1)"></i> Cari Invoice</h3>
    </div>
    <form method="POST">
        <div class="search-bar" style="margin-bottom:0;">
            <i class="bi bi-receipt" style="color:var(--text-muted);font-size:18px;"></i>
            <input type="text" name="no_invoice" id="input-invoice" required placeholder="Masukkan nomor invoice..." 
                   value="<?= $data_header['no_invoice'] ?? '' ?>">
            <button type="submit" name="cari" class="btn btn-primary">
                <i class="bi bi-search"></i> Cari
            </button>
        </div>
    </form>
</div>
<?php endif; ?>

<!-- ======================== -->
<!-- STEP 2: DATA DITEMUKAN — ISI TANGGAL KIRIM & SHIPPER -->
<!-- ======================== -->
<?php if ($mode === 'search' && $data_header && $data_detail): ?>

<div class="card mb-24">
    <div class="card-header">
        <h3><i class="bi bi-receipt-cutoff" style="color:var(--accent-2)"></i> Detail Invoice #<?= $data_header['no_invoice'] ?></h3>
    </div>

    <!-- Info Invoice -->
    <div class="detail-grid mb-24">
        <div class="detail-item">
            <span class="label">No Invoice</span>
            <span class="value"><?= $data_header['no_invoice'] ?></span>
        </div>
        <div class="detail-item">
            <span class="label">Customer / Toko</span>
            <span class="value"><?= $data_header['nama_toko'] ?></span>
        </div>
        <div class="detail-item">
            <span class="label">Alamat</span>
            <span class="value"><?= $data_header['alamat'] ?>, <?= $data_header['provinsi'] ?>, <?= $data_header['negara'] ?></span>
        </div>
        <div class="detail-item">
            <span class="label">Tanggal Order</span>
            <span class="value"><?= $data_header['tanggal'] ?></span>
        </div>
    </div>

    <!-- Tabel Detail Produk -->
    <h4 style="font-weight:600; margin-bottom:12px;">
        <i class="bi bi-box-seam" style="color:var(--accent-1)"></i> Detail Produk
    </h4>
    <div class="table-wrapper mb-24">
        <table>
            <thead>
                <tr>
                    <th>Nama Barang</th>
                    <th>Qty</th>
                    <th>Harga</th>
                    <th>Total</th>
                    <th>%</th>
                    <th>Diskon (Rp)</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
            <?php while($d = mysqli_fetch_assoc($data_detail)) { 
                $total = $d['harga'] * $d['qty'];
                $diskon = $d['diskon_rp'];
                $subtotal = $total - $diskon;
                $grand_total += $subtotal;
            ?>
                <tr>
                    <td style="color:var(--text-primary); font-weight:500;"><?= $d['name_product'] ?></td>
                    <td><?= $d['qty'] ?></td>
                    <td>Rp <?= number_format($d['harga'],0,',','.') ?></td>
                    <td>Rp <?= number_format($total,0,',','.') ?></td>
                    <td><?= $d['persen_diskon'] ?>%</td>
                    <td>Rp <?= number_format($diskon,0,',','.') ?></td>
                    <td style="color:var(--text-primary); font-weight:600;">Rp <?= number_format($subtotal,0,',','.') ?></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>

    <div class="grand-total mb-24">
        GRAND TOTAL: <span>Rp <?= number_format($grand_total,0,',','.') ?></span>
    </div>

    <!-- Form Isi Tanggal Kirim & Shipper -->
    <hr style="border-color:var(--border-color); margin: 24px 0;">

    <h4 style="font-weight:600; margin-bottom:16px;">
        <i class="bi bi-truck" style="color:var(--warning)"></i> Lengkapi Data Pengiriman
    </h4>

    <form method="POST">
        <input type="hidden" name="no_invoice" value="<?= $data_header['no_invoice'] ?>">

        <div class="form-row">
            <div class="form-group">
                <label>Tanggal Kirim</label>
                <input type="date" name="tanggal_kirim" required 
                       value="<?= $data_header['tanggal_kirim'] ?? '' ?>">
            </div>
            <div class="form-group">
                <label>Shipper</label>
                <select name="shipper" required>
                    <option value="">— Pilih Shipper —</option>
                    <?php while($s = mysqli_fetch_assoc($shipper_list)) { ?>
                        <option value="<?= $s['nama_shipper'] ?>" 
                            <?= ($data_header['shipper'] == $s['nama_shipper']) ? 'selected' : '' ?>>
                            <?= $s['nama_shipper'] ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
        </div>

        <button type="submit" name="cetak" class="btn btn-primary full-width mt-16">
            <i class="bi bi-printer"></i> Update & Cetak Invoice
        </button>
    </form>
</div>

<?php elseif ($mode === 'search' && !$data_header): ?>
    <div class="info-box danger">
        <i class="bi bi-exclamation-triangle"></i>
        Invoice tidak ditemukan. Pastikan nomor invoice sudah benar.
    </div>
<?php endif; ?>


<!-- ======================== -->
<!-- STEP 3: PRINT-READY INVOICE -->
<!-- ======================== -->
<?php if ($mode === 'print' && $data_header && $data_detail): ?>

<div class="info-box success mb-24">
    <i class="bi bi-check-circle"></i>
    Data pengiriman berhasil diupdate! Invoice siap di-download sebagai PDF.
</div>

<!-- Tombol Aksi (di luar area PDF) -->
<div class="flex gap-12 mb-24">
    <button onclick="downloadPDF()" class="btn btn-primary" id="btn-download">
        <i class="bi bi-file-earmark-pdf"></i> Download PDF
    </button>
    <a href="index_transaksi.php" class="btn btn-outline">
        <i class="bi bi-arrow-left"></i> Cari Invoice Lain
    </a>
</div>

<!-- Area Invoice untuk PDF (white background) -->
<div id="invoice-pdf" style="background:#fff; color:#1a1a1a; padding:40px; border-radius:12px;">

    <!-- Header Invoice -->
    <div style="text-align:center; margin-bottom:32px; border-bottom:3px solid #0ea5e9; padding-bottom:20px;">
        <h2 style="font-size:28px; font-weight:800; color:#0f172a; margin:0;">INVOICE</h2>
        <p style="color:#64748b; font-size:14px; margin-top:6px;">No. <?= $data_header['no_invoice'] ?></p>
    </div>

    <!-- Info Grid -->
    <table style="width:100%; margin-bottom:24px; border:none;" cellpadding="0" cellspacing="0">
        <tr>
            <td style="vertical-align:top; width:50%; padding:0 10px 0 0; border:none;">
                <p style="font-size:11px; text-transform:uppercase; letter-spacing:1px; color:#94a3b8; margin:0 0 4px;">Customer / Toko</p>
                <p style="font-size:15px; font-weight:600; color:#0f172a; margin:0 0 16px;"><?= $data_header['nama_toko'] ?></p>

                <p style="font-size:11px; text-transform:uppercase; letter-spacing:1px; color:#94a3b8; margin:0 0 4px;">Alamat</p>
                <p style="font-size:14px; color:#334155; margin:0;"><?= $data_header['alamat'] ?>, <?= $data_header['provinsi'] ?>, <?= $data_header['negara'] ?></p>
            </td>
            <td style="vertical-align:top; width:50%; padding:0 0 0 10px; border:none;">
                <p style="font-size:11px; text-transform:uppercase; letter-spacing:1px; color:#94a3b8; margin:0 0 4px;">Tanggal Order</p>
                <p style="font-size:14px; color:#334155; margin:0 0 16px;"><?= $data_header['tanggal'] ?></p>

                <p style="font-size:11px; text-transform:uppercase; letter-spacing:1px; color:#94a3b8; margin:0 0 4px;">Tanggal Kirim</p>
                <p style="font-size:14px; font-weight:600; color:#0f172a; margin:0 0 16px;"><?= $data_header['tanggal_kirim'] ?></p>

                <p style="font-size:11px; text-transform:uppercase; letter-spacing:1px; color:#94a3b8; margin:0 0 4px;">Shipper</p>
                <p style="font-size:14px; font-weight:600; color:#0f172a; margin:0;"><?= $data_header['shipper'] ?></p>
            </td>
        </tr>
    </table>

    <!-- Tabel Detail -->
    <table style="width:100%; border-collapse:collapse; margin-bottom:24px; font-size:13px;">
        <thead>
            <tr style="background:#0f172a; color:#fff;">
                <th style="padding:10px 12px; text-align:left; border:1px solid #334155;">Nama Barang</th>
                <th style="padding:10px 12px; text-align:center; border:1px solid #334155;">Qty</th>
                <th style="padding:10px 12px; text-align:right; border:1px solid #334155;">Harga</th>
                <th style="padding:10px 12px; text-align:right; border:1px solid #334155;">Total</th>
                <th style="padding:10px 12px; text-align:center; border:1px solid #334155;">%</th>
                <th style="padding:10px 12px; text-align:right; border:1px solid #334155;">Diskon</th>
                <th style="padding:10px 12px; text-align:right; border:1px solid #334155;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
        <?php 
        $row_num = 0;
        while($d = mysqli_fetch_assoc($data_detail)) { 
            $total = $d['harga'] * $d['qty'];
            $diskon = $d['diskon_rp'];
            $subtotal = $total - $diskon;
            $grand_total += $subtotal;
            $bg = ($row_num % 2 == 0) ? '#f8fafc' : '#ffffff';
            $row_num++;
        ?>
            <tr style="background:<?= $bg ?>;">
                <td style="padding:10px 12px; border:1px solid #e2e8f0; font-weight:500;"><?= $d['name_product'] ?></td>
                <td style="padding:10px 12px; border:1px solid #e2e8f0; text-align:center;"><?= $d['qty'] ?></td>
                <td style="padding:10px 12px; border:1px solid #e2e8f0; text-align:right;">Rp <?= number_format($d['harga'],0,',','.') ?></td>
                <td style="padding:10px 12px; border:1px solid #e2e8f0; text-align:right;">Rp <?= number_format($total,0,',','.') ?></td>
                <td style="padding:10px 12px; border:1px solid #e2e8f0; text-align:center;"><?= $d['persen_diskon'] ?>%</td>
                <td style="padding:10px 12px; border:1px solid #e2e8f0; text-align:right;">Rp <?= number_format($diskon,0,',','.') ?></td>
                <td style="padding:10px 12px; border:1px solid #e2e8f0; text-align:right; font-weight:600;">Rp <?= number_format($subtotal,0,',','.') ?></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>

    <!-- Grand Total -->
    <div style="text-align:right; padding:16px 20px; background:#0f172a; border-radius:8px; color:#fff;">
        <span style="font-size:14px;">GRAND TOTAL</span><br>
        <span style="font-size:24px; font-weight:800; color:#38bdf8;">Rp <?= number_format($grand_total,0,',','.') ?></span>
    </div>

    <!-- Footer -->
    <div style="margin-top:32px; padding-top:16px; border-top:1px solid #e2e8f0; text-align:center;">
        <p style="font-size:12px; color:#94a3b8; margin:0;">Invoice dibuat secara otomatis oleh FrozenHub</p>
    </div>
</div>

<!-- html2pdf.js CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.2/html2pdf.bundle.min.js"></script>
<script>
function downloadPDF() {
    var element = document.getElementById('invoice-pdf');
    var btn = document.getElementById('btn-download');
    
    btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Generating PDF...';
    btn.disabled = true;

    var opt = {
        margin:       0.4,
        filename:     'Invoice_<?= $data_header["no_invoice"] ?>.pdf',
        image:        { type: 'jpeg', quality: 0.98 },
        html2canvas:  { scale: 2, useCORS: true },
        jsPDF:        { unit: 'in', format: 'a4', orientation: 'portrait' }
    };

    html2pdf().set(opt).from(element).save().then(function() {
        btn.innerHTML = '<i class="bi bi-file-earmark-pdf"></i> Download PDF';
        btn.disabled = false;
    });
}
</script>

<?php endif; ?>

<?php include '../assets/layout_footer.php'; ?>