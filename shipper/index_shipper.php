<?php
session_start();
require_once '../config/koneksi.php';

// CEK LOGIN
if (!isset($_SESSION['login'])) {
    header("Location: ../login.php");
    exit;
}

// PROSES TAMBAH DATA
if (isset($_POST['simpan'])) {
    $nama_shipper = $_POST['nama_shipper'];
    $no_hp        = $_POST['no_hp'];
    $alamat       = $_POST['alamat'];
    $keterangan   = $_POST['keterangan'];
    $no_ktp       = $_POST['no_ktp'];

    $query = mysqli_query($conn, "
        INSERT INTO tb_shipper 
        (nama_shipper, no_hp, alamat, keterangan, no_ktp)
        VALUES 
        ('$nama_shipper', '$no_hp', '$alamat', '$keterangan', '$no_ktp')
    ");

    if ($query) {
        echo "<script>alert('Shipper berhasil ditambahkan!');</script>";
    } else {
        echo "<script>alert('Gagal tambah data!');</script>";
    }
}

$page_title = 'Data Shipper';
$active_menu = 'shipper';
$is_subfolder = true;
include '../assets/layout_header.php';
?>

<a href="<?= $base_url ?>dashboard.php" class="back-link">
    <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
</a>

<!-- FORM TAMBAH SHIPPER -->
<div class="card mb-24" style="max-width:600px;">
    <div class="card-header">
        <h3><i class="bi bi-truck" style="color:var(--warning)"></i> Tambah Shipper Baru</h3>
    </div>

    <form method="POST">
        <div class="form-row">
            <div class="form-group">
                <label>Nama Shipper</label>
                <input type="text" name="nama_shipper" required>
            </div>
            <div class="form-group">
                <label>No HP</label>
                <input type="text" name="no_hp" required>
            </div>
        </div>

        <div class="form-group">
            <label>Alamat</label>
            <textarea name="alamat" required></textarea>
        </div>

        <div class="form-group">
            <label>Keterangan</label>
            <textarea name="keterangan"></textarea>
        </div>

        <div class="form-group">
            <label>No KTP</label>
            <input type="text" name="no_ktp" required>
        </div>

        <button type="submit" name="simpan" class="btn btn-primary full-width mt-16">
            <i class="bi bi-check-circle"></i> Simpan Shipper
        </button>
    </form>
</div>

<!-- TABEL DATA SHIPPER -->
<div class="card">
    <div class="card-header">
        <h3><i class="bi bi-truck" style="color:var(--accent-1)"></i> Daftar Shipper</h3>
    </div>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama Shipper</th>
                    <th>No HP</th>
                    <th>Alamat</th>
                    <th>Keterangan</th>
                    <th>No KTP</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $data = mysqli_query($conn, "SELECT * FROM tb_shipper ORDER BY id DESC");
            while ($row = mysqli_fetch_array($data)) {
            ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td style="color:var(--text-primary); font-weight:500;"><?= $row['nama_shipper'] ?></td>
                    <td><?= $row['no_hp'] ?></td>
                    <td><?= $row['alamat'] ?></td>
                    <td><?= $row['keterangan'] ?></td>
                    <td><?= $row['no_ktp'] ?></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../assets/layout_footer.php'; ?>