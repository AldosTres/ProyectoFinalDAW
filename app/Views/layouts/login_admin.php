<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JLS | administración</title>
    <link rel="stylesheet" href="<?= base_url() ?>css/login_admin.css">
    <link rel="stylesheet" href="<?= base_url() ?>css/global.css">
</head>

<body>
    <form action="<?= base_url() ?>login-admin/check" class="form" method="post">
        <img src="<?= base_url() ?>img/logoTipo_JLS.png" alt="Logotipo JLS" width="100">
        <p>Administración JLS</p>
        <div class="group">
            <input required="true" class="main-input" type="text" id="jls_username" name="jls_username">
            <span class="highlight-span"></span>
            <label class="lebal-email">Usuario</label>
        </div>
        <div class="container-1">
            <div class="group">
                <input required="true" class="main-input" type="password" id="jls_user_password" name="jls_user_password">
                <span class="highlight-span"></span>
                <label class="lebal-email">Contraseña</label>
            </div>
        </div>
        <button class="submit">Entrar</button>
        <?php
        if (session()->getFlashdata('user_not_found_error')) {
        ?>
            <div class="alert-message">
                <?= session()->getFlashdata('user_not_found_error') ?>
            </div>
        <?php
        }
        ?>
    </form>
</body>

</html>