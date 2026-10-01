<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}
$id_user = (int) $_SESSION['id_user'];

$stmt = mysqli_prepare(
    $koneksi,
    "SELECT id, nama, email, username, hp, alamat, role, foto FROM tb_user WHERE id = ?"
);
mysqli_stmt_bind_param($stmt, "i", $id_user);
mysqli_stmt_execute($stmt);
$user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$user) {
    die("Data user tidak ditemukan.");
}

$pesan = $_SESSION['pesan'] ?? '';
unset($_SESSION['pesan']);

function e($teks) {
    return htmlspecialchars((string) $teks, ENT_QUOTES, 'UTF-8');
}

function isi($teks, $pengganti = 'Belum diisi') {
    return ($teks !== null && trim((string) $teks) !== '')
        ? e($teks)
        : '<span class="kosong">' . e($pengganti) . '</span>';
}

$nama_tampil = trim((string) $user['nama']) !== '' ? $user['nama'] : $user['username'];
$huruf_depan = strtoupper(mb_substr($nama_tampil, 0, 1));
$alamat_ada  = trim((string) $user['alamat']) !== '';

$foto_sekarang = (string) ($user['foto'] ?? '');
$punya_foto    = $foto_sekarang !== '' && is_file(__DIR__ . '/foto_profil/' . $foto_sekarang);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Profil Saya</title>
  <link href="assets/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="carousel.css" rel="stylesheet">
  <link rel="stylesheet" href="profil_user.css">
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
              </a>
            </li>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle d-flex align-items-center active" href="#" role="button"
                data-bs-toggle="dropdown" aria-expanded="false">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" class="me-1" viewBox="0 0 16 16">
                  <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/>
                  <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1z"/>
                </svg>
                <?= e($user['username']) ?>
              </a>
              <ul class="dropdown-menu dropdown-menu-end">
                <?php if ($user['role'] === 'admin') { ?>
                <li><a class="dropdown-item" href="dashboard.php">Dashboard Admin</a></li>
                <li><hr class="dropdown-divider"></li>
                <?php } ?>
                <li><a class="dropdown-item" href="profil_user.php">Profil Saya</a></li>
                <li><a class="dropdown-item" href="riwayat.php">Riwayat Pesanan</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="logout.php">Logout</a></li>
              </ul>
            </li>
          </ul>
        </div>
      </div>
    </nav>
  </header>

  <div class="halaman">

    <?php if ($pesan): ?>
      <p class="pesan pesan-ok" role="status"><?= e($pesan) ?></p>
    <?php endif; ?>

    <header class="identitas">
      <?php if ($punya_foto): ?>
        <img class="segel segel-foto" src="foto_profil/<?= e($foto_sekarang) ?>" alt="">
      <?php else: ?>
        <div class="segel" aria-hidden="true"><?= e($huruf_depan) ?></div>
      <?php endif; ?>
      <div class="nama-blok">
        <h1><?= e($nama_tampil) ?></h1>
        <p class="sub">
          <span>@<?= e($user['username']) ?></span>
          <span class="peran"><?= e(ucfirst($user['role'])) ?></span>
        </p>
      </div>
      <div class="aksi-identitas">
        <a class="tombol" href="edit_profil_user.php">Edit profil</a>
        <?php if ($user['role'] === 'admin') { ?>
          <a class="tombol tombol-admin" href="dashboard.php">Ke dashboard admin</a>
        <?php } ?>
      </div>
    </header>

    <section class="label-kirim" aria-labelledby="judul-kirim">
      <div class="pita" id="judul-kirim">Dikirim ke</div>
      <div class="label-isi">
        <p class="penerima"><?= isi($user['nama'], 'Nama belum diisi') ?></p>
        <?php if ($alamat_ada): ?>
          <p class="alamat"><?= nl2br(e($user['alamat'])) ?></p>
        <?php else: ?>
          <p class="ajakan">
            Alamat belum diisi. <a href="edit_profil_user.php">Tambahkan alamat</a> supaya pesananmu bisa dikirim.
          </p>
        <?php endif; ?>
        <p class="telp"><?= isi($user['hp'], 'No. HP belum diisi') ?></p>
      </div>
    </section>

    <section class="akun">
      <h2>Akun</h2>
      <dl>
        <div class="baris"><dt>Username</dt><dd><?= isi($user['username']) ?></dd></div>
        <div class="baris"><dt>Email</dt><dd><?= isi($user['email']) ?></dd></div>
      </dl>
    </section>
  </div>
  <script src="assets/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>