<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php base_url() ?>css/index.css">
    <script src="<?php echo base_url() ?>js/index.js"></script>
    <meta name="description" content="Web designer and front-end developer">
    <link href='https://fonts.googleapis.com/css?family=Raleway:100,200,400,600' rel='stylesheet' type='text/css'>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/fullPage.js/2.7.4/jquery.fullPage.min.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
    <link rel="icon" type="<?php base_url() ?>img/logoTipoLeagueSeries-removebg-preview.png" href="/images/favicon.ico">
    <title>Jumpstyle League Series</title>
</head>

<body>
    <!-- navbar header -->
    <div class="nav-header">
        <div class="nav-brand">
            <img src="<?php base_url() ?>img/logoTipoLeagueSeries-removebg-preview.png" alt="Logo_image">
        </div>
        <i class="fa fa-bars fa-3x"></i>
        <div class="header-links">
            <ul>
                <li data-menuanchor="fourthPage"><a href="get_login_page">Hola <?php echo session()->get('jumper_user_name') ?></a></li>
                <li data-menuanchor="thirdPage"><a href="get_upload_tournament_page">Subir Torneo</a></li>
                <li data-menuanchor="secondPage"><a href="#about">SOBRE NOSOTROS</a></li>
            </ul>
        </div>
    </div>
    <!-- end navbar header -->

    <!-- sidebar slider -->
    <div class="nav-screen">
        <i class="fa fa-times fa-3x"></i>
        <div class="nav-container">
            <div class="nav-links">
                <ul id='myMenu'>
                    <li data-menuanchor="secondPage"><a href="#about">SOBRE NOSOTROS</a></li>
                    <li data-menuanchor="thirdPage"><a href="#portfolio">PORTFOLIO</a></li>
                    <li data-menuanchor="fourthPage"><a href="#contact">CONTACT</a></li>
                </ul>
            </div>
        </div>
    </div>
    <!-- end navbar slider -->

    <!-- begin fullpage -->

    <div id="fullpage">
        <div class="section aboutme" data-anchor="aboutme">
            <div class="aboutme__capa-vacia">
                <h1 style="color:white">DESCUBRE EL PODER DEL JUMPSTYLE</h1>
                <button class="aboutme__find-btn">Descubrir</button>
            </div>
            <video playsinline="playsinline" autoplay="autoplay" muted="muted" loop="loop" class="nav-brand__background-video">
                <source src="<?php base_url() ?>video/sN.mp4" type="video/mp4">
            </video>
        </div>

        <div class="about-us">
            <div class="about-us__card-container">
                <img src="<?php echo base_url() ?>img/jumpstyleleagueseries_origin.jpg" alt="origin-jumpers" class="about-us__img-origin-jumpers">
            </div>
            <div class="about-us__text-container">
                <span class="about-us__title-label-container">Nuestros inicios</span>
                <h2 class="about-us__title-container">¿Qué es Jumpstyle League series?</h2>
                <p class="about-us__info-text-container">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Molestias, sed ipsam. Inventore consectetur ipsum optio maiores in ipsam dolorem perferendis. Omnis cum unde adipisci excepturi hic in ea, repudiandae delectus.</p>
                <p class="about-us__info-text-container">Lorem ipsum dolor sit amet consectetur adipisicing elit. Maiores perspiciatis, quibusdam temporibus quam voluptate quisquam inventore. Mollitia eius, corrupti adipisci unde minus maxime natus distinctio, blanditiis quibusdam labore, quasi aliquid.</p>
            </div>
        </div>

        <div class="available-tournaments">
            <h2 class="available-tournaments__title">Torneos Disponibles</h2>
            <?php
            foreach ($tournaments as $key => $tournament) {
            ?>
                <div class="available-tournaments__tournament-element">
                    <form action="get_tournamente_info_page" method="post">
                        <input type="hidden" name="tournament_id" value="<?= $tournament['id'] ?>">
                        <button type="submit" class="tournament-info-button">
                            <div class="available-tournaments__tournament-info">
                                <strong>Nombre del Torneo:</strong> <?php echo $tournament['nombre'] ?>
                            </div>
                            <div class="available-tournaments__tournament-info">
                                <strong>Fecha de Inicio:</strong> <?php echo $tournament['fecha_inicio'] ?>
                            </div>
                            <div class="available-tournaments__tournament-info">
                                <strong>Fecha de Fin:</strong> <?php echo $tournament['fecha_fin'] ?>
                            </div>
                        </button>
                    </form>

                </div>
        </div>
    <?php
            }
    ?>

    <!-- CONTENIDO DE PROXIMOS EVENTOS -->
    <div class="upcoming-events">
        <h2 class="upcoming-events__title">Próximos Eventos</h2>
        <div class="event">
            <img src="evento1.jpg" alt="Evento 1" class="event__image">
            <div class="event__info">
                <h3 class="event__title">Evento 1</h3>
                <p class="event__date">Fecha: 10 de mayo de 2024</p>
                <p class="event__location">Ubicación: Lugar del evento 1</p>
                <p class="event__description">Descripción del evento 1...</p>
            </div>
        </div>
        <div class="event">
            <img src="evento2.jpg" alt="Evento 2" class="event__image">
            <div class="event__info">
                <h3 class="event__title">Evento 2</h3>
                <p class="event__date">Fecha: 15 de mayo de 2024</p>
                <p class="event__location">Ubicación: Lugar del evento 2</p>
                <p class="event__description">Descripción del evento 2...</p>
            </div>
        </div>
        <!-- Agrega más eventos según sea necesario -->
    </div>
    <?php include_once TEMPLATES_VIEWS_PATH . 'footer.php'; ?>
</body>

</html>