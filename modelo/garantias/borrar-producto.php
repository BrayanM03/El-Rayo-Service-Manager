<?php
session_start();
include '../conexion.php';
include '../helpers/response_helper.php';

$con= $conectando->conexion(); 

if (!isset($_SESSION['id_usuario'])) {
    header("Location:../../login.php");
}


if(isset($_POST)){
    $id_detalle = $_POST['id_detalle'];

    $count = "SELECT COUNT(*) FROM productos_preventa WHERE id  = ?";
        $stmt = $con->prepare($count);
        $stmt->bind_param('s', $id_detalle);
        $stmt->execute();
        $stmt->bind_result($total_detalle);
        $stmt->fetch();
        $stmt->close();
        print_r($total_detalle);
        if($total_detalle>0){

            $del = "DELETE FROM productos_preventa WHERE id = ?";
            $stmt = $con->prepare($del);
            $stmt->bind_param('s', $id_producto);
            $stmt->execute();
            $stmt->close();
            
            responder(true, 'Producto borrado con exito', 'success', [], true);
        
        }else{
                responder(false, 'No se encontrarón productos con los IDs proporcionados', 'danger', [], true);
            }
}

    ?>