<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar — FrozenHub</title>
    <link rel="stylesheet" href="assets/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>

<div class="auth-wrapper">
    <div class="auth-card">
        <div class="logo"><i class="bi bi-snow2"></i></div>
        <h2>Buat Akun Baru</h2>
        <p class="subtitle">Daftar untuk mengakses FrozenHub</p>

        <form action="proses_register.php" method="POST">
            <div class="input-group">
                <input type="text" name="username" placeholder="Username" required>
                <i class="bi bi-person"></i>
            </div>
            <div class="input-group">
                <input type="email" name="email" placeholder="Email" required>
                <i class="bi bi-envelope"></i>
            </div>
            <div class="input-group">
                <input type="password" name="password" placeholder="Password" required>
                <i class="bi bi-lock"></i>
            </div>

            <button type="submit">
                <i class="bi bi-person-plus"></i> Daftar
            </button>
        </form>

        <div class="auth-footer">
            Sudah punya akun? <a href="login.php">Login di sini</a>
        </div>
    </div>
</div>

</body>
</html>