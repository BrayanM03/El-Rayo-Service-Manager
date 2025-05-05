<?php

class Catalogo {
    private $con;

    // Constructor para recibir la conexión de base de datos
    public function __construct($conexion) {
        $this->con = $conexion;
    }

    // Método para consultar rol
    public function obtenerProducto($codigo) {
        // Contar si la llanta existe
        $total_llantas=0;
        $count = 'SELECT count(*) FROM llantas WHERE id = ?';
        $stmt = $this->con->prepare($count);
        $stmt->bind_param('i', $codigo);
        $stmt->execute();
        $stmt->bind_result($total_llantas);
        $stmt->fetch();
        $stmt->close();

        if ($total_llantas > 0) {
            // Obtener información de la llanta
            $sqlContarLlantas = $this->con->prepare("SELECT * FROM llantas WHERE id = ?");
            $sqlContarLlantas->bind_param('i', $codigo);
            $sqlContarLlantas->execute();
            $resultado = $sqlContarLlantas->get_result();
            $fila = $resultado->fetch_assoc();
            $sqlContarLlantas->close();

           // Contar si hay imágenes relacionadas
           $total_imagenes=0;
            $selCount = 'SELECT COUNT(*) FROM llantas_imagenes WHERE id_llanta = ?';
            $stmt = $this->con->prepare($selCount);
            $stmt->bind_param('i', $codigo);
            $stmt->execute();
            $stmt->bind_result($total_imagenes);
            $stmt->fetch();
            $stmt->close();

            $imagenes = [];

            if ($total_imagenes > 0) {
                // Obtener URLs de imágenes relacionadas si hay registros
                $sel = 'SELECT * FROM llantas_imagenes WHERE id_llanta = ?';
                $stmt = $this->con->prepare($sel);
                $stmt->bind_param('i', $codigo);
                $stmt->execute();
                $resultado_ = $stmt->get_result();
                
                while ($fila_imagen = $resultado_->fetch_assoc()) {
                    $imagenes[] = $fila_imagen;
                }
                $stmt->close();
            }

            // Devolver información de la llanta y las imágenes
            return [
                'estatus' => true,
                'producto' => $fila,
                'imagenes' => $imagenes,
                'mensaje' => 'Producto encontrado'
            ];
        
        } else {
            return [
            'estatus' => false,
            'producto' => [],
            'mensaje' => 'Producto no encontrado'
            ];
        }
    }

    
}

?>
