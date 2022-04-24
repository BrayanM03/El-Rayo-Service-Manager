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
$hora = date("H:i a");

if(isset($_POST)){


    $id_usuario = $_POST["id_usuario"];
    $sucursal_id = $_SESSION["id_sucursal"];
    $tipo = 1;

    $traerusuario = "SELECT nombre, apellidos FROM usuarios WHERE id = ?";
    $result = $con->prepare($traerusuario);
    $result->bind_param('s', $id_usuario);
    $result->execute();
    $result->bind_result($nombre_usuario, $apellidos_usuario);
    $result->fetch();
    $result->close();
    $nombre_completo_usuario = $nombre_usuario . " ". $apellidos_usuario;

    $traercantidad = "SELECT SUM(cantidad) FROM detalle_cambio  WHERE id_usuario = ?";
    $result = $con->prepare($traercantidad);
    $result->bind_param('s', $id_usuario);
    $result->execute();
    $result->bind_result($total_llantas);
    $result->fetch();
    $result->close();
    
     $descripcion_movimiento = "Se realizo el movimiento de ". $total_llantas . " llanta(s)";
      $insertar = "INSERT INTO movimientos(id, 
                                                     descripcion, 
                                                     mercancia, 
                                                     fecha, 
                                                     hora, 
                                                     usuario,
                                                     tipo, sucursal) VALUES(null, ?,?,?,?,?,?,?)";
                $result = $con->prepare($insertar);
                $result->bind_param('sssssss',$descripcion_movimiento, $total_llantas,
                                                $fecha, $hora, $nombre_completo_usuario, $tipo, $sucursal_id);
                                                
                $result->execute();
                $result->close();

                //LAST ID
                $rs = mysqli_query($con, "SELECT MAX(id) AS id FROM movimientos");
                if ($rowss = mysqli_fetch_row($rs)) {
                $id_movimiento = trim($rowss[0]);
                }

    
    $traer_cambios= mysqli_query($con, "SELECT * FROM detalle_cambio WHERE id_usuario = $id_usuario");
    $mercancia ="";
    while ($rows = $traer_cambios->fetch_assoc()) {
        $id_llanta = $rows["id_llanta"];
        $id_ubicacion = $rows["id_ubicacion"];
        $id_destino = $rows["id_destino"];
        $cantidad = $rows["cantidad"];
        $id_usuario = $rows["id_usuario"];

        //Comprobar si esa llanta se encuentra en el inventario destino
        $comprobar = "SELECT COUNT(*) FROM inventario WHERE id_sucursal = ? AND id_Llanta = ?";
        $result = $con->prepare($comprobar);
        $result->bind_param('ss', $id_destino, $id_llanta);
        $result->execute();
        $result->bind_result($total1);
        $result->fetch();
        $result->close();

        //Trayendo descripcion de la llanta 
        $traer = "SELECT Descripcion FROM llantas WHERE id = ?";
        $result = $con->prepare($traer);
        $result->bind_param('s', $id_llanta);
        $result->execute();
        $result->bind_result($llanta_descripcion);
        $result->fetch();
        $result->close();
        $mercancia = $mercancia . ", " . $llanta_descripcion . ", ";

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
            $result->bind_result($stock_actual);
            $result->fetch();
            $result->close();

            $stock_actual = intval($stock_actual);
            $cantidad = intval($cantidad);
            $cantidad_restante = $stock_actual - $cantidad;
            if($stock_actual < $cantidad){

            $response = array("mensaje"=> "La cantidad que ingresas es mayor a el stock actual de " . $nombre_sucursal_remitente, "estatus"=>"error");
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            
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
                $result->bind_param('sss', $cantidad_restante, $id_ubicacion, $id_llanta);
                $result->execute();
                $result->close();

               /*  $descripcion_movimiento = "Se realizó movimiento de ". $cantidad . " item(s)  de sucursal " . $nombre_sucursal_remitente .
                " hacia ". $nombre_sucursal_destino .  " el dia ". $fecha. " a las " . $hora;
                $nombre_completo_usuario = $nombre_usuario . " ". $apellidos_usuario;
 */
                //Registrando el movimiento
               /*  $insertar = "INSERT INTO movimientos(id, 
                                                     descripcion, 
                                                     mercancia, 
                                                     fecha, 
                                                     hora, 
                                                     usuario,
                                                     sucursal_remitente,
                                                     sucursal_destino,
                                                     tipo) VALUES(null, ?,?,?,?,?,?,?,?)";
                $result = $con->prepare($insertar);
                $result->bind_param('ssssssss',$descripcion_movimiento, $llanta_descripcion,
                                                $fecha, $hora, $nombre_completo_usuario,
                                                $nombre_sucursal_remitente,
                                                $nombre_sucursal_destino, $tipo);
                $result->execute();
                $result->close(); */
                $insertar = "INSERT INTO historial_detalle_cambio(id, 
                id_llanta, 
                id_ubicacion, 
                id_destino, 
                cantidad, 
                id_usuario,
                id_movimiento) VALUES(null, ?,?,?,?,?,?)";
                $result = $con->prepare($insertar);
                $result->bind_param('ssssss',$id_llanta, $id_ubicacion, $id_destino, $cantidad, $id_usuario, $id_movimiento);
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
            $result->bind_result($stock_actual);
            $result->fetch();
            $result->close();

            //Traemos el stock de la sucursal destino para actualizarla

            $comprobar = "SELECT Stock FROM inventario WHERE id_sucursal = ? AND id_Llanta = ?";
            $result = $con->prepare($comprobar);
            $result->bind_param('ss', $id_destino, $id_llanta);
            $result->execute();
            $result->bind_result($stock_actual_destino);
            $result->fetch();
            $result->close();

            $stock_actual = intval($stock_actual);
            $cantidad = intval($cantidad);
            $stock_actual_destino = intval($stock_actual_destino);
            $nueva_cantidad = $cantidad + $stock_actual_destino;
            $cantidad_restante = $stock_actual - $cantidad;
           
            if ($stock_actual < $cantidad) {
                $response = array("mensaje"=> "La cantidad que ingresas es mayor a el stock actual de " . $nombre_sucursal_remitente, "estatus"=>"error");
                echo json_encode($response, JSON_UNESCAPED_UNICODE);
            }else{

                //Actualizando sucursal destino
                $update = "UPDATE inventario SET Stock = ? WHERE id_sucursal = ? AND id_Llanta = ?";
                $result = $con->prepare($update);
                $result->bind_param('sss', $nueva_cantidad, $id_destino, $id_llanta);
                $result->execute();
                $result->close();

                //Actualizando sucursal remitente
                $update = "UPDATE inventario SET Stock = ? WHERE id_sucursal = ? AND id_Llanta = ?";
                $result = $con->prepare($update);
                $result->bind_param('sss', $cantidad_restante, $id_ubicacion, $id_llanta);
                $result->execute();
                $result->close();

                //insertando moviemitno
               /*  $descripcion_movimiento = "Se realizó movimiento de ". $cantidad . " item(s)  de sucursal " . $nombre_sucursal_remitente .
                " hacia ". $nombre_sucursal_destino .  " el dia ". $fecha. " a las " . $hora;
                $nombre_completo_usuario = $nombre_usuario . " ". $apellidos_usuario; */
                //Registrando el movimiento
               /*  $insertar = "INSERT INTO movimientos(id, 
                                                     descripcion, 
                                                     mercancia, 
                                                     fecha, 
                                                     hora, 
                                                     usuario,
                                                     sucursal_remitente,
                                                     sucursal_destino,
                                                     tipo) VALUES(null, ?,?,?,?,?,?,?,?)";
                $result = $con->prepare($insertar);
                $result->bind_param('ssssssss',$descripcion_movimiento, $llanta_descripcion,
                                                $fecha, $hora, $nombre_completo_usuario,
                                                $nombre_sucursal_remitente,
                                                $nombre_sucursal_destino, $tipo);
                                                
                $result->execute();
                $result->close(); */

                $insertar = "INSERT INTO historial_detalle_cambio(id, 
                id_llanta, 
                id_ubicacion, 
                id_destino, 
                cantidad, 
                id_usuario,
                id_movimiento,
                stock_actual,
                stock_anterior) VALUES(null, ?,?,?,?,?,?,?,?)";
                $result = $con->prepare($insertar);
                $result->bind_param('ssssssss',$id_llanta, $id_ubicacion, $id_destino, $cantidad, $id_usuario, $id_movimiento, $stock_actual, $stock_anterior);
                $result->execute();
                $result->close();

            }


        }
      
        
    }

    $update = "UPDATE movimientos SET mercancia = ? WHERE id = ?";
    $respp = $con->prepare($update);
    $respp->bind_param('ss', $mercancia, $id_movimiento);
    $respp->execute();
    $respp->close();
 

    print_r($total_llantas);

}

?>