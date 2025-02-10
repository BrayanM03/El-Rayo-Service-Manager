<?php
session_start();
include '../conexion.php';
require_once '../catalogo/Catalogo.php';
include '../helpers/response_helper.php';
$con= $conectando->conexion(); 
$catalogo = new Catalogo($con);

if (!$con) {
    responder(false, 'Error en la conexión', 'danger', [], true);
} 
$id_usuario = $_SESSION['id_usuario'];
$count = "SELECT count(*) FROM productos_preventa WHERE id_usuario =?";
        $res = $con->prepare($count);
        $res->bind_param('s', $id_usuario);
        $res->execute();
        $res->bind_result($total);
        $res->fetch();
        $res->close();

        if($total >0){
            //Trayendo urls de imagenes
            $sel = 'SELECT pv.*, s.nombre as sucursal_nombre FROM productos_preventa pv lEFT JOIN sucursal s ON pv.id_sucursal = s.id WHERE id_usuario = ?';
            $stmt= $con->prepare($sel);
            $stmt->bind_param('s', $id_usuario);
            $stmt->execute();
            $resultado_ = $stmt->get_result();
            while ($registro = $resultado_->fetch_assoc()) {
                if($registro['tipo']==1){
                    $producto_arreglo = $catalogo->obtenerProducto($registro['id_llanta']);
                    $registro['marca']=$producto_arreglo['producto']['Marca'];
                    $datos['partidas'][] = $registro; // Añade cada fila al arreglo
                }else{
                    $producto_arreglo = $catalogo->obtenerServicio($registro['id_llanta']);
                    $datos['partidas'][] = $registro;
                }
                
            }
            $stmt->close();

            $count = "SELECT SUM(importe) FROM productos_preventa WHERE id_usuario =?";
            $res = $con->prepare($count);
            $res->bind_param('s', $id_usuario);
            $res->execute();
            $res->bind_result($importe_total);
            $res->fetch();
            $res->close();
            $datos['importe'] = $importe_total;
            responder(true,  "Se encontrarón productos", 'success', $datos, true);   
        }else{
            responder(false,  "No se encontrarón productos", 'warning', [], true);

        }


?>