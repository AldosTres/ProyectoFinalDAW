<?php

namespace App\models;

use CodeIgniter\Model;

class DataBaseHandler extends Model
{
    /**
     * Pre: 
     * Post: 
     */
    function jls_check_user($user, $password)
    {
        $result = $this->db->query("SELECT * FROM usuarios WHERE nombre_usuario_inicio = '{$user}' AND contraseña = '{$password}'");
        $user = $result->getRow();
        return isset($user) ? $user->id : 0;
    }

    /**
     * Pre:
     * Post: función que comprueba si un nombre de user ya existe, devuelve el código del user
     * si existe, y un 0 si no existe
     */
    function jls_check_user_name_exists($user)
    {
        $result = $this->db->query("SELECT * FROM usuarios WHERE nombre_usuario_inicio = '{$user}'");
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
     * 
     */
    public function jls_get_tournament_participant($tournament_id)
    {
        $result = $this->db->query("SELECT * FROM participantes WHERE torneo_id = {$tournament_id}");
        $row = $result->getRow();
        return $row;
    }

    /**
     * 
     */
    public function jls_upload_new_tournament($nombre, $fecha_inicio, $fecha_fin)
    {
        // Ejecutar la consulta
        if ($this->db->query("INSERT INTO torneos (nombre, fecha_inicio, fecha_fin) VALUES ('$nombre', '$fecha_inicio', '$fecha_fin')") === TRUE) {
            return true; // Insertado correctamente
        } else {
            return false; // No insertado o insertado erróneamente
        }
    }
    /**
     * 
     */
    function jls_get_all_active_tournaments()
    {
        $result = $this->db->query("SELECT * FROM torneos");
        $row = $result->getResultArray();
        return $row;
    }

    /**
     * 
     */
    public function jls_add_new_participant($tournament_id, $user_id)
    {
        // Verificar si el usuario ya está inscrito en el torneo
        $inscrito = $this->jls_check_participant_exists($tournament_id, $user_id);

        if ($inscrito) {
            // El usuario ya está inscrito, puedes manejar este caso según tu lógica de aplicación
            return "El usuario ya está inscrito en este torneo.";
        } else {
            // El usuario no está inscrito, puedes proceder a registrar la inscripción
            $data = [
                'id_torneo' => $tournament_id,
                'id_usuario' => $user_id
                // Puedes agregar más datos si es necesario, como fecha de inscripción, etc.
            ];

            // Insertar la inscripción en la base de datos
            $this->db->table('inscripciones')->insert($data);

            return "El usuario se ha inscrito correctamente en el torneo.";
        }
    }

    // Función para verificar si el usuario ya está inscrito en el torneo
    private function jls_check_participant_exists($tournament_id, $user_id)
    {
        $query = $this->db->query("SELECT * FROM inscripciones WHERE id_torneo = ? AND codigo_usuario = ?", [$tournament_id, $user_id]);

        return $query->getResult() ? true : false;
    }
}
