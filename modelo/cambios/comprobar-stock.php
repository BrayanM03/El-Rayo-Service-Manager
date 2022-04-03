<?php

session_start();
include '../conexion.php';
$con= $conectando->conexion(); 

if (!isset($_SESSION['id_usuario'])) {
    header("Location:../../login.php");
}

if($_POST){

    $id_usuario= $_POST['id_usuario'];
    $id_llanta = $_POST['id_llanta'];
    $id_sucursal= $_POST['id_sucursal'];
    $id_sucursal_destino = $_POST['id_sucursal_destino']; 
    $code_llanta = $_POST['code_llanta'];
    $stock = $_POST['stock'];
    $stock = intval($stock);

    if($stock < 0){
      
        print_r(2);
    }else {

        $contar = "SELECT Stock FROM inventario WHERE id = ? AND id_sucursal =?";
        $resp = $con->prepare($contar);
        $resp->bind_param("ii", $id_llanta, $id_sucursal);
        $resp->execute();
        $resp->bind_result($stock_actual);
        $resp->fetch();
        $resp->close();


        $contar = "SELECT COUNT(*) FROM detalle_cambio WHERE id_llanta = ? AND id_ubicacion =? AND id_destino = ? AND id_usuario =?";
        $resp = $con->prepare($contar);
        $resp->bind_param("iiii", $code_llanta, $id_sucursal, $id_sucursal_destino, $id_usuario);
        $resp->execute();
        $resp->bind_result($total_detalle_cambio);
        $resp->fetch();
        $resp->close();



        if($total_detalle_cambio > 0) {
        $contar = "SELECT cantidad FROM detalle_cambio WHERE id_llanta = ? AND id_ubicacion =? AND id_destino = ? AND id_usuario =?";
        $resp = $con->prepare($contar);
        $resp->bind_param("iiii", $code_llanta, $id_sucursal, $id_sucursal_destino, $id_usuario);
        $resp->execute();
        $resp->bind_result($cantidad_actual);
        $resp->fetch();
        $resp->close();


        }else{
            $cantidad_actual = 0;
        }



        $total_cantidad = $stock + $cantidad_actual;
    

        if($stock_actual > $total_cantidad &&  $stock > 0 || $stock_actual == $total_cantidad &&  $stock_actual > 0){
    
            print_r(1);
        }else if($stocK == 0){
          print_r(0);
        }else if($stock_actual < $total_cantidad){
    
            print_r(0);
        }else if($total_cantidad <= 0 || $total_cantidad == 0){
            print_r(2);
        }
    

  


    //print_r($id_llanta. " ". $id_sucursal);


    /* 
    $insertar_cambio = "INSERT INTO detalle_cambio(id, id_llanta, id_ubicacion, id_destino) VALUES(null, ?,?,?)";
    $resultado = $con->prepare($insertar_cambio);
    $resultado->bind_param('iii', $id_llanta, $sucursal_remitente, $sucursak_destino);
    $resultado->execute(); */
    
    /* if ($resultado) {
        print_r(1);
    }else {
        print_r(0);
    } */

    }

          
}

function is_nan2($n) {
    return $n !== $n;
}

?>
