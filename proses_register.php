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

    // Otomatis buat profil di tb_customer
    // Cek dulu apakah email sudah ada di tb_customer
    $cek_cust = mysqli_query($conn, "SELECT id FROM tb_customer WHERE email='$email'");
    if (mysqli_num_rows($cek_cust) == 0) {
        $id_cust_baru = "CUST-" . time(); // generate ID customer unik
        mysqli_query($conn, "
            INSERT INTO tb_customer (id_customer, nama_customer, email)
            VALUES ('$id_cust_baru', '$username', '$email')
        ");
    }

    echo "Registrasi berhasil! <a href='login.php'>Login</a>";
}
?>