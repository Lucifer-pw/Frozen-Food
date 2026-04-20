<?php
include 'config/koneksi.php';

$username = $_POST['username'];
$email    = $_POST['email'];
$password = $_POST['password'];

// HASH PASSWORD
$hash = password_hash($password, PASSWORD_DEFAULT);

// role default = user
$role = 'user';

// cek username sudah ada atau belum
$cek = mysqli_query($conn, "SELECT * FROM tb_users WHERE username='$username'");

if (mysqli_num_rows($cek) > 0) {
    echo "Username sudah digunakan!";
} else {
    mysqli_query($conn, "
        INSERT INTO tb_users (username, password, email, role)
        VALUES ('$username', '$hash', '$email', '$role')
    ");

    echo "Registrasi berhasil! <a href='login.php'>Login</a>";
}
?>