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
            <!-- <div class="aboutme__capa-vacia">
                <h1 style="color:white">JUMPSTYLE<span style="color:#FF6363">/</span>LEAGUE SERIES</h1>
                <p><span id="holder"></span><span class="blinking-cursor">|</span></p>
            </div> -->
            <video playsinline="playsinline" autoplay="autoplay" muted="muted" loop="loop" class="nav-brand__background-video">
                <source src="<?php base_url() ?>video/sN.mp4" type="video/mp4">
            </video>
        </div>

        <div class="available-tournaments">
            <h2 class="available-tournaments__title">Torneos Disponibles</h2>
            <?php
            foreach ($tournaments as $key => $value) {
                echo "hola";
            }
            ?>
            <div class="available-tournaments__tournament-element">
                <a href="detalle_torneo.php?id=1"> <!-- Enlace a la página de detalle del torneo con ID 1 -->
                    <div class="available-tournaments__tournament-info">
                        <strong>Nombre del Torneo:</strong> Torneo de Baile 2024
                    </div>
                    <div class="available-tournaments__tournament-info">
                        <strong>Fecha de Inicio:</strong> 2024-06-01
                    </div>
                    <div class="available-tournaments__tournament-info">
                        <strong>Fecha de Fin:</strong> 2024-06-30
                    </div>
                </a>
            </div>
            <!-- Otros torneos se pueden agregar aquí -->
        </div>
</body>

</html>