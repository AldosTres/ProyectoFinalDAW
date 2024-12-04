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