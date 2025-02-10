<?php

session_start();
include '../conexion.php';
$con= $conectando->conexion(); 

if (!isset($_SESSION['id_usuario'])) {
    header("Location:../../login.php"); 
} 


if(isset($_POST)) {
    include '../ventas/insertar_utilidad.php';
    include '../creditos/obtener-utilidad-abono.php';
    include '../helpers/response_helper.php';
    date_default_timezone_set("America/Matamoros");
    $hora = date("h:i a");

    //Variables para el historial venta
    $fecha_inicial = date("Y-m-d");
    $fecha_final = date("Y-m-d", strtotime($fecha_inicial . " +1 month"));

    if(isset($_POST["plazo"])){ 
        $estatus = 'Activo';
        $plazo = 3;
        $tipo = 'Apartado';
      } 
      $monto_pago=0;

      $pago_efectivo=0;
      $pago_transferencia=0;
      $pago_tarjeta=0;
      $pago_cheque=0;
      $pago_deposito=0;
      $pago_sin_definir=0;
      $arreglo_metodos = ['Efectivo', 'Tarjeta', 'Transferencia', 'Cheque', 'Sin definir', 'Deposito'];
        foreach ($_POST['metodos_formateado'] as $key => $value) {
        $metodo_id = isset($value['id_metodo']) ? $value['id_metodo']: $key;
        switch ($metodo_id) {
          case 0:
           $pago_efectivo = $value['monto']; 
            break;
          
          case 1:
          $pago_tarjeta = $value['monto'];
          break;
          
          case 2:
          $pago_transferencia = $value['monto'];
  
          break;
    
          case 3:
          $pago_cheque = $value['monto'];
          break;

          case 5:
            $pago_deposito = $value['monto'];
            break;
    
          case 4:
          $pago_sin_definir = $value['monto'];
          break;     
          
          default:
            break;
        }
        $monto_pago += $value['monto'];
        $metodo_pago = $arreglo_metodos[$value['metodo']];
        $desc_metodos ='';
        if($key != count($_POST["metodos_formateado"]) - 1) {
          // Este código se ejecutará para todos menos el último
          $desc_metodos .= $metodo_pago . ", ";
        }else{
          $desc_metodos .= $metodo_pago . ". ";
        }
      }
      
      $id_sucursal = $_SESSION["id_sucursal"];
      $id_usuario =   $_SESSION['id_usuario'];
      $cliente =  $_POST['id_cliente'];
      $restante = $_POST['restante'];
      $adelanto = $monto_pago; 

      $ID = $con->prepare("SELECT nombre, apellidos FROM usuarios WHERE id = ?");
      $ID->bind_param('i', $id_usuario);
      $ID->execute();
      $ID->bind_result($vendedor_name, $vendedor_apellido);
      $ID->fetch();
      $ID->close();

      $vendedor_usuario = $vendedor_name . " " . $vendedor_apellido;

    $querySuc = "SELECT nombre FROM sucursal WHERE id =?";
    $resp=$con->prepare($querySuc);
    $resp->bind_param('i', $id_sucursal);
    $resp->execute();
    $resp->bind_result($sucursal);
    $resp->fetch();
    $resp->close();

    


    $sele = "SELECT COUNT(*) FROM productos_preventa WHERE id_usuario = ?";
    $stmt = $con->prepare($sele);
    $stmt->bind_param('i', $id_usuario);
    $stmt->execute();
    $stmt->bind_result($total_productos);
    $stmt->fetch();
    $stmt->close();

    if ($total_productos < 0) {
        responder(false, 'No hay productos en preventa', 'warning', [], true);
    }

    $sele_ = "SELECT * FROM productos_preventa WHERE id_usuario = ?";
    $stmt = $con->prepare($sele_);
    $stmt->bind_param('i', $id_usuario);
    $stmt->execute();
    $datos = $stmt->get_result();
    $stmt->close();

    while ($fila = $datos->fetch_assoc()) {
        $info_producto_individual[] = $fila;
    }

    $stock_ok = true;
    $error_llantas = '';
    $total=0;
    //Recorremos el arreglo de los productos preventa
    foreach ($info_producto_individual as $key => $value) {

        $id_llanta = $value['id_llanta'];
        $cantidad_post = $value['cantidad'];
        $unidad_producto = $value['tipo'];
        $total += floatval($value['importe']);
     
        if ($unidad_producto == 1) {
         
            $ID = $con->prepare("SELECT Stock FROM inventario WHERE id_Llanta = ? AND id_sucursal =?");
            $ID->bind_param('ss', $id_llanta, $id_sucursal);
            $ID->execute();
            $ID->bind_result($stockActual);
            $ID->fetch();
            $ID->close();

            if ($stockActual < $cantidad_post) {
                $error_llantas .= $value['descripcion'] .', ';
                $stock_ok = false;
            }
        }

    }
    
   if($stock_ok){
    $comentario = $_POST['comentario'];

    include '../helpers/verificar-hora-corte.php';

    $queryInsertar = "INSERT INTO apartados (id, sucursal, id_sucursal, id_usuario, id_cliente, pago_efectivo, pago_tarjeta, pago_transferencia, pago_cheque, pago_deposito, pago_sin_definir, primer_abono, restante, total, tipo, estatus, metodo_pago, hora, comentario, plazo, fecha_inicial, fecha_final) VALUES (null,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
    $resultado = $con->prepare($queryInsertar);
    $resultado->bind_param('siisdddddddddsssssiss', $sucursal, $id_sucursal, $id_usuario, $cliente , $pago_efectivo, $pago_tarjeta, $pago_transferencia, $pago_cheque, $pago_deposito, $pago_sin_definir, $adelanto, $restante, $total, $tipo, $estatus, $desc_metodos, $hora, $comentario, $plazo, $fecha_inicial, $fecha_final);
    $resultado->execute();
    $id_apartado = $resultado->insert_id;
    $resultado->close();

    foreach ($info_producto_individual as $key => $value) { 
          
          $validacion = is_numeric($key);
          if($validacion){
            
            $codigo = $value["codigo"];
            $descripcion = $value["descripcion"];
            $modelo = $value["modelo"];
            $cantidad = $value["cantidad"];
            $precio_unitario = $value["precio"];
            $importe = $value["importe"];
            

            $subcadena = substr($codigo, 0, 4);


            if($subcadena == "SERV"){

              $ID = $con->prepare("SELECT id FROM servicios WHERE codigo LIKE ?");
              $ID->bind_param('s', $codigo);
              $ID->execute();
              $ID->bind_result($id_Servicio);
              $ID->fetch();
              $ID->close();

            }else{
              $ID = $con->prepare("SELECT id_Llanta, Stock FROM inventario WHERE Codigo = ?");
              $ID->bind_param('s', $codigo);
              $ID->execute();
              $ID->bind_result($id_Llanta, $stockActual);
              $ID->fetch();
              $ID->close();

              $resultStock = $stockActual - $cantidad;

             $updateStockSendero = $con->prepare("UPDATE inventario SET Stock = ? WHERE Codigo = ?");
             $updateStockSendero->bind_param('is', $resultStock, $codigo);
             $updateStockSendero->execute();
             $updateStockSendero->close();
            }
 
             if($subcadena == "SERV"){
              $unidad = "servicio";
              $queryInsertar = "INSERT INTO detalle_apartado (id, id_apartado, id_llanta, modelo, cantidad, unidad, precio_Unitario, importe) VALUES (null,?,?,?,?,?,?,?)";
              $resultado = $con->prepare($queryInsertar);
              $resultado->bind_param('iisisdd',$id_apartado, $id_Servicio, $modelo, $cantidad, $unidad, $precio_unitario, $importe);
              $resultado->execute();
              $resultado->close();
             }else{
              $unidad = "pieza";
              $queryInsertar = "INSERT INTO detalle_apartado (id, id_apartado, id_llanta, modelo, cantidad, unidad, precio_unitario, importe) VALUES (null,?,?,?,?,?,?,?)";
              $resultado = $con->prepare($queryInsertar);
              $resultado->bind_param('iisisdd',$id_apartado, $id_Llanta, $modelo, $cantidad, $unidad, $precio_unitario, $importe);
              $resultado->execute();
              $resultado->close();
             }     

             $queryInsertar = "DELETE FROM productos_preventa WHERE id_usuario = ?";
                $resultado = $con->prepare($queryInsertar);
                $resultado->bind_param('i', $id_usuario);
                $resultado->execute();
                $resultado->close();
          }else{
            echo "";
          }
        }

        insertarUtilidadApartado($con, $id_apartado);
        //Insertar abono

        if($adelanto>0){
          $estado =1; 
          $queryInsertar = "INSERT INTO abonos_apartados (id, id_apartado, fecha, hora, abono, metodo_pago, pago_efectivo, pago_tarjeta, pago_transferencia, pago_cheque, pago_deposito, pago_sin_definir, usuario, estado, sucursal, id_sucursal, fecha_corte, hora_corte) VALUES (null,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
          $resultado = $con->prepare($queryInsertar);
          $resultado->bind_param('issdsddddddssssss', $id_apartado, $fecha_inicial, $hora, $adelanto, $desc_metodos, $pago_efectivo, $pago_tarjeta, $pago_transferencia, $pago_cheque, $pago_deposito, $pago_sin_definir, $vendedor_usuario, $estado, $sucursal, $id_sucursal, $fecha_corte, $hora_corte);
          $resultado->execute();
          $resultado->close();
          $id_abono = $con->insert_id;

          
          $utlidad_res = insertarUtilidadAbonoApartados($id_abono, $con);
        }else{
          $utlidad_res= 'Sin abono de apartado';
        }

        $response = array('estatus' => true, 'mensaje' => 'Aparado realizado correctamente', 'folio' => $id_apartado, 'utlidad_res' => $utlidad_res);
        echo json_encode($response);
   }else{
        $response = array('estatus' => false, 'mensaje' => 'El stock de la llantas: '. $error_llantas.' no es suficiente. Cantidad solicitada<b>'.$cantidad_post .'</b>  Stock actual:<b>'.$stockActual);
        echo json_encode($response);
   }
    
  
//    print_r($_POST);
}

?>