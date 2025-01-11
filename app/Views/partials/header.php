<?php
if (session()->get('user_id')) {
?>
    <header class="p-3 mb-3 header <?= $title == 'Jumpstyle League Series' ? 'bg-transparent' : 'bg-dark' ?>">
        <div class="container-fluid">
            <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
                <a href="<?= base_url() ?>" class="d-flex align-items-center mb-2 mb-lg-0 link-body-emphasis text-decoration-none">
                    <img src="<?= base_url() ?>img/logoTipo_JLS.png" class=" img-fluid header__logo" alt="" width="50px">

                </a>

                <ul class="nav col-12 col-lg-auto me-lg-auto mb-2 justify-content-center mb-md-0 header__link">
                    <li class="header__link-item"><a href="<?= base_url() ?>index" class="nav-link px-2 text-secondary">Inicio</a></li>
                    <li class="header__link-item"><a href="<?= base_url() ?>about-us" class="nav-link px-2 text-white">Sobre nosotros</a></li>
                    <li class="header__link-item"><a href="#" class="nav-link px-2 text-white">Torneos</a></li>
                    <li class="header__link-item"><a href="#" class="nav-link px-2 text-white">Eventos</a></li>
                </ul>
                <span class="text-white header__user-name"><?= session()->get('jumper_user_name') ?></span>
                <div class="flex-shrink-0">
                    <a href="#" class="d-block link-body-emphasis text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="<?= base_url() ?>img/perfil_usuarios/<?= session()->get('jumper_image_profile') ?? 'user_default.png' ?>" alt="mdo" width="32" height="32" class="rounded-circle">
                    </a>
                    <ul class="dropdown-menu text-small shadow">
                        <li><a class="dropdown-item" href="#">Configuración</a></li>
                        <li><a class="dropdown-item" href="<?= base_url() ?>profile/<?= session()->get('jumper_user_name') ?>/<?= session()->get('user_id') ?>">Perfil</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item" href="<?= base_url() ?>logout">Cerrar sesión</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </header>
<?php
} else {
?>
    <header class="p-3 text-bg-dark header <?= $title == 'Jumpstyle League Series' ? 'bg-transparent' : 'bg-dark' ?>">
        <div class="container-fluid header__container">
            <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
                <a href="<?= base_url() ?>" class="d-flex align-items-center mb-2 mb-lg-0 text-white text-decoration-none">
                    <img src="<?= base_url() ?>img/logoTipo_JLS.png" class=" img-fluid header__logo" alt="" width="90px">
                </a>
                <ul class="nav col-12 col-lg-auto me-lg-auto mb-2 justify-content-center mb-md-0 header__link">
                    <li class="header__link-item"><a href="<?= base_url() ?>index" class="nav-link px-2 text-secondary">Inicio</a></li>
                    <li class="header__link-item"><a href="<?= base_url() ?>about-us" class="nav-link px-2 text-white">Sobre nosotros</a></li>
                    <li class="header__link-item"><a href="#" class="nav-link px-2 text-white">Torneos</a></li>
                    <li class="header__link-item"><a href="#" class="nav-link px-2 text-white">Eventos</a></li>
                </ul>
                <div class="text-end">
                    <a href="<?= base_url() ?>login">
                        <button type="button" class="btn btn-outline-light me-2">Iniciar sesión</button>
                    </a>
                    <a href="<?= base_url() ?>register">
                        <button type="button" class="btn btn-warning btn--sign-up">Regístrate</button>
                    </a>
                </div>
            </div>
        </div>
    </header>
<?php
}
?>