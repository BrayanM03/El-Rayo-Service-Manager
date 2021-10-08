<?php
    session_start();
    include '../conexion.php';
    $con= $conectando->conexion(); 
    
    date_default_timezone_set("America/Matamoros");

    if (!$con) {
        echo "Problemas con la conexion";
    }

    if (!isset($_SESSION['id_usuario'])) {
        header("Location:../login.php");
    }

    $id_usuario = $_SESSION["id_usuario"];
    $rol = $_SESSION["rol"];

    $fecha_hoy = date("Y-m-d");

    $estatusvencido = 4;
    $res = 0.00;
    $update = "UPDATE `creditos` SET estatus = ? WHERE restante <> ? AND fecha_final <= ?";
    $result = $con->prepare($update);
    $result->bind_param('sss', $estatusvencido, $res, $fecha_hoy);
    $result->execute();
    $result->close();
   

    ?>