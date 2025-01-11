<?php

namespace App\models;

use CodeIgniter\Model;
use InvalidArgumentException;

class DataBaseHandler extends Model
{
    /**
       Realiza una consulta genérica para obtener un único registro desde la base de datos.
     *
     * @param string $table Nombre de la tabla donde se realizará la consulta.
     * @param array $conditions Arreglo asociativo con las condiciones para filtrar los resultados.
     *                          Ejemplo: ['id' => 1].
     * @return object|null Devuelve un objeto con los datos del registro si se encuentra, o null si no hay coincidencias.
     */

    public function fetchRecord(string $table, array $conditions = []): ?object
    {
        $builder = $this->db->table($table);
        if (!empty($conditions)) {
            $builder->where($conditions);
        }
        $result = $builder->get();
        return $result->getRow(); // Devuelve un registro o null
    }

    /**
       Realiza una actualización genérica de datos en una tabla específica.
     *
     * @param string $table Nombre de la tabla donde se realizará la actualización.
     * @param array $data Arreglo asociativo con los datos a actualizar. Ejemplo: ['nombre' => 'Juan'].
     * @param array $conditions Arreglo asociativo con las condiciones para identificar los registros a actualizar.
     *                          Ejemplo: ['id' => 1].
     * @return bool Devuelve `true` si al menos un registro fue actualizado, o `false` si no hubo cambios.
     */
    private function updateRecord(string $table, array $data, array $conditions): bool
    {
        $builder = $this->db->table($table);
        $builder->update($data, $conditions);
        return $this->db->affectedRows() > 0;
    }

    /**
       Verifica si un usuario con las credenciales proporcionadas existe en la base de datos.
     *
     * @param string $user Nombre de usuario.
     * @param string $password Contraseña del usuario.
     * @return int Devuelve el ID del usuario si se encuentra, o 0 si no existe.
     */
    public function jls_check_user($user, $password)
    {
        $conditions = ['nombre_usuario' => $user, 'contraseña' => $password];
        $user = $this->fetchRecord('usuarios', $conditions);
        return $user ? $user->id : 0;
    }

    /**
       Comprueba si un nombre de usuario ya existe en la base de datos.
     *
     * @param string $user Nombre de usuario a buscar.
     * @return int Devuelve el ID del usuario si existe, o 0 si no se encuentra.
     */
    function jls_check_user_name_exists($user)
    {
        $conditions = ['nombre_usuario' => $user];
        $user = $this->fetchRecord('usuarios', $conditions);
        return $user ? $user->id : 0;
    }

    /**
       Actualiza el campo de última conexión (`ultima_conexion`) de un usuario específico.
     *
     * @param int $user_id ID del usuario cuyo campo de última conexión se actualizará.
     * @return bool Devuelve `true` si la actualización fue exitosa, o `false` si ocurrió un error o no se actualizó ningún registro.
     *
     * Nota:
     * - Este método utiliza la expresión `CURRENT_TIME` directamente para asignar la fecha y hora actual.
     * - Atrapa posibles excepciones para evitar errores en tiempo de ejecución.
     */
    public function jls_update_last_connection($user_id)
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
       Registra un nuevo usuario en la base de datos.
     *
     * @param string $name Alias del usuario.
     * @param string $user Nombre único del usuario.
     * @param string $password Contraseña del usuario.
     * @return bool Devuelve `true` si el registro fue exitoso, o `false` si ocurrió un error
     *              o si el nombre de usuario ya existe.
     */
    public function jls_register_user($name, $user, $password)
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
       Recupera la información de un usuario específico basado en su ID.
     *
     * @param int $user_id ID del usuario a buscar.
     * @return object|null Devuelve un objeto con los datos del usuario si se encuentra,
     *                     o `null` si no existe.
     */
    public function jls_get_user_data($user_id)
    {
        return $this->fetchRecord('usuarios', ['id' => $user_id]);
    }

    /**
       Actualiza la foto de perfil de un usuario.
     *
     * @param int $user_id ID del usuario cuyo perfil será actualizado.
     * @param string $user_img_profile Ruta de la nueva foto de perfil.
     * @return bool Devuelve `true` si la actualización fue exitosa, o `false` si ocurrió un error.
     */
    public function jls_update_user_img_profile($user_id, $user_img_profile)
    {


        try {
            $data = ['foto_perfil' => $user_img_profile];
            $conditions = ['id' => $user_id];
            return $this->updateRecord('usuarios', $data, $conditions);
        } catch (\Throwable $th) {
            log_message('error', 'Error al cambiar la foto de perfil: ' . $th->getMessage());
            return false;
        }
    }

    /**
       Obtiene la foto de perfil de un usuario específico.
     *
     * @param int $user_id ID del usuario cuyo perfil se solicita.
     * @return string|null Devuelve la ruta de la foto de perfil si existe, o `null` si no se encuentra.
     */
    public function jls_get_user_profile_picture($user_id)
    {
        $user = $this->fetchRecord('usuarios', ['id' => $user_id]);
        return $user ? $user->foto_perfil : null;
    }

    /**
     * Recupera la información de un rol específico basado en su ID.
     *
     * @param int $rol_id ID del rol a buscar.
     * @return object|null Devuelve un objeto con los datos del rol si se encuentra,
     *                     o `null` si no existe.
     */
    public function jls_get_rol_by_id($rol_id)
    {
        return $this->fetchRecord('tipos_rol', ['id' => $rol_id]);
    }

    /**
       Recupera toda la información de un torneo específico basado en su ID.
     *
     * @param int $tournament_id ID del torneo a buscar.
     * @return object|null Devuelve un objeto con los datos del torneo si se encuentra,
     *                     o `null` si no existe.
     */
    public function jls_get_tournament_info($tournament_id)
    {
        return $this->fetchRecord('torneos', ['id' => $tournament_id]);
    }

    /**
       Crea un nuevo torneo en la base de datos.
     *
     * @param string $nombre Nombre del torneo.
     * @param string $fecha_inicio Fecha de inicio del torneo (formato: YYYY-MM-DD).
     * @param string $fecha_fin Fecha de finalización del torneo (formato: YYYY-MM-DD).
     * @param string $logo Ruta del logotipo del torneo.
     * @return bool Devuelve `true` si el torneo fue creado exitosamente, o `false` si ocurrió un error.
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
            return false;
        }
    }

    /**
       Recupera torneos de la base de datos basados en filtros opcionales.
     *
     * @param string|null $status Estado del torneo. Valores aceptados:
     *                            - 'ongoing' (en curso),
     *                            - 'active' (activo),
     *                            - 'inactive' (inactivo),
     *                            - 'finished' (finalizado),
     *                            - null (todos los torneos).
     * @param string|null $name Nombre o parte del nombre del torneo para buscar.
     * @param int $limit Número máximo de registros a recuperar.
     * @param int $offset Desplazamiento inicial para la paginación.
     * @return array Devuelve un arreglo de torneos que coinciden con los filtros.
     */

    public function jls_get_tournaments_by_filter($status = null, $name = null, $limit = 10, $offset = 0)
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
       Cuenta el número de torneos en función de los filtros aplicados.
     *
     * Esta función permite contar los torneos que cumplen con ciertos criterios, como su estado (en curso, activo, inactivo, finalizado) 
     * y/o su nombre. Si no se proporcionan filtros, devuelve el total de torneos.
     *
     * @param string|null $status Estado del torneo. Puede ser:
     *  - 'ongoing': Torneos en curso (fecha actual entre fecha de inicio y fecha de fin).
     *  - 'active': Torneos activos (marcados como activos).
     *  - 'inactive': Torneos inactivos (marcados como inactivos).
     *  - 'finished': Torneos finalizados (fecha de fin pasada y marcados como inactivos).
     *  - null: No se filtra por estado.
     * @param string|null $name Nombre parcial o completo del torneo para filtrar. Si es null, no se filtra por nombre.
     * @return int Número de torneos que cumplen con los filtros aplicados.
     */

    public function jls_count_tournaments_by_filter($status = null, $name = null)
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
       Registra un nuevo participante en un torneo.
     *
     * Esta función permite inscribir a un usuario en un torneo, verificando previamente que no esté inscrito. 
     * Si el usuario ya está inscrito, no se realiza ninguna acción.
     *
     * @param string $alias Alias del participante que se utilizará en el torneo.
     * @param int $tournament_id ID del torneo en el que se quiere inscribir al usuario.
     * @param int $user_id ID del usuario que se desea inscribir.
     * @return bool Devuelve true si la inscripción se realiza correctamente, o false si:
     *  - El usuario ya está inscrito.
     *  - Ocurre un error durante el proceso de inscripción.
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
       Verifica si un usuario ya está inscrito en un torneo.
     *
     * Esta función consulta la tabla de inscripciones para determinar si un usuario específico 
     * ya se encuentra registrado en un torneo determinado.
     *
     * @param int $tournament_id ID del torneo.
     * @param int $user_id ID del usuario.
     * @return bool Devuelve true si el usuario está inscrito en el torneo, o false si no lo está.
     */

    private function jls_check_participant_exists($tournament_id, $user_id)
    {
        $participant = $this->fetchRecord('inscripciones', [
            'id_torneo' => $tournament_id,
            'id_usuario' => $user_id
        ]);

        return $participant ? true : false;
    }

    /**
       Cuenta el número total de participantes en la tabla inscripciones.
     *
     * @param array $conditions (Opcional) Condiciones para filtrar los registros. Ejemplo: ['id_torneo' => 1].
     * @return int Devuelve el número total de participantes que cumplen las condiciones.
     */
    public function jls_count_participants(array $conditions = []): int
    {
        $builder = $this->db->table('inscripciones');
        if (!empty($conditions)) {
            $builder->where($conditions);
        }
        return $builder->countAllResults(); // Devuelve el conteo total
    }


    /**
      Obtiene los participantes inscritos en un torneo.
     *
     * Esta función devuelve un listado de participantes inscritos en un torneo específico. 
     * Si se indica que existe un "bracket" (estructura de rondas), se filtran las inscripciones 
     * para excluir a aquellos participantes que ya están asignados en alguna ronda.
     *
     * @param int $tournament_id ID del torneo.
     * @param bool $existsBracket Indica si existe un bracket (estructura de rondas) en el torneo.
     *                            Si es true, filtra las inscripciones no asignadas en rondas.
     * @return array|false Devuelve un arreglo con los participantes del torneo o false en caso de error.
     *                     Cada participante incluye información como alias, nombre de usuario, foto de perfil y estado activo.
     */

    public function jls_get_tournament_participants($tournament_id, $existsBracket = false)
    {
        try {
            // Validar ID del torneo
            if (!is_numeric($tournament_id) || $tournament_id <= 0) {
                throw new InvalidArgumentException("El ID del torneo debe ser un valor numérico positivo.");
            }

            // Si existe un bracket, filtrar participantes que no están en rondas
            if ($existsBracket) {
                // Verificar si existen rondas para el torneo
                $exists = $this->db->table('rondas')
                    ->where('id_torneo', $tournament_id)
                    ->countAllResults();

                if ((int)$exists === 0) {
                    // Si no hay rondas, devolver todas las inscripciones activas del torneo
                    $builder = $this->db->table('inscripciones i');
                    $builder->select('i.id AS inscripcion_id, i.alias, i.id_usuario, i.id_torneo, i.activo, u.nombre_usuario, u.foto_perfil')
                        ->join('usuarios u', 'i.id_usuario = u.id', 'inner')
                        ->where('i.id_torneo', $tournament_id)
                        ->where('i.activo', 1);

                    return $builder->get()->getResultArray();
                }

                // Subconsulta para obtener las inscripciones ya asignadas en las rondas
                $subquery = $this->db->table('rondas')
                    ->select('id_participante1')
                    ->union(
                        $this->db->table('rondas')
                            ->select('id_participante2')
                    )
                    ->where('id_torneo', $tournament_id)
                    ->getCompiledSelect();

                // Consulta principal para obtener inscripciones no asignadas en rondas
                $builder = $this->db->table('inscripciones i');
                $builder->select('i.id, i.alias, i.id_usuario, i.id_torneo, i.activo, u.nombre_usuario, u.foto_perfil')
                    ->join('usuarios u', 'i.id_usuario = u.id', 'inner')
                    ->where('i.id_torneo', $tournament_id)
                    ->where('i.activo', 1)
                    ->where("i.id NOT IN ({$subquery})", null, false);

                return $builder->get()->getResultArray();
            }


            // Consulta predeterminada: Obtener inscripciones activas
            $builder = $this->db->table('inscripciones i');
            $builder->select('i.id, i.alias, i.id_usuario, u.nombre_usuario, u.foto_perfil, u.activo')
                ->join('usuarios u', 'i.id_usuario = u.id', 'inner')
                ->where('i.id_torneo', $tournament_id)
                ->where('i.activo', 1);

            return $builder->get()->getResultArray();
        } catch (InvalidArgumentException $e) {
            log_message('error', 'Argumento inválido: ' . $e->getMessage());
            return false;
        } catch (\Throwable $th) {
            log_message('error', 'Error al obtener participantes del torneo: ' . $th->getMessage());
            return false;
        }
    }


    /**
       Cambia el estado activo de un participante en un torneo.
     *
     * Esta función permite alternar el estado activo/inactivo de un participante en la tabla de inscripciones.
     * 
     * @param int $participant_id ID del participante cuya inscripción será actualizada.
     * @param bool $status Estado actual del participante (true para activo, false para inactivo).
     * @return bool Devuelve true si la actualización se realizó con éxito, o false en caso de error.
     */

    public function jls_change_participant_status($participant_id, $status)
    {
        try {
            $builder = $this->db->table('inscripciones');
            $data = [
                'activo' => !$status
            ];
            $builder->update($data, ['id' => $participant_id]);
            return $this->db->affectedRows() > 0;
        } catch (\Throwable $th) {
            log_message('error', 'Error al cambiar el estado del participante: ' . $th->getMessage());
            return false;
        }
    }


    /**
       Obtiene el nombre o la ruta del logotipo de un torneo.
     *
     * Esta función recupera la ruta del logotipo de un torneo específico utilizando su ID.
     *
     * @param int $tournament_id ID del torneo.
     * @return string|null Devuelve la ruta del logotipo si existe, o null si no se encuentra el torneo.
     */
    public function jls_get_tournament_logo_name($tournament_id)
    {
        $tournament = $this->fetchRecord('torneos', ['id' => $tournament_id]);
        return $tournament ? $tournament->logo_path : null;
    }

    /**
       Actualiza los datos de un torneo específico.
     *
     * Esta función permite modificar los datos principales de un torneo, incluyendo el nombre, 
     * fechas de inicio y fin, estado de actividad y la ruta del logotipo.
     *
     * @param int $id ID del torneo a actualizar.
     * @param string $nombre Nuevo nombre del torneo.
     * @param string $fecha_inicio Nueva fecha de inicio del torneo.
     * @param string $fecha_fin Nueva fecha de finalización del torneo.
     * @param bool $activo Indica si el torneo está activo.
     * @param string|null $logo_path Ruta del logotipo del torneo. Se puede omitir si no se desea actualizar.
     * @return bool Devuelve true si la actualización fue exitosa, o false en caso de error.
     */
    public function jls_update_tournament_data($id, $nombre, $fecha_inicio, $fecha_fin, $activo, $logo_path)
    {
        try {
            $data = [
                'nombre' => $nombre,
                'fecha_inicio' => $fecha_inicio,
                'fecha_fin' => $fecha_fin,
                'activo' => $activo,
                'logo_path' => isset($logo_path) && $logo_path !== '' ? $logo_path : null
            ];
            $conditions = ['id' => $id];
            return $this->updateRecord('torneos', $data, $conditions);
        } catch (\Throwable $th) {
            return false;
        }
    }

    /**
       Cambia el estado de un torneo.
     *
     * Invierte el estado de actividad de un torneo específico.
     *
     * @param int $tournament_id ID del torneo a actualizar.
     * @param bool $status Estado actual del torneo (activo o inactivo).
     * @return bool Devuelve true si la operación fue exitosa, o false en caso de error.
     */
    public function jls_change_tournament_status($tournament_id, $status)
    {
        try {
            $data = ['activo' => !$status];
            $conditions = ['id' => $tournament_id];
            return $this->updateRecord('torneos', $data, $conditions);
        } catch (\Throwable $th) {
            return false;
        }
    }

    /**
       Obtiene usuarios según filtros específicos.
     *
     * Permite buscar usuarios basados en filtros como alias, rol, estado, y rango de fechas de registro.
     * También permite limitar la cantidad de resultados.
     *
     * @param string|null $alias Alias del usuario a buscar.
     * @param string|null $role Rol del usuario a buscar.
     * @param int|null $status Estado del usuario (activo/inactivo).
     * @param string|null $registration_start Fecha de inicio del rango de registro.
     * @param string|null $registration_end Fecha de fin del rango de registro.
     * @param int $limit Límite de resultados (por defecto 10).
     * @param int $offset Desplazamiento para paginación (por defecto 0).
     * @return array Devuelve un arreglo con los usuarios que cumplen los filtros.
     */
    public function jls_get_users_by_filter($alias = null, $role = null, $status = null, $registration_start = null, $registration_end = null, $limit = 10, $offset = 0)
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
       Cuenta la cantidad de usuarios según filtros específicos.
     *
     * Devuelve el número total de usuarios que cumplen con los filtros proporcionados, como alias, rol, estado, y rango de fechas de registro.
     *
     * @param string|null $alias Alias del usuario a buscar.
     * @param string|null $role Rol del usuario a buscar.
     * @param int|null $status Estado del usuario (activo/inactivo).
     * @param string|null $registration_start Fecha de inicio del rango de registro.
     * @param string|null $registration_end Fecha de fin del rango de registro.
     * @return int Devuelve el número total de usuarios que cumplen los filtros.
     */
    public function jls_count_users_by_filter($alias = null, $role = null, $status = null, $registration_start = null, $registration_end = null)
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
       Obtiene todos los tipos de rol disponibles.
     *
     * Consulta la tabla `tipos_ronda` para obtener los roles disponibles.
     *
     * @return array Devuelve un arreglo con los tipos de rol disponibles.
     */
    public function jls_get_user_rol_types()
    {
        $builder = $this->db->table('tipos_rol');
        $result = $builder->get();
        return $result->getResultArray();
    }

    /**
       Cambia el rol de un usuario.
     *
     * Actualiza el rol de un usuario en la base de datos.
     *
     * @param int $user_id ID del usuario a actualizar.
     * @param int $rol_id ID del nuevo rol.
     * @return bool Devuelve true si la operación fue exitosa, o false en caso de error.
     */
    public function jls_change_user_rol($user_id, $rol_id)
    {
        // Validar los parámetros
        if (!is_numeric($user_id) || $user_id <= 0 || !is_numeric($rol_id) || $rol_id <= 0) {
            return false;
        }
        try {
            $data = ['id_rol' => $rol_id];
            $conditions = ['id' => $user_id];
            return $this->updateRecord('usuarios', $data, $conditions);
        } catch (\Throwable $th) {
            return false;
        }
    }


    /**
       Cambia el estado de un usuario.
     *
     * Invierte el estado de actividad (activo/inactivo) de un usuario específico.
     *
     * @param int $user_id ID del usuario a actualizar.
     * @param bool $activo Estado actual del usuario.
     * @return bool Devuelve true si la operación fue exitosa, o false en caso de error.
     */
    public function jls_change_user_status($user_id, $activo)
    {
        try {
            $data = ['activo' => !$activo];
            $conditions = ['id' => $user_id];
            return $this->updateRecord('usuarios', $data, $conditions);
        } catch (\Throwable $th) {
            return false;
        }
    }

    /**
       Obtiene todas las rondas disponibles.
     *
     * Recupera todas las rondas definidas en la tabla `tipos_ronda`, ordenadas por su ID.
     *
     * @return array|bool Devuelve un arreglo con las rondas disponibles, o false en caso de error.
     */
    public function jls_get_rounds()
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
     * Añade un nuevo enfrentamiento al torneo.
     *
     * Registra un enfrentamiento en la tabla `rondas` entre dos participantes en un torneo.
     *
     * @param int $tournament_id ID del torneo.
     * @param int $first_participant_id ID del primer participante.
     * @param int $second_participant_id ID del segundo participante.
     * @param int $round_type_id ID del tipo de ronda.
     * @param int $match_position Posición del enfrentamiento en la ronda.
     * @return bool Devuelve true si el enfrentamiento se añadió correctamente, o false en caso de error.
     */
    public function jls_add_new_tournament_match($tournament_id, $first_participant_id, $second_participant_id, $round_type_id, $match_position)
    {
        // Validación básica
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
       Recupera información de las rondas de un torneo.
     *
     * Obtiene detalles de las rondas, incluyendo participantes y resultados, en un torneo específico.
     *
     * @param int $tournament_id ID del torneo.
     * @return array|bool Devuelve un arreglo con la información de las rondas o false en caso de error.
     */
    public function jls_get_round_info($tournament_id)
    {
        try {
            $builder = $this->db->table('rondas r');

            $builder->select('r.id, i1.alias AS participante1_alias, i2.alias AS participante2_alias, i1.id AS participante1_id, i2.id AS participante2_id, r.resultado, r.id_tipo_ronda, r.posicion_enfrentamiento');

            $builder->join('inscripciones i1', 'r.id_participante1 = i1.id');
            $builder->join('inscripciones i2', 'r.id_participante2 = i2.id');

            $builder->where('r.id_torneo', $tournament_id);
            $builder->orderBy('r.id_tipo_ronda', 'ASC');
            $result = $builder->get();
            return $result->getResultArray();
        } catch (\Throwable $th) {
            return false;
        }
    }

    /**
       Recupera los criterios de puntuación de los torneos.
     *
     * Devuelve los criterios que se usan para evaluar las puntuaciones en los torneos.
     *
     * @return array Devuelve un arreglo con los criterios de puntuación.
     */
    public function jls_get_tournament_scoring_criteria()
    {
        $builder = $this->db->table('criterios');
        $result = $builder->get();
        return $result->getResultArray();
    }

    /**
       Sube puntuaciones de un participante en una ronda de un torneo.
     *
     * Registra las puntuaciones de un participante en la tabla `puntuaciones`. Si ya existen puntuaciones de ambos participantes, se actualiza la tabla de rondas.
     *
     * @param int $tournament_id ID del torneo.
     * @param int $round_id ID de la ronda.
     * @param int $participant_id ID del participante.
     * @param array $scores Arreglo con los criterios y puntuaciones del participante.
     * @return array Devuelve un arreglo con el estado y mensaje de la operación.
     */
    public function jls_upload_participant_scores($tournament_id, $round_id, $participant_id, $scores)
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
       Determina y registra al ganador de un enfrentamiento.
     *
     * Calcula el ganador de un enfrentamiento basado en las puntuaciones acumuladas y lo registra en la tabla `rondas`.
     *
     * @param int $tournament_id ID del torneo.
     * @param int $round_id ID de la ronda.
     * @return array Devuelve un arreglo con el estado, mensaje y ganador si es determinado.
     */
    public function jls_determine_and_register_winner($tournament_id, $round_id)
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
                $participant1 = $results[0];
                $participant2 = $results[1];

                if ($participant1['total'] == $participant2['total']) {
                    // Enviar estado de "retry" si hay empate
                    return [
                        'status' => 'retry',
                        'message' => 'Las puntuaciones están empatadas. Se requiere una nueva votación.',
                    ];
                }

                // Determinar ganador
                $winner = ($participant1['total'] > $participant2['total']) ?
                    $participant1['id_participante'] :
                    $participant2['id_participante'];

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
        } catch (\Exception $e) {
            log_message('error', $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Hubo un problema al determinar el ganador.',
            ];
        }
    }


    /**
       Crea o actualiza la siguiente ronda en un torneo.
     *
     * Calcula la posición y el tipo de ronda para el siguiente enfrentamiento en un torneo y lo registra en la tabla `rondas`.
     *
     * @param int $tournament_id ID del torneo.
     * @param int $round_id ID de la ronda actual.
     * @param int $winner_id ID del participante ganador.
     * @return array Devuelve un arreglo con el estado y mensaje de la operación.
     */
    public function jls_add_next_round($tournament_id, $round_id, $winner_id)
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
            /**
             * posiciones redondeo hacia arriba
             * 1 -> 0.5= posicion 1 en la siguiente ronda
             * 2 -> 1 - posición 1 en la siguiente ronda
             * 3 -> 2 = posición 2 en la siguiente ronda
             * 4 -> 2 = posición 2 en la siguiente ronda
             */
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

    /**
       Crea un nuevo evento.
     *
     * Registra un evento en la tabla `eventos` con sus detalles.
     *
     * @param string $event_name Nombre del evento.
     * @param string $event_description Descripción del evento.
     * @param string $event_start_date Fecha de inicio del evento.
     * @param string $event_end_date Fecha de finalización del evento.
     * @param string|null $event_location Ubicación del evento (opcional).
     * @param string|null $event_logo URL del logo del evento (opcional).
     * @return bool Devuelve true si el evento fue creado exitosamente, o false en caso de error.
     */
    public function jls_upload_new_event($event_name, $event_description, $event_start_date, $event_end_date, $event_location, $event_logo)
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
       Obtiene una lista de eventos aplicando filtros como nombre, estado, fecha y estado activo.
     *
     * @param string|null $event_name Filtro por nombre del evento (puede ser parcial).
     * @param string|null $event_status Filtro por estado del evento (por ejemplo, "activo" o "inactivo").
     * @param bool|null $event_active Filtro por estado activo del evento (1 o 0).
     * @param string|null $event_start_date Fecha mínima de inicio para los eventos.
     * @param string|null $event_end_date Fecha máxima de fin para los eventos.
     * @param int $limit Número máximo de resultados a devolver.
     * @param int $offset Desplazamiento inicial para los resultados.
     * @return array Devuelve un arreglo con los eventos que cumplen los filtros.
     */

    public function jls_get_events_by_filter($event_name = null, $event_status = null, $event_active = null, $event_start_date = null, $event_end_date = null, $limit = 10, $offset = 0)
    {
        // Utilizo nombre como 'e' para evitar ambigüedades
        $builder = $this->db->table('eventos e');
        $builder->select('e.id, e.nombre, e.descripcion, e.estado, e.activo, e.fecha_inicio, e.fecha_fin, e.fecha_creación, e.url_imagen, e.link_mapa'); // Selecciono las columnas necesarias
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

        //Filtro por estado activo
        if ($event_active != null) {
            $builder->where('activo', $event_active);
        }

        // Aplicar límite y desplazamiento
        $builder->limit($limit, $offset);

        $result = $builder->get();
        return $result->getResultArray();
    }

    /**
       Cuenta el número de eventos que cumplen con los filtros especificados.
     *
     * @param string|null $event_name Filtro por nombre del evento (puede ser parcial).
     * @param string|null $event_status Filtro por estado del evento.
     * @param bool|null $event_active Filtro por estado activo (1 o 0).
     * @param string|null $event_start_date Fecha mínima de inicio para los eventos.
     * @param string|null $event_end_date Fecha máxima de fin para los eventos.
     * @return int|string Devuelve la cantidad de eventos que cumplen los filtros.
     */

    public function jls_count_events_by_filter($event_name = null, $event_status = null, $event_active = null, $event_start_date = null, $event_end_date = null)
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

        //Filtro por estado activo
        if ($event_active) {
            $builder->where('activo', $event_active);
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

    /**
       Obtiene los detalles de un evento específico por su ID.
     *
     * @param int $event_id ID del evento a buscar.
     * @return object|bool Devuelve un objeto con los detalles del evento si existe, o false en caso de error.
     */

    public function jls_get_event_details($event_id)
    {
        try {
            return $this->fetchRecord('eventos', ['id' => $event_id]);
        } catch (\Exception $e) {
            log_message('error', $e->getMessage());
            return false;
        }
    }

    /**
       Obtiene el nombre del archivo de imagen asociado a un evento.
     *
     * @param int $event_id ID del evento.
     * @return string|null Devuelve el nombre de la imagen o null si no existe.
     */
    public function jls_get_event_image_name($event_id)
    {
        try {
            $event = $this->fetchRecord('eventos', ['id' => $event_id]);
            return $event ? $event->url_imagen : null;
        } catch (\Exception $e) {
            log_message('error', $e->getMessage());
            return false;
        }
    }

    /**
       Actualiza los datos de un evento existente.
     *
     * @param int $event_id ID del evento a actualizar.
     * @param string $event_name Nuevo nombre del evento.
     * @param string $event_description Nueva descripción del evento.
     * @param string $event_start_date Nueva fecha de inicio del evento.
     * @param string $event_end_date Nueva fecha de fin del evento.
     * @param string $event_location Nueva ubicación del evento (link de mapa).
     * @param string $event_image Nuevo nombre del archivo de imagen del evento.
     * @return bool Devuelve true si la actualización fue exitosa, o false en caso de error.
     */
    public function jls_update_event_data($event_id, $event_name, $event_description, $event_start_date, $event_end_date, $event_location, $event_image)
    {
        try {
            $data = [
                'nombre' => $event_name,
                'descripcion' => $event_description,
                'fecha_inicio' => $event_start_date,
                'fecha_fin' => $event_end_date,
                'link_mapa' => $event_location,
                'url_imagen' => $event_image
            ];
            $conditions = ['id' => $event_id];
            return $this->updateRecord('eventos', $data, $conditions);
        } catch (\Throwable $th) {
            log_message('error', $th->getMessage());
            return false;
        }
    }

    /**
       Cambia el estado activo de un evento.
     *
     * @param int $event_id ID del evento a actualizar.
     * @param bool $active Estado actual del evento (1 o 0).
     * @return bool Devuelve true si el cambio fue exitoso, o false en caso de error.
     */
    public function jls_change_event_active_status($event_id, $active)
    {
        try {
            $data = ['activo' => !$active];
            $conditions = ['id' => $event_id];
            return $this->updateRecord('eventos', $data, $conditions);
        } catch (\Throwable $th) {
            log_message('error', $th->getMessage());
            return false;
        }
    }

    /**
       Obtiene las rondas en las que un usuario aún no ha subido su video.
     *
     * @param int $userId ID del usuario.
     * @return array Devuelve un arreglo con las rondas faltantes de video.
     */
    public function jls_get_user_rounds_without_video($userId)
    {
        try {
            // Validar que el ID del usuario sea un número válido
            if (!is_numeric($userId)) {
                throw new InvalidArgumentException('El ID del usuario no es válido.');
            }

            // Inicializar el constructor de consultas para la tabla "rondas"
            $builder = $this->db->table('rondas r');

            // Seleccionar columnas necesarias de las tablas relacionadas
            $builder->select('
            r.id AS ronda_id,                       
            r.id_torneo,                            
            r.id_tipo_ronda,                        
            r.posicion_enfrentamiento,             
            t.nombre AS torneo_nombre,              
            tr.nombre AS ronda_nombre,              
            r.url_video_participante1,              
            r.url_video_participante2,              
            i1.alias AS participante1_alias,        
            i2.alias AS participante2_alias,        
            i1.id_usuario AS participante1_id,      
            i2.id_usuario AS participante2_id       
        ');

            // Unir con la tabla "inscripciones" para obtener los participantes
            $builder->join('inscripciones i1', 'r.id_participante1 = i1.id', 'inner')
                ->join('inscripciones i2', 'r.id_participante2 = i2.id', 'inner');

            // Unir con la tabla "torneos" para obtener detalles del torneo
            $builder->join('torneos t', 't.id = r.id_torneo', 'inner');

            // Unir con la tabla "tipos_ronda" para obtener detalles del tipo de ronda
            $builder->join('tipos_ronda tr', 'tr.id = r.id_tipo_ronda', 'inner');

            // Filtrar rondas donde el usuario aún no ha subido su video
            $builder->groupStart()
                ->groupStart()
                ->where('i1.id_usuario', $userId)            // El usuario es participante 1
                ->where('r.url_video_participante1 IS NULL') // Aún no ha subido su video
                ->groupEnd()
                ->orGroupStart()
                ->where('i2.id_usuario', $userId)            // El usuario es participante 2
                ->where('r.url_video_participante2 IS NULL') // Aún no ha subido su video
                ->groupEnd()
                ->groupEnd();

            // Ordenar los resultados por torneo y tipo de ronda
            $builder->orderBy('r.id_torneo', 'ASC')
                ->orderBy('r.id_tipo_ronda', 'ASC');

            // Ejecutar la consulta y obtener los resultados
            $result = $builder->get()->getResultArray();

            // Procesar los resultados para determinar el rol del usuario en cada ronda
            foreach ($result as &$round) {
                if ($round['participante1_id'] == $userId) {
                    $round['participant_role'] = 1; // Usuario es participante 1
                } elseif ($round['participante2_id'] == $userId) {
                    $round['participant_role'] = 2; // Usuario es participante 2
                } else {
                    $round['participant_role'] = null; // No pertenece a esta ronda
                }
            }

            return $result; // Devolver las rondas faltantes de video
        } catch (\Throwable $th) {
            // Registrar el error en el log y devolver un arreglo vacío
            log_message('error', 'Error al obtener rondas con videos faltantes: ' . $th->getMessage());
            return [];
        }
    }



    /**
       Obtiene las rondas con los videos subidos por un usuario.
     *
     * @param int $userId ID del usuario.
     * @return array Devuelve un arreglo con las rondas y los videos subidos por el usuario.
     */
    public function getUserUploadedVideos($userId)
    {
        try {
            // Validar el ID del usuario
            if (!is_numeric($userId)) {
                throw new InvalidArgumentException('El ID del usuario no es válido.');
            }

            // Tabla principal: 'rondas' (alias 'r')
            $builder = $this->db->table('rondas r');

            // Seleccionar las columnas necesarias para obtener los detalles de la ronda
            $builder->select('
            r.id AS ronda_id,
            r.id_torneo,
            r.id_tipo_ronda,
            r.posicion_enfrentamiento,
            t.nombre AS torneo_nombre,
            tr.nombre AS ronda_nombre,
            r.url_video_participante1,
            r.url_video_participante2,
            i1.id_usuario AS participante1_id,
            i2.id_usuario AS participante2_id
        ')
                // Unir con la tabla de inscripciones para identificar los participantes
                ->join('inscripciones i1', 'r.id_participante1 = i1.id', 'left') // Participante 1
                ->join('inscripciones i2', 'r.id_participante2 = i2.id', 'left') // Participante 2
                // Unir con la tabla de torneos para obtener detalles del torneo
                ->join('torneos t', 't.id = r.id_torneo', 'inner')
                // Unir con la tabla de tipos de ronda para obtener el nombre de la ronda
                ->join('tipos_ronda tr', 'tr.id = r.id_tipo_ronda', 'inner')
                // Condición: Verificar si el usuario es participante 1 y tiene video subido
                ->groupStart()
                ->where('i1.id_usuario', $userId)
                ->where('r.url_video_participante1 IS NOT NULL')
                ->groupEnd()
                // O bien, si el usuario es participante 2 y tiene video subido
                ->orGroupStart()
                ->where('i2.id_usuario', $userId)
                ->where('r.url_video_participante2 IS NOT NULL')
                ->groupEnd()
                // Ordenar resultados por torneo y tipo de ronda
                ->orderBy('r.id_torneo', 'ASC')
                ->orderBy('r.id_tipo_ronda', 'ASC');

            // Ejecutar la consulta y obtener los resultados
            $result = $builder->get()->getResultArray();

            // Procesar los resultados para identificar qué video corresponde al usuario
            foreach ($result as &$round) {
                if ($round['participante1_id'] == $userId) {
                    // Si el usuario es el participante 1, obtener su video
                    $round['user_video'] = $round['url_video_participante1'];
                } elseif ($round['participante2_id'] == $userId) {
                    // Si el usuario es el participante 2, obtener su video
                    $round['user_video'] = $round['url_video_participante2'];
                } else {
                    // Por seguridad, establecer el video como nulo si no corresponde al usuario
                    $round['user_video'] = null;
                }
            }

            // Devolver las rondas con los videos subidos por el usuario
            return $result;
        } catch (\Throwable $th) {
            // Manejar errores y registrar el mensaje
            log_message('error', 'Error al obtener videos del usuario: ' . $th->getMessage());
            return [];
        }
    }

    //aqui me quede

    /**
       Función que sube un video para un participante en una ronda específica.
     *
     * @param int $roundId ID de la ronda en la que se subirá el video.
     * @param int $participantRole Rol del participante (1 para participante 1, 2 para participante 2).
     * @param string $videoUrl URL del video a subir.
     * @return array Retorna un arreglo con el estado de la operación y un mensaje.
     */
    public function jls_upload_user_video($roundId, $participantRole, $videoUrl)
    {
        try {
            if (!is_numeric($roundId) || !in_array($participantRole, [1, 2]) || empty($videoUrl)) {
                throw new InvalidArgumentException('Datos inválidos proporcionados.');
            }

            // Determinar qué columna actualizar según el rol
            $data = [];
            if ($participantRole == 1) {
                $data = ['url_video_participante1' => $videoUrl];
            } elseif ($participantRole == 2) {
                $data = ['url_video_participante2' => $videoUrl];
            }

            // Actualizar la base de datos
            $this->db->table('rondas')
                ->set($data)
                ->where('id', $roundId)
                ->update();


            if ($this->db->affectedRows() > 0) {
                return [
                    'status' => 'success',
                    'message' => 'Video subido correctamente.',
                ];
            } else {
                return [
                    'status' => 'error',
                    'message' => 'No se pudo subir el video. Intente nuevamente.',
                ];
            }
        } catch (\Throwable $th) {
            log_message('error', 'Error al subir video: ' . $th->getMessage());
            return [
                'status' => 'error',
                'message' => 'Ocurrió un error al subir el video.',
            ];
        }
    }

    /**
       Obtiene la información de un video subido por un participante en una ronda específica.
     *
     * @param int $roundId ID de la ronda.
     * @param int $participantId ID del participante.
     * @return array Retorna un arreglo con el estado de la operación, el URL del video, y los detalles de la ronda.
     */

    public function jls_get_round_video_by_participant_id($roundId, $participantId)
    {
        try {
            // Validar los parámetros
            if (!is_numeric($roundId) || !is_numeric($participantId)) {
                throw new InvalidArgumentException('Datos inválidos proporcionados.');
            }

            // Construir la consulta
            $builder = $this->db->table('rondas r');
            $builder->select('
            r.id AS ronda_id,
            r.id_torneo,
            r.id_tipo_ronda,
            r.posicion_enfrentamiento,
            t.nombre AS torneo_nombre,
            r.url_video_participante1,
            r.url_video_participante2,
            r.id_participante1,
            r.id_participante2
        ')
                ->join('torneos t', 't.id = r.id_torneo', 'inner') //Solo para saber nombre torneo
                ->where('r.id', $roundId)
                ->groupStart()
                ->where('r.id_participante1', $participantId)
                ->orWhere('r.id_participante2', $participantId)
                ->groupEnd();

            $result = $builder->get()->getRowArray();

            // Procesar el resultado
            if ($result) {
                if ($result['id_participante1'] == $participantId) {
                    return [
                        'status' => 'success',
                        'video_url' => $result['url_video_participante1'],
                        'round_details' => $result,
                    ];
                } elseif ($result['id_participante2'] == $participantId) {
                    return [
                        'status' => 'success',
                        'video_url' => $result['url_video_participante2'],
                        'round_details' => $result,
                    ];
                } else {
                    return [
                        'status' => 'error',
                        'message' => 'El participante no está relacionado con esta ronda.',
                    ];
                }
            } else {
                return [
                    'status' => 'error',
                    'message' => 'No se encontró información para los criterios proporcionados.',
                ];
            }
        } catch (\Throwable $th) {
            log_message('error', 'Error al obtener video de la ronda: ' . $th->getMessage());
            return [
                'status' => 'error',
                'message' => 'Ocurrió un error al obtener el video.',
            ];
        }
    }

    /**
     * Actualiza los datos del perfil del usuario: nombre de usuario, alias y contraseña.
     *
     * @param int $user_id ID del usuario.
     * @param string $new_username Nuevo nombre de usuario.
     * @param string $new_alias Nuevo alias del usuario.
     * @param string|null $new_password Nueva contraseña (opcional).
     * @return array Devuelve un arreglo con el estado y mensaje de la operación.
     */
    public function jls_update_user_profile($user_id, $new_username, $new_alias, $new_password = null)
    {
        try {
            // Validar parámetros
            if (!is_numeric($user_id) || $user_id <= 0) {
                throw new InvalidArgumentException("El ID del usuario no es válido.");
            }

            if (empty($new_username) || empty($new_alias)) {
                return [
                    'status' => 'error',
                    'message' => 'El nombre de usuario y el alias no pueden estar vacíos.',
                ];
            }

            // Verificar que el nombre de usuario no esté en uso por otro usuario
            $existingUser = $this->jls_check_user_name_exists($new_username);
            if ($existingUser != 0 && $existingUser != $user_id) {
                return [
                    'status' => 'error',
                    'message' => 'El nombre de usuario ya está en uso por otro usuario.',
                ];
            }

            // Verificar que el alias no esté en uso por otro usuario
            $existingAlias = $this->fetchRecord('usuarios', ['alias_usuario' => $new_alias]);
            if ($existingAlias && $existingAlias->id != $user_id) {
                return [
                    'status' => 'error',
                    'message' => 'El alias ya está en uso por otro usuario.',
                ];
            }

            // Preparar los datos para la actualización
            $data = [
                'nombre_usuario' => $new_username,
                'alias_usuario' => $new_alias,
            ];

            // Si se proporciona una nueva contraseña, añadirla al array de actualización
            if (!empty($new_password)) {
                // Asegúrate de que la contraseña se almacene como hash para mayor seguridad
                $data['contraseña'] = $new_password;
            }

            // Actualizar los datos del usuario en la base de datos
            $conditions = ['id' => $user_id];
            $updated = $this->updateRecord('usuarios', $data, $conditions);

            if ($updated) {
                return [
                    'status' => 'success',
                    'message' => 'Los datos del perfil se han actualizado correctamente.',
                ];
            } else {
                return [
                    'status' => 'error',
                    'message' => 'No se realizaron cambios en los datos del perfil.',
                ];
            }
        } catch (\Throwable $th) {
            log_message('error', 'Error al actualizar el perfil del usuario: ' . $th->getMessage());
            return [
                'status' => 'error',
                'message' => 'Ocurrió un error al actualizar el perfil del usuario.',
            ];
        }
    }
}
