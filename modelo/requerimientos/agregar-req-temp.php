<?php

session_start();
include '../conexion.php';
$con= $conectando->conexion(); 

if (!isset($_SESSION['id_usuario'])) {
    header("Location:../../login.php");
}

if($_POST){

    $sucursal_remitente = $_POST['sucursal_remitente'];
    $sucursal_destino = $_POST['sucursal_destino']; 
    $cantidad = $_POST['cantidad'];
    $id_llanta = $_POST['id_llanta'];
    $id_usuario = $_SESSION['id_usuario'];

    

        $traer_stock = "SELECT Stock FROM inventario WHERE id_Llanta = ? AND id_sucursal =?";
        $r= $con->prepare($traer_stock);
        $r->bind_param("ii", $id_llanta, $sucursal_remitente);
        $r->execute();
        $r->bind_result($stock_actual);
        $r->fetch();
        $r->close();

        if($cantidad > $stock_actual){
            $response =  array("mensaje"=> "El cantidad soprepasa el stock actual", "estatus"=>"warning");
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
        }else{
            $comprobar= "SELECT COUNT(*) FROM detalle_cambio WHERE id_llanta =? AND id_ubicacion = ? AND id_destino =? AND id_usuario = ?";
            $res = $con->prepare($comprobar);
            $res->bind_param('iiii', $id_llanta, $sucursal_remitente, $sucursal_destino, $id_usuario);
            $res->execute();
            $res->bind_result($total);
            $res->fetch();
            $res->close();

            if($total > 0){

                $comprobar= "SELECT cantidad FROM detalle_cambio WHERE id_llanta =? AND id_ubicacion = ? AND id_destino =? AND id_usuario = ?";
                $res = $con->prepare($comprobar);
                $res->bind_param('iiii', $id_llanta, $sucursal_remitente, $sucursal_destino, $id_usuario);
                $res->execute();
                $res->bind_result($cantidad_actual);
                $res->fetch();
                $res->close();

                
                $nueva_cantidad= intval($cantidad_actual) + intval($cantidad);

                if($nueva_cantidad > $stock_actual){

                    $response = array("mensaje"=> "Esa cantidad soprebasa tu stock", "estatus"=>"warning");
                    echo json_encode($response, JSON_UNESCAPED_UNICODE);

                }else{

                    $update= "UPDATE detalle_cambio SET cantidad = ? WHERE id_llanta =? AND id_ubicacion = ? AND id_destino =? AND id_usuario = ?";
                    $res = $con->prepare($update);
                    $res->bind_param('iiiii', $nueva_cantidad, $id_llanta, $sucursal_remitente, $sucursal_destino, $id_usuario);
                    $res->execute();
                    $res->close();
    
                    $comprobar= "SELECT SUM(cantidad) FROM detalle_cambio WHERE id_usuario = ?";
                    $res = $con->prepare($comprobar);
                    $res->bind_param('i', $id_usuario);
                    $res->execute();
                    $res->bind_result($total_llantas);
                    $res->fetch();
                    $res->close();

                    $response = array("mensaje"=> "Actualizado correctamente", "estatus"=>"success", 'total_llantas'=>$total_llantas);
                    echo json_encode($response, JSON_UNESCAPED_UNICODE);
                }


            }else{

            $comprobar= "INSERT INTO detalle_cambio(id, id_llanta, id_ubicacion, id_destino, cantidad, id_usuario) VALUES(null, ?,?,?,?,?)";
            $res = $con->prepare($comprobar);
            $res->bind_param('iiiii', $id_llanta, $sucursal_remitente, $sucursal_destino, $cantidad, $id_usuario);
            $res->execute();
            $res->close();

            $comprobar= "SELECT SUM(cantidad) FROM detalle_cambio WHERE id_usuario = ?";
            $res = $con->prepare($comprobar);
            $res->bind_param('i', $id_usuario);
            $res->execute();
            $res->bind_result($total_llantas);
            $res->fetch();
            $res->close();

            $response = array("mensaje"=> "Agregado correctamente", "estatus"=>"success", 'total_llantas'=>$total_llantas);
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            }

        }
   
}


?>
