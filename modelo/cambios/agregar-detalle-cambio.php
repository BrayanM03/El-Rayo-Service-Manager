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
    $id_usuario = $_POST['id_usuario'];

    $comprobar = "SELECT COUNT(*) FROM inventario WHERE id_Llanta = ? AND id_sucursal =?";
    $r= $con->prepare($comprobar);
    $r->bind_param("ii", $id_llanta, $sucursal_remitente);
    $r->execute();
    $r->bind_result($conteo);
    $r->fetch();
    $r->close();

    if($conteo > 0){

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


            print_r($nueva_cantidad);
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
    
                    $response = array("mensaje"=> "Actualizado correctamente", "estatus"=>"success");
                    echo json_encode($response, JSON_UNESCAPED_UNICODE);
                }


            }else{

            $comprobar= "INSERT INTO detalle_cambio(id, id_llanta, id_ubicacion, id_destino, cantidad, id_usuario) VALUES(null, ?,?,?,?,?)";
            $res = $con->prepare($comprobar);
            $res->bind_param('iiiii', $id_llanta, $sucursal_remitente, $sucursal_destino, $cantidad, $id_usuario);
            $res->execute();
            $res->close();

            $response = array("mensaje"=> "Agregado correctamente", "estatus"=>"success");
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            }

        }
    }else{
        
        $response =  array("mensaje"=> "Al parecer esa llanta no se encuentra en el inventario.", "estatus"=>"error");
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
    }

    /* $insertar_cambio = "INSERT INTO detalle_cambio(id, id_llanta, id_ubicacion, id_destino) VALUES(null, ?,?,?)";
    $resultado = $con->prepare($insertar_cambio);
    $resultado->bind_param('iii', $id_llanta, $sucursal_remitente, $sucursak_destino);
    $resultado->execute();
    
    if ($resultado) {
        print_r(1);
    }else {
        print_r(0);
    } */
   
}


?>
