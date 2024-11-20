<div class="nav-screen">
    <i class="fa fa-times fa-3x"></i>
    <div class="nav-container">
        <div class="nav-links">
            <ul id='myMenu'>
                <?php

                ?>
                <li data-menuanchor="secondPage">
                    <?php
                    if (session()->get('jumper_user_name')) {
                    ?>
                        <a href=""><?= session()->get('jumper_user_name'); ?></a>
                    <?php
                    } else {
                    ?>
                        <a href="get_login_page">INICIA SESION</a>
                    <?php
                    } ?>
                </li>
                <li data-menuanchor="thirdPage"><a href="#portfolio">EVENTOS</a></li>
                <li data-menuanchor="fourthPage"><a href="#contact">SOBRE NOSOTROS</a></li>
            </ul>
        </div>
    </div>
</div>