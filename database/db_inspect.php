<?php
include 'config/koneksi.php';
$tables = ['tb_users', 'tb_customer', 'tb_products', 'transactions', 'transaction_detail', 'tb_shipper'];
foreach ($tables as $table) {
    echo "TABLE: $table\n";
    $result = mysqli_query($conn, "SHOW COLUMNS FROM $table");
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            echo " - {$row['Field']} ({$row['Type']})\n";
        }
    } else {
        echo " - Not found or error\n";
    }
    echo "\n";
}
?>
