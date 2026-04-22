<?php
session_start();
require_once '../config/koneksi.php';

// CEK ADMIN
if (!isset($_SESSION['login']) || $_SESSION['role'] != 'admin') {
    echo "Akses ditolak!";
    exit;
}

// ambil produk
$produk = mysqli_query($conn, "SELECT * FROM tb_products");

// ambil customer (opsional)
$customer = mysqli_query($conn, "SELECT * FROM tb_customer");

// ambil shipper
$shipper = mysqli_query($conn, "SELECT * FROM tb_shipper");

// Pre-fill data dari URL jika ada (agar bisa input multiple item ke invoice yang sama)
$pre_invoice = $_GET['no_invoice'] ?? '';
$pre_customer = $_GET['customer_id'] ?? '';
$pre_tanggal_kirim = $_GET['tanggal_kirim'] ?? '';
$pre_shipper = $_GET['shipper'] ?? '';

// AUTO GENERATE NO INVOICE JIKA KOSONG (ORDER BARU)
if (empty($pre_invoice)) {
    $q_last = mysqli_query($conn, "SELECT no_invoice FROM transactions ORDER BY CAST(no_invoice AS UNSIGNED) DESC LIMIT 1");
    $d_last = mysqli_fetch_assoc($q_last);
    if ($d_last) {
        $pre_invoice = (int)$d_last['no_invoice'] + 1;
    } else {
        $pre_invoice = 1;
    }
}

$page_title = 'Form Order';
$active_menu = 'order';
$is_subfolder = true;
include '../assets/layout_header.php';
?>

<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<style>
/* Custom Select2 Styling to match the theme */
.select2-container--default .select2-selection--single {
    background-color: var(--bg-input);
    border: 1px solid var(--border-color);
    border-radius: var(--radius-sm);
    height: 45px;
    display: flex;
    align-items: center;
    transition: var(--transition);
}
.select2-container--default .select2-selection--single .select2-selection__rendered {
    color: var(--text-primary);
    padding-left: 14px;
    font-size: 14px;
}
.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 43px;
    right: 10px;
}
.select2-container--default .select2-selection--single .select2-selection__arrow b {
    border-color: var(--text-muted) transparent transparent transparent;
}
.select2-container--default.select2-container--focus .select2-selection--single {
    border-color: var(--border-focus);
    box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.1);
}
.select2-dropdown {
    background-color: var(--bg-secondary);
    border: 1px solid var(--border-color);
    border-radius: var(--radius-sm);
    box-shadow: var(--shadow-lg);
    z-index: 1001;
}
.select2-search--dropdown .select2-search__field {
    background-color: var(--bg-input);
    border: 1px solid var(--border-color);
    color: var(--text-primary);
    border-radius: 4px;
    outline: none;
}
.select2-results__option {
    padding: 10px 14px;
    font-size: 14px;
    color: var(--text-secondary);
}
.select2-container--default .select2-results__option--highlighted[aria-selected] {
    background-color: var(--accent-1);
    color: #0f172a;
}
.select2-container--default .select2-results__option[aria-selected=true] {
    background-color: var(--bg-input);
    color: var(--accent-1);
}
.select2-results__option {
    transition: var(--transition);
}
</style>

<a href="<?= $base_url ?>dashboard.php" class="back-link">
    <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
</a>

<div class="card">
    <div class="card-header" style="display:flex; justify-content:space-between; align-items:center;">
        <h3 style="margin:0;"><i class="bi bi-cart-check-fill" style="color:var(--accent-1)"></i> Input Pesanan Baru</h3>
        <?php if($pre_invoice): ?>
            <a href="index_order.php" class="btn btn-outline btn-sm"><i class="bi bi-plus-circle"></i> Buat Invoice Baru</a>
        <?php endif; ?>
    </div>

    <form method="POST" action="proses_order.php">

        <div class="form-row">
            <div class="form-group">
                <label>No Invoice</label>
                <input type="text" name="no_invoice" value="<?= $pre_invoice ?>" required>
            </div>
            <div class="form-group">
                <label>Customer</label>
                <select name="customer_id" class="select2">
                <?php while($c = mysqli_fetch_assoc($customer)) { ?>
                    <option value="<?= $c['id']; ?>" <?= ($pre_customer == $c['id']) ? 'selected' : '' ?>><?= $c['nama_toko']; ?></option>
                <?php } ?>
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Tanggal Kirim</label>
                <input type="date" name="tanggal_kirim" value="<?= $pre_tanggal_kirim ?>">
            </div>
            <div class="form-group">
                <label>Shipper</label>
                <select name="shipper">
                <?php while($s = mysqli_fetch_assoc($shipper)) { ?>
                    <option value="<?= $s['nama_shipper']; ?>" <?= ($pre_shipper == $s['nama_shipper']) ? 'selected' : '' ?>><?= $s['nama_shipper']; ?></option>
                <?php } ?>
                </select>
            </div>
        </div>

        <hr style="border-color:var(--border-color); margin: 24px 0;">

        <h4 class="mb-16" style="font-weight:600;">
            <i class="bi bi-box-seam" style="color:var(--accent-2)"></i> Detail Produk
        </h4>

        <div class="form-group">
            <label>Pilih Produk</label>
            <select name="product_id" id="product" class="select2">
            <option value="">-- Pilih Produk --</option>
            <?php while($p = mysqli_fetch_assoc($produk)) { ?>
            <option 
                value="<?= $p['id_Unique']; ?>"
                data-harga="<?= $p['price']; ?>"
                data-stok="<?= $p['stock']; ?>"
            >
                <?= $p['name_product']; ?> (Stok: <?= $p['stock']; ?>)
            </option>
            <?php } ?>
            </select>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Qty</label>
                <input type="number" name="qty" id="qty" required>
            </div>
            <div class="form-group">
                <label>Harga</label>
                <input type="number" name="harga" id="harga" readonly style="opacity:0.7">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Diskon (%)</label>
                <input type="number" name="persen" step="any">
            </div>
            <div class="form-group">
                <label>Diskon (Rp)</label>
                <input type="number" name="diskon" step="any">
            </div>
        </div>

        <button type="submit" name="simpan" class="btn btn-primary full-width mt-16">
            <i class="bi bi-check-circle"></i> Simpan Order
        </button>

    </form>
</div>

<!-- 🔥 JAVASCRIPT DI SINI -->
<!-- Select2 & jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    // Initialize Select2
    $('.select2').select2({
        width: '100%',
        placeholder: "Cari data...",
        allowClear: true
    });

    // Handle change event for product selection
    $('#product').on('select2:select', function (e) {
        setHarga();
    });

    // Auto fill saat pertama kali jika sudah ada pilihan
    if ($('#product').val()) {
        setHarga();
    }
});

function setHarga() {
    var select = document.getElementById("product");
    if (select.selectedIndex === -1) return;
    
    var option = select.options[select.selectedIndex];
    var harga = option.getAttribute("data-harga");

    if (harga) {
        document.getElementById("harga").value = harga;
    } else {
        document.getElementById("harga").value = "";
    }
    hitungTotal();
}

function hitungTotal() {
    var harga = document.getElementById("harga").value;
    var qty = document.getElementById("qty") ? document.getElementById("qty").value : 0;

    // hitung subtotal (harga * qty)
    // jika ada field total (saat ini tidak ada di form tapi ada di fungsi lama)
}

if (document.getElementById("qty")) {
    document.getElementById("qty").addEventListener("input", hitungTotal);
}
</script>

<?php include '../assets/layout_footer.php'; ?>