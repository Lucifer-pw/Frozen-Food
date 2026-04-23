<?php
session_start();
require_once '../config/koneksi.php';

// CEK ADMIN
if (!isset($_SESSION['login']) || $_SESSION['role'] != 'admin') {
    echo "Akses ditolak!";
    exit;
}

// =====================
// EDIT CUSTOMER
// =====================
if (isset($_POST['edit_simpan'])) {
    $id = $_POST['id'];
    $id_customer = $_POST['id_customer'];
    $nama = $_POST['nama_customer'];
    $toko = $_POST['nama_toko'];
    $alamat = $_POST['alamat'];
    $provinsi = $_POST['provinsi'];
    $negara = $_POST['negara'];
    $phone = $_POST['phone'];
    $ktp = $_POST['no_ktp'];
    $detail = $_POST['detail'];
    $maps_link = $_POST['maps_link'];

    mysqli_query($conn, "
        UPDATE tb_customer SET
        id_customer = '$id_customer',
        nama_customer = '$nama',
        nama_toko = '$toko',
        alamat = '$alamat',
        provinsi = '$provinsi',
        negara = '$negara',
        phone = '$phone',
        no_ktp = '$ktp',
        detail = '$detail',
        maps_link = '$maps_link'
        WHERE id = '$id'
    ");

    echo "<script>alert('Data Customer berhasil diupdate!'); window.location='index_customer.php';</script>";
}

// =====================
// TAMBAH CUSTOMER
// =====================
if (isset($_POST['tambah'])) {

    $id_customer = $_POST['id_customer'];
    $nama = $_POST['nama_customer'];
    $toko = $_POST['nama_toko'];
    $alamat = $_POST['alamat'];
    $provinsi = $_POST['provinsi'];
    $negara = $_POST['negara'];
    $phone = $_POST['phone'];
    $ktp = $_POST['no_ktp'];
    $detail = $_POST['detail'];
    $maps_link = $_POST['maps_link'];

    mysqli_query($conn, "
        INSERT INTO tb_customer 
        (id_customer, nama_customer, nama_toko, alamat, provinsi, negara, phone, no_ktp, detail, maps_link)
        VALUES 
        ('$id_customer','$nama','$toko','$alamat','$provinsi','$negara','$phone','$ktp','$detail','$maps_link')
    ");

    echo "<script>alert('Customer berhasil ditambahkan');</script>";
}

// =====================
// SEARCH
// =====================
$keyword = $_GET['search'] ?? '';

$query = mysqli_query($conn, "
    SELECT * FROM tb_customer 
    WHERE nama_customer LIKE '%$keyword%' 
    OR nama_toko LIKE '%$keyword%'
    ORDER BY id DESC
");

$page_title = 'Data Customer';
$active_menu = 'customer';
$is_subfolder = true;
include '../assets/layout_header.php';
?>

<a href="<?= $base_url ?>dashboard.php" class="back-link">
    <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
</a>

<!-- SEARCH -->
<div class="card mb-24">
    <form method="GET" class="search-bar" style="margin-bottom:0;">
        <i class="bi bi-search" style="color:var(--text-muted);font-size:18px;"></i>
        <input type="text" name="search" placeholder="Cari nama customer atau toko..." value="<?= $keyword ?>">
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-search"></i> Cari
        </button>
    </form>
</div>

<!-- TABEL CUSTOMER -->
<div class="card mb-24">
    <div class="card-header">
        <h3><i class="bi bi-people-fill" style="color:var(--success)"></i> Daftar Customer</h3>
    </div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>ID Customer</th>
                    <th>Nama</th>
                    <th>Nama Toko</th>
                    <th>Alamat</th>
                    <th>Phone</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php 
            $no = 1;
            while($c = mysqli_fetch_assoc($query)) { 
            ?>
                <tr>
                    <td><?= $no++; ?></td>
                    <td><code style="background:var(--info-bg);color:var(--accent-1);padding:2px 8px;border-radius:4px;font-size:12px;"><?= $c['id_customer']; ?></code></td>
                    <td style="color:var(--text-primary); font-weight:500;"><?= $c['nama_customer']; ?></td>
                    <td><?= $c['nama_toko']; ?></td>
                    <td>
                        <div style="font-weight:500; color:var(--text-primary);"><?= $c['alamat']; ?></div>
                        <div style="font-size:12px; color:var(--text-muted);">
                            <?= $c['provinsi']; ?>, <?= $c['negara']; ?>
                        </div>
                        <?php 
                        $final_maps = !empty($c['maps_link']) ? $c['maps_link'] : "https://www.google.com/maps/search/" . urlencode($c['alamat'] . ' ' . $c['provinsi'] . ' ' . $c['negara']);
                        ?>
                        <a href="<?= $final_maps ?>" 
                           target="_blank" style="font-size:11px; color:var(--accent-1); text-decoration:none; display:inline-block; margin-top:4px;">
                            <i class="bi bi-geo-alt-fill"></i> Lihat di Maps
                        </a>
                    </td>
                    <td><?= $c['phone']; ?></td>
                    <td>
                        <a href="?detail=<?= $c['id']; ?><?= $keyword ? '&search='.$keyword : '' ?>" class="btn btn-outline btn-sm">
                            <i class="bi bi-eye"></i> Detail
                        </a>
                        <a href="?edit=<?= $c['id']; ?><?= $keyword ? '&search='.$keyword : '' ?>" class="btn btn-primary btn-sm" style="margin-left: 4px;">
                            <i class="bi bi-pencil"></i> Edit
                        </a>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<!-- DETAIL CUSTOMER + HISTORY -->
<?php if (isset($_GET['detail'])): 

$id = $_GET['detail'];

// ambil data customer
$data = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT * FROM tb_customer WHERE id='$id'
"));

// ambil history transaksi
$history = mysqli_query($conn, "
    SELECT * FROM transactions 
    WHERE customer_id='$id'
");
?>

<div class="card mb-24">
    <div class="card-header">
        <h3><i class="bi bi-person-badge" style="color:var(--accent-2)"></i> Detail Customer</h3>
    </div>

    <div class="detail-grid mb-24">
        <div class="detail-item">
            <span class="label">Nama</span>
            <span class="value"><?= $data['nama_customer']; ?></span>
        </div>
        <div class="detail-item">
            <span class="label">Toko</span>
            <span class="value"><?= $data['nama_toko']; ?></span>
        </div>
        <div class="detail-item">
            <span class="label">Alamat</span>
            <span class="value"><?= $data['alamat']; ?>, <?= $data['provinsi']; ?>, <?= $data['negara']; ?></span>
        </div>
        <div class="detail-item">
            <span class="label">Phone</span>
            <span class="value"><?= $data['phone']; ?></span>
        </div>
        <div class="detail-item">
            <span class="label">No KTP</span>
            <span class="value"><?= $data['no_ktp']; ?></span>
        </div>
        <div class="detail-item">
            <span class="label">Detail</span>
            <span class="value"><?= $data['detail']; ?></span>
        </div>
    </div>

    <h4 style="font-weight:600; margin-bottom:12px;">
        <i class="bi bi-clock-history" style="color:var(--warning)"></i> History Pembelian
    </h4>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>No Invoice</th>
                    <th>Tanggal</th>
                    <th>Tanggal Kirim</th>
                </tr>
            </thead>
            <tbody>
            <?php while($h = mysqli_fetch_assoc($history)) { ?>
                <tr>
                    <td><code style="background:var(--info-bg);color:var(--accent-1);padding:2px 8px;border-radius:4px;font-size:12px;"><?= $h['no_invoice']; ?></code></td>
                    <td><?= $h['tanggal']; ?></td>
                    <td><?= $h['tanggal_kirim']; ?></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php endif; ?>

<!-- FORM EDIT CUSTOMER -->
<?php if (isset($_GET['edit'])): 
$id_edit = $_GET['edit'];
$data_edit = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_customer WHERE id='$id_edit'"));
if ($data_edit):
?>
<div class="card mb-24" id="form-edit">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
        <h3 style="margin: 0;"><i class="bi bi-pencil-square" style="color:var(--warning)"></i> Edit Customer</h3>
        <a href="index_customer.php" class="btn btn-outline btn-sm"><i class="bi bi-x"></i> Batal</a>
    </div>

    <form method="POST">
        <input type="hidden" name="id" value="<?= $data_edit['id'] ?>">
        <div class="form-row">
            <div class="form-group">
                <label>ID Customer</label>
                <input type="text" name="id_customer" value="<?= $data_edit['id_customer'] ?>" required>
            </div>
            <div class="form-group">
                <label>Nama Customer</label>
                <input type="text" name="nama_customer" value="<?= $data_edit['nama_customer'] ?>" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Nama Toko</label>
                <input type="text" name="nama_toko" value="<?= $data_edit['nama_toko'] ?>">
            </div>
            <div class="form-group">
                <label>Phone</label>
                <input type="text" name="phone" value="<?= $data_edit['phone'] ?>">
            </div>
        </div>

        <div class="form-group">
            <label>Alamat</label>
            <input type="text" name="alamat" value="<?= $data_edit['alamat'] ?>">
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Provinsi</label>
                <input type="text" name="provinsi" value="<?= $data_edit['provinsi'] ?>">
            </div>
            <div class="form-group">
                <label>Negara</label>
                <input type="text" name="negara" value="<?= $data_edit['negara'] ?>">
            </div>
        </div>

        <div class="form-group">
            <label>No KTP</label>
            <input type="text" name="no_ktp" value="<?= $data_edit['no_ktp'] ?>">
        </div>

        <div class="form-group">
            <label>Link Google Maps (URL)</label>
            <input type="url" name="maps_link" value="<?= $data_edit['maps_link'] ?>" placeholder="https://maps.google.com/...">
        </div>

        <div class="form-group">
            <label>Detail Alamat / Catatan Tambahan</label>
            <textarea name="detail" placeholder="Contoh: Pagar hitam, dekat masjid, dll..."><?= $data_edit['detail'] ?></textarea>
        </div>

        <button name="edit_simpan" class="btn btn-primary full-width mt-16" style="background: var(--warning); border-color: var(--warning);">
            <i class="bi bi-save"></i> Update Customer
        </button>
    </form>
</div>
<script>
    document.getElementById('form-edit').scrollIntoView({ behavior: 'smooth' });
</script>
<?php 
endif;
endif; 
?>

<!-- FORM TAMBAH CUSTOMER -->
<div class="card">
    <div class="card-header">
        <h3><i class="bi bi-person-plus" style="color:var(--accent-1)"></i> Tambah Customer Baru</h3>
    </div>

    <form method="POST">
        <div class="form-row">
            <div class="form-group">
                <label>ID Customer</label>
                <input type="text" name="id_customer" required>
            </div>
            <div class="form-group">
                <label>Nama Customer</label>
                <input type="text" name="nama_customer" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Nama Toko</label>
                <input type="text" name="nama_toko">
            </div>
            <div class="form-group">
                <label>Phone</label>
                <input type="text" name="phone">
            </div>
        </div>

        <div class="form-group">
            <label>Alamat</label>
            <input type="text" name="alamat">
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Provinsi</label>
                <input type="text" name="provinsi">
            </div>
            <div class="form-group">
                <label>Negara</label>
                <input type="text" name="negara" value="INDONESIA">
            </div>
        </div>

        <div class="form-group">
            <label>No KTP</label>
            <input type="text" name="no_ktp">
        </div>

        <div class="form-group">
            <label>Link Google Maps (URL)</label>
            <input type="url" name="maps_link" placeholder="https://maps.google.com/...">
        </div>

        <div class="form-group">
            <label>Detail Alamat / Catatan Tambahan</label>
            <textarea name="detail" placeholder="Contoh: Pagar hitam, dekat masjid, dll..."></textarea>
        </div>

        <button name="tambah" class="btn btn-primary full-width mt-16">
            <i class="bi bi-check-circle"></i> Simpan Customer
        </button>
    </form>
</div>

<?php include '../assets/layout_footer.php'; ?>