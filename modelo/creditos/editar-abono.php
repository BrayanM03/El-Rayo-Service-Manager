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
    $fecha = date("d-m-Y");
    $hora = date("h:i a");
    $metodo = $_POST["metodo"];
    $usuario = $_SESSION["user"];

     //Obtenemos estatus del credito
     $obtenerStatus = "SELECT id_credito FROM abonos WHERE id = ?";
     $stmt = $con->prepare($obtenerStatus);
     $stmt->bind_param('i', $id_abono);
     $stmt->execute();
     $stmt->bind_result($id_credito);
     $stmt->fetch(); 
     $stmt->close();


    $actualizar = "UPDATE abonos SET fecha = ?, hora = ?, abono= ?, usuario =? WHERE id = ?";
    $res = $con->prepare($actualizar);
    $res->bind_param('ssdsi', $fecha, $hora, $abono, $usuario, $id_abono);
    $res->execute();
    $res->close();

    $obtenerStatus = "SELECT pagado, restante, total FROM creditos WHERE id = ?";
    $stmt = $con->prepare($obtenerStatus);
    $stmt->bind_param('i', $id_credito);
    $stmt->execute();
    $stmt->bind_result($pagado, $restante, $total_a_pagar);
    $stmt->fetch(); 
    $stmt->close();
    

    $nuevo_restante =  doubleval($restante) - doubleval($abono);
    $nuevo_pagado = doubleval($abono) + doubleval($pagado);

    $actualizar = "UPDATE creditos SET pagado = ?, restante = ? WHERE id = ?";
    $res = $con->prepare($actualizar);
    $res->bind_param('ddi', $nuevo_pagado, $nuevo_restante, $id_credito);
    $res->execute();
    $res->close();

    $data = array("nuevo_pagado"=>$nuevo_pagado, "nuevo_restante"=>$nuevo_restante);
    echo json_encode($data, JSON_UNESCAPED_UNICODE); 

    

    
}


?>