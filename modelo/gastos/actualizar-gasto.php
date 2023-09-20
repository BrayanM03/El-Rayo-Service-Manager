<?php

session_start();
include '../conexion.php';
$con = $conectando->conexion();

if (!isset($_SESSION['id_usuario'])) {
    header("Location:../../login.php");
}


if(isset($_POST)) {
    date_default_timezone_set("America/Matamoros");

    //Declaración de variables
    $hora = date("h:i a");
    $fecha = $_POST['fecha'];
    $id_forma_pago = 1;
    $extension_actual = $_POST['extension'];
    $id_gasto = $_POST['id_gasto'];
    $eliminar_comprobante = $_POST['eliminar_comprobante'];

    if($extension_actual == 'NA'){
        $comprobante = 0;
    }else{
        $targetDir = '../../src/docs/gastos/GA'.$id_gasto.'.'. $extension_actual;
        if(file_exists($targetDir)){
            if($eliminar_comprobante == true){
                $comprobante = 0;
                $extension_actual = 'NA';
                unlink($targetDir);
            }else{
                $comprobante = 1;
            }
        }else{
            $comprobante = 0;
        }
    }

    if(empty($_FILES['comprobante'])){
        $imageFileType = $extension_actual;
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
    
   
    //actualización de gasto
    $insert = "UPDATE gastos SET fecha = ?, id_categoria_gasto = ?, descripcion = ?, id_usuario = ?, facturado = ?, no_factura = ?, id_sucursal = ?, comprobante = ?, comprobante_extension =? WHERE id = ?";
    $res = $con->prepare($insert);
    $res->bind_param('ssssssssss', $fecha, $id_categoria, $descripcion, $id_usuario, $facturado, $folio_factura, $id_sucursal, $comprobante, $imageFileType, $id_gasto);
    $res->execute();
    $err = $res->error;
    $res->close();

    $insert = "UPDATE gastos_formas_pago SET monto = ? WHERE id_gasto = ?";
    $res = $con->prepare($insert);
    $res->bind_param('ss', $monto, $id_gasto); 
    $res->execute();
    $err = $res->error;
    $res->close();

    if($err) {
        $estatus = false;
        $mensaje = $err;
    } else {
        $estatus = true;
        $mensaje = 'Gasto actualizado correctamente';
    }

    
        // Directorio de destino para guardar el archivo
        if(empty($_FILES['comprobante'])){
            $mensaje_archivo = 'No se subio un archivo';
        }else{
        
            if ($imageFileType != 'jpg' && $imageFileType != 'png' && $imageFileType != 'jpeg' && $imageFileType != 'pdf') {
                echo json_encode(["error" => "El archivo no es un archivo valido."]);
                exit;
            }
    
            $filename = $targetDir . 'GA' . $id_gasto.'.' .$imageFileType;

           
                if (move_uploaded_file($_FILES["comprobante"]["tmp_name"], $targetDir . 'GA' . $id_gasto.'.' .$imageFileType)) {
                    $mensaje_archivo ="El archivo se ha subido correctamente.";
                } else {
                    $mensaje_archivo ="Hubo un error al subir el archivo.";
                }
            
           
        }
        
    
    $response = array('estatus' => $estatus, 'mensaje' => $mensaje, 'err' => $err, 'archivo'=> $mensaje_archivo);
    echo json_encode($response);

}
