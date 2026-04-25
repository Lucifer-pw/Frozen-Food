<?php
session_start();
include 'config/koneksi.php';

$username = $_POST['username'];
$password = $_POST['password'];

$query = mysqli_query($conn, "SELECT * FROM tb_users WHERE username='$username'");
$data = mysqli_fetch_assoc($query);
if ($data && password_verify($password, $data['password'])) {
    $_SESSION['login'] = true;
    $_SESSION['role'] = $data['role'];
    $_SESSION['user_id'] = $data['id'];
    $_SESSION['username'] = $data['username'];
    $_SESSION['email'] = $data['email'];

    // Fetch membership status
    $q_member = mysqli_query($conn, "SELECT status FROM tb_member WHERE user_id='{$data['id']}'");
    $d_member = mysqli_fetch_assoc($q_member);
    $_SESSION['member_status'] = $d_member['status'] ?? 'standar';

    header("Location: dashboard.php");
} else {
    echo "<script>alert('Login gagal! Username atau password salah.'); window.location='login.php';</script>";
}