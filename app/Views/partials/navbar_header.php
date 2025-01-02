<div class="nav-header">
    <div class="nav-brand">
        <img src="<?= base_url() ?>img/logoTipoLeagueSeries-removebg-preview.png" alt="index-logo">
    </div>
    <i class="fa fa-bars fa-3x"></i>
    <div class="header-links">
        <ul>
            <li data-menuanchor="fourthPage">
                <?php
                if (session()->get('jumper_user_name')) {
                ?>
                    <a href=""><?= session()->get('jumper_user_name'); ?></a>
                    <div class="d-flex flex-column flex-md-row p-4 gap-4 py-md-5 align-items-center justify-content-center dropdown">
                        <ul
                            class="dropdown-menu position-static d-grid gap-1 p-2 rounded-3 mx-0 border-0 shadow w-220px"
                            data-bs-theme="dark">
                            <li><a class="dropdown-item rounded-2 active" href="#">Action</a></li>
                            <li><a class="dropdown-item rounded-2" href="#">Another action</a></li>
                            <li>
                                <a class="dropdown-item rounded-2" href="#">Something else here</a>
                            </li>
                            <li>
                                <hr class="dropdown-divider" />
                            </li>
                            <li><a class="dropdown-item rounded-2" href="#">Separated link</a></li>
                        </ul>
                    </div>
                <?php
                } else {
                ?>
                    <a href="<?= base_url() ?>login">INICIA SESION</a>
                <?php
                } ?>
            </li>
            <li data-menuanchor="thirdPage"><a href="#portfolio">EVENTOS</a></li>
            <li data-menuanchor="secondPage"><a href="get_vista_rapida">SOBRE NOSOTROS</a></li>
        </ul>
    </div>
</div>