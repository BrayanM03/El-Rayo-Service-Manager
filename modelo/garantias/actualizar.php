<?php

session_start();
include '../conexion.php';
$con = $conectando->conexion();

if (!isset($_SESSION['id_usuario'])) {
    header("Location:../../login.php");
}

if(isset($_POST)) {
    $analisis = $_POST['analisis'];
    $dictamen = $_POST['dictamen'];
    $lugar_expedicion = $_POST['lugar_expedicion'];
    $fecha_expedicion = $_POST['fecha_expedicion'];    
    $id_garantia = $_POST['id_garantia'];
    $count = "SELECT COUNT(*) FROM garantias WHERE id = ?";
    $stmt = $con->prepare($count);
    $stmt->bind_param('i', $id_garantia);
    $stmt->execute();
    $stmt->bind_result($total_count);
    $stmt->fetch();
    $stmt->close();

    if($total_count > 0){
        $updt = "UPDATE garantias SET analisis = ?, dictamen = ?, lugar_expedicion = ?, fecha_expedicion = ? WHERE id = ?";
        $stmt = $con->prepare($updt);
        $stmt->bind_param('ssssi', $analisis, $dictamen, $lugar_expedicion, $fecha_expedicion, $id_garantia);
        $stmt->execute();
        $stmt->close();

        $response = array('estatus' => true, 'mensaje' => 'Se actualizó correctamente');
    }else{
        $response = array('estatus' => false, 'data' => [], 'mensaje' => 'Esta garantía no existe');

    }

    echo json_encode($response);
}
?>