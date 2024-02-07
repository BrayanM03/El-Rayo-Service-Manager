<?php 
/* include '../conexion.php';
$con = $conectando->conexion(); */
//'2021-06-01' AND '2021-12-31' Junio 2021 a Diciembre 2021
//'2022-01-01' AND '2022-06-30' Enero 2022 a Junio 2022
//'2022-07-01' AND '2022-12-31' Julio 2022 a Diciembre 2022
//'2023-01-01' AND '2023-06-30' Enero 2023 a Junio 2023
//'2023-07-01' AND '2023-12-31' Julio 2023 a Diciembre 2023
//'2024-01-01' AND '2024-02-06' Enero 2024 a Febrero 2024
/* $query = "SELECT * FROM ventas WHERE Fecha BETWEEN '2024-01-01' AND '2024-02-06'";
$stmt = $con->prepare($query);
$stmt->execute();
$ventas_general =$stmt->get_result();
$stmt->close();
$llantas_no_enc =0;
$dv_no_enc =0;
foreach($ventas_general as $fila){
    $id_venta = $fila['id'];
    $re = insertarUtilidad($con, $id_venta);
    $llantas_no_enc += $re['llantas_empty'];
    $dv_no_enc += $re['dv_empty'];
}
$resppp = array('llantas no encontradas'=> $llantas_no_enc, 'Detalle de venta no encontradas'=> $dv_no_enc);
echo json_encode($resppp); */

    function insertarUtilidad($con, $id_venta){
        $total_ventas=0;
        $select = "SELECT COUNT(*) FROM ventas WHERE id=?";
        $stmt = $con->prepare($select);
        $stmt->bind_param('i',$id_venta);
        $stmt->execute();
        $stmt->bind_result($total_ventas);
        $stmt->fetch();
        $stmt->close();
        $dv_no_enc =0;
        if($total_ventas>0){
            $query = "SELECT * FROM detalle_venta WHERE id_Venta =?";
            $stmt = $con->prepare($query);
            $stmt->bind_param('i',$id_venta);
            $stmt->execute();
            $resultado =$stmt->get_result();
            $stmt->close();
            $costo =0;
            $suma_utilidad =0;
            $total_llantas =0;
            $llantas_no_enc =0;
            foreach($resultado as $row){
                $importe = $row['Importe'];
                $cantidad = $row['Cantidad'];
                $id_llanta = $row['id_Llanta'];
                $tipo_unidad = $row['Unidad'];
                $id_detalle = $row['id'];
                $utilidad_x_servicio=0;
                $utilidad_x_pieza=0;
                $select_count="SELECT COUNT(*) FROM llantas WHERE id=?";
                $stmt = $con->prepare($select_count);
                $stmt->bind_param('i',$id_llanta);
                $stmt->execute();
                $stmt->bind_result($total_llantas);
                $stmt->fetch(); 
                $stmt->close();
                if($total_llantas>0)
                {
                    //Obteniendo utilidad del catalogo actual
                    $obtener = "SELECT precio_Inicial FROM llantas WHERE id = ?";
                    $stmt = $con->prepare($obtener);
                    $stmt->bind_param('i',$id_llanta);
                    $stmt->execute();
                    $stmt->bind_result($costo);
                    $stmt->fetch();
                    $stmt->close();
                    $utilidad_x_pieza = $importe - ($cantidad * $costo); 
                }else{
                    if($tipo_unidad == 'servicio'){
                        $utilidad_x_servicio += $importe; 
                    }else{
                        $llantas_no_enc++;
                    }
                }   
                $utilidad_x_partida = $utilidad_x_pieza + $utilidad_x_servicio;
                $update = $con->prepare("UPDATE detalle_venta SET utilidad =? WHERE id = ?");
                $update->bind_param('di',$utilidad_x_partida, $id_detalle);
                $update->execute();
                $update->close();
                $suma_utilidad += $utilidad_x_partida;
    
            }
    
            $update = "UPDATE ventas SET utilidad =? WHERE id = ?";
            $stmt = $con->prepare($update);
            $stmt->bind_param('di',$suma_utilidad, $id_venta);
            $stmt->execute();
            $stmt->close();
            $resp = array('estatus' =>true, 'mensaje'=> 'Utilidad insertada correctamente');
        }else{
            $dv_no_enc++;
            $resp = array('estatus' =>false, 'mensaje'=> 'No se pudo insertar utilidad, no se encontro el id de la venta');
        }
        $resp['llantas_empty'] = $llantas_no_enc;
        $resp['dv_empty'] = $dv_no_enc;
        return $resp;
       
    }


?>