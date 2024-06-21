<?php
session_start();
include '../conexion.php';
$con= $conectando->conexion(); 
date_default_timezone_set("America/Matamoros");

if (!isset($_SESSION['id_usuario'])) {
    header("Location:../../login.php");
}
 
if (!$con) {
    echo "maaaaal";
}
$fecha = date("Y-m-d");
$hora = date("h:i a");

if(isset($_POST)){
    $id_requerimiento = $_POST['id_requerimiento'];
    $query_count = "SELECT count(*) FROM detalle_requerimientos WHERE estatus = 3 AND id_requerimiento = ?";
    $stmt = $con->prepare($query_count);
    $stmt->bind_param('s', $id_requerimiento);
    $stmt->execute();
    $stmt->bind_result($total_detalles);
    $stmt->fetch();
    $stmt->close();

    if($total_detalles >0){
        //Traemos los IDs de las llantas aprobadas
        $query = "SELECT id FROM detalle_requerimientos WHERE estatus = 3 AND id_requerimiento = ?";
        $stmt = $con->prepare($query);
        $stmt->bind_param('s', $id_requerimiento);
        $stmt->execute();
        $datos=$stmt->get_result();
        $stmt->free_result();
        $stmt->close();

        //Traemos el total de las llantas a mover, el id usuario y id sucursal
        $query_count = "SELECT SUM(dr.cantidad) as total_llantas, r.id_usuario, r.id_sucursal 
                FROM detalle_requerimientos dr 
                INNER JOIN requerimientos r ON dr.id_requerimiento = r.id 
                WHERE dr.estatus = 3 AND dr.id_requerimiento = ? 
                GROUP BY r.id_usuario, r.id_sucursal";
        $stmt = $con->prepare($query_count);
        $stmt->bind_param('s', $id_requerimiento);
        $stmt->execute();
        $stmt->bind_result($cantidad_llantas_mover, $id_usuario, $sucursal_id);
        $stmt->fetch();
        $stmt->close();

        $tipo = 1;
        $estatus_movimiento = 'Pendiente';

        $traerusuario = "SELECT nombre, apellidos FROM usuarios WHERE id = ?";
        $result = $con->prepare($traerusuario);
        $result->bind_param('s', $id_usuario);
        $result->execute();
        $result->bind_result($nombre_usuario, $apellidos_usuario);
        $result->fetch();
        $result->close();
        $nombre_completo_usuario = $nombre_usuario . " ". $apellidos_usuario;

   
        $descripcion_movimiento = "Se realizo el movimiento de ".$cantidad_llantas_mover . " llanta(s)";
        $insertar = "INSERT INTO movimientos(id, 
                                                descripcion, 
                                                mercancia, 
                                                fecha, 
                                                hora, 
                                                usuario,
                                                tipo, sucursal, estatus, id_usuario) VALUES(null, ?,?,?,?,?,?,?,?,?)";
        $result = $con->prepare($insertar);
        $result->bind_param('sssssssss',$descripcion_movimiento, $cantidad_llantas_mover,
                            $fecha, $hora, $nombre_completo_usuario, $tipo, $sucursal_id, $estatus_movimiento, $id_usuario);
                                                    
        $result->execute();
        $result->close();
    
                    //LAST ID
        $rs = mysqli_query($con, "SELECT MAX(id) AS id FROM movimientos");
        if ($rowss = mysqli_fetch_row($rs)) {
            $id_movimiento = trim($rowss[0]);
            }
    
        foreach($datos as $fila){

            $id_detalle = $fila['id'];
            
            //Actualizar id_movimiento en el detalle del requerimiento
            $up = "UPDATE detalle_requerimientos SET id_movimiento = ? WHERE id = ?";
            $respon=$con->prepare($up);
            $respon->bind_param('ss',$id_movimiento, $id_detalle);
            $respon->execute();
            $respon->close();
    
            $traer_cambios= mysqli_query($con, "SELECT * FROM detalle_requerimientos WHERE id = $id_detalle");
            $mercancia ='';
            while ($rows = $traer_cambios->fetch_assoc()) {
                $id_llanta = $rows['id_llanta'];
                $id_ubicacion = $rows['id_ubicacion'];
                $id_destino = $rows['id_destino'];
                $cantidad = $rows['cantidad'];
                $id_usuario = $rows['id_usuario'];
                $estatus_actual = $rows['estatus'];

                //Comprobar si esa llanta se encuentra en el inventario destino
                $comprobar = "SELECT COUNT(*) FROM inventario WHERE id_sucursal = ? AND id_Llanta = ?";
                $result = $con->prepare($comprobar);
                $result->bind_param('ss', $id_destino, $id_llanta);
                $result->execute();
                $result->bind_result($total1);
                $result->fetch();
                $result->close();

                //Trayendo codigo y nombre de sucursal destino
                $comprobar = "SELECT code, nombre FROM sucursal WHERE id = ?";
                $result = $con->prepare($comprobar);
                $result->bind_param('s', $id_destino);
                $result->execute();
                $result->bind_result($code, $nombre_sucursal_destino);
                $result->fetch();
                $result->close();

                //Traer nombre de sucursal remitente
                $comprobar = "SELECT nombre FROM sucursal WHERE id = ?";
                $result = $con->prepare($comprobar);
                $result->bind_param('s', $id_ubicacion);
                $result->execute();
                $result->bind_result($nombre_sucursal_remitente);
                $result->fetch();
                $result->close();

                $acumulado = 0;
                if($total1 == 0){
                
                    //Tryendo el stock actual de la sucursal remitente
                    $comprobar = "SELECT Stock FROM inventario WHERE id_sucursal = ? AND id_Llanta = ?";
                    $result = $con->prepare($comprobar);
                    $result->bind_param('ss', $id_ubicacion, $id_llanta);
                    $result->execute();
                    $result->bind_result($stock_ubicacion_anterior);
                    $result->fetch();
                    $result->close();

                    $stock_ubicacion_anterior = intval($stock_ubicacion_anterior);
                    $cantidad = intval($cantidad);
                    $stock_ubicacion_actual = $stock_ubicacion_anterior - $cantidad;
                    if($stock_ubicacion_anterior < $cantidad){

                    $response = array("mensaje"=> "La cantidad que ingresas es mayor a el stock actual de " . $nombre_sucursal_remitente, "estatus"=>false, 'id_requerimiento'=>$id_requerimiento);
                    echo json_encode($response, JSON_UNESCAPED_UNICODE);
                    die();

                    
                    }else{

                        //Insertando llanta a sucursal destino
                        $codigo = $code . $id_llanta;
                        $insertar = "INSERT INTO inventario(id, id_Llanta, Codigo, Sucursal, id_sucursal, Stock) VALUES(null, ?,?,?,?,?)";
                        $result = $con->prepare($insertar);
                        $result->bind_param('sssss',$id_llanta, $codigo, $nombre_sucursal_destino, $id_destino, $cantidad);
                        $result->execute();
                        $result->close();


                        //Actualizando el stock restante de la sucursal remitente
                        $update = "UPDATE inventario SET Stock = ? WHERE id_sucursal = ? AND id_Llanta = ?";
                        $result = $con->prepare($update);
                        $result->bind_param('sss', $stock_ubicacion_actual, $id_ubicacion, $id_llanta);
                        $result->execute();
                        $result->close();

                    
                        $insertar = "INSERT INTO historial_detalle_cambio(id, 
                        id_llanta, 
                        id_ubicacion, 
                        id_destino, 
                        cantidad, 
                        id_usuario,
                        id_movimiento,
                        stock_ubicacion_actual,
                        stock_ubicacion_anterior,
                        stock_destino_actual,
                        stock_destino_anterior,
                        aprobado_receptor,
                        aprobado_emisor,
                        usuario_emisor,
                        usuario_receptor) VALUES(null, ?,?,?,?,?,?,?,?,?,0,0,0,?,?)";
                        $result = $con->prepare($insertar);
                        $result->bind_param('sssssssssss',$id_llanta, $id_ubicacion, $id_destino, $cantidad, $id_usuario, $id_movimiento, $stock_ubicacion_actual, $stock_ubicacion_anterior, $cantidad, $id_usuario, $id_usuario);
                        $result->execute();
                        $result->close();
                    }

                }else{
           
                    /*Para el siguiente codigo la llanta se encuentra en la sucursal destino y lo que haremos sera actualizar el stock de
                    la llanta */
        
                    //Tryendo el stock actual de la sucursal remitente
                    $comprobar = "SELECT Stock FROM inventario WHERE id_sucursal = ? AND id_Llanta = ?";
                    $result = $con->prepare($comprobar);
                    $result->bind_param('ss', $id_ubicacion, $id_llanta);
                    $result->execute();
                    $result->bind_result($stock_ubicacion_anterior);
                    $result->fetch();
                    $result->close();

                    //Traemos el stock de la sucursal destino para actualizarla

                    $comprobar = "SELECT Stock FROM inventario WHERE id_sucursal = ? AND id_Llanta = ?";
                    $result = $con->prepare($comprobar);
                    $result->bind_param('ss', $id_destino, $id_llanta);
                    $result->execute();
                    $result->bind_result($stock_destino_anterior);
                    $result->fetch();
                    $result->close();

                    $stock_ubicacion_anterior = intval($stock_ubicacion_anterior);
                    $cantidad = intval($cantidad);
                    $stock_destino_anterior = intval($stock_destino_anterior);
                    $stock_destino_actual = $cantidad + $stock_destino_anterior;
                    $stock_ubicacion_actual = $stock_ubicacion_anterior - $cantidad;
                
                    if ($stock_ubicacion_anterior < $cantidad) {
                        $response = array("mensaje"=> "La cantidad que ingresas es mayor a el stock actual de " . $nombre_sucursal_remitente, "estatus"=>false, 'id_requerimiento'=>$id_requerimiento);
                        echo json_encode($response, JSON_UNESCAPED_UNICODE);
                        die();
                    }else{

                        //Actualizando sucursal destino
                        $update = "UPDATE inventario SET Stock = ? WHERE id_sucursal = ? AND id_Llanta = ?";
                        $result = $con->prepare($update);
                        $result->bind_param('sss', $stock_destino_actual, $id_destino, $id_llanta);
                        $result->execute();
                        $result->close();

                        //Actualizando sucursal remitente
                        $update = "UPDATE inventario SET Stock = ? WHERE id_sucursal = ? AND id_Llanta = ?";
                        $result = $con->prepare($update);
                        $result->bind_param('sss', $stock_ubicacion_actual, $id_ubicacion, $id_llanta);
                        $result->execute();
                        $result->close();

                        $insertar = "INSERT INTO historial_detalle_cambio(id, 
                        id_llanta, 
                        id_ubicacion, 
                        id_destino, 
                        cantidad, 
                        id_usuario,
                        id_movimiento,
                        stock_ubicacion_actual,
                        stock_ubicacion_anterior,
                        stock_destino_actual,
                        stock_destino_anterior,
                        aprobado_receptor,
                        aprobado_emisor,
                        usuario_emisor,
                        usuario_receptor) VALUES(null, ?,?,?,?,?,?,?,?,?,?,0,0,?,?)";
                        $result = $con->prepare($insertar);
                        $result->bind_param('ssssssssssss',$id_llanta, $id_ubicacion, $id_destino, $cantidad, $id_usuario, $id_movimiento, $stock_ubicacion_actual, $stock_ubicacion_anterior, $stock_destino_actual, $stock_destino_anterior, $id_usuario, $id_usuario);
                        $result->execute();
                        $result->close();

                    }


                }
      
        
            }


            $traer_cambios= mysqli_query($con, "SELECT * FROM historial_detalle_cambio WHERE id_movimiento = $id_movimiento");
            $suma_llantas = "SELECT SUM(cantidad) FROM historial_detalle_cambio WHERE id_movimiento = ?";
            $result = $con->prepare($suma_llantas);
            $result->bind_param('s', $id_movimiento);
            $result->execute();
            $result->bind_result($cantidad_llantas);
            $result->fetch();
            $result->close();
            $mercancia ='';
            $descripcion_movimiento = "Se realizo el movimiento de ". $cantidad_llantas . " llanta(s)";
            while ($rowsx = $traer_cambios->fetch_assoc()) {
                    //Trayendo descripcion de la llanta 
                    $id_llanta = $rowsx['id_llanta'];
                    $traer = "SELECT Descripcion FROM llantas WHERE id = ?";
                    $result = $con->prepare($traer);
                    $result->bind_param('s', $id_llanta);
                    $result->execute();
                    $result->bind_result($llanta_descripcion);
                    $result->fetch();
                    $result->close();
                    $mercancia = $mercancia . ", " . $llanta_descripcion . ", ";
            }

            $update = "UPDATE movimientos SET descripcion = ?, mercancia = ?, estatus = 'Pendiente' WHERE id = ?";
            $respp = $con->prepare($update);
            $respp->bind_param('sss', $descripcion_movimiento, $mercancia, $id_movimiento);
            $respp->execute();
            $respp->close();
            $update = "UPDATE detalle_requerimientos SET estatus = 9 WHERE id = ?";
            $respp = $con->prepare($update);
            $respp->bind_param('s', $id_detalle);
            $respp->execute();
            $respp->close();

            //Actualizar estatus requerimiento
            $traer_detalle_req= mysqli_query($con, "SELECT * FROM detalle_requerimientos WHERE id_requerimiento = $id_requerimiento");
            $estatus_nuevo = 3;
            while ($filas = $traer_detalle_req->fetch_assoc()) {
                $estatus_actual = $filas['estatus'];
                //print_r($estatus_actual);
                if($estatus_actual !=9){
                    $estatus_nuevo = 5;
                }
            } 
             $update = "UPDATE requerimientos SET estatus = ?, id_movimiento = ? WHERE id = ?";
            $respp = $con->prepare($update);
            $respp->bind_param('iss', $estatus_nuevo, $id_movimiento, $id_requerimiento);
            $respp->execute();
            $error = $respp->error;
            $respp->close();

            $respon = array('estatus' =>true, 'mensaje' =>'Llanta agregada con exito', 'id_requerimiento'=>$id_requerimiento,/*  'estatus_nuevo'=>$estatus_nuevo, 'error'=>$error */);
                    
        }
        
    }else{

        $respon = array('estatus' =>false, 'mensaje' =>'No se encontrarón llantas aprobadas en este requerimiento', 'id_requerimiento'=>$id_requerimiento);

    }
    
    echo json_encode($respon);
}

?>