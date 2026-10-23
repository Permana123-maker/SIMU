<?php
/*
|--------------------------------------------------------------------------
| NAVBAR TEMPLATE SIMU
|--------------------------------------------------------------------------
*/
?>

<nav class="navbar navbar-expand-lg navbar-dark shadow-sm">

    <div class="container-fluid">

        <!-- Tombol Sidebar -->

        <button
            class="btn btn-light me-3"
            id="toggleSidebar"
            type="button">

            <i class="bi bi-list fs-5"></i>

        </button>

        <!-- Logo -->

        <a class="navbar-brand fw-bold"
           href="<?= BASE_URL ?>guru/dashboard.php">

            <i class="bi bi-mortarboard-fill"></i>

            <?= NAMA_APLIKASI ?>

        </a>

        <!-- Tombol Responsive -->

        <button class="navbar-toggler"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navbarSIMU">

            <span class="navbar-toggler-icon"></span>

        </button>

        <!-- Menu -->

        <div class="collapse navbar-collapse"
             id="navbarSIMU">

            <ul class="navbar-nav ms-auto align-items-center">

                <!-- Jam -->

                <li class="nav-item me-4">

                    <span
                        id="clock"
                        class="text-white fw-semibold">

                        --:--:--

                    </span>

                </li>

                <!-- Guru -->

                <li class="nav-item dropdown">

                    <a
                        class="nav-link dropdown-toggle"
                        href="#"
                        role="button"
                        data-bs-toggle="dropdown">

                        <i class="bi bi-person-circle"></i>

                        <?= htmlspecialchars($nama_guru) ?>

                    </a>

                    <ul class="dropdown-menu dropdown-menu-end shadow">

                        <li>

                            <h6 class="dropdown-header">

                                Login sebagai

                            </h6>

                        </li>

                        <li>

                            <span class="dropdown-item-text">

                                <?= htmlspecialchars($nama_guru) ?>

                            </span>

                        </li>

                        <li>

                            <hr class="dropdown-divider">

                        </li>

                        <li>

                            <a
                                class="dropdown-item"
                                href="<?= BASE_URL ?>guru/profil.php">

                                <i class="bi bi-person"></i>

                                Profil

                            </a>

                        </li>

                        <li>

                            <a
                                class="dropdown-item text-danger"
                                href="<?= BASE_URL ?>guru/logout.php">

                                <i class="bi bi-box-arrow-right"></i>

                                Logout

                            </a>

                        </li>

                    </ul>

                </li>

            </ul>

        </div>

    </div>

</nav>