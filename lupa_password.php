<?php
session_start();
require_once 'config/koneksi.php';

$message = "";
$token_generated = "";

if (isset($_POST['submit'])) {
    $email = mysqli_real_escape_with_htmlspecialchars($conn, $_POST['email']);
    
    // Cek email
    $query = mysqli_query($conn, "SELECT * FROM tb_users WHERE email='$email'");
    if (mysqli_num_rows($query) > 0) {
        $token = bin2hex(random_bytes(32));
        $expires = date("Y-m-d H:i:s", strtotime("+1 hour"));
        
        mysqli_query($conn, "UPDATE tb_users SET reset_token='$token', reset_expires='$expires' WHERE email='$email'");
        
        $message = "success";
        $token_generated = $token;
    } else {
        $message = "error";
    }
}

// Helper function to escape and sanitize
function mysqli_real_escape_with_htmlspecialchars($conn, $str) {
    return mysqli_real_escape_string($conn, htmlspecialchars($str));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password — FrozenHub</title>
    <link rel="stylesheet" href="assets/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>

<div class="auth-wrapper">
    <div class="auth-card">
        <div class="logo"><i class="bi bi-key-fill"></i></div>
        <h2>Lupa Password</h2>
        <p class="subtitle">Masukkan email Anda untuk verifikasi reset password</p>

        <?php if ($message == "success"): ?>
            <div class="info-box success mb-16">
                <i class="bi bi-check-circle"></i>
                <div>
                    Permintaan berhasil! Dalam sistem nyata, link reset dikirim ke email Anda. 
                    <br><br>
                    <strong>Simulasi Link:</strong><br>
                    <a href="reset_password.php?token=<?= $token_generated ?>" style="color:var(--accent-1); font-weight:600;">Klik di sini untuk Reset Password</a>
                </div>
            </div>
        <?php elseif ($message == "error"): ?>
            <div class="info-box danger mb-16">
                <i class="bi bi-exclamation-triangle"></i>
                <div>Email tidak terdaftar di sistem kami.</div>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="input-group">
                <input type="email" name="email" placeholder="Email Terdaftar" required>
                <i class="bi bi-envelope"></i>
            </div>

            <button type="submit" name="submit">
                <i class="bi bi-send"></i> Kirim Verifikasi
            </button>
        </form>

        <div class="auth-footer">
            Ingat password? <a href="login.php">Kembali ke Login</a>
        </div>
    </div>
</div>

</body>
</html>
