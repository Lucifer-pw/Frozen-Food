<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — FrozenHub</title>
    <link rel="stylesheet" href="assets/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>

<div class="auth-wrapper">
    <div class="auth-card">
        <div class="logo"><i class="bi bi-snow2"></i></div>
        <h2>Selamat Datang</h2>
        <p class="subtitle">Masuk ke akun FrozenHub Anda</p>

        <form action="proses_login.php" method="POST">
            <div class="input-group">
                <input type="text" name="username" placeholder="Username" required>
                <i class="bi bi-person"></i>
            </div>
            <div class="input-group">
                <input type="password" name="password" placeholder="Password" required>
                <i class="bi bi-lock"></i>
            </div>

            <button type="submit">
                <i class="bi bi-box-arrow-in-right"></i> Login
            </button>
        </form>

        <div class="auth-footer">
            Belum punya akun? <a href="register.php">Daftar di sini</a>
        </div>
    </div>
</div>

</body>
</html>