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
       Función que obtiene los torneos dependiendo de un estado y nombre que se pasa por parámetro
       Por defecto obtiene todos los torneos
     * @param mixed $status
     * @param mixed $name
     * @return array
     */
    function jls_get_tournaments_by_filter($status = null, $name = null, $limit = 10, $offset = 0)
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

        if ($name && $name != null) {
            $builder->like('nombre', $name);
        }

        $builder->limit($limit, $offset);

        $query = $builder->get();
        return $query->getResultArray();
    }

    /**
       Función que genera el numero total de torneos segun los filtros pasados
     * @param mixed $status
     * @param mixed $name
     * @return int|string
     */
    function jls_count_tournaments_by_filter($status = null, $name = null)
    {
        $builder = $this->db->table('torneos');

        switch ($status) {
            case 'ongoing':
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

        if ($name && $name != null) {
            $builder->like('nombre', $name);
        }

        return $builder->countAllResults();
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
       Función que cambia el estado del torneo
     * @param mixed $user_id
     * @param mixed $activo
     * @return bool
     */
    function jls_change_tournament_status($tournament_id, $status)
    {
        try {
            $builder = $this->db->table('torneos');
            $data = [
                'activo' => !$status
            ];
            $builder->update($data, ['id' => $tournament_id]);
            return $this->db->affectedRows() > 0;
        } catch (\Throwable $th) {
            return false;
        }
    }

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
    function jls_get_users_by_filter($alias = null, $role = null, $status = null, $registration_start = null, $registration_end = null, $limit = 10, $offset = 0)
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

        // Aplicar límite y desplazamiento
        $builder->limit($limit, $offset);

        $result = $builder->get();
        return $result->getResultArray();
    }

    /**
       Función que devuelve el número de usuarios y, si es que hay, los que dependen de una serie de filtros
     * @param mixed $alias
     * @param mixed $role
     * @param mixed $status
     * @param mixed $registration_start
     * @param mixed $registration_end
     * @return int|string
     */
    function jls_count_users_by_filter($alias = null, $role = null, $status = null, $registration_start = null, $registration_end = null)
    {
        $builder = $this->db->table('usuarios u');
        $builder->join('tipos_rol r', 'r.id = u.id_rol');

        if ($alias && $alias != 'all') {
            $builder->like('alias_usuario', $alias);
        }
        if ($role && $role != 'all') {
            $builder->where('r.nombre', $role);
        }
        if ($status && $status != 'all') {
            $builder->where('activo', $status);
        }
        if ($registration_start) {
            $builder->where('fecha_registro >=', $registration_start);
        }
        if ($registration_end) {
            $builder->where('fecha_registro <=', $registration_end);
        }

        return $builder->countAllResults();
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
            $builder->select('r.id, i1.alias AS participante1_alias, i2.alias AS participante2_alias, i1.id AS participante1_id, i2.id AS participante2_id, r.resultado, r.id_tipo_ronda, r.posicion_enfrentamiento');
            // sí o sí necesito hacer dos JOIN porque estás consultando la tabla inscripciones dos veces para
            // relacionarla con diferentes campos de la tabla rondas: id_participante1 y id_participante2. 
            $builder->join('inscripciones i1', 'r.id_participante1 = i1.id');
            $builder->join('inscripciones i2', 'r.id_participante2 = i2.id');
            // $builder->join('usuarios u1', 'i1.id_usuario = u1.id');
            // $builder->join('usuarios u2', 'i2.id_usuario = u2.id');
            $builder->where('r.id_torneo', $tournament_id);
            $builder->orderBy('r.id_tipo_ronda', 'ASC');
            $result = $builder->get();
            return $result->getResultArray();
        } catch (\Throwable $th) {
            return false;
        }
    }

    /**
       Función que devuelve los criterios de puntuación de los torneos
     * @return array
     */
    function jls_get_tournament_scoring_criteria()
    {
        $builder = $this->db->table('criterios');
        $result = $builder->get();
        return $result->getResultArray();
    }

    /**
       Función que se encarga de subir puntuaciones y en caso de que haya puntuaciones para dos participantes
       añade el resultado en la tabla rondas
     * @param mixed $tournament_id
     * @param mixed $round_id
     * @param mixed $participant_id
     * @param mixed $scores
     * @throws \Exception
     * @return array
     */
    function jls_upload_participant_scores($tournament_id, $round_id, $participant_id, $scores)
    {
        try {
            // Iniciar transacción solo para insertar puntuaciones
            $this->db->transStart();

            foreach ($scores as $scoreData) {
                $data = [
                    'id_torneo' => $tournament_id,
                    'id_ronda' => $round_id,
                    'id_participante' => $participant_id,
                    'id_criterio' => $scoreData['criterionId'],
                    'puntuacion' => $scoreData['score'],
                ];
                $this->db->table('puntuaciones')->insert($data);
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === FALSE) {
                throw new \Exception("Error al insertar puntuaciones");
            }

            return [
                'status' => 'success',
                'message' => 'Puntuaciones registradas exitosamente.',
            ];
        } catch (\Exception $e) {
            log_message('error', $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Hubo un problema al registrar las puntuaciones.',
            ];
        }
    }

    /**
       Función que determina y registra un ganador de un enfrentamiento
     * @param mixed $tournament_id
     * @param mixed $round_id
     * @return array
     */
    function jls_determine_and_register_winner($tournament_id, $round_id)
    {
        try {
            // Verificar si ya hay puntuaciones de ambos participantes
            $builder = $this->db->table('puntuaciones');
            $builder->select('id_participante, SUM(puntuacion) as total');
            $builder->where('id_ronda', $round_id);
            $builder->where('id_torneo', $tournament_id);
            $builder->groupBy('id_participante');
            $query = $builder->get();
            $results = $query->getResultArray();

            if (count($results) === 2) {
                $winner = ($results[0]['total'] > $results[1]['total']) ?
                    $results[0]['id_participante'] :
                    $results[1]['id_participante'];

                // Actualizar el ganador en la tabla rondas
                $this->db->table('rondas')
                    ->set('resultado', $winner)
                    ->where('id', $round_id)
                    ->update();

                return [
                    'status' => 'success',
                    'message' => 'Ganador registrado exitosamente.',
                    'winner' => $winner,
                ];
            } else {
                return [
                    'status' => 'pending',
                    'message' => 'No se ha podido determinar un ganador aún.',
                ];
            }
            // if (count($results) < 2) {
            //     return [
            //         'status' => 'pending',
            //         'message' => 'No se ha podido determinar un ganador aún.',
            //     ];
            // }
        } catch (\Exception $e) {
            log_message('error', $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Hubo un problema al determinar el ganador.',
            ];
        }
    }

    function add_next_round($tournament_id, $round_id, $winner_id)
    {
        try {
            // Obtener datos del enfrentamiento actual
            $builder = $this->db->table('rondas');
            $currentRound = $builder->select('posicion_enfrentamiento, id_tipo_ronda')
                ->where('id', $round_id)
                ->get()
                ->getRowArray();

            if (!$currentRound) {
                throw new \Exception('Ronda no encontrada.');
            }

            // Calcular siguiente posición y tipo de ronda
            $nextPosition = ceil($currentRound['posicion_enfrentamiento'] / 2);
            $nextRoundType = $currentRound['id_tipo_ronda'] + 1; // Incrementa el tipo de ronda

            // Verificar si ya existe el siguiente enfrentamiento
            $builder = $this->db->table('rondas');
            $existingRound = $builder->where([
                'id_torneo' => $tournament_id,
                'id_tipo_ronda' => $nextRoundType,
                'posicion_enfrentamiento' => $nextPosition,
            ])->get()->getRowArray();

            if ($existingRound) {
                // Actualizar participante2
                $this->db->table('rondas')
                    ->set('id_participante2', $winner_id)
                    ->where('id', $existingRound['id'])
                    ->update();
            } else {
                // Crear un nuevo enfrentamiento
                $data = [
                    'id_torneo' => $tournament_id,
                    'id_participante1' => $winner_id,
                    'id_participante2' => null,
                    'id_tipo_ronda' => $nextRoundType,
                    'posicion_enfrentamiento' => $nextPosition,
                ];
                $this->db->table('rondas')->insert($data);
            }

            return [
                'status' => 'success',
                'message' => 'Siguiente ronda creada o actualizada exitosamente.',
            ];
        } catch (\Exception $e) {
            log_message('error', $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Error al crear la siguiente ronda: ' . $e->getMessage(),
            ];
        }
    }

    /*
     * Apartado de eventos 
     **/

    function jls_upload_new_event($event_name, $event_description, $event_start_date, $event_end_date, $event_location, $event_logo)
    {
        //Comprobación inicial, para corroborar que todos los datos han sido rellenado
        if (empty($event_name) || empty($event_start_date) || empty($event_end_date)) {
            log_message('error', 'Faltan datos para crear el evento');
            return false;
        }
        //Datos correspondientes al torneo
        $data = [
            'nombre' => $event_name,
            'descripcion' => $event_description,
            'fecha_inicio' => $event_start_date,
            'fecha_fin' => $event_end_date,
            'link_mapa' => $event_location,
            'url_imagen' => $event_logo
        ];
        try {
            //Inserto la linea
            $this->db->table('eventos')->insert($data);

            /**
             * affectedRows() nos indicará si después de la inserción, se modificó algo en la tabla
             * > 0, nos indicará que si hubo cambios, = 0 será que no hubo cambio y por ende, no se
             * insertó
             */
            return $this->db->affectedRows() > 0; //Retorn true o false
        } catch (\Exception $e) {
            log_message('error', $e->getMessage());
            return false;
        }
    }

    /**
     * 
     * @param mixed $event_name
     * @param mixed $event_status
     * @param mixed $event_start_date
     * @param mixed $event_end_date
     * @param mixed $limit
     * @param mixed $offset
     * @return array
     */
    function jls_get_events_by_filter($event_name = null, $event_status = null, $event_start_date = null, $event_end_date = null, $limit = 10, $offset = 0)
    {
        // Utilizo nombre como 'e' para evitar ambigüedades
        $builder = $this->db->table('eventos e');
        $builder->select('e.id, e.nombre, e.estado, e.fecha_inicio, e.fecha_fin, e.fecha_creación, e.url_imagen, e.link_mapa'); // Selecciono las columnas necesarias
        // No es necesario hacer JOIN, ya que los eventos no parecen depender de otras tablas (según lo que has proporcionado)

        // Filtros por nombre de evento
        if ($event_name && $event_name != 'all') {
            $builder->like('nombre', $event_name);
        }
        // Filtro por estado de evento
        if ($event_status && $event_status != 'all') {
            $builder->where('estado', $event_status);
        }

        // Filtro por fechas
        if ($event_start_date) {
            // Buscando eventos que empiezan en la fecha indicada o después
            $builder->where('fecha_inicio >=', $event_start_date);
        }
        if ($event_end_date) {
            // Buscando eventos que terminan en la fecha indicada o antes
            $builder->where('fecha_fin <=', $event_end_date);
        }

        // Aplicar límite y desplazamiento
        $builder->limit($limit, $offset);

        $result = $builder->get();
        return $result->getResultArray();
    }

    /**
     * 
     * @param mixed $event_name
     * @param mixed $event_status
     * @param mixed $event_start_date
     * @param mixed $event_end_date
     * @return int|string
     */
    function jls_count_events_by_filter($event_name = null, $event_status = null, $event_start_date = null, $event_end_date = null)
    {
        $builder = $this->db->table('eventos e');

        // Filtros por nombre de evento
        if ($event_name && $event_name != 'all') {
            $builder->like('nombre', $event_name);
        }
        // Filtro por estado de evento
        if ($event_status && $event_status != 'all') {
            $builder->where('estado', $event_status);
        }

        // Filtro por fechas
        if ($event_start_date) {
            // Buscando eventos que empiezan en la fecha indicada o después
            $builder->where('fecha_inicio >=', $event_start_date);
        }
        if ($event_end_date) {
            // Buscando eventos que terminan en la fecha indicada o antes
            $builder->where('fecha_fin <=', $event_end_date);
        }

        return $builder->countAllResults();
    }

    function jls_get_event_details($event_id)
    {
        try {
            $builder = $this->db->table('eventos');
            $builder->where('id', $event_id);
            $result = $builder->get();
            return $result->getRow();
        } catch (\Exception $e) {
            log_message('error', $e->getMessage());
            return false;
        }
    }

    /**
       Función que obtiene la imagen de un evento
     * @param mixed $event_id
     * @return mixed
     */
    function jls_get_event_image_name($event_id)
    {
        try {
            $builder = $this->db->table('eventos');
            $builder->where('id', $event_id);
            $result = $builder->get();
            return $result->getRow()->url_imagen;
        } catch (\Exception $e) {
            log_message('error', $e->getMessage());
            return false;
        }
    }
    function jls_update_event_data($event_id, $event_name, $event_description, $event_start_date, $event_end_date, $event_location, $event_image)
    {
        try {
            $builder = $this->db->table('eventos');
            $data = [
                'nombre' => $event_name,
                'descripcion' => $event_description,
                'fecha_inicio' => $event_start_date,
                'fecha_fin' => $event_end_date,
                'link_mapa' => $event_location,
                'url_imagen' => $event_image
            ];
            $builder->update($data, ['id' => $event_id]);
            return $this->db->affectedRows() > 0;
        } catch (\Throwable $th) {
            log_message('error', $th->getMessage());
            return false;
        }
    }
}
