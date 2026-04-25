<?php
session_start();
require_once '../config/koneksi.php';

if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'user') {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// PROSES TAMBAH KERANJANG (BULK)
if (isset($_POST['bulk_add_to_cart'])) {
    $qtys = $_POST['qtys']; // Array: [product_id => qty]
    $items_added = 0;
    $errors = [];

    foreach ($qtys as $product_id => $qty) {
        $qty = (int)$qty;
        if ($qty <= 0) continue;

        // Cek stok produk
        $q_produk = mysqli_query($conn, "SELECT name_product, stock FROM tb_products WHERE id_Unique='$product_id'");
        $d_produk = mysqli_fetch_assoc($q_produk);

        if ($qty > $d_produk['stock']) {
            $errors[] = "Stok {$d_produk['name_product']} tidak mencukupi (Tersedia: {$d_produk['stock']})";
            continue;
        }

        // Cek apakah produk sudah ada di keranjang
        $cek_cart = mysqli_query($conn, "SELECT id, qty FROM tb_cart WHERE user_id='$user_id' AND product_id='$product_id'");
        if (mysqli_num_rows($cek_cart) > 0) {
            $d_cart = mysqli_fetch_assoc($cek_cart);
            $new_qty = $d_cart['qty'] + $qty;
            
            if ($new_qty > $d_produk['stock']) {
                $errors[] = "Total {$d_produk['name_product']} di keranjang melebihi stok.";
            } else {
                mysqli_query($conn, "UPDATE tb_cart SET qty='$new_qty' WHERE id='{$d_cart['id']}'");
                $items_added++;
            }
        } else {
            mysqli_query($conn, "INSERT INTO tb_cart (user_id, product_id, qty) VALUES ('$user_id', '$product_id', '$qty')");
            $items_added++;
        }
    }

    if (!empty($errors)) {
        $err_msg = implode("\\n", $errors);
        echo "<script>alert('$err_msg');</script>";
    }
    
    if ($items_added > 0) {
        echo "<script>alert('$items_added produk berhasil dimasukkan ke keranjang!'); window.location='keranjang.php';</script>";
    }
}

$page_title = 'Katalog Produk';
$active_menu = 'katalog';
$is_subfolder = true;
include '../assets/layout_header.php';

// SEARCH KEYWORD
$keyword = $_GET['search'] ?? '';
?>

<style>
/* Menghilangkan spinner (panah up/down) pada input number */
input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}
input[type=number] {
  -moz-appearance: textfield;
}

.qty-input {
    width: 80px;
    padding: 8px;
    border-radius: 8px;
    border: 1px solid var(--border-color);
    background: var(--bg-secondary);
    color: var(--text-primary);
    text-align: center;
    font-weight: 600;
    transition: all 0.2s;
}

.qty-input:focus {
    border-color: var(--accent-1);
    background: var(--bg-primary);
    outline: none;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.qty-input.active {
    border-color: var(--accent-1);
    background: rgba(59, 130, 246, 0.05);
}

.bulk-footer {
    position: sticky;
    bottom: 20px;
    left: 0;
    right: 0;
    display: flex;
    justify-content: center;
    z-index: 100;
    pointer-events: none;
}

.bulk-btn {
    pointer-events: auto;
    padding: 12px 32px;
    border-radius: 50px;
    font-weight: 700;
    font-size: 16px;
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3), 0 8px 10px -6px rgba(0, 0, 0, 0.3);
    display: flex;
    align-items: center;
    gap: 10px;
    transform: translateY(0);
    transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.bulk-btn:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 30px -10px rgba(59, 130, 246, 0.5);
}

/* MODAL POPUP STYLES */
.modal-zoom {
    display: none;
    position: fixed;
    z-index: 9999;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.85);
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
}

.modal-popup-card {
    position: relative;
    background: #1e293b;
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 16px;
    width: 90%;
    max-width: 500px;
    margin: 10vh auto;
    overflow: hidden;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
    animation: slideDownPop 0.3s ease-out;
}

@keyframes slideDownPop {
    from { transform: translateY(-30px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}

.modal-popup-header {
    padding: 16px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    background: rgba(255, 255, 255, 0.05);
}

.modal-popup-header h3 {
    margin: 0;
    font-size: 16px;
    font-weight: 600;
    color: #38bdf8;
}

.modal-popup-close {
    font-size: 28px;
    cursor: pointer;
    color: #94a3b8;
    line-height: 1;
    transition: all 0.2s;
}

.modal-popup-close:hover {
    color: #f87171;
}

.modal-popup-body {
    padding: 0;
    background: #000;
    display: flex;
    justify-content: center;
}

.modal-popup-body img {
    width: 100%;
    height: auto;
    max-height: 70vh;
    display: block;
    object-fit: contain;
}

.zoomable-image {
    cursor: zoom-in;
    transition: transform 0.2s;
}

.zoomable-image:hover {
    transform: scale(1.1);
}
</style>

<a href="<?= $base_url ?>dashboard.php" class="back-link">
    <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
</a>

<div class="card mb-24">
    <div class="card-header" style="display:flex; justify-content:space-between; align-items:center;">
        <h3 style="margin:0;"><i class="bi bi-shop" style="color:var(--accent-2)"></i> Katalog Frozen Food</h3>
        <div style="display:flex; gap:12px;">
            <!-- Form Pencarian -->
            <form method="GET" style="display:flex; gap:8px;">
                <input type="text" name="search" id="search-input" placeholder="Cari produk..." value="<?= htmlspecialchars($keyword) ?>" 
                       style="padding:6px 12px; border-radius:8px; border:1px solid var(--border-color); background:var(--bg-secondary); color:var(--text-primary); outline:none; font-size:14px; width:200px;" autocomplete="off">
                <button type="submit" class="btn btn-outline btn-sm">
                    <i class="bi bi-search"></i>
                </button>
            </form>
            <a href="keranjang.php" class="btn btn-outline btn-sm">
                <i class="bi bi-cart3"></i> Keranjang
            </a>
        </div>
    </div>

    <form method="POST" id="bulk-form">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th style="width:80px;">Foto</th>
                        <th>Nama Produk</th>
                        <th>Harga</th>
                        <th>Stok</th>
                        <th style="width:120px; text-align:center;">Beli Qty</th>
                    </tr>
                </thead>
                <tbody id="catalog-body">
                <?php
                $member_status = $_SESSION['member_status'] ?? 'standar';
                $member_only_ids = "28,29,30,31,32,33";

                $query_sql = "SELECT * FROM tb_products WHERE stock > 0";
                
                // Jika bukan member, sembunyikan barang tertentu
                if ($member_status !== 'member') {
                    $query_sql .= " AND id_Unique NOT IN ($member_only_ids)";
                }

                if (!empty($keyword)) {
                    $query_sql .= " AND name_product LIKE '%$keyword%'";
                }
                $query_sql .= " ORDER BY name_product ASC";
                
                $produk = mysqli_query($conn, $query_sql);
                
                if (mysqli_num_rows($produk) == 0) {
                    echo "<tr><td colspan='5' style='text-align:center; padding:32px; color:var(--text-secondary);'>Produk tidak ditemukan.</td></tr>";
                }

                while ($p = mysqli_fetch_assoc($produk)) {
                ?>
                    <tr>
                        <td>
                            <?php if($p['image']): ?>
                                <img src="../assets/img/produk/<?= $p['image'] ?>" 
                                     alt="Produk" 
                                     class="zoomable-image"
                                     style="width:60px; height:60px; object-fit:cover; border-radius:8px; border:1px solid var(--border-color);"
                                     onclick="openModal(this.src, '<?= addslashes($p['name_product']) ?>')">
                            <?php else: ?>
                                <div style="width:60px; height:60px; background:var(--bg-secondary); border-radius:8px; display:flex; align-items:center; justify-content:center; border:1px dashed var(--border-color);">
                                    <i class="bi bi-image" style="color:var(--text-muted); font-size:20px;"></i>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td style="font-weight:600; color:var(--text-primary);"><?= $p['name_product'] ?></td>
                        <td style="color:var(--success); font-weight:600;">Rp <?= number_format($p['price'],0,',','.') ?></td>
                        <td>
                            <span class="badge" style="background:var(--info-bg); color:var(--accent-1);"><?= $p['stock'] ?></span>
                        </td>
                        <td style="text-align:center;">
                            <input type="number" name="qtys[<?= $p['id_Unique'] ?>]" 
                                   class="qty-input" value="0" min="0" max="<?= $p['stock'] ?>"
                                   onchange="toggleActive(this)">
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>

        <div class="bulk-footer">
            <button type="submit" name="bulk_add_to_cart" class="btn btn-primary bulk-btn">
                <i class="bi bi-cart-plus-fill"></i> Masukkan ke Keranjang
            </button>
        </div>
    </form>
</div>

<!-- IMAGE ZOOM MODAL (POPUP CARD) -->
<div id="imageModal" class="modal-zoom">
    <div class="modal-popup-card">
        <div class="modal-popup-header">
            <h3 id="caption">Detail Produk</h3>
            <span class="modal-popup-close" onclick="closeModal()">&times;</span>
        </div>
        <div class="modal-popup-body">
            <img id="imgFull">
        </div>
    </div>
</div>

<script>
function openModal(src, name) {
    const modal = document.getElementById("imageModal");
    const modalImg = document.getElementById("imgFull");
    const captionText = document.getElementById("caption");
    
    modal.style.display = "block";
    modalImg.src = src;
    captionText.innerHTML = name;
    document.body.style.overflow = 'hidden'; // Disable scroll
}

function closeModal() {
    document.getElementById("imageModal").style.display = "none";
    document.body.style.overflow = 'auto'; // Enable scroll
}

// Close modal when clicking outside the card
window.onclick = function(event) {
    const modal = document.getElementById("imageModal");
    if (event.target == modal) {
        closeModal();
    }
}

// Close modal with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === "Escape") {
        closeModal();
    }
});

function toggleActive(input) {
    if (parseInt(input.value) > 0) {
        input.classList.add('active');
    } else {
        input.classList.remove('active');
    }
}

// Mencegah scroll mouse mengubah angka pada input number
document.addEventListener("wheel", function(event) {
    if (document.activeElement.type === "number") {
        document.activeElement.blur();
    }
});

// Fitur Live Search
document.getElementById('search-input').addEventListener('keyup', function() {
    let keyword = this.value.toLowerCase();
    let rows = document.querySelectorAll('#catalog-body tr');
    let found = false;

    rows.forEach(row => {
        // Skip row "no-data"
        if (row.id === 'no-data-row') return;
        
        let productName = row.cells[1].textContent.toLowerCase();
        if (productName.includes(keyword)) {
            row.style.display = "";
            found = true;
        } else {
            row.style.display = "none";
        }
    });

    // Cek jika tidak ada produk yang ditemukan
    let existingNoData = document.getElementById('no-data-row');
    if (!found) {
        if (!existingNoData) {
            let noDataRow = document.createElement('tr');
            noDataRow.id = 'no-data-row';
            noDataRow.innerHTML = `<td colspan="5" style="text-align:center; padding:32px; color:var(--text-secondary);">Produk tidak ditemukan.</td>`;
            document.getElementById('catalog-body').appendChild(noDataRow);
        }
    } else {
        if (existingNoData) {
            existingNoData.remove();
        }
    }
});
</script>

<?php include '../assets/layout_footer.php'; ?>
