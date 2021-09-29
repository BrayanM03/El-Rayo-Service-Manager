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


if(isset($_POST)){

    $id_abono = $_POST["id"];
    $abono = $_POST["abono"];
    $fecha = date("Y-m-a");
    $hora = date("h:i a");
    $metodo = $_POST["metodo"];
    $usuario = $_SESSION["user"];

    if($abono < 0){
        print_r(2);
    }else{
        

        //Obtenemos estatus del credito
     $obtenerStatus = "SELECT id_credito FROM abonos WHERE id = ?";
     $stmt = $con->prepare($obtenerStatus);
     $stmt->bind_param('i', $id_abono);
     $stmt->execute();
     $stmt->bind_result($id_credito);
     $stmt->fetch(); 
     $stmt->close();

     //Obtenemos de los creditos el pagado, el restante y el total
    $obtenerStatus = "SELECT pagado, restante, total FROM creditos WHERE id = ?";
    $stmt = $con->prepare($obtenerStatus);
    $stmt->bind_param('i', $id_credito);
    $stmt->execute();
    $stmt->bind_result($pagado, $restante, $total_a_pagar);
    $stmt->fetch(); 
    $stmt->close();

  

    $nuevo_restante =  doubleval($restante) - doubleval($abono);
   

    if($nuevo_restante < 0 ){
        print_r(0);
    }else{
        

        $actualizar = "UPDATE abonos SET fecha = ?, hora = ?, abono= ?, metodo_pago = ?, usuario =? WHERE id = ?";
        $res = $con->prepare($actualizar);
        $res->bind_param('ssdssi', $fecha, $hora, $abono, $metodo, $usuario, $id_abono);
        $res->execute();
        $res->close();

        $obtenerSumatoria = "SELECT SUM(abono) AS sumtotal FROM abonos WHERE id_credito= ?";
        $response = $con->prepare($obtenerSumatoria);
        $response->bind_param('i', $id_credito); 
        $response->execute();
        $response->bind_result($totalAbonos);
        $response->fetch(); 
        $response->close();

        $restante_total = doubleval($total_a_pagar) - doubleval($totalAbonos); 
        $format_restante_total = doubleval($restante_total);
        $format_abonos_total = doubleval($totalAbonos);

        $actualizar = "UPDATE creditos SET pagado = ?, restante = ? WHERE id = ?";
        $res = $con->prepare($actualizar);
        $res->bind_param('ddi', $totalAbonos, $restante_total, $id_credito);
        $res->execute();
        $res->close();

        //Volvemos a traer los datos de creditos ya actualizados
        $obtenerStatus = "SELECT restante FROM creditos WHERE id = ?";
        $stmt = $con->prepare($obtenerStatus);
        $stmt->bind_param('i', $id_credito);
        $stmt->execute();
        $stmt->bind_result($restante_actualizado);
        $stmt->fetch(); 
        $stmt->close(); 

        //Si el restante esta en 0 actualizamos el estatus a pagado
        if($restante_actualizado ==0){
        $new_stat = 3;
        $actualizar = "UPDATE creditos SET estatus = ? WHERE id = ?";
        $res = $con->prepare($actualizar);
        $res->bind_param('di', $new_stat, $id_credito);
        $res->execute();
        $res->close();
        }else{
            $new_stat = 2;
            $actualizar = "UPDATE creditos SET estatus = ? WHERE id = ?";
            $res = $con->prepare($actualizar);
            $res->bind_param('di', $new_stat, $id_credito);
            $res->execute();
            $res->close();
        }

        //Se inserta movimiento

        $data = array("nuevo_pagado"=>$format_abonos_total, "nuevo_restante"=>$format_restante_total);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
       

    }

   /* 

    
    

    

        /*  */

    /*

     */ 

    

 

    }

        
}


?>