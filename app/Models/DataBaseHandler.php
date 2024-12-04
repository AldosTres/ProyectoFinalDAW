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

    // public function jls_get_tournaments_by_filter($status = null)
    // {
    //     $query = 'SELECT * FROM torneos';
    //     $params = [];
    //     switch ($status) {
    //         case 'ongoing':
    //             //Un torneo puede estar en curso activo o inactivo.
    //             $query .= ' WHERE CURRENT_TIME BETWEEN fecha_inicio AND fecha_fin';
    //             break;
    //         case 'active':
    //             $query .= ' WHERE activo = ?';
    //             $params[] = 1;
    //             break;
    //         case 'inactive':
    //             $query .= ' WHERE activo = ?';
    //             $params[] = 0;
    //             break;
    //         case 'finished':
    //             //Si el torneo finaliza, se entiende que queda desactivado
    //             $query .= ' WHERE activo = ? AND fecha_fin < CURRENT_TIME';
    //             $params[] = 0;
    //             break;
    //         default:
    //             //En caso contrario a todos estos, mostrará todos los torneos
    //             break;
    //     }
    //     $result = $this->db->query($query, $params);
    //     $row = $result->getResultArray();
    //     return $row;
    // }


    /**
     * 
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
       Devuelve a todos los participantes de un torneo específico
     * 
     * @param int $tournament_id
     */
    function jls_get_tournament_participants($tournament_id)
    {
        $query = $this->db->query("SELECT * FROM inscripciones WHERE id_torneo = ? AND activo = ?", [$tournament_id, 1]);
        $row = $query->getResultArray();
        return $row;
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
     * Función que actualiza los datos de un torneo específico
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

    //      1. Listado de usuarios
    // Datos a mostrar en la tabla:
    // ID del usuario.
    // Nombre o alias.
    // Correo electrónico.
    // Rol (usuario, juez, administrador, etc.).
    // Estado (activo/inactivo/desactivado).
    // Número de torneos inscritos.
    // Fecha del último inicio de sesión (sí, lo puedes implementar con una columna en la tabla usuarios que se actualice en cada inicio de sesión).
    // Opciones rápidas:
    // Botón para editar el usuario (cambiar rol o modificar información relevante).
    // Botón para desactivar/activar al usuario (baneo temporal o reactivo).
    // 2. Filtro y búsqueda
    // Permitir que el administrador busque y filtre usuarios por:

    // Nombre o alias.
    // Correo electrónico.
    // Rol.
    // Estado.
    // Fecha de registro.
    // Esto facilita encontrar usuarios específicos rápidamente.
    // 3. Gestión de roles y estado
    // Cambiar roles: Una opción clave para designar usuarios como jueces, administradores, etc.
    // Desactivar usuarios: Lo que mencionas es ideal. En lugar de eliminarlos, se desactivan, dejando la cuenta en un estado de "no usable" pero sin perder el historial.
    // Nota: Si es necesario, puedes registrar en otra tabla o columna la razón de la desactivación (opcional).
    // 4. Estadísticas individuales
    // Además del número de torneos en los que ha participado un usuario, puedes agregar:

    // Inscripciones activas: Si hay torneos en los que está inscrito pero aún no han comenzado.
    // Participaciones completadas: Cuántos torneos ha completado el usuario.
    // 5. Último inicio de sesión
    // Actualizar la fecha en la tabla de usuarios cada vez que el usuario se loguee es una muy buena idea.

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





    function jls_get_users_by_filter($alias = null, $role = null, $status = null, $registration_start = null, $registration_end = null)
    {
        //Utilizo alias como u o r para evitar ambiguedades
        $builder = $this->db->table('usuarios u');
        $builder->select('u.id, u.alias_usuario, r.nombre AS rol_nombre, u.activo, u.fecha_registro, u.ultima_conexion'); // AS para evitar ambiguedades también
        $builder->join('roles r', 'r.id = u.id_rol');
        if ($alias && $alias != 'all') {
            $builder->like('alias_usuario', $alias);
        }
        if ($role && $role != 'all') {
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
}
