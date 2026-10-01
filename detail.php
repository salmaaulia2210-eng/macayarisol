<?php
session_start();
include 'koneksi.php';

$id = (int) ($_GET['id'] ?? 0);
$stmt = mysqli_prepare($koneksi, "SELECT * FROM tb_produk WHERE id_produk = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

$jumlah_keranjang = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
$sudah_login = isset($_SESSION['pelanggan']) || isset($_SESSION['admin']);
$nama_user   = $_SESSION['username'] ?? '';
?>
<!doctype html>
<html lang="id" data-bs-theme="light">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?= $data ? htmlspecialchars($data['nama']) . ' - Risol Macaya' : 'Produk tidak ditemukan - Risol Macaya' ?></title>

    <link href="assets/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@500;600;700&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <meta name="theme-color" content="#4aa9c9">
    <link href="carousel.css" rel="stylesheet" />
    <style>
      .rm-detail-wrap {
        padding: 2.5rem 0 1rem;
      }
      .rm-detail-card {
        background-color: var(--rm-cream);
        border-radius: var(--rm-radius);
        box-shadow: var(--rm-shadow);
        overflow: hidden;
      }
      .rm-detail-img {
        width: 100%;
        height: 100%;
        min-height: 320px;
        object-fit: cover;
      }
      .rm-detail-body {
        padding: 2rem 2.25rem;
      }
      .rm-detail-body h1 {
        font-weight: 700;
        color: var(--rm-blue-700);
        margin-bottom: 0.4rem;
      }
      .rm-detail-price {
        font-size: 1.6rem;
        font-weight: 700;
        color: var(--rm-blue-600);
        margin-bottom: 1rem;
      }
      .rm-detail-desc {
        color: var(--rm-text);
        margin-bottom: 1.75rem;
        line-height: 1.6;
      }
      .rm-qty-group {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 1.5rem;
      }
      .rm-qty-group label {
        font-weight: 600;
        color: var(--rm-blue-700);
      }
      .rm-qty-group input[type="number"] {
        width: 5.5rem;
        border: 2px solid var(--rm-blue-200);
        border-radius: 50rem;
        text-align: center;
        padding: 0.4rem 0.5rem;
      }
      .rm-qty-group input[type="number"]:focus {
        outline: none;
        border-color: var(--rm-blue-400);
        box-shadow: 0 0 0 0.2rem rgba(74, 169, 201, 0.25);
      }
      .rm-empty {
        text-align: center;
        padding: 4rem 1rem;
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
              <li class="nav-item"><a class="nav-link active" href="carousel.php#produk">Produk</a></li>
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
      <div class="container rm-detail-wrap">
        <h2 class="rm-section-title">Detail Makanan</h2>

        <?php if ($data) { ?>

          <div class="row g-0 rm-detail-card">
            <div class="col-md-5">
              <img src="img/<?= htmlspecialchars($data['foto']) ?>" class="rm-detail-img" alt="<?= htmlspecialchars($data['nama']) ?>">
            </div>
            <div class="col-md-7">
              <div class="rm-detail-body">
                <h1><?= htmlspecialchars($data['nama']) ?></h1>
                <div class="rm-detail-price">Rp <?= number_format($data['harga'], 0, ',', '.') ?></div>
                <p class="rm-detail-desc"><?= nl2br(htmlspecialchars($data['deskripsi'])) ?></p>

                <form method="post" action="keranjang.php?id=<?= $data['id_produk'] ?>">
                  <input type="hidden" name="hidden_foto" value="<?= htmlspecialchars($data['foto']) ?>">
                  <input type="hidden" name="hidden_nama" value="<?= htmlspecialchars($data['nama']) ?>">
                  <input type="hidden" name="hidden_harga" value="<?= $data['harga'] ?>">

                  <div class="rm-qty-group">
                    <label for="jumlah">Jumlah</label>
                    <input type="number" id="jumlah" name="jumlah" value="1" min="1">
                  </div>

                  <button type="submit" name="add" class="btn btn-rm-primary btn-lg">
                    Tambah ke keranjang
                  </button>
                </form>
              </div>
            </div>
          </div>
        <?php } else { ?>
          <div class="rm-empty">
            <h2 class="rm-section-title">Produk tidak ditemukan</h2>
            <p class="text-body-secondary">Sepertinya produk ini sudah tidak tersedia.</p>
            <a href="carousel.php" class="btn btn-rm-primary">Kembali ke Menu</a>
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