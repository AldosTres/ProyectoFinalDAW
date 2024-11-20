<?php

namespace App\models;

use CodeIgniter\Model;

class DataBaseHandler extends Model
{
    /**
       Verifica si el usuario existe en la BBDD
     * @param mixed $user
     * @param mixed $password
     * @return mixed
     */
    function jls_check_user($user, $password)
    {
        $result = $this->db->query("SELECT * FROM usuarios WHERE nombre_usuario_inicio = ? AND contraseña = ?", [$user, $password]);
        $user = $result->getRow();
        return isset($user) ? $user->id : 0;
    }

    /**
       Función que comprueba si un nombre de usuario ya existe, devuelve el código del user
       si existe, y un 0 si no existe
     * @param mixed $user
     * @return mixed
     */
    function jls_check_user_name_exists($user)
    {
        $result = $this->db->query("SELECT * FROM usuarios WHERE nombre_usuario_inicio = ?", [$user]);
        $user = $result->getRow();
        if (isset($user)) {
            return $user->id;
        } else {
            return 0;
        }
    }

    /**
     * Pre:
     * Post:
     */
    function jls_register_user($name, $user, $password)
    {
        $exis = $this->jls_check_user_name_exists($user);
        if ($exis == 0) {
            $this->db->query("INSERT INTO usuarios(`nombre_usuario`, `nombre_usuario_inicio`, `contraseña`) VALUES ('{$name}', '{$user}' , '{$password}')");
        }
    }

    /**
     * 
     */
    function jls_get_user_data($user_id)
    {
        $result = $this->db->query("SELECT * FROM usuarios WHERE id = {$user_id}");
        $row = $result->getRow();
        return $row;
    }

    /**
     * 
     */
    public function jls_get_tournament_info($tournament_id)
    {
        $result = $this->db->query("SELECT * FROM torneos WHERE id = {$tournament_id}");
        $row = $result->getRow();
        return $row;
    }

    /**
     * Funcion que permite crear torneos
     * @param string $nombre
     * @param string $fecha_inicio
     * @param string $fecha_fin
     * @return bool
     */
    public function jls_upload_new_tournament($nombre, $fecha_inicio, $fecha_fin, $logo)
    {
        //Comprobación inicial, para corroborar que todos los datos han sido rellenado
        if (empty($nombre) || empty($fecha_inicio) || empty($fecha_fin)) {
            log_message('error', 'Faltan datos para crear el torneo');
            return false;
        }
        //Datos correspondientes al torneo
        $data = [
            'nombre' => $nombre,
            'fecha_inicio' => $fecha_inicio,
            'fecha_fin' => $fecha_fin,
            'activo' => 1,
            'logo_path' => $logo
        ];
        try {
            //Inserto la linea
            $this->db->table('torneos')->insert($data);

            /**
             * affectedRows() nos indicará si después de la inserción, se modificó algo en la tabla
             * > 0, nos indicará que si hubo cambios, = 0 será que no hubo cambio y por ende, no se
             * insertó
             */
            return $this->db->affectedRows() > 0;
        } catch (\Throwable $th) {
            //Registrando el error cuando no se puede crear el torneo
            log_message('error', 'No se ha podido crear el torneo: ' . $th->getMessage());
            return false;
        }
    }

    /**
     * 
     */
    public function jls_get_all_active_tournaments()
    {
        $result = $this->db->query("SELECT * FROM torneos WHERE activo = 1");
        $row = $result->getResultArray();
        return $row;
    }

    /**
     * 
     */
    public function jls_add_new_participant($alias, $tournament_id, $user_id)
    {
        // Verificar si el usuario ya está inscrito en el torneo
        $inscrito = $this->jls_check_participant_exists($tournament_id, $user_id);

        if ($inscrito) {
            // El usuario ya está inscrito, puedes manejar este caso según tu lógica de aplicación
            return false;
        } else {
            // El usuario no está inscrito, puedes proceder a registrar la inscripción
            $data = [
                'alias' => $alias,
                'id_torneo' => $tournament_id,
                'id_usuario' => $user_id,
            ];

            // Insertar la inscripción en la base de datos
            $this->db->table('inscripciones')->insert($data);

            return true;
        }
    }

    // Función para verificar si el usuario ya está inscrito en el torneo
    private function jls_check_participant_exists($tournament_id, $user_id)
    {
        $query = $this->db->query("SELECT * FROM inscripciones WHERE id_torneo = ? AND id_usuario = ?", [$tournament_id, $user_id]);

        return $query->getResult() ? true : false;
    }

    /**
     * Pre: se conocoe el parámetro $tournament_id como el id de un torneo en específico
     * Post: devuelve los participantes correspondientes a torneo específico
     * 
     * @param int $tournament_id
     */
    public function jls_get_tournament_participants($tournament_id)
    {
        $query = $this->db->query("SELECT * FROM inscripciones WHERE id = {$tournament_id} AND activo = 1");
        $row = $query->getResultArray();
        return $row;
    }
}
