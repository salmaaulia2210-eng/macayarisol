<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}
$id_user = (int) $_SESSION['id_user'];

function e($teks) {
    return htmlspecialchars((string) $teks, ENT_QUOTES, 'UTF-8');
}
if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

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

$input = [
    'nama'     => (string) $user['nama'],
    'email'    => (string) $user['email'],
    'username' => (string) $user['username'],
    'hp'       => (string) $user['hp'],
    'alamat'   => (string) $user['alamat'],
];
$errors = [];

$folder_foto     = __DIR__ . '/foto_profil/';
$ukuran_maks     = 2 * 1024 * 1024;
$tipe_diizinkan  = [
    'image/jpeg' => 'jpg',
    'image/png'  => 'png',
    'image/webp' => 'webp',
];
$foto_baru = null;
$hapus_foto = isset($_POST['hapus_foto']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($_SESSION['csrf'], (string) ($_POST['csrf'] ?? ''))) {
        die("Permintaan tidak valid. Muat ulang halaman lalu coba lagi.");
    }

    foreach ($input as $kolom => $nilai) {
        $input[$kolom] = trim((string) ($_POST[$kolom] ?? ''));
    }

    if (mb_strlen($input['nama']) > 100) {
        $errors[] = "Nama maksimal 100 karakter.";
    }

    if ($input['username'] === '') {
        $errors[] = "Username wajib diisi.";
    } elseif (!preg_match('/^[A-Za-z0-9_.]{3,30}$/', $input['username'])) {
        $errors[] = "Username 3–30 karakter, hanya huruf, angka, titik, dan garis bawah.";
    }

    if ($input['email'] !== '') {
        if (!filter_var($input['email'], FILTER_VALIDATE_EMAIL) || mb_strlen($input['email']) > 100) {
            $errors[] = "Format email belum benar.";
        }
    }

    if ($input['hp'] !== '' && !preg_match('/^[0-9+\-\s]{8,20}$/', $input['hp'])) {
        $errors[] = "No. HP hanya boleh berisi angka, spasi, tanda + dan -, sepanjang 8–20 karakter.";
    }

    if (mb_strlen($input['alamat']) > 255) {
        $errors[] = "Alamat maksimal 255 karakter.";
    }

    $ada_upload = isset($_FILES['foto']) && $_FILES['foto']['error'] !== UPLOAD_ERR_NO_FILE;
    if ($ada_upload) {
        $berkas = $_FILES['foto'];

        if ($berkas['error'] !== UPLOAD_ERR_OK) {
            $errors[] = "Foto gagal diunggah. Coba lagi.";
        } elseif ($berkas['size'] > $ukuran_maks) {
            $errors[] = "Ukuran foto maksimal 2 MB.";
        } else {
            $info_gambar = @getimagesize($berkas['tmp_name']);
            $mime        = $info_gambar['mime'] ?? null;

            if (!$info_gambar || !isset($tipe_diizinkan[$mime])) {
                $errors[] = "Foto harus berformat JPG, PNG, atau WEBP.";
            } else {
                $ekstensi  = $tipe_diizinkan[$mime];
                $foto_baru = bin2hex(random_bytes(16)) . '.' . $ekstensi;

                if (!is_dir($folder_foto)) {
                    @mkdir($folder_foto, 0755, true);
                }

                if (!is_dir($folder_foto) || !is_writable($folder_foto)) {
                    $errors[] = "Folder foto belum siap di server. Hubungi admin.";
                } elseif (!move_uploaded_file($berkas['tmp_name'], $folder_foto . $foto_baru)) {
                    $errors[] = "Foto gagal disimpan. Coba lagi.";
                    $foto_baru = null;
                }
            }
        }
    }

    if (!$errors) {
        $cek = mysqli_prepare($koneksi, "SELECT id FROM tb_user WHERE username = ? AND id <> ? LIMIT 1");
        mysqli_stmt_bind_param($cek, "si", $input['username'], $id_user);
        mysqli_stmt_execute($cek);
        if (mysqli_fetch_assoc(mysqli_stmt_get_result($cek))) {
            $errors[] = "Username sudah dipakai akun lain.";
        }

        if ($input['email'] !== '') {
            $cek = mysqli_prepare($koneksi, "SELECT id FROM tb_user WHERE email = ? AND id <> ? LIMIT 1");
            mysqli_stmt_bind_param($cek, "si", $input['email'], $id_user);
            mysqli_stmt_execute($cek);
            if (mysqli_fetch_assoc(mysqli_stmt_get_result($cek))) {
                $errors[] = "Email sudah dipakai akun lain.";
            }
        }
    }

    if (!$errors) {
        try {
            $foto_lama = (string) ($user['foto'] ?? '');

            if ($foto_baru !== null) {
                $foto_disimpan = $foto_baru;
            } elseif ($hapus_foto) {
                $foto_disimpan = null;
            } else {
                $foto_disimpan = $foto_lama !== '' ? $foto_lama : null;
            }

            $upd = mysqli_prepare(
                $koneksi,
                "UPDATE tb_user SET nama = ?, email = ?, username = ?, hp = ?, alamat = ?, foto = ? WHERE id = ?"
            );
            mysqli_stmt_bind_param(
                $upd,
                "ssssssi",
                $input['nama'],
                $input['email'],
                $input['username'],
                $input['hp'],
                $input['alamat'],
                $foto_disimpan,
                $id_user
            );
            mysqli_stmt_execute($upd);

            $foto_berubah = ($foto_baru !== null) || $hapus_foto;
            if ($foto_berubah && $foto_lama !== '' && is_file($folder_foto . $foto_lama)) {
                @unlink($folder_foto . $foto_lama);
            }

            $_SESSION['username'] = $input['username'];
            if (isset($_SESSION['admin'])) {
                $_SESSION['admin'] = $input['username'];
            }
            if (isset($_SESSION['pelanggan'])) {
                $_SESSION['pelanggan'] = $input['username'];
            }

            $_SESSION['pesan'] = "Profil berhasil disimpan.";
            header("Location: profil_user.php");
            exit;
        } catch (mysqli_sql_exception $ex) {
            $errors[] = "Profil belum tersimpan karena ada kendala di database. Coba lagi sebentar lagi.";
        }
    }

    if ($errors && $foto_baru !== null && is_file($folder_foto . $foto_baru)) {
        @unlink($folder_foto . $foto_baru);
    }
}

$foto_sekarang = (string) ($user['foto'] ?? '');
$punya_foto    = $foto_sekarang !== '' && is_file($folder_foto . $foto_sekarang);

$nama_tampil = $input['nama'] !== '' ? $input['nama'] : $input['username'];
$huruf_depan = strtoupper(mb_substr($nama_tampil !== '' ? $nama_tampil : '?', 0, 1));
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Edit Profil</title>
  <link rel="stylesheet" href="profil_user.css">
</head>
<body>
  <div class="halaman">
    <nav class="atas">
      <a href="profil_user.php">&larr; Kembali ke profil</a>
    </nav>

    <?php if ($errors): ?>
      <div class="pesan pesan-error" role="alert">
        <ul>
          <?php foreach ($errors as $err): ?>
            <li><?= e($err) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <header class="identitas">
      <?php if ($punya_foto): ?>
        <img class="segel segel-foto" src="foto_profil/<?= e($foto_sekarang) ?>" alt="">
      <?php else: ?>
        <div class="segel" aria-hidden="true"><?= e($huruf_depan) ?></div>
      <?php endif; ?>
      <div class="nama-blok">
        <h1>Edit profil</h1>
        <p class="sub">
          <span>Perubahan langsung tampil di profilmu.</span>
        </p>
      </div>
    </header>

    <form method="post" class="form-edit" enctype="multipart/form-data" novalidate>
      <input type="hidden" name="csrf" value="<?= e($_SESSION['csrf']) ?>">

      <section class="label-kirim" aria-labelledby="judul-foto">
        <div class="pita" id="judul-foto">Foto profil</div>
        <div class="label-isi">
          <div class="field">
            <label for="foto">Ganti foto</label>
            <input type="file" id="foto" name="foto" accept="image/jpeg,image/png,image/webp">
            <small>JPG, PNG, atau WEBP, maksimal 2 MB.</small>
          </div>
          <?php if ($punya_foto): ?>
            <label class="cek-hapus">
              <input type="checkbox" name="hapus_foto" value="1">
              Hapus foto saat ini
            </label>
          <?php endif; ?>
        </div>
      </section>

      <section class="label-kirim" aria-labelledby="judul-kirim">
        <div class="pita" id="judul-kirim">Dikirim ke</div>
        <div class="label-isi">
          <div class="field">
            <label for="nama">Nama penerima</label>
            <input type="text" id="nama" name="nama" value="<?= e($input['nama']) ?>"
                   maxlength="100" autocomplete="name">
          </div>

          <div class="field">
            <label for="alamat">Alamat</label>
            <textarea id="alamat" name="alamat" maxlength="255"
                      autocomplete="street-address"><?= e($input['alamat']) ?></textarea>
          </div>

          <div class="field">
            <label for="hp">No. HP</label>
            <input type="tel" id="hp" name="hp" value="<?= e($input['hp']) ?>"
                   maxlength="20" inputmode="tel" autocomplete="tel">
          </div>
        </div>
      </section>

      <section class="akun">
        <h2>Akun</h2>

        <div class="field">
          <label for="username">Username</label>
          <input type="text" id="username" name="username" value="<?= e($input['username']) ?>"
                 maxlength="30" required autocomplete="username">
          <small>3&ndash;30 karakter: huruf, angka, titik, atau garis bawah.</small>
        </div>

        <div class="field">
          <label for="email">Email</label>
          <input type="email" id="email" name="email" value="<?= e($input['email']) ?>"
                 maxlength="100" autocomplete="email">
        </div>
      </section>

      <div class="aksi">
        <button type="submit" class="tombol">Simpan perubahan</button>
        <a href="profil_user.php" class="tombol tombol-batal">Batal</a>
      </div>
    </form>
  </div>
</body>
</html>