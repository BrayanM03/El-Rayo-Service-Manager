<?php

session_start();
include '../conexion.php';
$con = $conectando->conexion();

if (!isset($_SESSION['id_usuario'])) {
    header("Location:../../login.php");
}

$id_suc = $_POST['ubi'];

if($id_suc ==0){

    $querySucu = "SELECT COUNT(*) FROM sucursal";
$resps = $con->prepare($querySucu);
$resps->execute();
$resps->bind_result($total_sucu);
$resps->fetch();
$resps->close();

if ($total_sucu > 0) {
    $querySuc = "SELECT * FROM sucursal";
    $respon = mysqli_query($con, $querySuc);

    while ($rows = $respon->fetch_assoc()) {
        $suc_identificador = $rows['id'];
        $nombre_suc = $rows['nombre'];

        $data[]= $rows;
    }

    echo json_encode($data, JSON_UNESCAPED_UNICODE);
}

}else{
    $querySucu = "SELECT COUNT(*) FROM sucursal WHERE id != ?";
    $resps = $con->prepare($querySucu);
    $resps->bind_param("i", $id_suc);
    $resps->execute();
    $resps->bind_result($total_sucu);
    $resps->fetch();
    $resps->close();
    
    if ($total_sucu > 0) {
        $querySuc = "SELECT * FROM sucursal WHERE id != $id_suc";
        $respon = mysqli_query($con, $querySuc);
    
        while ($rows = $respon->fetch_assoc()) {
            $suc_identificador = $rows['id'];
            $nombre_suc = $rows['nombre'];
    
            $data[]= $rows;
        }
    
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    } 
}

