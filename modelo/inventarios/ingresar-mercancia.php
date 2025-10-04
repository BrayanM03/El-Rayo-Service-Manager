<?php
session_start();
include '../conexion.php';
$con= $conectando->conexion(); 
date_default_timezone_set("America/Matamoros");
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
if (!isset($_SESSION['id_usuario'])) {
    header("Location:../../login.php");
}

if (!$con) {
    echo "maaaaal";
}
$fecha = date("Y-m-d");
$hora = date("H:i a");
$comprobante = 0;
if(isset($_POST)){


    $id_usuario = $_POST["id_usuario"];
    $sucursal_user_id = $_SESSION["id_sucursal"];
    $sucursal_id = $_POST["id_sucursal"]; 
    $id_bodega =0;
    $folio_factura = $_POST['folio_factura'];
    $id_proveedor = $_POST['id_proveedor'];
    $estado_movimiento = $_POST['estado_movimiento'];
    $fecha_emision_fact = $_POST['fecha_emision'];
    $fecha_vencido_fact = $_POST['fecha_vencido'];

    if($estado_movimiento == 1){
        $fecha_emision_fact = '';
        $fecha_vencido_fact = '';
    }

   
    $tipo = 2; //categoria tipo ingreso
    $pagado = 0;

    //Trayendo codigo y nombre de sucursal destino
    $comprobar = "SELECT nombre FROM sucursal WHERE id = ?";
    $result = $con->prepare($comprobar);
    $result->bind_param('s', $sucursal_id);
    $result->execute();
    $result->bind_result($nombre_sucursal);
    $result->fetch();
    $result->close();


    if($id_proveedor ==0){
        $response = array('mensaje'=> 'Selecciona un proveedor', "estatus"=>false);
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
    }else if($folio_factura =='' && $estado_movimiento !=1){
        $response = array('mensaje'=> 'Ingresa un folio', "estatus"=>false);
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
    }else{

        if($estado_movimiento ==1){
            $movimientos_duplicados =0;
        }else{
            $consultar = "SELECT COUNT(*) FROM movimientos WHERE proveedor_id = ? AND folio_factura = ?";
            $result = $con->prepare($consultar);
            $result->bind_param('is', $id_proveedor, $folio_factura);
            $result->execute();
            $result->bind_result($movimientos_duplicados);
            $result->fetch();
            $result->close();
        }
        
    
    if($movimientos_duplicados >0){
        $response = array('mensaje'=> 'El folio ingresado ya existe', "estatus"=>false);
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
    }else{
        $traerusuario = "SELECT nombre, apellidos FROM usuarios WHERE id = ?";
        $result = $con->prepare($traerusuario);
        $result->bind_param('s', $id_usuario);
        $result->execute();
        $result->bind_result($nombre_usuario, $apellidos_usuario);
        $result->fetch();
        $result->close();
        $nombre_completo_usuario = $nombre_usuario . " ". $apellidos_usuario;
    
        $traercantidad = "SELECT SUM(cantidad) FROM detalle_cambio WHERE id_usuario = ?";
        $result = $con->prepare($traercantidad);
        $result->bind_param('s', $id_usuario);
        $result->execute();
        $result->bind_result($total_llantas);
        $result->fetch();
        $result->close();
        
         $descripcion_movimiento = "Se realizo el ingreso de ". $total_llantas . " llanta(s) al inventario de " . $nombre_sucursal;
         $insertar = "INSERT INTO movimientos(id, 
                                              descripcion, 
                                              mercancia, 
                                              fecha, 
                                              hora, 
                                              usuario,
                                              tipo, 
                                              sucursal, 
                                              proveedor_id, 
                                              folio_factura, 
                                              estatus, 
                                              id_usuario, 
                                              estado_factura, 
                                              archivo,
                                              fecha_emision,
                                              fecha_vencido) VALUES(null, ?,?,?,?,?,?,?,?,?, 'Pendiente',?,?,?,?,?)";
                    $result = $con->prepare($insertar);
                    $result->bind_param('sssssssisssiss',$descripcion_movimiento, $total_llantas,
                                                    $fecha, $hora, $nombre_completo_usuario, $tipo, 
                                                    $sucursal_id, $id_proveedor, $folio_factura, $id_usuario, 
                                                    $estado_movimiento, $comprobante, $fecha_emision_fact, $fecha_vencido_fact);
                                                    
                    $result->execute();
                    $id_movimiento = $con->insert_id;
                    $result->close();
                   
    
        
        $traer_cambios= mysqli_query($con, "SELECT * FROM detalle_cambio WHERE id_usuario = $id_usuario");
        $mercancia ="";
        $costo_sumatoria = 0;
        while ($rows = $traer_cambios->fetch_assoc()) {
            $id_llanta = $rows["id_llanta"];
            $id_ubicacion = $rows["id_ubicacion"];
            $id_destino = $rows["id_destino"];
            $cantidad = $rows["cantidad"];
            $id_usuario = $rows["id_usuario"];
            $costo = $rows["costo"];
            $importe_partida = $costo * $cantidad;
            $costo_sumatoria += $importe_partida;
            //Comprobar si esa llanta se encuentra en el inventario destino
            $comprobar = "SELECT COUNT(*) FROM inventario WHERE id_sucursal = ? AND id_Llanta = ?";
            $result = $con->prepare($comprobar);
            $result->bind_param('ss', $id_destino, $id_llanta);
            $result->execute();
            $result->bind_result($llantas_coincidentes_sucursal);
            $result->fetch();
            $result->close();
    
            //Trayendo descripcion de la llanta 
            $traer = "SELECT Ancho, Proporcion, Diametro, Descripcion FROM llantas WHERE id = ?";
            $result = $con->prepare($traer);
            $result->bind_param('s', $id_llanta);
            $result->execute();
            $result->bind_result($llanta_ancho, $llanta_proporcion, $llanta_diametro, $llanta_descripcion);
            $result->fetch();
            $result->close();
            $mercancia = $mercancia . ", " . $llanta_descripcion . ", ";
    
              //Trayendo codigo y nombre de sucursal destino
            $comprobar = "SELECT COUNT(*) FROM medidas_stock WHERE ancho = ? AND perfil = ? AND rin = ? AND id_sucursal = ?";
            $result = $con->prepare($comprobar);
            $result->bind_param('ssss', $llanta_ancho, $llanta_proporcion, $llanta_diametro, $id_destino);
            $result->execute();
            $result->bind_result($medidas_encontradas);
            $result->fetch();
            $result->close();

            if($medidas_encontradas==1){
                $comprobar = "SELECT stock_minimo, stock_maximo, estatus FROM medidas_stock WHERE ancho = ? AND perfil = ? AND rin = ? AND id_sucursal = ?";
                $result = $con->prepare($comprobar);
                $result->bind_param('ssss', $llanta_ancho, $llanta_proporcion, $llanta_diametro, $id_destino);
                $result->execute();
                $result->bind_result($stock_minimo, $stock_maximo, $stock_estatus);
                $result->fetch();
                $result->close();
            }else{
                $stock_minimo=0;
                $stock_maximo =0;
                $stock_estatus = 2;
            }
            //Trayendo codigo y nombre de sucursal destino
            $comprobar = "SELECT code, nombre FROM sucursal WHERE id = ?";
            $result = $con->prepare($comprobar);
            $result->bind_param('s', $id_destino);
            $result->execute();
            $result->bind_result($code, $nombre_sucursal);
            $result->fetch();
            $result->close();
    
            $acumulado = 0;
            if($llantas_coincidentes_sucursal == 0){

    
                    //Insertando llanta a sucursal destino
                    $codigo = $code . $id_llanta;
                    $insertar = "INSERT INTO inventario(id, id_Llanta, Codigo, Sucursal, id_sucursal, Stock, stock_minimo, stock_maximo, medida_stock_estatus) VALUES(null, ?,?,?,?,?,?,?,?)";
                    $result = $con->prepare($insertar);
                    $result->bind_param('ssssssss',$id_llanta, $codigo, $nombre_sucursal, $id_destino, $cantidad, $stock_minimo, $stock_maximo, $stock_estatus);
                    $result->execute();
                    $result->close();
    
                    $insertar = "INSERT INTO historial_detalle_cambio(id, 
                    id_llanta, 
                    id_ubicacion, 
                    id_destino, 
                    cantidad, 
                    id_usuario, 
                    id_movimiento,
                    stock_ubicacion_actual,
                    stock_ubicacion_anterior,
                    stock_destino_actual,
                    stock_destino_anterior,
                    aprobado_receptor,
                    aprobado_emisor,
                    usuario_emisor,
                    usuario_receptor,
                    costo,
                    importe) VALUES(null, ?,?,?,?,?,?,0,0,?,0,0,0,?,?,?,?)";
                    $result = $con->prepare($insertar);
                    $result->bind_param('sssssssssss',$id_llanta, $id_bodega, $id_destino, $cantidad, $id_usuario, $id_movimiento, $cantidad, $id_usuario, $id_usuario, $costo, $importe_partida);
                    $result->execute();
                    $result->close();
                
    
            }else{
                /*Para el siguiente codigo la llanta se encuentra en la sucursal destino y lo que haremos sera actualizar el stock de
                la llanta */
                //Traemos el stock de la sucursal destino para actualizarla
                $comprobar = "SELECT Stock FROM inventario WHERE id_sucursal = ? AND id_Llanta = ?";
                $result = $con->prepare($comprobar);
                $result->bind_param('ss', $id_destino, $id_llanta);
                $result->execute();
                $result->bind_result($stock_destino_anterior);
                $result->fetch();
                $result->close();
    
                $stock_destino_anterior= intval($stock_destino_anterior);
                $cantidad = intval($cantidad);
                $stock_destino_actual = $cantidad + $stock_destino_anterior;
               
                if ($cantidad <= 0) {
                    $response = array('mensaje'=> 'La cantidad no puede ser menor o igual a 0', "estatus"=>false);
                    echo json_encode($response, JSON_UNESCAPED_UNICODE);
                }else{
    
                    //Actualizando sucursal destino
                    $update = "UPDATE inventario SET Stock = ? WHERE id_sucursal = ? AND id_Llanta = ?";
                    $result = $con->prepare($update);
                    $result->bind_param('sss', $stock_destino_actual, $id_destino, $id_llanta);
                    $result->execute();
                    $result->close();
    
                    $insertar = "INSERT INTO historial_detalle_cambio(id, 
                    id_llanta, 
                    id_ubicacion, 
                    id_destino, 
                    cantidad, 
                    id_usuario,
                    id_movimiento,
                    stock_ubicacion_actual,
                    stock_ubicacion_anterior,
                    stock_destino_actual,
                    stock_destino_anterior,
                    aprobado_receptor,
                    aprobado_emisor,
                    usuario_emisor,
                    usuario_receptor,
                    costo,
                    importe) VALUES(null, ?,?,?,?,?,?,0,0,?,?,0,0,?,?,?,?)";
                    $result = $con->prepare($insertar);
                    $result->bind_param('ssssssssssss',$id_llanta, $id_bodega, $id_destino, $cantidad, $id_usuario, $id_movimiento, $stock_destino_actual, $stock_destino_anterior, $id_usuario, $id_usuario, $costo, $importe_partida);
                    $result->execute();
                    $result->close();
    
                }
            }
            
        }

        if(empty($_FILES['documento_adjunto'])){
            $comprobante = 0;
            $imageFileType = 'NA';
            $mensaje_archivo = 'No se subio un archivo';

        }else{
            $comprobante = 1;
            $targetDir = '../../src/docs/facturas_compras/';
            $targetFile = $targetDir . basename($_FILES['documento_adjunto']['name']);
            $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
            if ($imageFileType != 'jpg' && $imageFileType != 'png' && $imageFileType != 'jpeg' && $imageFileType != 'pdf') {
                echo json_encode(["error" => "El archivo no es una imagen válida."]);
                exit;
            }
    
            // Mover el archivo al directorio de destino
            if (move_uploaded_file($_FILES["documento_adjunto"]["tmp_name"], $targetDir . 'FAC-' . $id_movimiento.'.' .$imageFileType)) {
                
                $mensaje_archivo ="El archivo se ha subido correctamente.";
            } else {
                $comprobante = 0;
                $mensaje_archivo ="Hubo un error al subir el archivo.";
            }
        }

        $update = "UPDATE movimientos SET mercancia = ?, total = ?, pagado = ?, restante = ?, archivo = ?, extension_archivo = ? WHERE id = ?";
        $respp = $con->prepare($update);
        $respp->bind_param('sssssss', $mercancia, $costo_sumatoria, $pagado, $costo_sumatoria, $comprobante, $imageFileType, $id_movimiento);
        $respp->execute();
        $respp->close();
        $response = array('mensaje'=> 'Mercancia agregada correctamente', "estatus"=>true, 'id_entrada'=> $id_movimiento, 'archivo'=>$mensaje_archivo, 'post'=>$_POST, 'files'=>$_FILES);
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
    }
    }
}

?>