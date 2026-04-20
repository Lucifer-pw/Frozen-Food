<?php
$conn = mysqli_connect("localhost", "root", "", "dbfrozen_food");

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>