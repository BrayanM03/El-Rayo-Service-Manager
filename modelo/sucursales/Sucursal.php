<?php

class Sucursal {
    private $con;

    // Constructor para recibir la conexión de base de datos
    public function __construct($conexion) {
        $this->con = $conexion;
    }

    // Método para obtener un producto (llanta) y sus imágenes
    public function obtenerSucursales() {
        // Contar si la llanta existe
        $total_sucursales=0;
        $count = 'SELECT count(*) FROM sucursal';
        $stmt = $this->con->prepare($count);
        $stmt->execute();
        $stmt->bind_result($total_sucursales);
        $stmt->fetch();
        $stmt->close();

        if ($total_sucursales > 0) {
            // Obtener información de la llanta
            $sqlContarLlantas = $this->con->prepare("SELECT * FROM sucursal");
            $sqlContarLlantas->execute();
            $resultado = $sqlContarLlantas->get_result();
            $fila = $resultado->fetch_all();
            $sqlContarLlantas->close();

          
            // Devolver información de la llanta y las imágenes
            return [
                'estatus' => true,
                'data' => $fila,
                'mensaje' => 'Producto encontrado'
            ];
        
        } else {
            return [
            'estatus' => false,
            'data' => [],
            'mensaje' => 'Producto no encontrado'
            ];
        }
    }

}

?>
