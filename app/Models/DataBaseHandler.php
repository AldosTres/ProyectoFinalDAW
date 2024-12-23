<?php

namespace App\models;

use CodeIgniter\Model;
use InvalidArgumentException;

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
     * Función que me permite actualizar la última conexión de los usuarios del sistema
     * @param mixed $user_id
     * @return bool
     */
    function jls_update_last_connection($user_id)
    {
        try {
            //De esta manera si me permite introducir correctamente CURRENT_TIME
            $this->db->table('usuarios')
                ->set('ultima_conexion', 'CURRENT_TIME', false) // El 'false' evita que se ponga comillas alrededor de la expresión
                ->where('id', $user_id)
                ->update();
            return $this->db->affectedRows() > 0; //Retorn true o false
        } catch (\Throwable $th) {
            return false;
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
     * Función que devuelve la información sobre un usuario
     * @param mixed $user_id El ID del usuario a buscar.
     * @return array|\stdClass|null Retorna un array asociativo, un objeto stdClass, o null si no se encuentra el usuario.
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
    function jls_get_tournament_info($tournament_id)
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
    function jls_upload_new_tournament($nombre, $fecha_inicio, $fecha_fin, $logo)
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
            return false;
        }
    }

    /**
       Función que obtiene los torneos dependiendo de un estado que se pasa por parámetro
       Por defecto obtiene todos los torneos
     * @param mixed $status
     * @return array
     */

    function jls_get_tournaments_by_filter($status = null)
    {
        $builder = $this->db->table('torneos');

        switch ($status) {
            case 'ongoing':
                // Un torneo puede estar en curso, activo o inactivo.
                $builder->where('fecha_inicio <=', 'CURRENT_TIME', false)
                    ->where('fecha_fin >=', 'CURRENT_TIME', false);
                break;
            case 'active':
                $builder->where('activo', 1);
                break;
            case 'inactive':
                $builder->where('activo', 0);
                break;
            case 'finished':
                $builder->where('activo', 0)
                    ->where('fecha_fin <', date('Y-m-d H:i:s'));
                break;
            default:
                // Mostrar todos los torneos (no se aplican filtros adicionales).
                break;
        }

        $query = $builder->get();
        return $query->getResultArray();
    }

    /**
       Función que añade un nuevo participante a un torneo
     * @param mixed $alias
     * @param mixed $tournament_id
     * @param mixed $user_id
     * @return bool
     */
    function jls_add_new_participant($alias, $tournament_id, $user_id)
    {
        // Verificar si el usuario ya está inscrito en el torneo
        $inscrito = $this->jls_check_participant_exists($tournament_id, $user_id);
        if ($inscrito) {
            // El usuario ya está inscrito, puedes manejar este caso según tu lógica de aplicación
            return false;
        } else {
            // El usuario no está inscrito, puedes proceder a registrar la inscripción
            try {
                $data = [
                    'alias' => $alias,
                    'id_torneo' => $tournament_id,
                    'id_usuario' => $user_id,
                ];
                // Insertar la inscripción en la base de datos
                $this->db->table('inscripciones')->insert($data);
                return $this->db->affectedRows() > 0;
            } catch (\Throwable $th) {
                return false;
            }
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

        return $query->getRow() ? true : false;
    }

    /**
       Devuelve a todos los participantes de un torneo específico o en caso de que $existsBracket = true, devuelve
       solo los participantes que no se encuentran en la tabla rondas
     * @param mixed $tournament_id
     * @param mixed $existsBracket
     * @throws \InvalidArgumentException
     * @return array|bool
     */
    function jls_get_tournament_participants($tournament_id, $existsBracket = false)
    {
        // SELECT * FROM usuarios u WHERE NOT EXISTS (
        //     SELECT r.* FROM rondas r 
        //     JOIN inscripciones i1 ON i1.id = r.id_participante1
        //     JOIN inscripciones i2 ON i2.id = r.id_participante2
        //     where r.id_torneo = $tournament_id   
        //     WHERE u.id = i1.id_usuario OR u.id = i2.id_usuario)
        //Pero esta subconsulta no devuelve nada, luego la consulta entera devolvería todos los usuarios
        try {
            // Valida el ID del torneo
            if (!is_numeric($tournament_id)) {
                throw new InvalidArgumentException("El ID del torneo debe ser un valor numérico.");
            }

            // Si existe un bracket, devuelvo solo a los que no se encuentran ya seleccionados para rondas
            if ($existsBracket) {
                /**
                 * Importante aplicar esto ya que si no hay filas en rondas para el torneo dado,
                 * la subconsulta de NOT EXISTS no encontrará resultados, y todos los usuarios serán devueltos.
                 */
                $exists = $this->db->table('rondas')
                    ->where('id_torneo', $tournament_id)
                    ->countAllResults();

                // Si no hay rondas, devuelve todos los usuarios inscritos al torneo
                if ($exists === 0) {
                    $query = $this->db->table('inscripciones')
                        ->select('usuarios.*')
                        ->join('usuarios', 'inscripciones.id_usuario = usuarios.id', 'inner')
                        ->where('inscripciones.id_torneo', $tournament_id)
                        ->where('inscripciones.activo', 1)
                        ->get();

                    return $query->getResultArray();
                }

                $builder = $this->db->table('usuarios');

                // Subconsulta para verificar participantes en rondas
                $subquery = $this->db->table('rondas')
                    ->join('inscripciones AS i1', 'rondas.id_participante1 = i1.id', 'inner')
                    ->join('inscripciones AS i2', 'rondas.id_participante2 = i2.id', 'inner')
                    ->select('1')
                    ->where('rondas.id_torneo', $tournament_id) // Usuarios solo del torneo específico
                    ->groupStart()
                    ->where('i1.id_usuario = usuarios.id')
                    ->orWhere('i2.id_usuario = usuarios.id')
                    ->groupEnd();

                // Consulta principal
                $builder->select('*')
                    ->where("NOT EXISTS ({$subquery->getCompiledSelect()})", null, false);

                $query = $builder->get();
                return $query->getResultArray(); // O getResult(), según prefieras.
            }

            // Si no existe bracket, selecciona inscripciones activas del torneo Función predeterminada
            $query = $this->db->table('inscripciones')
                ->select('*')
                ->where('id_torneo', $tournament_id)
                ->where('activo', 1)
                ->get();

            return $query->getResultArray();
        } catch (InvalidArgumentException $e) {
            log_message('error', 'Argumento inválido: ' . $e->getMessage());
            return false;
        } catch (\Throwable $th) {
            log_message('error', 'Error al obtener participantes del torneo: ' . $th->getMessage());
            return false;
        }
    }


    /**
       Funcion que obtiene el logotipo de un torneo
     * @param mixed $tournament_id
     * @return mixed
     */
    function jls_get_tournament_logo_name($tournament_id)
    {
        $query = $this->db->query("SELECT * FROM torneos WHERE id = ?", [$tournament_id]);
        $row = $query->getRow();
        return $row->logo_path;
    }

    /**
       Función que actualiza los datos de un torneo específico
     * @param mixed $id
     * @param mixed $nombre
     * @param mixed $fecha_inicio
     * @param mixed $fecha_fin
     * @param mixed $activo
     * @param mixed $logo_path
     * @return bool
     */
    function jls_update_tournament_data($id, $nombre, $fecha_inicio, $fecha_fin, $activo, $logo_path)
    {
        try {
            $data = [
                'nombre' => $nombre,
                'fecha_inicio' => $fecha_inicio,
                'fecha_fin' => $fecha_fin,
                'activo' => $activo,
                //En caso de que no cambie de foto, no actualizo la ruta
                'logo_path' => isset($logo_path) && $logo_path !== '' ? $logo_path : null
            ];
            $this->db->table('torneos')->update($data, ['id' => $id]);
            return $this->db->affectedRows() > 0; //Retorn true o false
        } catch (\Throwable $th) {
            return false;
        }
    }

    // 4. Estadísticas individuales
    // Además del número de torneos en los que ha participado un usuario, puedes agregar:

    // Inscripciones activas: Si hay torneos en los que está inscrito pero aún no han comenzado.
    // Participaciones completadas: Cuántos torneos ha completado el usuario.

    // Implementación sugerida:
    // Crear una columna ultimo_login en la tabla usuarios.
    // En el proceso de login, después de validar las credenciales, actualizas esa columna con la fecha y hora actuales (CURRENT_TIMESTAMP).
    // 6. Crear un nuevo usuario (opcional)
    // Aunque no es imprescindible, tener una opción para crear usuarios manualmente puede ser útil en ciertos casos, como:

    // Registrar un juez o administrador sin pasar por el registro estándar.
    // Registrar usuarios masivamente para un evento o torneo grande.
    // Si decides implementarlo, podrías incluir un formulario básico con:

    // Nombre/alias.
    // Correo electrónico.
    // Rol inicial (usuario, juez, etc.).
    // Estado inicial (activo/inactivo).
    // 7. Auditoría (opcional pero útil):
    // Registrar quién realizó cambios importantes como desactivar usuarios o cambiar roles. Esto puede ser útil para rastrear acciones en caso de errores o problemas administrativos.

    /**
     * 
       Función que permite obtener usuarios por filtros, en caso de que no haya filtros, obtiene todos
     * @param mixed $alias
     * @param mixed $role
     * @param mixed $status
     * @param mixed $registration_start
     * @param mixed $registration_end
     * @return array
     */
    function jls_get_users_by_filter($alias = null, $role = null, $status = null, $registration_start = null, $registration_end = null)
    {
        //Utilizo alias como u o r para evitar ambiguedades
        $builder = $this->db->table('usuarios u');
        $builder->select('u.id, u.alias_usuario, r.nombre AS rol_nombre, u.activo, u.fecha_registro, u.ultima_conexion'); // AS para evitar ambiguedades también
        $builder->join('tipos_rol r', 'r.id = u.id_rol');
        if ($alias && $alias != 'all') {
            $builder->like('alias_usuario', $alias);
        }
        if ($role && $role != 'all') {
            //  revisar
            $builder->where('', $role);
        }
        if ($status && $status != 'all') {
            $builder->where('activo', $status);
        }

        //Filtrado por fecha
        if ($registration_start) {
            //Buscando usuarios registrados el mismo día o después de la fecha elegida
            $builder->where('fecha_registro >=', $registration_start);
        }
        if ($registration_end) {
            //Buscando usuarios registrados el mismo día o antes de la fecha elegida
            $builder->where('fecha_registro <=', $registration_end);
        }

        $result = $builder->get();
        return $result->getResultArray();
    }

    /**
     * 
       Funcion que obtiene los distintos tipos de rol de usuario
     * @return array
     */
    function jls_get_user_rol_types()
    {
        $builder = $this->db->table('tipos_rol');
        $result = $builder->get();
        return $result->getResultArray();
    }

    /**
     * 
       Función que cambia el rol de un usuario
     * @param int $user_id
     * @param int $rol_id
     * @return bool
     */
    function jls_change_user_rol($user_id, $rol_id)
    {
        // Validar los parámetros
        if (!is_numeric($user_id) || $user_id <= 0 || !is_numeric($rol_id) || $rol_id <= 0) {
            return false;
        }
        try {
            $builder = $this->db->table('usuarios');
            $data = [
                'id_rol' => $rol_id
            ];
            $builder->update($data, ['id' => $user_id]);
            return $this->db->affectedRows() > 0; //Retorn true o false
        } catch (\Throwable $th) {
            return false;
        }
    }


    /**
     * 
       Función que cambio el estado de un usuario
     * @param mixed $user_id
     * @return bool
     */
    function jls_change_user_status($user_id, $activo)
    {
        try {
            $builder = $this->db->table('usuarios');
            $data = [
                'activo' => !$activo
            ];
            $builder->update($data, ['id' => $user_id]);
            return $this->db->affectedRows() > 0;
        } catch (\Throwable $th) {
            return false;
        }
    }

    /**
       Función que obtiene las rondas disponibles en la tabla tipos_ronda
     * @return array|bool
     */
    function jls_get_rounds()
    {
        try {
            $builder = $this->db->table('tipos_ronda');
            $builder->orderBy('id', 'ASC');
            $result = $builder->get();
            return $result->getResultArray();
        } catch (\Throwable $th) {
            return false;
        }
    }

    /**
       Función que permite insertar enfrentamientos en la tabla rondas
     * @param mixed $tournament_id
     * @param mixed $first_participant_id
     * @param mixed $second_participant_id
     * @param mixed $round_type_id
     * @param mixed $match_position
     * @return bool
     */
    function jls_add_new_tournament_match($tournament_id, $first_participant_id, $second_participant_id, $round_type_id, $match_position)
    {
        // Validación básica
        if (
            !is_numeric($tournament_id) || !is_numeric($first_participant_id) || !is_numeric($second_participant_id) ||
            !is_numeric($round_type_id) || !is_numeric($match_position)
        ) {
            log_message('error', 'Datos inválidos para añadir una nueva ronda');
            return false;
        }

        if ($first_participant_id === $second_participant_id) {
            log_message('error', 'Los participantes no pueden ser iguales');
            return false;
        }

        try {
            $data = [
                'id_torneo' => $tournament_id,
                'id_participante1' => $first_participant_id,
                'id_participante2' => $second_participant_id,
                'id_tipo_ronda' => $round_type_id,
                'posicion_enfrentamiento' => $match_position
            ];

            $this->db->table('rondas')->insert($data);

            if ($this->db->affectedRows() > 0) {
                return true;
            } else {
                log_message('error', 'No se pudo insertar la nueva ronda en la base de datos');
                return false;
            }
        } catch (\CodeIgniter\Database\Exceptions\DatabaseException $e) {
            log_message('error', 'Error de base de datos: ' . $e->getMessage());
            return false;
        } catch (\Throwable $th) {
            log_message('error', 'Error inesperado: ' . $th->getMessage());
            return false;
        }
    }

    /**
       Función que recupera información de las rondas de un torneo
     * @param mixed $tournament_id
     * @return array|bool
     */
    function jls_get_round_info($tournament_id)
    {
        try {
            $builder = $this->db->table('rondas r');
            // $builder->select('r.id, u1.alias_usuario AS participante1_alias, u2.alias_usuario AS participante2_alias, r.resultado, r.id_tipo_ronda, r.posicion_enfrentamiento');
            $builder->select('r.id, i1.alias AS participante1_alias, i2.alias AS participante2_alias, r.resultado, r.id_tipo_ronda, r.posicion_enfrentamiento');
            // sí o sí necesito hacer dos JOIN porque estás consultando la tabla inscripciones dos veces para
            // relacionarla con diferentes campos de la tabla rondas: id_participante1 y id_participante2. 
            $builder->join('inscripciones i1', 'r.id_participante1 = i1.id');
            $builder->join('inscripciones i2', 'r.id_participante2 = i2.id');
            // $builder->join('usuarios u1', 'i1.id_usuario = u1.id');
            // $builder->join('usuarios u2', 'i2.id_usuario = u2.id');
            $builder->where('r.id_torneo', $tournament_id);
            $builder->orderBy('r.posicion_enfrentamiento', 'ASC');
            $result = $builder->get();
            return $result->getResultArray();
        } catch (\Throwable $th) {
            return false;
        }
    }

    /**
     * 
     * @return array
     */
    public function jls_get_tournament_scoring_criteria()
    {
        $builder = $this->db->table('criterios');
        $result = $builder->get();
        return $result->getResultArray();
    }
}
