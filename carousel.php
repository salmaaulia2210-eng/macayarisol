<?php
session_start();
$jumlah_keranjang = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;

$sudah_login = isset($_SESSION['pelanggan']) || isset($_SESSION['admin']);
$nama_user   = $_SESSION['username'] ?? '';
?>
<!doctype html>
<html lang="id" data-bs-theme="light">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="Risol Macaya - risol kekinian dengan berbagai varian rasa manis dan gurih" />
    <title>Risol Macaya - Risol Kekinian</title>

    <link href="assets/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@500;600;700&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <meta name="theme-color" content="#4aa9c9">
    <link href="carousel.css" rel="stylesheet" />
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
              <li class="nav-item"><a class="nav-link active" href="carousel.php">Beranda</a></li>
              <li class="nav-item"><a class="nav-link" href="#produk">Produk</a></li>
              <li class="nav-item"><a class="nav-link" href="#tentang">Tentang Kami</a></li>
              <li class="nav-item"><a class="nav-link" href="#beli">Cara Beli</a></li>
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
      <div id="myCarousel" class="carousel slide mb-3" data-bs-ride="carousel">
        <div class="carousel-indicators">
          <button type="button" data-bs-target="#myCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
          <button type="button" data-bs-target="#myCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
          <button type="button" data-bs-target="#myCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
        </div>
        <div class="carousel-inner">
          <div class="carousel-item active">
            <img src="risol img\benner 1.png" class="hero-img" alt="Risol Mayo">
            <div class="container">
            </div>
          </div>
          <div class="carousel-item">
            <img src="risol img/benner 2.png" class="hero-img" alt="Risol Smoked Beef Keju">
            <div class="container">
            </div>
          </div>
          <div class="carousel-item">
            <img src="risol img/benner 3.png" class="hero-img" alt="Risol Ubi Keju Lumer">
            <div class="container">
            </div>
          </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#myCarousel" data-bs-slide="prev">
          <span class="carousel-control-prev-icon" aria-hidden="true"></span>
          <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#myCarousel" data-bs-slide="next">
          <span class="carousel-control-next-icon" aria-hidden="true"></span>
          <span class="visually-hidden">Next</span>
        </button>
      </div>

      <div class="container marketing" id="produk">
        <div class="py-4">
          <h2 class="rm-section-title">Risol Manis</h2>
          <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-4">
            <?php
            include_once 'koneksi.php';
            $query = mysqli_query($koneksi, "SELECT * FROM tb_produk WHERE nama != '' AND foto != '' AND id_kategori = '1'");
            while ($data = mysqli_fetch_array($query)) {
            ?>
            <div class="col">
              <div class="card rm-card">
                <img src="img/<?= $data['foto'] ?>" class="card-img-top" alt="<?= htmlspecialchars($data['nama']) ?>">
                <div class="card-body">
                  <p class="card-text mb-1"><b><?= $data['nama'] ?></b></p>
                  <p class="card-text rm-price mb-1">Rp <?= number_format($data['harga'], 0, ',', '.') ?></p>
                  <p class="card-text text-body-secondary small"><?= $data['deskripsi'] ?></p>
                  <div class="d-flex justify-content-end">
                    <a href="detail.php?id=<?= $data['id_produk'] ?>" class="btn btn-sm btn-outline-rm">Lihat Detail</a>
                  </div>
                </div>
              </div>
            </div>
            <?php } ?>
          </div>
        </div>

        <div class="py-4">
          <h2 class="rm-section-title">Risol Gurih</h2>
          <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-4">
            <?php
            include_once 'koneksi.php';
            $query = mysqli_query($koneksi, "SELECT * FROM tb_produk WHERE nama != '' AND foto != '' AND id_kategori = '2'");
            while ($data = mysqli_fetch_array($query)) {
            ?>
            <div class="col">
              <div class="card rm-card">
                <img src="img/<?= $data['foto'] ?>" class="card-img-top" alt="<?= htmlspecialchars($data['nama']) ?>">
                <div class="card-body">
                  <p class="card-text mb-1"><b><?= $data['nama'] ?></b></p>
                  <p class="card-text rm-price mb-1">Rp <?= number_format($data['harga'], 0, ',', '.') ?></p>
                  <p class="card-text text-body-secondary small"><?= $data['deskripsi'] ?></p>
                  <div class="d-flex justify-content-end">
                    <a href="detail.php?id=<?= $data['id_produk'] ?>" class="btn btn-sm btn-outline-rm">Lihat Detail</a>
                  </div>
                </div>
              </div>
            </div>
            <?php } ?>
          </div>
        </div>

        <hr class="featurette-divider" />
        <div class="row featurette align-items-center rm-about" id="tentang">
          <div class="col-md-7">
            <h2 class="featurette-heading fw-normal lh-1">
              Tentang Kami <span class="text-body-secondary">Risol Macaya</span>
            </h2>
            <p class="lead">
              Risol Macaya adalah usaha rumahan yang bikin risol kekinian dengan
              isian melimpah, digoreng garing di luar dan lumer di dalam. Ada
              varian manis dan gurih, cocok buat cemilan.
            </p>
            <ul class="list-unstyled">
              <li>Bahan fresh, digoreng setelah pesanan masuk</li>
              <li>Tanpa pengawet</li>
              <li>Kemasan rapi, siap dikirim</li>
            </ul>
          </div>
          <div class="col-md-5">
            <img src="risol img/gtw.jpg" class="img-fluid mx-auto d-block" alt="Tentang Risol Macaya">
          </div>
        </div>

        <hr class="featurette-divider" />
        <div class="rm-buy" id="beli">
          <h2 class="featurette-heading fw-normal lh-1 text-center mb-4">
            Mau Beli? <span class="text-body-secondary">Gampang!</span>
          </h2>
          <div class="row g-3">
            <div class="col-md-4">
              <div class="rm-buy-item">
                <h5>Lewat Website Ini</h5>
                <p class="small text-body-secondary mb-2">Pilih produk, masukkan keranjang, lalu checkout langsung di sini.</p>
                <a href="#produk" class="btn btn-sm btn-outline-rm">Lihat Menu</a>
              </div>
            </div>
            <div class="col-md-4">
              <div class="rm-buy-item">
                <h5>Chat WhatsApp</h5>
                <p class="small text-body-secondary mb-2">Order cepat langsung ke admin kami.</p>
                <a href="https://wa.me/62812xxxxxxxx" target="_blank" class="btn btn-sm btn-outline-rm">Chat Sekarang</a>
              </div>
            </div>
            <div class="col-md-4">
              <div class="rm-buy-item">
                <h5>Datang Langsung</h5>
                <p class="small text-body-secondary mb-2">Kamu juga bisa ambil langsung di lokasi kami.</p>
                <span class="d-block small">Jl. dijalan No. 44</span>
              </div>
            </div>
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