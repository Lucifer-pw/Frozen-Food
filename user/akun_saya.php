<?php
session_start();
require_once '../config/koneksi.php';

// CEK LOGIN
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'user') {
    header("Location: ../login.php");
    exit;
}

$email_session = $_SESSION['email'];

// AMBIL DATA USER DAN CUSTOMER
$query = mysqli_query($conn, "
    SELECT u.id as user_id, u.username, u.email as user_email, 
           c.id as cust_id, c.nama_customer, c.nama_toko, c.phone, c.jenis_kelamin, c.alamat, c.provinsi, c.negara
    FROM tb_users u
    LEFT JOIN tb_customer c ON u.email = c.email
    WHERE u.email = '$email_session'
");
$data = mysqli_fetch_assoc($query);

// PROSES UPDATE
if (isset($_POST['update_profil'])) {
    $username = $_POST['username'];
    $nama = $_POST['nama_customer'];
    $toko = $_POST['nama_toko'];
    $phone = $_POST['phone'];
    $jk = $_POST['jenis_kelamin'];
    $alamat = $_POST['alamat'];
    $provinsi = $_POST['provinsi'];
    $negara = $_POST['negara'];
    
    // update tb_users (hanya username, karena email jadi acuan)
    mysqli_query($conn, "UPDATE tb_users SET username='$username' WHERE email='$email_session'");
    $_SESSION['username'] = $username;

    // update tb_customer
    mysqli_query($conn, "
        UPDATE tb_customer 
        SET nama_customer='$nama', nama_toko='$toko', phone='$phone', jenis_kelamin='$jk', alamat='$alamat', provinsi='$provinsi', negara='$negara'
        WHERE email='$email_session'
    ");

    echo "<script>alert('Profil berhasil diperbarui!'); window.location='akun_saya.php';</script>";
}

$page_title = 'Akun Saya';
$active_menu = 'akun';
$is_subfolder = true;
include '../assets/layout_header.php';
?>

<a href="<?= $base_url ?>dashboard.php" class="back-link">
    <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
</a>

<div class="card" style="max-width: 600px; margin: 0 auto;">
    <div class="card-header">
        <h3><i class="bi bi-person-circle" style="color:var(--accent-1)"></i> Profil Saya</h3>
    </div>
    
    <div style="padding: 24px;">
        <form method="POST">
            <div class="form-group">
                <label>Email (Tidak bisa diubah)</label>
                <input type="email" value="<?= $data['user_email'] ?>" readonly style="background:var(--bg-secondary); opacity:0.7; cursor:not-allowed;">
            </div>

            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" value="<?= $data['username'] ?>" required>
            </div>

            <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" name="nama_customer" value="<?= $data['nama_customer'] ?>" required>
            </div>

            <div class="form-group">
                <label>Nama Toko</label>
                <input type="text" name="nama_toko" value="<?= $data['nama_toko'] ?>">
            </div>

            <div class="form-group">
                <label>No Telepon</label>
                <input type="text" name="phone" value="<?= $data['phone'] ?>">
            </div>

            <div class="form-group">
                <label>Jenis Kelamin</label>
                <select name="jenis_kelamin">
                    <option value="">- Pilih Jenis Kelamin -</option>
                    <option value="Laki-Laki" <?= ($data['jenis_kelamin'] == 'Laki-Laki') ? 'selected' : '' ?>>Laki-Laki</option>
                    <option value="Perempuan" <?= ($data['jenis_kelamin'] == 'Perempuan') ? 'selected' : '' ?>>Perempuan</option>
                </select>
            </div>

            <div class="form-group">
                <label>Alamat Pengiriman Default</label>
                <textarea name="alamat" rows="3"><?= $data['alamat'] ?></textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Provinsi</label>
                    <input type="text" name="provinsi" value="<?= $data['provinsi'] ?>">
                </div>
                <div class="form-group">
                    <label>Negara</label>
                    <input type="text" name="negara" value="<?= $data['negara'] ?>">
                </div>
            </div>

            <button type="submit" name="update_profil" class="btn btn-primary full-width mt-16">
                <i class="bi bi-save"></i> Simpan Profil
            </button>
        </form>
    </div>
</div>

<?php include '../assets/layout_footer.php'; ?>
