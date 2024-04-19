<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php base_url() ?>css/upload_tournament.css">
    <title>Subir torneo</title>
</head>

<body>
    <div class="form-upload-tournament">
        <h2 class="form-upload-tournament__title">Subir Torneo</h2>
        <form action="upload_tournament" method="POST">
            <label for="tournament_name" class="form-upload-tournament__input_tag">Nombre del Torneo:</label>
            <input type="text" id="jls_tournamente_name" name="jls_tournament_name" class="form-upload-tournament__input-field" required>

            <label for="init_date" class="form-upload-tournament__input_tag">Fecha de Inicio:</label>
            <input type="date" id="jls_tournament_init_date" name="jls_tournament_init_date" class="form-upload-tournament__input-field" required>
            ç
            <label for="end_date" class="form-upload-tournament__input_tag">Fecha de Fin:</label>
            <input type="date" id="jls_tournament_end_date" name="jls_tournament_end_date" class="form-upload-tournament__input-field" required>

            <input type="submit" class="form-upload__submit-button" value="Subir Torneo">
        </form>
    </div>
</body>

</html>