
<?php
session_start();
include '../conexion.php';
require_once '../catalogo/Catalogo.php';
include '../helpers/response_helper.php';

$con= $conectando->conexion(); 

if (!isset($_SESSION['id_usuario'])) {
    header("Location:../../login.php");
}


if(isset($_POST)){
    $id_usuario = $_SESSION['id_usuario'];
    $id_producto = $_POST['id_producto'];
    $cantidad = $_POST['cantidad'];
    $id_sucursal = $_POST['id_sucursal'];
    $iduser = $_SESSION["id_usuario"]; 
    $tipo = $_POST['tipo'];
    $sidebar = $_POST['ocultar_sidebar']; //Aqui el tipo es desde el sidebar

    $catalogo = new Catalogo($con);
    $servicio_arreglo = $catalogo->obtenerServicio($id_producto);
    if(!$servicio_arreglo['estatus']){
        responder(false, $servicio_arreglo['mensaje'], 'danger', [], true);
    }

    $producto = $servicio_arreglo['producto'];
    $importe = floatval($producto['precio']) * $cantidad;
    $count = "SELECT COUNT(*) FROM productos_preventa WHERE id_usuario = ? AND id_llanta = ?";
    $stmt = $con->prepare($count);
    $stmt->bind_param('ss', $id_usuario, $id_producto);
    $stmt->execute();
    $stmt->bind_result($total_detalle);
    $stmt->fetch();
    $stmt->close();
    if($total_detalle>0){
        
        $count = "SELECT pv.* FROM productos_preventa pv
         WHERE pv.id_usuario = ? AND pv.id_llanta = ?";
        $stmt = $con->prepare($count);
        $stmt->bind_param('ss', $id_usuario, $id_producto);
        $stmt->execute();
        $resultado_ = $stmt->get_result();
        $stmt->close();

        while ($fila = $resultado_->fetch_assoc()) {
            $detalle_arreglo = $fila;
        }
        if($sidebar == 1){
            $cantidad=1;
            $sumar_restar_cant = $_POST['sumar_restar_cantidad'];
            if($sumar_restar_cant==1){
                $nueva_cantidad = $detalle_arreglo['cantidad'] + $cantidad;
            }else if($sumar_restar_cant==0){
                $nueva_cantidad = $detalle_arreglo['cantidad'] - $cantidad;
            }
        }else{
            $nueva_cantidad = $detalle_arreglo['cantidad'] + $cantidad;
        }
       
        $id_detalle =$detalle_arreglo['id'];
        $nuevo_importe = $nueva_cantidad * floatval($detalle_arreglo['precio']);
        //print_r($nuevo_importe);
        $upda = "UPDATE productos_preventa SET cantidad = ?, importe = ? WHERE id = ?";
        $stmt = $con->prepare($upda);
        $stmt->bind_param('isi', $nueva_cantidad, $nuevo_importe, $id_detalle);
        $stmt->execute();
        $stmt->close();
        $mensaje = 'Producto actualizado con exito';

    }else{

        
        $insert = "INSERT INTO productos_preventa(codigo, descripcion, modelo, cantidad, precio, importe, id_usuario, id_llanta, tipo, promocion)
        VALUES (?,?,?,?,?,?,?,?,?,0)";
        $stmt = $con->prepare($insert);
        $stmt->bind_param('sssssssss', $producto['codigo'], $producto['descripcion'], $producto['img'], $cantidad, $producto['precio'],
        $importe, $id_usuario, $id_producto, $tipo);
        $stmt->execute();
        $stmt->close();
        $mensaje = 'Producto agregado con exito';
    
}

responder(true, $mensaje, 'success', [], true, true);
}
responder(false, 'No hay una solicitud POST', 'error', [], true, true);


?>