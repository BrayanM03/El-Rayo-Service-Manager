<?php

session_start();
include '../conexion.php';
$con = $conectando->conexion();

if (!isset($_SESSION['id_usuario'])) {
    header("Location:../../login.php");
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
if(isset($_POST)) {
    /* 
    $comentario = $_POST['comentario']; */
    $datos = $_POST['datos'];
    $datos = json_decode($datos);
    $comentario = $_POST['comentario'];
    $factura = $_POST['folio_factura'];
    $id_cliente = $_POST['id_cliente'] == 'undefined' ? 1 : $_POST['id_cliente'];
    $id_sucursal = $_POST['sucursal'];
  /*   print_r($id_sucursal);
    die(); */
   // $id_sucursal = $_POST['id_sucursal'];
    $id_venta = $_POST['id_venta'] == '' ? 0: $_POST['id_venta'];
    
    if(empty($_FILES['comprobante'])){
        $comprobante = 0;
        $imageFileType = 'NA';
    }else{
        $comprobante = 1;
        $targetDir = '../../src/docs/garantias/';
        $targetFile = $targetDir . basename($_FILES['comprobante']['name']);
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    }
    $id_garantia =0;
    
    foreach ($datos as $key => $value) {
        $numero=$value->numero;
        $id_llanta = intval($value->codigo);
        $cantidad= $value->cantidad;
        $descripcion= $value->descripcion;
        $marca = $value->marca;
        $precio = $value->precio;
        $dot = $value->dot;
        $dictamen = 'pendiente';
        $insert = "INSERT INTO garantias(id, id_cliente, cantidad, id_llanta, dot, descripcion, marca, comentario_inicial, dictamen, factura, id_sucursal, id_venta, comprobante_extension) VALUES(null,?,?,?,?,?,?,?,?,?,?,?,?)";
        $stmt = $con->prepare($insert);
        $stmt->bind_param('ssssssssssss', $id_cliente, $cantidad, $id_llanta, $dot, $descripcion, $marca, $comentario, $dictamen, $factura, $id_sucursal, $id_venta, $imageFileType);
        $stmt->execute();
        $error = $stmt->error;
        if($error){
            print_r($error);
        }
        $id_garantia = $con->insert_id;
        $stmt->close();
    }
    // Directorio de destino para guardar el archivo
    if(empty($_FILES['comprobante'])){
        $mensaje_archivo = 'No se subio un archivo';
    }else{
        /* $targetDir = '../../src/docs/gastos/';
        $targetFile = $targetDir . basename($_FILES['comprobante']['name']);
   
        // Verificar si el archivo es una imagen (por ejemplo, JPG, PNG)
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION)); */
        if ($imageFileType != 'jpg' && $imageFileType != 'png' && $imageFileType != 'jpeg' && $imageFileType != 'pdf') {
            echo json_encode(["error" => "El archivo no es una imagen válida."]);
            exit;
        }

        // Mover el archivo al directorio de destino
        if (move_uploaded_file($_FILES["comprobante"]["tmp_name"], $targetDir . 'GT' . $id_garantia.'.' .$imageFileType)) {
            $mensaje_archivo ="El archivo se ha subido correctamente.";
        } else {
            $mensaje_archivo ="Hubo un error al subir el archivo.";
        }
    }

    $response = array('estatus'=>true, 'mensaje'=>'Garantia registrada correctamente', 'error'=>$error, 'POST'=>$_POST);
    echo json_encode($response);
}
