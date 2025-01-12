<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/d10a6cd004.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="<?= base_url() ?>css/login.css">
    <link rel="stylesheet" href="<?= base_url() ?>css/global.css">
    <title>Registro</title>
</head>

<body>
    <form action="user/register" class="form-login" method="post">
        <div class="form-login__mark">
            <a href="index" class="form_login__link form_login__link--no_color">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
        </div>
        <p class="form-login__heading">Registro</p>
        <div class="form-login__input-container">
            <div class="form-login__input-icon">
                <i class="fa-solid fa-user-ninja"></i>
            </div>
            <input type="text" class="form-login__input-field" id="jls_username" name="jls_username" placeholder="Nombre Jumper" required maxlength="10" minlength="5">
        </div>

        <div class="form-login__input-container">
            <div class="form-login__input-icon">
                <i class="fa-solid fa-user"></i>
            </div>
            <input type="text" class="form-login__input-field" id="jls_username_init" name="jls_username_init" placeholder="Nombre usuario" autocomplete="nickname" required maxlength="10" minlength="5">
        </div>

        <div class="form-login__input-container">
            <div class="form-login__input-icon">
                <i class="fa-solid fa-lock"></i>
            </div>
            <input type="password" class="form-login__input-field" id="jls_user_password" name="jls_user_password" placeholder="Contraseña" autocomplete="new-password" required maxlength="14" minlength="8">
        </div>


        <button id="form-login__button">Crear cuenta</button>
        <?php
        if (session()->getFlashdata('user_exists')) {
        ?>
            <div class="alert-message">
                <?= session()->getFlashdata('user_exists') ?>
            </div>
        <?php
        }
        ?>
        <a class="form-login__forgot-link form_login__link" href="login">¿Tienes cuenta? Inicia sesión</a>

    </form>

</body>

</html>