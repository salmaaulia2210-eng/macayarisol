<?php
session_start();
include 'koneksi.php';

$id_user = $_SESSION['id_user'] ?? null;
if (empty($id_user)) {
    header("Location: login.php?pesan=harus_login");
    exit;
}

$stmt = mysqli_prepare($koneksi, "SELECT id_transaksi, tanggal, total_harga, metode_bayar
                                   FROM tb_transaksi
                                   WHERE id_pelanggan = ?
                                   ORDER BY tanggal DESC, id_transaksi DESC");
mysqli_stmt_bind_param($stmt, "i", $id_user);
mysqli_stmt_execute($stmt);
$riwayat = mysqli_stmt_get_result($stmt);

$jumlah_keranjang = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
$sudah_login = isset($_SESSION['pelanggan']) || isset($_SESSION['admin']);
$nama_user   = $_SESSION['username'] ?? '';
?>
<!doctype html>
<html lang="id" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Riwayat Pesanan - Risol Macaya</title>

    <link href="assets/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@500;600;700&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <meta name="theme-color" content="#4aa9c9">
    <link href="carousel.css" rel="stylesheet">
    <style>
        .rm-riwayat-wrap { padding: 2.5rem 0 6rem; }
        .rm-riwayat-card {
            background-color: var(--rm-cream);
            border-radius: var(--rm-radius);
            box-shadow: var(--rm-shadow);
            overflow: hidden;
        }
        .rm-riwayat-table { margin-bottom: 0; }
        .rm-riwayat-table thead th {
            background-color: var(--rm-blue-100);
            color: var(--rm-blue-700);
            border: none;
            font-weight: 700;
            padding: 1rem 0.75rem;
        }
        .rm-riwayat-table td {
            vertical-align: middle;
            padding: 0.9rem 0.75rem;
            border-color: var(--rm-blue-100);
        }
        .rm-riwayat-id {
            font-weight: 700;
            color: var(--rm-blue-700);
        }
        .btn-rm-outline {
            border: 2px solid var(--rm-blue-400);
            color: var(--rm-blue-600);
            border-radius: 50rem;
            font-weight: 600;
            padding: 0.35rem 1.1rem;
            background: transparent;
            text-decoration: none;
            font-size: 0.85rem;
            display: inline-block;
        }
        .btn-rm-outline:hover { background-color: var(--rm-blue-400); color: #fff; }
        .rm-empty-riwayat {
            text-align: center;
            padding: 4rem 1rem;
        }
        .rm-empty-riwayat .rm-buy-icon {
            width: 4.5rem; height: 4.5rem; font-size: 2.2rem;
            border-radius: 50%; background-color: var(--rm-blue-100);
            color: var(--rm-blue-600); display: flex; align-items: center;
            justify-content: center; margin: 0 auto 1rem;
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
                <a class="nav-link position-relative" href="keranjang.php" title="Keranjang">
                  <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 13 12H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"/>
                  </svg>
                  <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"><?= $jumlah_keranjang ?></span>
                </a>
              </li>
              <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle d-flex align-items-center active" href="#" role="button"
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
                  <li><a class="dropdown-item active" href="riwayat.php">Riwayat Pesanan</a></li>
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
        <div class="container rm-riwayat-wrap">
            <h2 class="rm-section-title">Riwayat Pesanan</h2>

            <?php if (mysqli_num_rows($riwayat) > 0) { ?>
                <div class="rm-riwayat-card">
                    <table class="table rm-riwayat-table">
                        <thead>
                            <tr class="text-center">
                                <th class="text-start">No. Struk</th>
                                <th>Tanggal</th>
                                <th>Metode Bayar</th>
                                <th>Total Bayar</th>
                                <th>Invoice</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($trx = mysqli_fetch_assoc($riwayat)) {
                                $no_struk = 'RM-' . str_pad($trx['id_transaksi'], 5, '0', STR_PAD_LEFT);
                            ?>
                                <tr>
                                    <td class="rm-riwayat-id">#<?= htmlspecialchars($no_struk) ?></td>
                                    <td class="text-center"><?= date('d F Y', strtotime($trx['tanggal'])) ?></td>
                                    <td class="text-center"><?= htmlspecialchars($trx['metode_bayar'] ?? '-') ?></td>
                                    <td class="text-center">Rp <?= number_format($trx['total_harga'], 0, ',', '.') ?></td>
                                    <td class="text-center">
                                        <a href="cetak.php?id=<?= (int) $trx['id_transaksi'] ?>" class="btn-rm-outline" target="_blank">
                                            Lihat &amp; Cetak
                                        </a>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            <?php } else { ?>
                <div class="rm-empty-riwayat">
                    <p class="lead mb-3">Belum ada riwayat pesanan</p>
                    <a href="carousel.php#produk" class="btn btn-rm-primary">Yuk Belanja Sekarang</a>
                </div>
            <?php } ?>

        </div>

        <footer class="rm-footer">
            <div class="container d-flex flex-wrap justify-content-between align-items-center">
                <p class="mb-0">Semua rasa, semua suka.</p>
            </div>
        </footer>
    </main>

    <script src="assets/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>