<?php
    date_default_timezone_set("America/Matamoros");

class Proveedor
{
    private $con;

    // Constructor para recibir la conexión de base de datos
    public function __construct($conexion)
    {
        $this->con = $conexion;
    }

     // Método para obtener un producto (llanta) y sus imágenes
     public function obtenerProveedor($id){
         $total_proveedores=0;
         $count = "SELECT COUNT(*) FROM proveeedores WHERE id = ?";
        $stmt = $this->con->prepare($count);
        $stmt->execute();
        $stmt->bind_result($total_proveedores);
        $stmt->fetch();
        $stmt->close();

        if ($total_proveedores > 0) {
            // Obtener información de la llanta
            $stmt = $this->con->prepare("SELECT * FROM proveedores");
            $stmt->execute();
            $resultado = $stmt->get_result();
            $fila = $resultado->fetch_all();
            $stmt->close();

          
            // Devolver información de la llanta y las imágenes
            return [
                'estatus' => true,
                'data' => $fila,
                'mensaje' => 'Proveedor encontrado'
            ];
        
        } else {
            return [
            'estatus' => false,
            'data' => [],
            'mensaje' => 'Prov no encontrado'
            ];
        }
     }
     public function obtenerEstadoCuenta($id_proveedor)
     {
         // Verificar si $sucursal es un array y tiene elementos
         $monto_total= 0;
         $monto_total_vencido =0;
         $pagado = 0;
         $pagado_vencido = 0;
         $restante = 0;
         $restante_vencido = 0;
         // Consulta para obtener empleados filtrados por sucursal si aplica
         $sql = "SELECT * FROM proveedores WHERE id = ?";
         $stmt = $this->con->prepare($sql);
         $stmt->bind_param('i', $id_proveedor);
         $stmt->execute();
         $resultado = $stmt->get_result();
         $prov = $resultado->fetch_all(MYSQLI_ASSOC);
         $stmt->close();
         $proveedor = $prov[0]['nombre'];
       

         $sql = "SELECT * FROM movimientos WHERE proveedor_id = ?
         AND (estado_factura != 4 AND estado_factura != 1 AND estado_factura != '')";
         $stmt = $this->con->prepare($sql);
         $stmt->bind_param('i', $id_proveedor);
         $stmt->execute();
         $resultado = $stmt->get_result();
         $fila = $resultado->fetch_all(MYSQLI_ASSOC);
         $stmt->close();
 
         $fecha_hoy = date('Y-m-d');
         foreach ($fila as $key => $value) {
            if($fecha_hoy < $value['fecha_vencido']){
                $restante += floatval($value['restante']); 
            }else{
                $restante_vencido += floatval($value['restante']); 
            }
            
         }
         
         // Verificar si hay resultados
         if (!empty($fila)) {
             return [
                 'estatus' => true,
                 'data' => [
                    'proveedor'=>$proveedor,
                    'restante_vencido' => $restante_vencido,
                    'restante_sin_vencer' => $restante,
                    'restante' =>  $restante + $restante_vencido,

                ],
                 'mensaje' => 'Empleados encontrados'
             ];
         } else {
             return [
                 'estatus' => false,
                 'data' => [ 
                 'proveedor'=>$proveedor,
                 'restante_vencido' => 0,
                 'restante_sin_vencer' => 0,
                 'restante' =>  0
                    ],
                 'mensaje' => 'No se encontraron movimientos'
             ];
         }
     }
 
}

?>