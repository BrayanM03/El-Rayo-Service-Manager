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
    $tipo = 1;
    
    $traer_cambios= mysqli_query($con, "SELECT * FROM detalle_cambio WHERE id_usuario = $id_usuario");
    while ($rows = $traer_cambios->fetch_assoc()) {
        $id_llanta = $rows["id_llanta"];
        $id_ubicacion = $rows["id_ubicacion"];
        $id_destino = $rows["id_destino"];
        $cantidad = $rows["cantidad"];
        $id_usuario = $rows["id_usuario"];

        $traerusuario = "SELECT nombre, apellidos FROM usuarios WHERE id = ?";
        $result = $con->prepare($traerusuario);
        $result->bind_param('s', $id_usuario);
        $result->execute();
        $result->bind_result($nombre_usuario, $apellidos_usuario);
        $result->fetch();
        $result->close();


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


                //Actualizando sucursal destino
                $update = "UPDATE inventario SET Stock = ? WHERE id_sucursal = ? AND id_Llanta = ?";
                $result = $con->prepare($update);
                $result->bind_param('sss', $cantidad_restante, $id_ubicacion, $id_llanta);
                $result->execute();
                $result->close();

                $descripcion_movimiento = "Se realizó movimiento de ". $cantidad . " item(s)  de sucursal " . $nombre_sucursal_remitente .
                " hacia ". $nombre_sucursal_destino .  " el dia ". $fecha. " a las " . $hora;
                $nombre_completo_usuario = $nombre_usuario . " ". $apellidos_usuario;
                //Registrando el movimiento
                $insertar = "INSERT INTO movimientos(id, 
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
                $result->close();

            }


        }else{


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
                $descripcion_movimiento = "Se realizó movimiento de ". $cantidad . " item(s)  de sucursal " . $nombre_sucursal_remitente .
                " hacia ". $nombre_sucursal_destino .  " el dia ". $fecha. " a las " . $hora;
                $nombre_completo_usuario = $nombre_usuario . " ". $apellidos_usuario;
                //Registrando el movimiento
                $insertar = "INSERT INTO movimientos(id, 
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
                $result->close();

            }


        }
      
        
    }
    

    $traercantidad = "SELECT SUM(cantidad) FROM detalle_cambio  WHERE id_usuario = ?";
    $result = $con->prepare($traercantidad);
    $result->bind_param('s', $id_usuario);
    $result->execute();
    $result->bind_result($total_llantas);
    $result->fetch();
    $result->close();

    print_r($total_llantas);

}

?>