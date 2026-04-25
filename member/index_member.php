<?php
session_start();
require_once '../config/koneksi.php';

if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// =====================
// TAMBAH MEMBER
// =====================
if (isset($_POST['tambah'])) {
    $user_id = $_POST['user_id'];
    $status = $_POST['status'];

    $cek = mysqli_query($conn, "SELECT id FROM tb_member WHERE user_id='$user_id'");
    if (mysqli_num_rows($cek) > 0) {
        mysqli_query($conn, "UPDATE tb_member SET status='$status' WHERE user_id='$user_id'");
    } else {
        mysqli_query($conn, "INSERT INTO tb_member (user_id, status) VALUES ('$user_id', '$status')");
    }
    echo "<script>alert('Member berhasil ditambahkan/diupdate!'); window.location='index_member.php';</script>";
}

// =====================
// EDIT MEMBER
// =====================
if (isset($_POST['edit_simpan'])) {
    $id = $_POST['id'];
    $status = $_POST['status'];

    mysqli_query($conn, "UPDATE tb_member SET status='$status' WHERE id='$id'");
    echo "<script>alert('Status member berhasil diupdate!'); window.location='index_member.php';</script>";
}

// =====================
// HAPUS MEMBER
// =====================
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM tb_member WHERE id='$id'");
    echo "<script>alert('Member berhasil dihapus!'); window.location='index_member.php';</script>";
}

$page_title = 'Kelola Member';
$active_menu = 'member';
$is_subfolder = true;
include '../assets/layout_header.php';

// Fetch users for dropdown (hanya user-Member yang dapat melihat semua produk katalog)
$users_dropdown = mysqli_query($conn, "SELECT id, username FROM tb_users WHERE role='user' ORDER BY username ASC");

// Fetch members for table
$query = "SELECT m.id as member_id, u.username, u.email, m.status 
          FROM tb_member m
          JOIN tb_users u ON m.user_id = u.id
          ORDER BY u.username ASC";
$result = mysqli_query($conn, $query);
?>

<a href="<?= $base_url ?>dashboard.php" class="back-link">
    <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
</a>

<div class="card mb-24">
    <div class="card-header">
        <h3><i class="bi bi-person-badge-fill" style="color:var(--accent-1)"></i> Daftar Member</h3>
    </div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th style="text-align:center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                <tr>
                    <td><?= $row['username'] ?></td>
                    <td><?= $row['email'] ?></td>
                    <td>
                        <?php if ($row['status'] == 'member') : ?>
                            <span class="badge" style="background:var(--success-bg); color:var(--success);">MEMBER</span>
                        <?php else : ?>
                            <span class="badge" style="background:var(--bg-secondary); color:var(--text-secondary);">STANDAR</span>
                        <?php endif; ?>
                    </td>
                    <td style="text-align:center;">
                        <a href="?edit=<?= $row['member_id'] ?>" class="btn btn-primary btn-sm">
                            <i class="bi bi-pencil"></i> Edit
                        </a>
                        <a href="?hapus=<?= $row['member_id'] ?>" class="btn btn-outline btn-sm" style="color:var(--danger); border-color:var(--danger);" onclick="return confirm('Hapus member ini?')">
                            <i class="bi bi-trash"></i> Hapus
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- FORM EDIT -->
<?php if(isset($_GET['edit'])): 
    $id_edit = $_GET['edit'];
    $data_edit = mysqli_fetch_assoc(mysqli_query($conn, "SELECT m.*, u.username FROM tb_member m JOIN tb_users u ON m.user_id = u.id WHERE m.id='$id_edit'"));
    if($data_edit):
?>
<div class="card mb-24" id="form-edit">
    <div class="card-header">
        <h3><i class="bi bi-pencil-square" style="color:var(--warning)"></i> Edit Status Member: <?= $data_edit['username'] ?></h3>
    </div>
    <div style="padding:24px;">
        <form method="POST">
            <input type="hidden" name="id" value="<?= $data_edit['id'] ?>">
            <div class="form-group">
                <label>Status Keanggotaan</label>
                <select name="status" class="form-control" required>
                    <option value="standar" <?= $data_edit['status'] == 'standar' ? 'selected' : '' ?>>STANDAR</option>
                    <option value="member" <?= $data_edit['status'] == 'member' ? 'selected' : '' ?>>MEMBER</option>
                </select>
            </div>
            <button type="submit" name="edit_simpan" class="btn btn-primary mt-16">
                <i class="bi bi-save"></i> Simpan Perubahan
            </button>
            <a href="index_member.php" class="btn btn-outline mt-16">Batal</a>
        </form>
    </div>
</div>
<script>document.getElementById('form-edit').scrollIntoView({ behavior: 'smooth' });</script>
<?php endif; endif; ?>

<!-- FORM TAMBAH -->
<div class="card">
    <div class="card-header">
        <h3><i class="bi bi-person-plus-fill" style="color:var(--accent-1)"></i> Tambah/Set Status Member</h3>
    </div>
    <div style="padding:24px;">
        <form method="POST">
            <div class="form-row">
                <div class="form-group">
                    <label>Pilih User</label>
                    <select name="user_id" required>
                        <option value="">- Pilih User -</option>
                        <?php while($u = mysqli_fetch_assoc($users_dropdown)): ?>
                            <option value="<?= $u['id'] ?>"><?= $u['username'] ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select name="status" required>
                        <option value="standar">STANDAR</option>
                        <option value="member">MEMBER</option>
                    </select>
                </div>
            </div>
            <button type="submit" name="tambah" class="btn btn-primary full-width mt-16">
                <i class="bi bi-check-circle"></i> Simpan Member
            </button>
        </form>
    </div>
</div>

<?php include '../assets/layout_footer.php'; ?>
