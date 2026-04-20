<?php
session_start();
require_once '../config/koneksi.php';

if (isset($_POST['simpan'])) {

    $no_invoice = $_POST['no_invoice'];
    $customer   = $_POST['customer_id'];
    $tanggal    = date('Y-m-d');
    $kirim      = $_POST['tanggal_kirim'];
    $shipper    = $_POST['shipper'];

    $product_id = $_POST['product_id'];
    $qty        = (int) $_POST['qty'];
    $harga      = (int) $_POST['harga'];
    $persen     = isset($_POST['persen']) && $_POST['persen'] !== '' ? floatval(str_replace(',', '.', $_POST['persen'])) : 0;
    $diskon     = isset($_POST['diskon']) && $_POST['diskon'] !== '' ? floatval(str_replace(',', '.', $_POST['diskon'])) : 0;

    // AMBIL DATA PRODUK DULU (sebelum simpan apa pun)
    $produk = mysqli_fetch_assoc(mysqli_query($conn, "
        SELECT name_product, stock FROM tb_products WHERE id_Unique='$product_id'
    "));

    // CEK STOK SEBELUM PROSES
    if ($qty > $produk['stock']) {
        echo "<script>alert('Stok tidak cukup! Stok tersedia: {$produk['stock']}'); history.back();</script>";
        exit;
    }

    // HITUNG
    $total = $harga * $qty;

    if ($persen > 0) {
        $diskon = ($persen / 100) * $total;
    }

    $subtotal = $total - $diskon;

    // CEK APAKAH INVOICE SUDAH ADA
    $cek_invoice = mysqli_fetch_assoc(mysqli_query($conn, "
        SELECT id FROM transactions WHERE no_invoice='$no_invoice' LIMIT 1
    "));

    if ($cek_invoice) {
        // GUNAKAN TRANSACTION ID YANG SUDAH ADA
        $transaction_id = $cek_invoice['id'];
    } else {
        // BUAT HEADER BARU
        mysqli_query($conn, "
            INSERT INTO transactions 
            (no_invoice, customer_id, tanggal, tanggal_kirim, shipper)
            VALUES 
            ('$no_invoice', '$customer', '$tanggal', '$kirim', '$shipper')
        ");
        $transaction_id = mysqli_insert_id($conn);
    }

    // SIMPAN DETAIL
    mysqli_query($conn, "
        INSERT INTO transaction_detail
        (transaction_id, name_product, harga, qty, persen_diskon, diskon_rp)
        VALUES
        ('$transaction_id', '{$produk['name_product']}', '$harga', '$qty', '$persen', '$diskon')
    ");

    // KURANGI STOK
    $stok_baru = $produk['stock'] - $qty;

    mysqli_query($conn, "
        UPDATE tb_products 
        SET stock='$stok_baru'
        WHERE id_Unique='$product_id'
    ");

    echo "<script>alert('Order berhasil disimpan!'); window.location='index_order.php';</script>";
}
?>