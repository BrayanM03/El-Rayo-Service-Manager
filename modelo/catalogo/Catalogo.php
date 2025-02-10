<?php

class Catalogo {
    private $con;

    // Constructor para recibir la conexión de base de datos
    public function __construct($conexion) {
        $this->con = $conexion;
    }

    // Método para obtener un producto (llanta) y sus imágenes
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

    // Método para obtener un servicio y sus imágenes
    public function obtenerServicio($codigo) {
        // Contar si la llanta existe
        $total_llantas=0;
        $count = 'SELECT count(*) FROM servicios WHERE id = ?';
        $stmt = $this->con->prepare($count);
        $stmt->bind_param('i', $codigo);
        $stmt->execute();
        $stmt->bind_result($total_llantas);
        $stmt->fetch();
        $stmt->close();

        if ($total_llantas > 0) {
            // Obtener información de la llanta
            $sqlContarLlantas = $this->con->prepare("SELECT * FROM servicios WHERE id = ?");
            $sqlContarLlantas->bind_param('i', $codigo);
            $sqlContarLlantas->execute();
            $resultado = $sqlContarLlantas->get_result();
            $fila = $resultado->fetch_assoc();
            $sqlContarLlantas->close();

            // Devolver información de la llanta y las imágenes
            return [
                'estatus' => true,
                'producto' => $fila,
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

    public function comprobarStock($id_llanta, $id_sucursal, $cantidad){
        $total =0;
        $count = "SELECT count(*) FROM inventario WHERE id_Llanta =? AND id_sucursal =?";
        $res = $this->con->prepare($count);
        $res->bind_param('ss', $id_llanta, $id_sucursal);
        $res->execute();
        $res->bind_result($total);
        $res->fetch();
        $res->close();

        if($total >0){
            $sel = 'SELECT * FROM inventario WHERE id_Llanta = ? AND id_sucursal = ?';
            $stmt= $this->con->prepare($sel);
            $stmt->bind_param('ss', $id_llanta, $id_sucursal);
            $stmt->execute();
            $resultado_ = $stmt->get_result();
            $fila = $resultado_->fetch_assoc();
            $stmt->close();
            $stock_actual = $fila['Stock'];
            if($cantidad>$stock_actual){
                return [
                    'estatus' => false,
                    'mensaje' => 'La cantidad supera el stock actual de la llanta',
                    'tipo'=>'warning'
                ];
            }else{
                return [
                    'estatus' => true,
                    'mensaje' => 'Stock disponible',
                    'tipo'=>'success',
                    'data'=>$fila
                ];
            }
        }else{
            return [
                'estatus' => false,
                'tipo'=>'danger',
                'mensaje' => 'No se encontrarón llantas con ese ID'
            ];
        }
    }
}

?>
