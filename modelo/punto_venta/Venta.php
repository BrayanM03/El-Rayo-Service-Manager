<?php

class Ventas {
    private $con;
    private $id_usuario;
    private $rol_usuario;
    // Constructor para recibir la conexión de base de datos
    public function __construct($conexion) {
        $this->con = $conexion;
        $this->id_usuario = $_SESSION['id_usuario'];
        $this->rol_usuario = $_SESSION['rol'];
    }

    // Método para obtener un producto (llanta) y sus imágenes
    public function existeDetalleVenta($id_venta) {
        // Contar si la llanta existe
        $total_registros=0;
        $count = 'SELECT count(*) FROM detalle_venta WHERE id_Venta = ?';
        $stmt = $this->con->prepare($count);
        $stmt->bind_param('i', $id_venta);
        $stmt->execute();
        $stmt->bind_result($total_registros);
        $stmt->fetch();
        $stmt->close();

        if ($total_registros > 0) {
            // Obtener información de la llanta
            $sqlContarDetalles = $this->con->prepare("SELECT * FROM detalle_venta WHERE id_Venta = ?");
            $sqlContarDetalles->bind_param('i', $id_venta);
            $sqlContarDetalles->execute();
            $resultado = $sqlContarDetalles->get_result();
            $fila = $resultado->fetch_assoc();
            $sqlContarDetalles->close();

            // Devolver información de la llanta y las imágenes
            return [
                'estatus' => true,
                'llanta' => $fila,
                'mensaje' => 'Se encontró perrou'
            ];
        
        } else {
            return [
            'estatus' => false,
            'producto' => [],
            'mensaje' => 'No se encontrarón detalles, eliminar venta'
            ];
        }
    }


    function form_date($fecha_original){
        $timestamp = strtotime($fecha_original);
        $fecha_formateada = date("d-M-y", $timestamp);
        return $fecha_formateada;
    }

}

?>
