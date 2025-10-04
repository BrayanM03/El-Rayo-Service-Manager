<?php

class Permiso {
    private $con;

    // Constructor para recibir la conexión de base de datos (mysqli)
    public function __construct($conexion) {
        $this->con = $conexion;
    }

    /**
     * Verifica si un usuario tiene un permiso (directo o por rol).
     *
     * @param int $idUsuario - ID del usuario
     * @param string $clavePermiso - Clave del permiso (ej: 'ver_cuentas')
     * @return bool - true si tiene permiso, false si no
     */
    public function tienePermiso($idUsuario, $clavePermiso) {
        // 1. Buscar el permiso en la tabla permisos
        $sql = "SELECT id FROM permisos WHERE clave_permiso = ? AND estatus = 1 LIMIT 1";
        $stmt = $this->con->prepare($sql);
        $stmt->bind_param("s", $clavePermiso);
        $stmt->execute();
        $result = $stmt->get_result();
        $permiso = $result->fetch_assoc();
        $stmt->close();

        if (!$permiso) {
            return false; // El permiso no existe o está inactivo
        }

        $idPermiso = $permiso['id'];

        // 2. Verificar si hay un override en permisos_usuarios
        $sql = "SELECT estatus FROM permisos_usuarios 
                WHERE id_usuario = ? AND id_permiso = ? LIMIT 1";
        $stmt = $this->con->prepare($sql);
        $stmt->bind_param("ii", $idUsuario, $idPermiso);
        $stmt->execute();
        $result = $stmt->get_result();
        $override = $result->fetch_assoc();
        $stmt->close();

        if ($override) {
            return $override['estatus'] == 1;
        }

        // 3. Verificar si el rol del usuario lo tiene
        $sql = "SELECT pr.estatus 
                FROM usuarios u
                INNER JOIN permisos_roles pr ON pr.id_rol = u.rol
                WHERE u.id = ? AND pr.id_permiso = ?
                LIMIT 1";
        $stmt = $this->con->prepare($sql);
        $stmt->bind_param("ii", $idUsuario, $idPermiso);
        $stmt->execute();
        $result = $stmt->get_result();
        $rolPermiso = $result->fetch_assoc();
        $stmt->close();

        if ($rolPermiso) {
            return $rolPermiso['estatus'] == 1;
        }

        // 4. Si no lo encontró, no tiene permiso
        return false;
    }
}
