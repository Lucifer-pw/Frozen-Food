<?php
session_start();
require_once 'config/koneksi.php';

$message = "";
$valid_token = false;
$token = $_GET['token'] ?? '';

if (!empty($token)) {
    $now = date("Y-m-d H:i:s");
    $query = mysqli_query($conn, "SELECT * FROM tb_users WHERE reset_token='$token' AND reset_expires > '$now'");
    if (mysqli_num_rows($query) > 0) {
        $valid_token = true;
        $user_data = mysqli_fetch_assoc($query);
    }
}

if (isset($_POST['reset'])) {
    $password = $_POST['password'];
    $confirm  = $_POST['confirm_password'];
    
    if ($password !== $confirm) {
        $message = "mismatch";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        mysqli_query($conn, "UPDATE tb_users SET password='$hashed_password', reset_token=NULL, reset_expires=NULL WHERE reset_token='$token'");
        $message = "success";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password — FrozenHub</title>
    <link rel="stylesheet" href="assets/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>

<div class="auth-wrapper">
    <div class="auth-card">
        <div class="logo"><i class="bi bi-shield-lock-fill"></i></div>
        <h2>Reset Password</h2>
        
        <?php 
        if (!$valid_token && $message != "success"): ?>
            <div class="info-box danger mb-24">
                <i class="bi bi-x-circle"></i>
                <div>Link verifikasi tidak valid atau telah kadaluarsa. Silakan ajukan ulang permintaan lupa password.</div>
            </div>
            <a href="lupa_password.php" class="btn btn-outline full-width">Lupa Password Lagi</a>
        <?php 
        elseif ($message == "success"): ?>
            <div class="info-box success mb-24">
                <i class="bi bi-check-circle"></i>
                <div>Password berhasil diubah! Silakan login dengan password baru Anda.</div>
            </div>
            <a href="login.php" class="btn btn-primary full-width">Login Sekarang</a>
        <?php 
        else: ?>
            <p class="subtitle">Buat password baru untuk akun Anda</p>

            <?php 
            if ($message == "mismatch"): ?>
                <div class="info-box danger mb-16">
                    <i class="bi bi-exclamation-triangle"></i>
                    <div>Konfirmasi password tidak cocok!</div>
                </div>
            <?php 
            endif; ?>

            <form method="POST">
                <div class="input-group">
                    <input type="password" name="password" placeholder="Password Baru" required>
                    <i class="bi bi-lock"></i>
                </div>
                <div class="input-group">
                    <input type="password" name="confirm_password" placeholder="Konfirmasi Password" required>
                    <i class="bi bi-lock-fill"></i>
                </div>

                <button type="submit" name="reset">
                    <i class="bi bi-check2-circle"></i> Ubah Password
                </button>
            </form>
        <?php 
        endif; ?>
    </div>
</div>

</body>
</html>
