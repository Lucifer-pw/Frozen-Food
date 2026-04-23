<?php
session_start();
require_once '../config/koneksi.php';

if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'user') {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$email_session = $_SESSION['email'];

// HAPUS ITEM DARI KERANJANG
if (isset($_GET['hapus'])) {
    $id_cart = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM tb_cart WHERE id='$id_cart' AND user_id='$user_id'");
    header("Location: keranjang.php");
    exit;
}

// PROSES CHECKOUT
if (isset($_POST['checkout'])) {
    $tanggal_kirim = $_POST['tanggal_kirim'];
    $shipper = ""; // Shipper akan diisi oleh Admin
    $tanggal = date('Y-m-d');

    // Ambil data customer (id)
    $q_cust = mysqli_query($conn, "SELECT id FROM tb_customer WHERE email='$email_session'");
    $d_cust = mysqli_fetch_assoc($q_cust);
    
    if (!$d_cust) {
        echo "<script>alert('Profil Customer belum lengkap! Silakan lengkapi di menu Akun Saya.'); window.location='akun_saya.php';</script>";
        exit;
    }
    $customer_id = $d_cust['id'];

    // Ambil item di keranjang
    $q_cart = mysqli_query($conn, "
        SELECT c.*, p.name_product, p.price, p.stock 
        FROM tb_cart c
        JOIN tb_products p ON c.product_id = p.id_Unique
        WHERE c.user_id='$user_id'
    ");

    if (mysqli_num_rows($q_cart) == 0) {
        echo "<script>alert('Keranjang kosong!');</script>";
    } else {
        // Generate No Invoice Baru
        $q_last = mysqli_query($conn, "SELECT no_invoice FROM transactions ORDER BY CAST(no_invoice AS UNSIGNED) DESC LIMIT 1");
        $d_last = mysqli_fetch_assoc($q_last);
        $no_invoice = $d_last ? ((int)$d_last['no_invoice'] + 1) : 1;

        // Insert Header Transaksi
        mysqli_query($conn, "
            INSERT INTO transactions (no_invoice, customer_id, tanggal, tanggal_kirim, shipper)
            VALUES ('$no_invoice', '$customer_id', '$tanggal', '$tanggal_kirim', '$shipper')
        ");
        $transaction_id = mysqli_insert_id($conn);

        // Loop dan Insert Detail, kurangi stok
        while ($item = mysqli_fetch_assoc($q_cart)) {
            $nama_produk = $item['name_product'];
            $harga = $item['price'];
            $qty = $item['qty'];
            
            // Bypass jika qty melebihi stok (stok mungkin berkurang sejak masuk keranjang)
            if ($qty > $item['stock']) {
                $qty = $item['stock']; // Paksa pakai stok sisa, atau di-skip. Kita paksa ambil stok tersisa jika kurang.
            }
            if ($qty > 0) {
                // Insert detail
                mysqli_query($conn, "
                    INSERT INTO transaction_detail (transaction_id, name_product, harga, qty, persen_diskon, diskon_rp)
                    VALUES ('$transaction_id', '$nama_produk', '$harga', '$qty', '0', '0')
                ");

                // Kurangi stok
                $new_stock = $item['stock'] - $qty;
                mysqli_query($conn, "UPDATE tb_products SET stock='$new_stock' WHERE id_Unique='{$item['product_id']}'");
            }
        }

        // Kosongkan keranjang
        mysqli_query($conn, "DELETE FROM tb_cart WHERE user_id='$user_id'");

        echo "<script>alert('Checkout berhasil! Pesanan Anda sedang diproses.'); window.location='../dashboard.php';</script>";
        exit;
    }
}

$page_title = 'Keranjang Belanja';
$active_menu = 'keranjang';
$is_subfolder = true;
include '../assets/layout_header.php';
?>

<a href="katalog.php" class="back-link">
    <i class="bi bi-arrow-left"></i> Kembali Belanja
</a>

<div class="card mb-24">
    <div class="card-header">
        <h3 style="margin:0;"><i class="bi bi-cart3" style="color:var(--accent-3)"></i> Keranjang Belanja Anda</h3>
    </div>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Produk</th>
                    <th style="text-align:right;">Harga</th>
                    <th style="text-align:center;">Qty</th>
                    <th style="text-align:right;">Subtotal</th>
                    <th style="text-align:center; width:80px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $grand_total = 0;
            $q_cart = mysqli_query($conn, "
                SELECT c.*, p.name_product, p.price, p.stock 
                FROM tb_cart c
                JOIN tb_products p ON c.product_id = p.id_Unique
                WHERE c.user_id='$user_id'
            ");

            if (mysqli_num_rows($q_cart) == 0) {
                echo "<tr><td colspan='5' style='text-align:center; padding:32px; color:var(--text-secondary);'>Keranjang masih kosong. <br><a href='katalog.php' class='btn btn-primary btn-sm mt-16'>Belanja Sekarang</a></td></tr>";
            }

            while ($item = mysqli_fetch_assoc($q_cart)) {
                $subtotal = $item['price'] * $item['qty'];
                $grand_total += $subtotal;
            ?>
                <tr>
                    <td style="font-weight:500; color:var(--text-primary);">
                        <?= $item['name_product'] ?>
                        <?php if($item['qty'] > $item['stock']) echo "<br><small style='color:var(--danger);'>Stok tersisa: {$item['stock']}</small>"; ?>
                    </td>
                    <td style="text-align:right;">Rp <?= number_format($item['price'],0,',','.') ?></td>
                    <td style="text-align:center;">
                        <?= $item['qty'] ?>
                    </td>
                    <td style="text-align:right; font-weight:600;">Rp <?= number_format($subtotal,0,',','.') ?></td>
                    <td style="text-align:center;">
                        <a href="?hapus=<?= $item['id'] ?>" class="btn btn-outline btn-sm" style="border-color:var(--danger); color:var(--danger);" onclick="return confirm('Hapus item ini?')">
                            <i class="bi bi-trash"></i>
                        </a>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php if (mysqli_num_rows($q_cart) > 0): ?>
<!-- FORM CHECKOUT -->
<div class="card" style="max-width: 500px; margin-left:auto;">
    <div class="card-header" style="background:var(--bg-secondary); display:flex; justify-content:space-between;">
        <h4 style="margin:0;">Total Tagihan</h4>
        <h3 style="margin:0; color:var(--accent-1);">Rp <?= number_format($grand_total,0,',','.') ?></h3>
    </div>
    
    <div style="padding: 24px;">
        <form method="POST">
            <div class="form-group">
                <label>Tanggal Permintaan Kirim (Estimasi)</label>
                <input type="date" name="tanggal_kirim" required min="<?= date('Y-m-d') ?>">
            </div>

            <button type="submit" name="checkout" class="btn btn-primary full-width mt-16" style="background:var(--success); border-color:var(--success);">
                <i class="bi bi-check2-circle"></i> Buat Pesanan (Checkout)
            </button>
        </form>
    </div>
</div>
<?php endif; ?>

<?php include '../assets/layout_footer.php'; ?>
