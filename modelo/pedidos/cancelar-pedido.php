<?php


session_start();
include '../conexion.php';
$con = $conectando->conexion();

if (!isset($_SESSION['id_usuario'])) {
    header("Location:../../login.php");
}



if ($_SESSION["rol"]  !== "1") {

    header("Location:../../notfound_404.php");

}
date_default_timezone_set("America/Matamoros");
$hora = date("h:i a");
$fecha = date('Y-m-d');

if(isset($_POST)) {
    $id_pedido = $_POST["id_pedido"];
    $motivo = $_POST["motivo"];
    $id_sesion = $_SESSION['id_usuario'];
    //Conseguir susucusal

    $obtenerSuc = "SELECT estatus FROM pedidos WHERE id = ?";
    $stmt = $con->prepare($obtenerSuc);
    $stmt->bind_param('i', $id_pedido);
    $stmt->execute();
    $stmt->bind_result($estatus);
    $stmt->fetch();
    $stmt->close();

    if($estatus =='Cancelado'){
        $response = array('estatus'=>false, 'data'=>3);
    }else{
        $newStatus = "Cancelado";
        $editar_status = $con->prepare("UPDATE pedidos SET estatus = ?, comentario = ? WHERE id = ?");
        $editar_status->bind_param('ssi', $newStatus, $motivo, $id_pedido);
        $editar_status->execute();
        $editar_status->close();
        $response = array('estatus'=>true, 'data'=>1);

    }

    echo json_encode($response);
}
    ?>