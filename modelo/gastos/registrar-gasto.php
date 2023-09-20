<?php

session_start();
include '../conexion.php';
$con = $conectando->conexion();

if (!isset($_SESSION['id_usuario'])) {
    header("Location:../../login.php");
}


if(isset($_POST)) {
    date_default_timezone_set("America/Matamoros");
    $hora = date("h:i a");
    $fecha = date("Y-m-d");
    $id_forma_pago = 1;
    if(empty($_FILES['comprobante'])){
        $comprobante = 0;
        $imageFileType = 'NA';
    }else{
        $comprobante = 1;
        $targetDir = '../../src/docs/gastos/';
        $targetFile = $targetDir . basename($_FILES['comprobante']['name']);
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    }
    $descripcion = $_POST["descripcion"];
    $id_categoria = $_POST["categoria"];
    $id_usuario = $_SESSION['id_usuario'];
    $folio_factura = $_POST["folio_factura"];
    $id_sucursal = $_POST["id_sucursal"];
    $monto = $_POST["monto"];
    $facturado = $_POST['folio_factura'] == null ? 0 : 1;
    $insert = "INSERT INTO gastos (fecha, id_categoria_gasto, descripcion, id_usuario, facturado, no_factura, id_sucursal, comprobante, comprobante_extension) VALUES(?,?,?,?,?,?,?,?,?)";
    $res = $con->prepare($insert);
    $res->bind_param('sssssssss', $fecha, $id_categoria, $descripcion, $id_usuario, $facturado, $folio_factura, $id_sucursal, $comprobante, $imageFileType);
    $res->execute();
    $err = $res->error;
    $id_gasto = $con->insert_id;
    $res->close();

    $insert = "INSERT INTO gastos_formas_pago (id_gasto, id_forma_pago, monto) VALUES(?,?,?)";
    $res = $con->prepare($insert);
    $res->bind_param('sss', $id_gasto, $id_forma_pago, $monto); 
    $res->execute();
    $err = $res->error;
    $res->close();

    if($err) {
        $estatus = false;
        $mensaje = $err;
    } else {
        $estatus = true;
        $mensaje = 'Gasto registrado correctamente';
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
            if (move_uploaded_file($_FILES["comprobante"]["tmp_name"], $targetDir . 'GA' . $id_gasto.'.' .$imageFileType)) {
                $mensaje_archivo ="El archivo se ha subido correctamente.";
            } else {
                $mensaje_archivo ="Hubo un error al subir el archivo.";
            }
        }
        
    
    $response = array('estatus' => $estatus, 'mensaje' => $mensaje, 'err' => $err, 'archivo'=> $mensaje_archivo);
    echo json_encode($response);

}
