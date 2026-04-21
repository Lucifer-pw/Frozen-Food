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
    $status        = $_POST['status'] ?? 'Undelivered';
    $mode = 'print';

    // update tanggal kirim, shipper dan status
    mysqli_query($conn, "
        UPDATE transactions 
        SET tanggal_kirim='$tanggal_kirim', shipper='$shipper', status='$status'
        WHERE no_invoice='$no_invoice'
    ");

    // Update diskon per item
    if (isset($_POST['diskon_persen'])) {
        foreach ($_POST['diskon_persen'] as $detail_id => $persen) {
            $persen = floatval($persen);
            // Ambil data harga dan qty untuk menghitung ulang diskon_rp
            $q_item = mysqli_query($conn, "SELECT harga, qty FROM transaction_detail WHERE id='$detail_id'");
            $item = mysqli_fetch_assoc($q_item);
            if ($item) {
                $diskon_rp = ($item['harga'] * $item['qty']) * ($persen / 100);
                mysqli_query($conn, "UPDATE transaction_detail SET persen_diskon='$persen', diskon_rp='$diskon_rp' WHERE id='$detail_id'");
            }
        }
    }

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
    SELECT t.no_invoice, t.tanggal, t.tanggal_kirim, t.shipper, t.status, c.nama_toko
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
                        <?php if ($t['status'] == 'Delivered'): ?>
                            <span style="padding:4px 10px; border-radius:12px; font-size:11px; font-weight:600; background:var(--success-bg); color:var(--success);">
                                <i class="bi bi-check-circle"></i> Delivered
                            </span>
                        <?php else: ?>
                            <span style="padding:4px 10px; border-radius:12px; font-size:11px; font-weight:600; background:var(--danger-bg); color:var(--danger);">
                                <i class="bi bi-clock"></i> Undelivered
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
        <i class="bi bi-box-seam" style="color:var(--accent-1)"></i> Detail Produk & Diskon
    </h4>
    <form method="POST">
        <input type="hidden" name="no_invoice" value="<?= $data_header['no_invoice'] ?>">
        
        <div class="table-wrapper mb-24">
            <table>
                <thead>
                    <tr>
                        <th>Nama Barang</th>
                        <th style="text-align:center;">Qty</th>
                        <th style="text-align:right;">Harga</th>
                        <th style="text-align:right;">Total</th>
                        <th style="width:100px; text-align:center;">Diskon (%)</th>
                        <th style="text-align:right;">Potongan (Rp)</th>
                        <th style="text-align:right;">Subtotal</th>
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
                    <td style="text-align:center;"><?= $d['qty'] ?></td>
                    <td style="text-align:right;">Rp <?= number_format($d['harga'],0,',','.') ?></td>
                    <td style="text-align:right;">Rp <?= number_format($total,0,',','.') ?></td>
                    <td style="text-align:center;">
                        <input type="number" name="diskon_persen[<?= $d['id'] ?>]" 
                               value="<?= $d['persen_diskon'] ?>" step="0.1" min="0" max="100"
                               style="width:60px; padding:4px 8px; border:1px solid var(--border-color); border-radius:4px; text-align:center;">
                    </td>
                    <td style="text-align:right;">Rp <?= number_format($diskon,0,',','.') ?></td>
                    <td style="color:var(--text-primary); font-weight:600; text-align:right;">Rp <?= number_format($subtotal,0,',','.') ?></td>
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
                       value="<?= ($data_header['tanggal_kirim'] != '0000-00-00') ? $data_header['tanggal_kirim'] : date('Y-m-d') ?>">
            </div>
            <div class="form-group">
                <label>Shipper</label>
                <select name="shipper" required>
                    <option value="">— Pilih Shipper —</option>
                    <?php 
                    // Reset pointer shipper_list
                    mysqli_data_seek($shipper_list, 0);
                    while($s = mysqli_fetch_assoc($shipper_list)) { 
                    ?>
                        <option value="<?= $s['nama_shipper'] ?>" 
                            <?= ($data_header['shipper'] == $s['nama_shipper']) ? 'selected' : '' ?>>
                            <?= $s['nama_shipper'] ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
            <div class="form-group">
                <label>Status Pengiriman</label>
                <select name="status" required>
                    <option value="Undelivered" <?= ($data_header['status'] == 'Undelivered') ? 'selected' : '' ?>>Undelivered</option>
                    <option value="Delivered" <?= ($data_header['status'] == 'Delivered') ? 'selected' : '' ?>>Delivered</option>
                </select>
            </div>
        </div>

        <button type="submit" name="cetak" class="btn btn-primary full-width mt-16">
            <i class="bi bi-printer"></i> Update Diskon & Cetak Invoice
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
<div id="invoice-pdf" style="width:1050px; background:#fff; color:#1a1a1a; padding:24px 30px; border-radius:12px; margin:0; box-sizing:border-box;">

    <!-- Header Invoice -->
    <div style="text-align:center; margin-bottom:16px; border-bottom:3px solid #0ea5e9; padding-bottom:12px;">
        <h2 style="font-size:24px; font-weight:800; color:#0f172a; margin:0;">INVOICE</h2>
        <p style="color:#64748b; font-size:14px; margin-top:6px;">No. <?= $data_header['no_invoice'] ?></p>
    </div>

    <!-- Info Grid -->
    <table style="width:100%; margin-bottom:16px; border:none;" cellpadding="0" cellspacing="0">
        <tr>
            <td style="vertical-align:top; width:50%; padding:0 10px 0 0; border:none;">
                <p style="font-size:10px; text-transform:uppercase; letter-spacing:1px; color:#94a3b8; margin:0 0 2px;">Customer / Toko</p>
                <p style="font-size:14px; font-weight:600; color:#0f172a; margin:0 0 12px;"><?= $data_header['nama_toko'] ?></p>

                <p style="font-size:10px; text-transform:uppercase; letter-spacing:1px; color:#94a3b8; margin:0 0 2px;">Alamat</p>
                <p style="font-size:12px; color:#334155; margin:0;"><?= $data_header['alamat'] ?>, <?= $data_header['provinsi'] ?>, <?= $data_header['negara'] ?></p>
            </td>
            <td style="vertical-align:top; width:50%; padding:0 0 0 10px; border:none;">
                <p style="font-size:10px; text-transform:uppercase; letter-spacing:1px; color:#94a3b8; margin:0 0 2px;">Tanggal Order</p>
                <p style="font-size:12px; color:#334155; margin:0 0 12px;"><?= $data_header['tanggal'] ?></p>

                <p style="font-size:10px; text-transform:uppercase; letter-spacing:1px; color:#94a3b8; margin:0 0 2px;">Tanggal Kirim</p>
                <p style="font-size:13px; font-weight:600; color:#0f172a; margin:0 0 12px;"><?= $data_header['tanggal_kirim'] ?></p>

                <p style="font-size:10px; text-transform:uppercase; letter-spacing:1px; color:#94a3b8; margin:0 0 2px;">Shipper</p>
                <p style="font-size:13px; font-weight:600; color:#0f172a; margin:0;"><?= $data_header['shipper'] ?></p>
            </td>
        </tr>
    </table>

    <!-- Tabel Detail -->
    <table style="width:100%; border-collapse:collapse; margin-bottom:16px; font-size:12px;">
        <thead>
            <tr style="background:#0f172a; color:#fff;">
                <th style="padding:6px 10px; text-align:left; border:1px solid #334155;">Nama Barang</th>
                <th style="padding:6px 10px; text-align:center; border:1px solid #334155;">Qty</th>
                <th style="padding:6px 10px; text-align:right; border:1px solid #334155;">Harga</th>
                <th style="padding:6px 10px; text-align:right; border:1px solid #334155;">Total</th>
                <th style="padding:6px 10px; text-align:center; border:1px solid #334155;">%</th>
                <th style="padding:6px 10px; text-align:right; border:1px solid #334155;">Diskon</th>
                <th style="padding:6px 10px; text-align:right; border:1px solid #334155;">Subtotal</th>
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
                <td style="padding:6px 10px; border:1px solid #e2e8f0; font-weight:500;"><?= $d['name_product'] ?></td>
                <td style="padding:6px 10px; border:1px solid #e2e8f0; text-align:center;"><?= $d['qty'] ?></td>
                <td style="padding:6px 10px; border:1px solid #e2e8f0; text-align:right;">Rp <?= number_format($d['harga'],0,',','.') ?></td>
                <td style="padding:6px 10px; border:1px solid #e2e8f0; text-align:right;">Rp <?= number_format($total,0,',','.') ?></td>
                <td style="padding:6px 10px; border:1px solid #e2e8f0; text-align:center;"><?= $d['persen_diskon'] ?>%</td>
                <td style="padding:6px 10px; border:1px solid #e2e8f0; text-align:right;">Rp <?= number_format($diskon,0,',','.') ?></td>
                <td style="padding:6px 10px; border:1px solid #e2e8f0; text-align:right; font-weight:600;">Rp <?= number_format($subtotal,0,',','.') ?></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>

    <!-- Grand Total -->
    <div style="text-align:right; padding:10px 20px; background:#0f172a; border-radius:8px; color:#fff;">
        <span style="font-size:12px;">GRAND TOTAL</span><br>
        <span style="font-size:20px; font-weight:800; color:#38bdf8;">Rp <?= number_format($grand_total,0,',','.') ?></span>
    </div>

    <!-- Area Tanda Tangan -->
    <table style="width:100%; margin-top:24px; text-align:center; border:none; font-size:12px; color:#0f172a;" cellpadding="0" cellspacing="0">
        <tr>
            <td style="width:33%; vertical-align:top; border:none;">
                <p style="margin:0; font-weight:600;">Diterima Oleh,</p>
                <div style="height:50px;"></div>
                <p style="margin:0; text-decoration:underline;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</p>
            </td>
            <td style="width:33%; vertical-align:top; border:none;">
                <p style="margin:0; font-weight:600;">Pengirim</p>
                <div style="height:50px;"></div>
                <p style="margin:0; font-weight:600; text-decoration:underline;"><?= $data_header['shipper'] ?: '........................' ?></p>
            </td>
            <td style="width:33%; vertical-align:top; border:none;">
                <p style="margin:0; font-weight:600;">Hormat Kami,</p>
                <div style="height:50px;"></div>
                <p style="margin:0; font-weight:600; text-decoration:underline;">Setiawan</p>
            </td>
        </tr>
    </table>

    <!-- Footer -->
    <div style="margin-top:16px; padding-top:12px; border-top:1px solid #e2e8f0; text-align:center;">
        <p style="font-size:11px; color:#94a3b8; margin:0;">Cabang Jawa Tengah FrozenHub</p>
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

    <?php
    $f_inv = $data_header['no_invoice'];
    $f_toko = trim(preg_replace('/[^A-Za-z0-9]+/', '_', strtoupper($data_header['nama_toko'])), '_');
    $f_alamat = trim(preg_replace('/[^A-Za-z0-9]+/', '_', strtoupper($data_header['alamat'])), '_');
    $f_tgl = date('Ymd', strtotime($data_header['tanggal_kirim']));
    $pdf_name = "{$f_inv}_{$f_toko}_{$f_alamat}_{$f_tgl}.pdf";
    ?>

    var opt = {
        margin:       0.2, // Margin sangat tipis agar ruang lebih maksimal
        filename:     '<?= $pdf_name ?>',
        image:        { type: 'jpeg', quality: 0.98 },
        html2canvas:  { scale: 2, useCORS: true, scrollX: 0, scrollY: 0 }, 
        pagebreak:    { mode: 'avoid-all' },
        jsPDF:        { unit: 'in', format: 'a4', orientation: 'landscape' }
    };

    // Generate PDF menggunakan chaining agar lebih stabil
    html2pdf().set(opt).from(element).toPdf().get('pdf').then(function(pdf) {
        // Ambil base64 untuk dikirim ke server
        var pdfBase64 = pdf.output('datauristring');
        
        // 1. Simpan ke server (AJAX)
        fetch('save_pdf.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ 
                filename: opt.filename, 
                pdfData: pdfBase64 
            })
        })
        .then(res => res.json())
        .then(data => console.log('Server response:', data))
        .catch(err => console.error('Error saving PDF:', err));

    }).save().then(function() {
        // 2. Setelah proses save (download) selesai, kembalikan tombol & popup
        btn.innerHTML = '<i class="bi bi-file-earmark-pdf"></i> Download PDF';
        btn.disabled = false;
        alert('PDF Berhasil Diunduh dan Tersimpan di Sistem!');
    });
}
</script>

<?php endif; ?>

<?php include '../assets/layout_footer.php'; ?>