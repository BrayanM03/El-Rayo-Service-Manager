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
    $promocion = $_POST['promocion'];

    $catalogo = new Catalogo($con);
    $producto_arreglo = $catalogo->obtenerProducto($id_producto);
    if(!$producto_arreglo['estatus']){
        responder(false, $producto_arreglo['mensaje'], 'danger', [], true);
    }

    $comparar_pu_preventa = $_POST['comparar_pu_preventa'];
    $producto = $producto_arreglo['producto'];
    
    if($comparar_pu_preventa=='false'){
        if($promocion==1){
            $precio_unitario = floatval($producto['precio_promocion']);
            $importe = $precio_unitario * $cantidad;
        }else{
            $precio_unitario=floatval($producto['precio_Venta']);
            $importe =  $precio_unitario * $cantidad;
        }
    }


    $comprobacion = $catalogo->comprobarStock($id_producto, $id_sucursal, $cantidad);
   
    if(!$comprobacion['estatus']){
        responder(false, $comprobacion['mensaje'], 'warning', [], true);
    }

        $count = "SELECT COUNT(*) FROM productos_preventa WHERE id_usuario = ? AND id_llanta = ? AND id_sucursal = ?";
        $stmt = $con->prepare($count);
        $stmt->bind_param('sss', $id_usuario, $id_producto, $id_sucursal);
        $stmt->execute();
        $stmt->bind_result($total_detalle);
        $stmt->fetch();
        $stmt->close();
        if($total_detalle>0){
            
            $count = "SELECT pv.* FROM productos_preventa pv
             WHERE pv.id_usuario = ? AND pv.id_llanta = ? AND pv.id_sucursal = ?";
            $stmt = $con->prepare($count);
            $stmt->bind_param('sss', $id_usuario, $id_producto, $id_sucursal);
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
            $segunda_comprobacion = $catalogo->comprobarStock($id_producto, $id_sucursal, $nueva_cantidad);            

            if(!$segunda_comprobacion['estatus']){
                responder(false, $segunda_comprobacion['mensaje'], 'warning', [], true);
            }
            $id_detalle =$detalle_arreglo['id'];
            $nuevo_importe = $nueva_cantidad * floatval($detalle_arreglo['precio']);
            $upda = "UPDATE productos_preventa SET cantidad = ?, importe = ? WHERE id = ?";
            $stmt = $con->prepare($upda);
            $stmt->bind_param('isi', $nueva_cantidad, $nuevo_importe, $id_detalle);
            $stmt->execute();
            $stmt->close();
         
            $mensaje = 'Producto actualizado con exito';

        }else{
 
            $inventario_registro=$comprobacion['data'];
            $insert = "INSERT INTO productos_preventa(codigo, descripcion, modelo, cantidad, precio, importe, id_usuario, id_sucursal, id_llanta, tipo, promocion)
            VALUES (?,?,?,?,?,?,?,?,?,?,?)";
            $stmt = $con->prepare($insert);
            $stmt->bind_param('sssssssssss', $inventario_registro['Codigo'], $producto['Descripcion'], $producto['Modelo'], $cantidad, $precio_unitario,
            $importe, $id_usuario, $id_sucursal, $id_producto, $tipo, $promocion);
            $stmt->execute();
            $stmt->close();
            $mensaje = 'Producto agregado con exito';
    }

    if($tipo==1){
        insertarValvulaBalanceo($con, 1, $id_usuario, 'SERV1', 'BALANCEO CORTESIA', 'balanceo', $id_producto);
        insertarValvulaBalanceo($con, 4, $id_usuario, 'SERV4', 'VALVULA CORTESIA', 'pivote', $id_producto);
    }
         


    responder(true, $mensaje, 'success', [], true, true);
}else{
    responder(false, 'No hay una solicitud POST', 'error', [], true, true);

}


function insertarValvulaBalanceo($con, $id_producto, $id_usuario, $codigo, $descripcion, $modelo, $id_llanta){
    $total_detalle=0;
    $count = "SELECT COUNT(*) FROM productos_preventa WHERE id_usuario = ? AND tipo = 1";
    $stmt = $con->prepare($count);
    $stmt->bind_param('s', $id_usuario);
    $stmt->execute();
    $stmt->bind_result($total_detalle);
    $stmt->fetch();
    $stmt->close();

    $cantidad_llantas=0;
    if($total_detalle>0){
        //Comprobamos la cantidad de llantas
        $count = "SELECT SUM(cantidad) FROM productos_preventa WHERE id_usuario = ? AND tipo = 1";
        $stmt = $con->prepare($count);
        $stmt->bind_param('s', $id_usuario);
        $stmt->execute();
        $stmt->bind_result($cantidad_llantas);
        $stmt->fetch();
        $stmt->close();
    }else{
        return false;
    }
    

    $cantidad_servicios=0;
    $count = "SELECT COUNT(*) FROM productos_preventa WHERE id_usuario = ? AND id_llanta = ? AND tipo = 2";
    $stmt = $con->prepare($count);
    $stmt->bind_param('ss', $id_usuario, $id_producto);
    $stmt->execute();
    $stmt->bind_result($cantidad_servicios);
    $stmt->fetch();
    $stmt->close();

    if($cantidad_servicios>0){

        $upda = "UPDATE productos_preventa SET cantidad = ?, precio=0, importe = 0 WHERE id_usuario = ? AND tipo = 2";
        $stmt = $con->prepare($upda);
        $stmt->bind_param('ii', $cantidad_llantas, $id_usuario);
        $stmt->execute();
        $stmt->close();
    }else{
        $insert = "INSERT INTO productos_preventa(codigo, descripcion, modelo, cantidad, precio, importe, id_usuario, id_sucursal, id_llanta, tipo, promocion)
        VALUES (?,?,?,?,0,0,?,null,?,2,0)";
        $stmt = $con->prepare($insert);
        $stmt->bind_param('ssssss', $codigo, $descripcion, $modelo, $cantidad_llantas, $id_usuario, $id_producto);
        $stmt->execute();
        $stmt->close();
    }
}
?>