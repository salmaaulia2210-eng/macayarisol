<?php
session_start();
include 'koneksi.php';

$id_user = $_SESSION['id_user'] ?? null;
if (empty($id_user)) {
    header("Location: login.php?pesan=harus_login");
    exit;
}

$id_transaksi = (int) ($_GET['id'] ?? 0);

$stmt = mysqli_prepare($koneksi, "SELECT t.*, u.nama AS nama_pelanggan, u.alamat, u.hp
                                   FROM tb_transaksi t
                                   JOIN tb_user u ON u.id = t.id_pelanggan
                                   WHERE t.id_transaksi = ?");
mysqli_stmt_bind_param($stmt, "i", $id_transaksi);
mysqli_stmt_execute($stmt);
$transaksi = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

$is_admin = isset($_SESSION['admin']);
if (!$transaksi || (!$is_admin && (int) $transaksi['id_pelanggan'] !== (int) $id_user)) {
    header("Location: carousel.php");
    exit;
}

$stmt2 = mysqli_prepare($koneksi, "SELECT d.jumlah, p.nama, p.harga, p.foto
                                    FROM tb_detail d
                                    JOIN tb_produk p ON p.id_produk = d.id_produk
                                    WHERE d.id_transaksi = ?");
mysqli_stmt_bind_param($stmt2, "i", $id_transaksi);
mysqli_stmt_execute($stmt2);
$items = mysqli_stmt_get_result($stmt2);

$total = 0;
$rows = [];
while ($row = mysqli_fetch_assoc($items)) {
    $subtotal = $row['jumlah'] * $row['harga'];
    $total += $subtotal;
    $rows[] = $row;
}

$no_struk = 'RM-' . str_pad($transaksi['id_transaksi'], 5, '0', STR_PAD_LEFT);
$metode_bayar = (string) ($transaksi['metode_bayar'] ?? 'COD (Bayar di Tempat)');
$info_bayar_teks = 'Bayar tunai saat produk diterima/diambil.';

$jumlah_keranjang = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
$sudah_login = isset($_SESSION['pelanggan']) || isset($_SESSION['admin']);
$nama_user   = $_SESSION['username'] ?? '';
?>
<!doctype html>
<html lang="id" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Struk <?= htmlspecialchars($no_struk) ?> - Risol Macaya</title>

    <link href="assets/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@500;600;700&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <meta name="theme-color" content="#4aa9c9">
    <link href="carousel.css" rel="stylesheet">
    <style>
        .rm-cetak-wrap { padding: 2.5rem 0 6rem; }
        .rm-cetak-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 1.75rem;
        }
        .rm-cetak-actions { display: flex; gap: 0.6rem; }
        .btn-rm-outline {
            border: 2px solid var(--rm-blue-400);
            color: var(--rm-blue-600);
            border-radius: 50rem;
            font-weight: 600;
            padding: 0.55rem 1.4rem;
            background: transparent;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
        }
        .btn-rm-outline:hover { background-color: var(--rm-blue-400); color: #fff; }

        .rm-struk-card {
            background-color: var(--rm-cream);
            border-radius: var(--rm-radius);
            box-shadow: var(--rm-shadow);
            overflow: hidden;
            max-width: 720px;
            margin: 0 auto;
        }
        .rm-struk-head {
            background: linear-gradient(90deg, var(--rm-blue-600), var(--rm-blue-500));
            color: #fff;
            padding: 1.75rem 2rem;
            text-align: center;
        }
        .rm-struk-head h2 {
            font-weight: 700;
            margin-bottom: 0.2rem;
        }
        .rm-struk-head p { margin: 0; opacity: 0.9; }
        .rm-struk-status {
            display: inline-block;
            margin-top: 0.75rem;
            background-color: rgba(255,255,255,0.2);
            border-radius: 50rem;
            padding: 0.3rem 1rem;
            font-weight: 600;
            font-size: 0.85rem;
        }

        .rm-struk-body { padding: 2rem 2rem 1rem; }
        .rm-struk-meta {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 1.5rem;
            padding-bottom: 1.25rem;
            border-bottom: 1px dashed var(--rm-blue-200);
        }
        .rm-struk-meta h6 {
            color: var(--rm-blue-700);
            font-weight: 700;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            margin-bottom: 0.4rem;
        }
        .rm-struk-meta p { margin: 0; line-height: 1.5; }

        .rm-struk-table { margin-bottom: 0; }
        .rm-struk-table thead th {
            background-color: var(--rm-blue-100);
            color: var(--rm-blue-700);
            border: none;
            font-weight: 700;
            padding: 0.85rem 0.75rem;
        }
        .rm-struk-table td {
            vertical-align: middle;
            padding: 0.75rem;
            border-color: var(--rm-blue-100);
        }
        .rm-struk-produk { display: flex; align-items: center; }
        .rm-struk-thumb {
            width: 46px;
            height: 46px;
            object-fit: cover;
            border-radius: 0.7rem;
            margin-right: 0.65rem;
        }
        .rm-struk-produk span { font-weight: 600; }

        .rm-struk-total {
            padding: 1.25rem 2rem 2rem;
        }
        .rm-total-line {
            display: flex;
            justify-content: space-between;
            padding: 0.35rem 0;
        }
        .rm-total-line.rm-grand {
            border-top: 2px dashed var(--rm-blue-200);
            margin-top: 0.5rem;
            padding-top: 0.85rem;
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--rm-blue-700);
        }

        .rm-struk-footnote {
            text-align: center;
            padding: 1.5rem 2rem 2rem;
            color: var(--rm-blue-600);
            font-weight: 600;
        }
        .rm-struk-footnote small {
            display: block;
            color: var(--rm-text);
            font-weight: 400;
            opacity: 0.7;
            margin-top: 0.25rem;
        }

        @media print {
            header, .rm-cetak-actions, .rm-footer, footer.rm-footer { display: none !important; }
            body { background: #fff !important; padding-top: 0 !important; }
            .rm-cetak-wrap { padding: 0 !important; }
            .rm-struk-card { box-shadow: none !important; max-width: 100% !important; }
            .rm-struk-head { background: #fff !important; color: var(--rm-blue-700) !important; border-bottom: 2px solid var(--rm-blue-700); }
            .rm-struk-status { background-color: var(--rm-blue-100) !important; color: var(--rm-blue-700) !important; }
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
        <div class="container rm-cetak-wrap">

            <div class="rm-cetak-top">
                <h2 class="rm-section-title mb-0">Struk Pembelian</h2>
                <div class="rm-cetak-actions">
                    <a href="carousel.php#produk" class="btn-rm-outline">Belanja Lagi</a>
                    <button type="button" class="btn btn-rm-primary" onclick="window.print()">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="margin-right:.35rem;vertical-align:-2px;">
                          <path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1Z"/>
                          <path d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2H5Zm7 5H4V3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v3ZM4 10h8v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3Z"/>
                        </svg>
                        Cetak Struk
                    </button>
                </div>
            </div>

            <div class="rm-struk-card">
                <div class="rm-struk-head">
                    <h2>Risol Macaya</h2>
                    <p>Semua rasa, semua suka.</p>
                    <span class="rm-struk-status">Pesanan Berhasil</span>
                </div>

                <div class="rm-struk-body">
                    <div class="rm-struk-meta">
                        <div>
                            <h6>No. Struk</h6>
                            <p><?= htmlspecialchars($no_struk) ?></p>
                            <h6 class="mt-3">Tanggal</h6>
                            <p><?= date('d F Y', strtotime($transaksi['tanggal'])) ?></p>
                            <h6 class="mt-3">Metode Pembayaran</h6>
                            <p><?= htmlspecialchars($metode_bayar) ?><br>
                               <small><?= htmlspecialchars($info_bayar_teks) ?></small></p>
                        </div>
                        <div class="text-md-end">
                            <h6>Pelanggan</h6>
                            <p><?= htmlspecialchars($transaksi['nama_pelanggan'] ?? '-') ?></p>
                            <?php if (!empty($transaksi['hp'])) { ?>
                                <p><?= htmlspecialchars($transaksi['hp']) ?></p>
                            <?php } ?>
                            <?php if (!empty($transaksi['alamat'])) { ?>
                                <p><?= htmlspecialchars($transaksi['alamat']) ?></p>
                            <?php } ?>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table rm-struk-table">
                            <thead>
                                <tr class="text-center">
                                    <th class="text-start">Produk</th>
                                    <th>Jumlah</th>
                                    <th>Harga</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($rows as $r) { ?>
                                <tr>
                                    <td>
                                        <div class="rm-struk-produk">
                                            <img src="img/<?= htmlspecialchars($r['foto']) ?>" class="rm-struk-thumb" alt="<?= htmlspecialchars($r['nama']) ?>">
                                            <span><?= htmlspecialchars($r['nama']) ?></span>
                                        </div>
                                    </td>
                                    <td class="text-center"><?= (int) $r['jumlah'] ?></td>
                                    <td class="text-center">Rp <?= number_format($r['harga'], 0, ',', '.') ?></td>
                                    <td class="text-center">Rp <?= number_format($r['jumlah'] * $r['harga'], 0, ',', '.') ?></td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="rm-struk-total">
                    <div class="rm-total-line">
                        <span>Subtotal</span>
                        <span>Rp <?= number_format($total, 0, ',', '.') ?></span>
                    </div>
                    <div class="rm-total-line rm-grand">
                        <span>Total Bayar</span>
                        <span>Rp <?= number_format($total, 0, ',', '.') ?></span>
                    </div>
                </div>

                <div class="rm-struk-footnote">
                    Terima kasih sudah belanja di Risol Macaya!
                    <small>Struk ini adalah bukti pembelian yang sah.</small>
                </div>
            </div>

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