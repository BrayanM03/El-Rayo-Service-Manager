<?php

session_start();
include '../conexion.php';
include '../helpers/response_helper.php';
$con = $conectando->conexion();
date_default_timezone_set("America/Matamoros");

if (!isset($_SESSION['id_usuario'])) {
    header("Location:../../login.php");
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$fecha = date('Y-m-d');
if(isset($_POST)) {
    /* 
    $comentario = $_POST['comentario']; */
    $datos = $_POST['datos'];
    $datos = json_decode($datos);
    $comentario = $_POST['comentario'];
    $factura = $_POST['folio_factura'];
    $id_cliente = $_POST['id_cliente'] == 'undefined' ? 0 : $_POST['id_cliente'];
    $id_vendedor = empty($_POST['id_vendedor']) ? 0 : $_POST['id_vendedor'];
    $id_sucursal = $_POST['id_sucursal'] == 'undefined' ? 0 : $_POST['id_sucursal'];
    $id_sucursal_recibe = $_POST['id_sucursal_recibe'];
    $id_usuario_recibe = $_POST['id_usuario_recibe'];

    $id_venta = $_POST['id_venta'] == '' ? 0: $_POST['id_venta'];
    
    if(count($_FILES)>0){
            //Validacion de comprobantes y adjuntos de la garantias
            

            foreach ($datos as $key => $value) {
                $numero=$value->numero;
                $id_llanta = intval($value->codigo);
                $cantidad= $value->cantidad;
                $descripcion= $value->descripcion;
                $marca = $value->marca;
                $precio = $value->precio;
                $dot = $value->dot;
                $dictamen = 'pendiente';
                $insert = "INSERT INTO garantias(id, id_cliente, cantidad, id_llanta, dot, 
                descripcion, marca, comentario_inicial, dictamen, factura, id_sucursal, id_venta, id_usuario, id_sucursal_recibe,
                id_usuario_recibe, estatus_fisico, fecha_registro) 
                VALUES(null,?,?,?,?,?,?,?,?,?,?,?,?,?,?,1,?)";
                $stmt = $con->prepare($insert);
                $stmt->bind_param('sssssssssssssss', $id_cliente, $cantidad, $id_llanta, $dot, $descripcion, 
                $marca, $comentario, $dictamen, $factura, $id_sucursal, $id_venta, $id_vendedor, $id_sucursal_recibe,
                 $id_usuario_recibe, $fecha);
                $stmt->execute();
                $error = $stmt->error;
                if($error){
                    responder(false, 'Ocurrio un error, mensaje: ' . $error, 'danger', null, true);
                }
                $id_garantia = $con->insert_id;
                $stmt->close();
            }
            
                $estructura = '../../src/docs/garantias/'.$id_garantia;
                if(!mkdir($estructura, 0777, true)) {
                    responder(false, 'Ocurrio un error al crear la estructura de carpetas', 'danger', null, true);
                }

            //Recorremos los ficheros adjuntos
            foreach ($_FILES as $key => $value) {
                $insert ="INSERT INTO garantias_imagenes(id, id_garantia) VALUES(null,?)";
                $stmt = $con->prepare($insert);
                $stmt->bind_param('i',$id_garantia);
                $stmt->execute();
                $stmt->close();
                $id_garantia_imagen = $con->insert_id;

                $targetDir = '../../src/docs/garantias/';
                $targetFile = $targetDir . basename($value['name']);
                $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

                if ($imageFileType != 'jpg' && $imageFileType != 'png' && $imageFileType != 'jpeg' && $imageFileType != 'pdf') {
                    responder(false, 'El archivo no es una imagen válida ', 'danger', null, true);
                }

                $ruta_final = '../../src/docs/garantias/'.$id_garantia.'/'.$id_garantia_imagen . '.'. $imageFileType;
                $ruta_db = $id_garantia_imagen .  '.'. $imageFileType;
                $updt ="UPDATE garantias_imagenes SET ruta = ? WHERE id = ?";
                $stmt = $con->prepare($updt);
                $stmt->bind_param('si',$ruta_db, $id_garantia_imagen);
                $stmt->execute();
                $stmt->close();

                
                // Mover el archivo al directorio de destino
                if (move_uploaded_file($value["tmp_name"], $ruta_final)) {
                         $mensaje_archivo ="El archivo se ha subido correctamente.";
                } else {
                        $mensaje_archivo ="Hubo un error al subir el archivo.";
                        responder(false, $mensaje_archivo, 'danger', null, true);
                }

            }    
            responder(true, 'Garantía registrada correctamente', 'success', null, true);
            
    }else{
        responder(false, 'Debes adjuntar archivos de evidencia fotografica', 'danger', null, true);
    }
}
