<?php
include 'koneksi.php';

if (isset($_POST['register'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $register = mysqli_query($koneksi, "INSERT INTO tb_user (username, password, role) VALUES ('$username', '$password', 'pelanggan')");

    $register_sukses = (bool) $register;

}
?>

<!doctype html>
<html lang="en" data-bs-theme="auto">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="" />
    <meta
      name="author"
      content="Mark Otto, Jacob Thornton, and Bootstrap contributors"
    />
    <meta name="generator" content="Astro v5.13.2" />
    <title>Risol Macaya - Risol Kekinian</title>
    <link
      rel="canonical"
      href="https://getbootstrap.com/docs/5.3/examples/sign-in/"
    />
    <script src="assets/js/color-modes.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.10.6/sweetalert2.all.min.js"></script>
    <link href="assets/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@500;600;700&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet" />
    <meta name="theme-color" content="#4aa9c9" />
    <style>
      .bd-placeholder-img {
        font-size: 1.125rem;
        text-anchor: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        user-select: none;
      }
      @media (min-width: 768px) {
        .bd-placeholder-img-lg {
          font-size: 3.5rem;
        }
      }
      .b-example-divider {
        width: 100%;
        height: 3rem;
        background-color: #0000001a;
        border: solid rgba(0, 0, 0, 0.15);
        border-width: 1px 0;
        box-shadow:
          inset 0 0.5em 1.5em #0000001a,
          inset 0 0.125em 0.5em #00000026;
      }
      .b-example-vr {
        flex-shrink: 0;
        width: 1.5rem;
        height: 100vh;
      }
      .bi {
        vertical-align: -0.125em;
        fill: currentColor;
      }
      .nav-scroller {
        position: relative;
        z-index: 2;
        height: 2.75rem;
        overflow-y: hidden;
      }
      .nav-scroller .nav {
        display: flex;
        flex-wrap: nowrap;
        padding-bottom: 1rem;
        margin-top: -1px;
        overflow-x: auto;
        text-align: center;
        white-space: nowrap;
        -webkit-overflow-scrolling: touch;
      }
      .btn-bd-primary {
        --bd-violet-bg: #712cf9;
        --bd-violet-rgb: 112.520718, 44.062154, 249.437846;
        --bs-btn-font-weight: 600;
        --bs-btn-color: var(--bs-white);
        --bs-btn-bg: var(--bd-violet-bg);
        --bs-btn-border-color: var(--bd-violet-bg);
        --bs-btn-hover-color: var(--bs-white);
        --bs-btn-hover-bg: #6528e0;
        --bs-btn-hover-border-color: #6528e0;
        --bs-btn-focus-shadow-rgb: var(--bd-violet-rgb);
        --bs-btn-active-color: var(--bs-btn-hover-color);
        --bs-btn-active-bg: #5a23c8;
        --bs-btn-active-border-color: #5a23c8;
      }
      .bd-mode-toggle {
        z-index: 1500;
      }
      .bd-mode-toggle .bi {
        width: 1em;
        height: 1em;
      }
      .bd-mode-toggle .dropdown-menu .active .bi {
        display: block !important;
      }
    </style>
    <style>
      :root {
        --rm-blue-900: #1f4258;
        --rm-blue-800: #2c6f8a;
        --rm-blue-400: #6fc0db;
        --rm-blue-600: #3a8fae;
      }

      html,
      body {
        height: 100%;
      }

      body.auth-register { --auth-bg: url("img/login.jpg"); }

      body.auth-body {
        position: relative;
        min-height: 100vh;
        background-color: var(--rm-blue-900);
      }

      body.auth-body::before {
        content: "";
        position: fixed;
        inset: -10px; 
        z-index: -2;
        background: var(--auth-bg) center / cover no-repeat;
        filter: blur(2px) saturate(1.15);
      }

      body.auth-body::after {
        content: "";
        position: fixed;
        inset: 0;
        z-index: -1;
      }

      .form-signin {
        font-family: "Baloo 2", "Poppins", system-ui, sans-serif;
        max-width: 400px;
        padding: 2.25rem 2rem 1.75rem;
        position: relative;
        background-color: rgba(var(--bs-body-bg-rgb), 0.94);
        -webkit-backdrop-filter: blur(6px);
        backdrop-filter: blur(6px);
        color: var(--bs-body-color);
        border-radius: 1rem;
        border-top: 5px solid var(--rm-blue-400);
        box-shadow: 0 1.25rem 3rem rgba(0, 0, 0, 0.35);
      }

      @media (max-width: 575.98px) {
        .form-signin {
        font-family: "Baloo 2", "Poppins", system-ui, sans-serif;
          max-width: calc(100% - 2rem);
          padding: 1.75rem 1.25rem 1.5rem;
        }
      }

      .auth-brand {
        display: inline-block;
        margin-bottom: 0.5rem;
        font-size: 1.75rem;
        font-weight: 800;
        letter-spacing: 0.3px;
        text-decoration: none;
        color: var(--rm-blue-900);
      }

      .auth-brand span {
        color: var(--rm-blue-600);
      }

      [data-bs-theme="dark"] .auth-brand {
        color: #fff;
      }

      .form-signin h1 {
        font-size: 1.05rem;
        font-weight: 500 !important;
        color: var(--bs-secondary-color);
        margin-bottom: 1.5rem !important;
      }

      /* ---------- Input ---------- */
      .form-signin .form-floating {
        margin-bottom: 0.85rem;
      }

      .form-signin .form-floating:focus-within {
        z-index: 2;
      }

      .form-signin .form-control {
        border-radius: 0.6rem;
        border-color: var(--bs-border-color);
      }

      .form-signin .form-control:focus {
        border-color: var(--rm-blue-400);
        box-shadow: 0 0 0 0.25rem rgba(74, 169, 201, 0.25);
      }

      .form-signin .form-check-input:checked {
        background-color: var(--rm-blue-600);
        border-color: var(--rm-blue-600);
      }

      .form-signin .form-check-input:focus {
        border-color: var(--rm-blue-400);
        box-shadow: 0 0 0 0.25rem rgba(74, 169, 201, 0.25);
      }

      .form-signin .btn-primary {
        --bs-btn-color: var(--rm-blue-900);
        --bs-btn-bg: var(--rm-blue-400);
        --bs-btn-border-color: var(--rm-blue-400);
        --bs-btn-hover-color: var(--rm-blue-900);
        --bs-btn-hover-bg: var(--rm-blue-600);
        --bs-btn-hover-border-color: var(--rm-blue-600);
        --bs-btn-active-color: var(--rm-blue-900);
        --bs-btn-active-bg: var(--rm-blue-600);
        --bs-btn-active-border-color: var(--rm-blue-600);
        --bs-btn-focus-shadow-rgb: 74, 169, 201;
        --bs-btn-border-radius: 0.6rem;
        font-weight: 700;
        letter-spacing: 0.3px;
      }

      .form-signin a:not(.auth-brand) {
        color: var(--rm-blue-600);
        font-weight: 600;
        text-decoration: none;
      }

      .form-signin a:not(.auth-brand):hover {
        text-decoration: underline;
      }

      .form-signin p.text-body-secondary {
        font-size: 0.8rem;
        text-align: center;
      }

      .rm-back-link {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--rm-blue-600) !important;
        text-decoration: none;
        margin-bottom: 0.9rem;
      }
      .rm-back-link:hover {
        text-decoration: underline;
      }

      .bd-mode-toggle .btn-bd-primary {
        --bs-btn-bg: var(--rm-blue-800);
        --bs-btn-border-color: rgba(255, 255, 255, 0.25);
        --bs-btn-hover-bg: var(--rm-blue-600);
        --bs-btn-hover-border-color: var(--rm-blue-600);
        --bs-btn-hover-color: var(--rm-blue-900);
        --bs-btn-active-bg: var(--rm-blue-400);
        --bs-btn-active-border-color: var(--rm-blue-400);
        --bs-btn-active-color: var(--rm-blue-900);
        --bs-btn-focus-shadow-rgb: 74, 169, 201;
      }
    </style>
  </head>
  <body class="d-flex align-items-center py-4 auth-body auth-register">
    <svg xmlns="http://www.w3.org/2000/svg" class="d-none">
      <symbol id="check2" viewBox="0 0 16 16">
        <path
          d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z"
        ></path>
      </symbol>
      <symbol id="circle-half" viewBox="0 0 16 16">
        <path
          d="M8 15A7 7 0 1 0 8 1v14zm0 1A8 8 0 1 1 8 0a8 8 0 0 1 0 16z"
        ></path>
      </symbol>
      <symbol id="moon-stars-fill" viewBox="0 0 16 16">
        <path
          d="M6 .278a.768.768 0 0 1 .08.858 7.208 7.208 0 0 0-.878 3.46c0 4.021 3.278 7.277 7.318 7.277.527 0 1.04-.055 1.533-.16a.787.787 0 0 1 .81.316.733.733 0 0 1-.031.893A8.349 8.349 0 0 1 8.344 16C3.734 16 0 12.286 0 7.71 0 4.266 2.114 1.312 5.124.06A.752.752 0 0 1 6 .278z"
        ></path>
        <path
          d="M10.794 3.148a.217.217 0 0 1 .412 0l.387 1.162c.173.518.579.924 1.097 1.097l1.162.387a.217.217 0 0 1 0 .412l-1.162.387a1.734 1.734 0 0 0-1.097 1.097l-.387 1.162a.217.217 0 0 1-.412 0l-.387-1.162A1.734 1.734 0 0 0 9.31 6.593l-1.162-.387a.217.217 0 0 1 0-.412l1.162-.387a1.734 1.734 0 0 0 1.097-1.097l.387-1.162zM13.863.099a.145.145 0 0 1 .274 0l.258.774c.115.346.386.617.732.732l.774.258a.145.145 0 0 1 0 .274l-.774.258a1.156 1.156 0 0 0-.732.732l-.258.774a.145.145 0 0 1-.274 0l-.258-.774a1.156 1.156 0 0 0-.732-.732l-.774-.258a.145.145 0 0 1 0-.274l.774-.258c.346-.115.617-.386.732-.732L13.863.1z"
        ></path>
      </symbol>
      <symbol id="sun-fill" viewBox="0 0 16 16">
        <path
          d="M8 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8zM8 0a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 0zm0 13a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 13zm8-5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2a.5.5 0 0 1 .5.5zM3 8a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2A.5.5 0 0 1 3 8zm10.657-5.657a.5.5 0 0 1 0 .707l-1.414 1.415a.5.5 0 1 1-.707-.708l1.414-1.414a.5.5 0 0 1 .707 0zm-9.193 9.193a.5.5 0 0 1 0 .707L3.05 13.657a.5.5 0 0 1-.707-.707l1.414-1.414a.5.5 0 0 1 .707 0zm9.193 2.121a.5.5 0 0 1-.707 0l-1.414-1.414a.5.5 0 0 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .707zM4.464 4.465a.5.5 0 0 1-.707 0L2.343 3.05a.5.5 0 1 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .708z"
        ></path>
      </symbol>
    </svg>
    <div
      class="dropdown position-fixed bottom-0 end-0 mb-3 me-3 bd-mode-toggle"
    >
      <button
        class="btn btn-bd-primary py-2 dropdown-toggle d-flex align-items-center"
        id="bd-theme"
        type="button"
        aria-expanded="false"
        data-bs-toggle="dropdown"
        aria-label="Toggle theme (auto)"
      >
        <svg class="bi my-1 theme-icon-active" aria-hidden="true">
          <use href="#circle-half"></use>
        </svg>
        <span class="visually-hidden" id="bd-theme-text">Toggle theme</span>
      </button>
      <ul
        class="dropdown-menu dropdown-menu-end shadow"
        aria-labelledby="bd-theme-text"
      >
        <li>
          <button
            type="button"
            class="dropdown-item d-flex align-items-center"
            data-bs-theme-value="light"
            aria-pressed="false"
          >
            <svg class="bi me-2 opacity-50" aria-hidden="true">
              <use href="#sun-fill"></use>
            </svg>
            Light
            <svg class="bi ms-auto d-none" aria-hidden="true">
              <use href="#check2"></use>
            </svg>
          </button>
        </li>
        <li>
          <button
            type="button"
            class="dropdown-item d-flex align-items-center"
            data-bs-theme-value="dark"
            aria-pressed="false"
          >
            <svg class="bi me-2 opacity-50" aria-hidden="true">
              <use href="#moon-stars-fill"></use>
            </svg>
            Dark
            <svg class="bi ms-auto d-none" aria-hidden="true">
              <use href="#check2"></use>
            </svg>
          </button>
        </li>
        <li>
          <button
            type="button"
            class="dropdown-item d-flex align-items-center active"
            data-bs-theme-value="auto"
            aria-pressed="true"
          >
            <svg class="bi me-2 opacity-50" aria-hidden="true">
              <use href="#circle-half"></use>
            </svg>
            Auto
            <svg class="bi ms-auto d-none" aria-hidden="true">
              <use href="#check2"></use>
            </svg>
          </button>
        </li>
      </ul>
    </div>
    <main class="form-signin w-100 m-auto">
      <form method="post">
        <a href="carousel.php" class="rm-back-link">&larr; Kembali ke beranda</a>
        <a href="carousel.php" class="auth-brand d-block">Risol <span>Macaya</span></a>
        <h1 class="h3 mb-3 fw-normal">Buat akun baru</h1>
        <div class="form-floating">
          <input
            type="text"
            name="username"
            class="form-control"
            id="floatingInput"
            placeholder="name@example.com"
          />
          <label for="floatingInput">Username</label>
        </div>
        <div class="form-floating">
          <input
            type="password"
            name="password"
            class="form-control"
            id="floatingPassword"
            placeholder="Password"
          />
          <label for="floatingPassword">Password</label>
        </div>
        <button class="btn btn-primary w-100 py-2" type="submit" name="register">
          Daftar
        </button>
        <a href="login.php" class="d-block text-center mt-3">Sudah punya akun?</a>
      </form>
    </main>
    <script
      src="assets/dist/js/bootstrap.bundle.min.js"
      class="astro-vvvwv3sm"
    ></script>
    <?php if (!empty($register_sukses)) { ?>
    <script>
      Swal.fire({
        icon: 'success',
        title: 'Register berhasil!',
        text: 'Silakan login pakai akun barumu.',
        confirmButtonColor: '#4aa9c9'
      }).then(() => {
        window.location = 'login.php';
      });
    </script>
    <?php } ?>
  </body>
</html>