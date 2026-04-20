<?php
session_start();
require_once '../config/koneksi.php';

// ambil produk
$produk = mysqli_query($conn, "SELECT * FROM tb_products");

// ambil customer (opsional)
$customer = mysqli_query($conn, "SELECT * FROM tb_customer");

// ambil shipper
$shipper = mysqli_query($conn, "SELECT * FROM tb_shipper");

$page_title = 'Form Order';
$active_menu = 'order';
$is_subfolder = true;
include '../assets/layout_header.php';
?>

<a href="<?= $base_url ?>dashboard.php" class="back-link">
    <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
</a>

<div class="card">
    <div class="card-header">
        <h3><i class="bi bi-cart-check-fill" style="color:var(--accent-1)"></i> Input Pesanan Baru</h3>
    </div>

    <form method="POST" action="proses_order.php">

        <div class="form-row">
            <div class="form-group">
                <label>No Invoice</label>
                <input type="text" name="no_invoice" required>
            </div>
            <div class="form-group">
                <label>Customer</label>
                <select name="customer_id">
                <?php while($c = mysqli_fetch_assoc($customer)) { ?>
                    <option value="<?= $c['id']; ?>"><?= $c['nama_toko']; ?></option>
                <?php } ?>
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Tanggal Kirim</label>
                <input type="date" name="tanggal_kirim">
            </div>
            <div class="form-group">
                <label>Shipper</label>
                <select name="shipper">
                <?php while($s = mysqli_fetch_assoc($shipper)) { ?>
                    <option value="<?= $s['nama_shipper']; ?>"><?= $s['nama_shipper']; ?></option>
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
            <select name="product_id" id="product" onchange="setHarga()">
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
<script>
function setHarga() {
    var select = document.getElementById("product");
    var harga = select.options[select.selectedIndex].getAttribute("data-harga");

    document.getElementById("harga").value = harga;
    hitungTotal();
}

function hitungTotal() {
    var harga = document.getElementById("harga").value;
    var stock = document.getElementById("stock") ? document.getElementById("stock").value : 0;

    if (document.getElementById("total")) {
        document.getElementById("total").value = harga * stock;
    }
}

if (document.getElementById("qty")) {
    document.getElementById("qty").addEventListener("input", hitungTotal);
}

// auto isi saat pertama buka
window.onload = setHarga;
</script>

<?php include '../assets/layout_footer.php'; ?>