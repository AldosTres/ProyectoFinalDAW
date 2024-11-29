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
        $result = $this->db->query("SELECT * FROM usuarios WHERE nombre_usuario = ? AND contraseña = ?", [$user, $password]);
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
        $result = $this->db->query("SELECT * FROM usuarios WHERE nombre_usuario = ?", [$user]);
        $user = $result->getRow();

        // Verifica explícitamente si se encontró un usuario
        if ($user !== null) {
            return $user->id;
        } else {
            return 0;
        }
    }

    /**
       Función para registrar usuarios nuevos
     * @param mixed $name
     * @param mixed $user
     * @param mixed $password
     * @return bool
     */
    function jls_register_user($name, $user, $password)
    {
        //Datos correspondientes al torneo
        try {
            $exis = $this->jls_check_user_name_exists($user);
            $data = [
                'nombre_usuario' => $user,
                'alias_usuario' => $name,
                'contraseña' => $password,
            ];
            if ($exis == 0) {
                //Inserto la linea
                $this->db->table('usuarios')->insert($data);
                /**
                 * affectedRows() nos indicará si después de la inserción, se modificó algo en la tabla
                 * > 0, nos indicará que si hubo cambios, = 0 será que no hubo cambio y por ende, no se
                 * insertó
                 */
                return $this->db->affectedRows() > 0; //Retorna true o false
            } else {
                return false;
            }
        } catch (\Throwable $th) {
            //Registrando el error cuando no se puede crear el torneo
            log_message('error', 'No se ha podido registrar al usuario: ' . $th->getMessage());
            return false;
        }
    }

    /**
       Función que devuelve la información sobre un usuario
     * @param mixed $user_id
     * @return array|T|\stdClass|null
     */
    function jls_get_user_data($user_id)
    {
        $result = $this->db->query("SELECT * FROM usuarios WHERE id = ?", [$user_id]);
        $row = $result->getRow();
        return $row;
    }

    /**
       Función que devuelve toda la información de un torneo específico
     * @param string $tournament_id
     * @return array
     */
    public function jls_get_tournament_info($tournament_id)
    {
        $result = $this->db->query("SELECT * FROM torneos WHERE id = ?", [$tournament_id]);
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
            return $this->db->affectedRows() > 0; //Retorn true o false
        } catch (\Throwable $th) {
            //Registrando el error cuando no se puede crear el torneo
            log_message('error', 'No se ha podido crear el torneo: ' . $th->getMessage());
            return false;
        }
    }


    /**
       Función que devuelve todos los torneos
     * @return array
     */
    public function jls_get_all_tournaments()
    {
        $result = $this->db->query("SELECT * FROM torneos");
        $row = $result->getResultArray();
        return $row;
    }


    /**
       Función que obtiene los torneos dependiendo de un estado que se pasa por parámetro
       Por defecto obtiene todos los torneos
     * @param mixed $status
     * @return array
     */
    public function jls_get_tournaments_by_filter($status = null)
    {
        $query = 'SELECT * FROM torneos';
        $params = [];
        switch ($status) {
            case 'ongoing':
                //Un torneo puede estar en curso activo o inactivo.
                $query .= ' WHERE CURRENT_TIME BETWEEN fecha_inicio AND fecha_fin';
                break;
            case 'active':
                $query .= ' WHERE activo = ?';
                $params[] = 1;
                break;
            case 'inactive':
                $query .= ' WHERE activo = ?';
                $params[] = 0;
                break;
            case 'finished':
                //Si el torneo finaliza, se entiende que queda desactivado
                $query .= ' WHERE activo = ? AND fecha_fin < CURRENT_TIME';
                $params[] = 0;
                break;
            default:
                //En caso contrario a todos estos, mostrará todos los torneos
                break;
        }
        $result = $this->db->query($query, $params);
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

    /**
       Función que verifica si un usuario ya se encuentra registrado en un torneo
     * @param string $tournament_id
     * @param string $user_id
     * @return bool
     */
    private function jls_check_participant_exists($tournament_id, $user_id)
    {
        $query = $this->db->query("SELECT * FROM inscripciones WHERE id_torneo = ? AND id_usuario = ?", [$tournament_id, $user_id]);

        return $query->getResult() ? true : false;
    }

    /**
       Devuelve a todos los participantes de un torneo específico
     * 
     * @param int $tournament_id
     */
    public function jls_get_tournament_participants($tournament_id)
    {
        $query = $this->db->query("SELECT * FROM inscripciones WHERE id = ? AND activo = ?", [$tournament_id, 1]);
        $row = $query->getResultArray();
        return $row;
    }
}
