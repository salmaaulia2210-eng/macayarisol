<?php
session_start();
include 'koneksi.php';

$id = $_SESSION['id_user'] ?? null;

if (isset($_POST["add"])) {
    $id_produk = $_GET["id"];
    $jumlah_baru = max(1, (int) $_POST["jumlah"]);

    if (isset($_SESSION["cart"][$id_produk])) {
        $_SESSION["cart"][$id_produk]['jumlah'] += $jumlah_baru;
    } else {
        $_SESSION["cart"][$id_produk] = array(
            'id' => $id_produk,
            'nama' => $_POST["hidden_nama"],
            'harga' => $_POST["hidden_harga"],
            'foto' => $_POST["hidden_foto"],
            'jumlah' => $jumlah_baru
        );
    }
    header("Location: keranjang.php");
    exit;
}

$pesan_hapus = false;
if (isset($_GET["aksi"])) {
    if ($_GET["aksi"] == "hapus") {
        $id_produk = $_GET["id"];
        if (isset($_SESSION["cart"][$id_produk])) {
            unset($_SESSION["cart"][$id_produk]);
            $pesan_hapus = true;
        }
    }
}

$metode_bayar_toko = 'COD (Bayar di Tempat)';

if (isset($_POST["beli"])) {
    if (empty($id)) {
        header("Location: login.php?pesan=harus_login");
        exit;
    }

    if (empty($_SESSION["cart"])) {
        header("Location: keranjang.php");
        exit;
    }

    $metode_bayar = $metode_bayar_toko;

    $total = 0;
    foreach ($_SESSION["cart"] as $value) {
        $total += ($value["jumlah"] * $value["harga"]);
    }

    $stmt = mysqli_prepare($koneksi, "INSERT INTO tb_transaksi(tanggal, id_pelanggan, total_harga, metode_bayar) VALUES (?, ?, ?, ?)");
    $tanggal = date("Y-m-d");

    mysqli_stmt_bind_param($stmt, "siis", $tanggal, $id, $total, $metode_bayar);
    mysqli_stmt_execute($stmt);
    $id_transaksi = mysqli_insert_id($koneksi);

    $stmt2 = mysqli_prepare($koneksi, "INSERT INTO tb_detail(id_transaksi, id_produk, jumlah) VALUES (?, ?, ?)");
    foreach ($_SESSION["cart"] as $value) {
        $id_produk = $value['id'];
        $jumlah = $value['jumlah'];
        mysqli_stmt_bind_param($stmt2, "iii", $id_transaksi, $id_produk, $jumlah);
        mysqli_stmt_execute($stmt2);
    }

    unset($_SESSION["cart"]);
    header("Location: cetak.php?id=" . $id_transaksi);
    exit;
}

$jumlah_keranjang = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
$sudah_login = isset($_SESSION['pelanggan']) || isset($_SESSION['admin']);
$nama_user   = $_SESSION['username'] ?? '';
?>
<!doctype html>
<html lang="id" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Keranjang - Risol Macaya</title>

    <link href="assets/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@500;600;700&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <meta name="theme-color" content="#4aa9c9">
    <link href="carousel.css" rel="stylesheet">
    <style>
        .rm-cart-wrap { padding: 2.5rem 0 6rem; }
        .rm-cart-card {
            background-color: var(--rm-cream);
            border-radius: var(--rm-radius);
            box-shadow: var(--rm-shadow);
            overflow: hidden;
        }
        .rm-cart-table { margin-bottom: 0; }
        .rm-cart-table thead th {
            background-color: var(--rm-blue-100);
            color: var(--rm-blue-700);
            border: none;
            font-weight: 700;
            padding: 1rem 0.75rem;
        }
        .rm-cart-table td {
            vertical-align: middle;
            padding: 0.9rem 0.75rem;
            border-color: var(--rm-blue-100);
        }
        .rm-cart-thumb {
            width: 64px;
            height: 64px;
            object-fit: cover;
            border-radius: 0.9rem;
            margin-right: 0.75rem;
        }
        .rm-cart-produk { display: flex; align-items: center; }
        .rm-cart-produk span { font-weight: 600; }
        .rm-badge-qty {
            background-color: var(--rm-blue-100);
            color: var(--rm-blue-700);
            border-radius: 50rem;
            padding: 0.25rem 0.75rem;
            font-weight: 700;
        }
        .rm-btn-hapus {
            border: 2px solid #ffc2cf;
            color: #d6486a;
            background: transparent;
            border-radius: 50rem;
            padding: 0.3rem 0.9rem;
            font-weight: 600;
            font-size: 0.85rem;
            text-decoration: none;
            display: inline-block;
        }
        .rm-btn-hapus:hover { background-color: #ffc2cf; color: #7a1f34; }
        .rm-total-row {
            background-color: var(--rm-blue-50);
            font-weight: 700;
        }
        .rm-empty-cart {
            text-align: center;
            padding: 4rem 1rem;
        }
        .rm-empty-cart .rm-buy-icon {
            width: 4.5rem; height: 4.5rem; font-size: 2.2rem;
            border-radius: 50%; background-color: var(--rm-blue-100);
            color: var(--rm-blue-600); display: flex; align-items: center;
            justify-content: center; margin: 0 auto 1rem;
        }
        .rm-cart-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 0.4rem;
        }
        .btn-rm-outline {
            border: 2px solid var(--rm-blue-400);
            color: var(--rm-blue-600);
            border-radius: 50rem;
            font-weight: 600;
            padding: 0.5rem 1.25rem;
            background: transparent;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
        }
        .btn-rm-outline:hover { background-color: var(--rm-blue-400); color: #fff; }

        .rm-bayar-card {
            background-color: var(--rm-cream);
            border-radius: var(--rm-radius);
            box-shadow: var(--rm-shadow);
            padding: 1.75rem;
            margin-top: 1.5rem;
        }
        .rm-bayar-card h5 {
            color: var(--rm-blue-700);
            font-weight: 700;
            margin-bottom: 1rem;
        }
        .rm-bayar-total {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
            border-top: 1px dashed var(--rm-blue-200);
            padding-top: 1.25rem;
        }
        .rm-bayar-total .rm-jumlah {
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--rm-blue-700);
        }
    </style>
</head>
<body>

    <header>
      <nav class="navbar navbar-expand-lg site-navbar" data-bs-theme="dark">
        <div class="container">
          <a class="navbar-brand" href="carousel.php">Risol Macaya</a>
          <button class="navbar-toggler" type="button"
                  data-bs-toggle="collapse" data-bs-target="#navbarMenu"
                  aria-controls="navbarMenu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>
          <div class="collapse navbar-collapse" id="navbarMenu">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
              <li class="nav-item"><a class="nav-link" href="carousel.php">Beranda</a></li>
              <li class="nav-item"><a class="nav-link" href="carousel.php#produk">Produk</a></li>
            </ul>
            <ul class="navbar-nav align-items-lg-center gap-lg-2">
              <li class="nav-item">
                <a class="nav-link position-relative active" href="keranjang.php" title="Keranjang">
                  <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 13 12H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"/>
                  </svg>
                  <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"><?= $jumlah_keranjang ?></span>
                </a>
              </li>
              <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button"
                  data-bs-toggle="dropdown" aria-expanded="false">
                  <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" class="me-1" viewBox="0 0 16 16">
                    <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/>
                    <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1z"/>
                  </svg>
                  <?= $sudah_login ? htmlspecialchars($nama_user) : 'Akun' ?>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                  <?php if ($sudah_login) { ?>
                  <?php if (isset($_SESSION['admin'])) { ?>
                  <li><a class="dropdown-item" href="dashboard.php">Dashboard Admin</a></li>
                  <li><hr class="dropdown-divider"></li>
                  <?php } ?>
                  <li><a class="dropdown-item" href="profil_user.php">Profil Saya</a></li>
                  <li><a class="dropdown-item" href="riwayat.php">Riwayat Pesanan</a></li>
                  <li><hr class="dropdown-divider"></li>
                  <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                  <?php } else { ?>
                  <li><a class="dropdown-item" href="login.php">Login</a></li>
                  <li><a class="dropdown-item" href="register.php">Daftar</a></li>
                  <?php } ?>
                </ul>
              </li>
            </ul>
          </div>
        </div>
      </nav>
    </header>

    <main>
        <div class="container rm-cart-wrap">
            <div class="rm-cart-top">
                <h2 class="rm-section-title mb-0">Keranjangmu</h2>
                <?php if (!empty($_SESSION["cart"])) { ?>
                <a href="carousel.php#produk" class="btn-rm-outline">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4Z"/>
                        <path d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8Zm8-7a7 7 0 1 0 0 14A7 7 0 0 0 8 1Z"/>
                    </svg>
                    Tambah Barang Lain
                </a>
                <?php } ?>
            </div>

            <?php if ($pesan_hapus) { ?>
              <div class="alert alert-success rounded-4" role="alert">
                Produk berhasil dihapus dari keranjang.
              </div>
            <?php } ?>

            <?php if (!empty($_SESSION["cart"])) { ?>
                <div class="rm-cart-card">
                    <table class="table rm-cart-table">
                        <thead>
                            <tr class="text-center">
                                <th class="text-start">Produk</th>
                                <th>Jumlah</th>
                                <th>Harga Satuan</th>
                                <th>Total</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $total = 0;
                            foreach ($_SESSION["cart"] as $key => $value) {
                            ?>
                                <tr>
                                    <td>
                                        <div class="rm-cart-produk">
                                            <img src="img/<?= htmlspecialchars($value["foto"]) ?>" class="rm-cart-thumb" alt="<?= htmlspecialchars($value["nama"]) ?>">
                                            <span><?= htmlspecialchars($value["nama"]) ?></span>
                                        </div>
                                    </td>
                                    <td class="text-center"><span class="rm-badge-qty"><?= $value["jumlah"] ?></span></td>
                                    <td class="text-center">Rp <?= number_format($value["harga"], 0, ',', '.') ?></td>
                                    <td class="text-center rm-price">Rp <?= number_format($value["jumlah"] * $value["harga"], 0, ',', '.') ?></td>
                                    <td class="text-center">
                                        <a href="keranjang.php?aksi=hapus&id=<?= $value["id"]; ?>"
                                           class="rm-btn-hapus"
                                           onclick="return confirm('Hapus produk ini dari keranjang?');">Hapus</a>
                                    </td>
                                </tr>
                            <?php
                                $total = $total + ($value["jumlah"] * $value["harga"]);
                            }
                            ?>
                            <tr class="rm-total-row">
                                <td colspan="3" class="text-end">Total</td>
                                <td class="text-center">Rp <?= number_format($total, 0, ',', '.') ?></td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <?php if ($id) { ?>
                <div class="rm-bayar-card">
                    <h5>Pembayaran</h5>
                    <p class="mb-4">Toko ini cuma nerima <strong>COD (Bayar di Tempat)</strong> bayar tunai langsung pas produk diterima/diambil.</p>
                    <form method="POST">
                        <div class="rm-bayar-total">
                            <div>
                                <div>Total Bayar</div>
                                <div class="rm-jumlah">Rp <?= number_format($total, 0, ',', '.') ?></div>
                            </div>
                            <button type="submit" name="beli" class="btn btn-rm-primary">Beli Sekarang</button>
                        </div>
                    </form>
                </div>
                <?php } else { ?>
                <div class="rm-bayar-card text-center">
                    <p class="mb-3">Login dulu yuk buat lanjut ke pembayaran.</p>
                    <a href="login.php" class="btn btn-rm-primary">Login untuk Beli</a>
                </div>
                <?php } ?>
            <?php } else { ?>
                <div class="rm-empty-cart">
                    <p class="lead mb-3">Keranjangmu masih kosong</p>
                    <a href="carousel.php#produk" class="btn btn-rm-primary">Yuk pilih risol</a>
                </div>
            <?php } ?>

        </div>

        <footer class="rm-footer">
            <div class="container d-flex flex-wrap justify-content-between align-items-center">
                <p class="mb-0"> Semua rasa, semua suka.</p>
            </div>
        </footer>
    </main>

    <script src="assets/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>