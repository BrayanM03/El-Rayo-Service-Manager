<?php

session_start();
include '../conexion.php';
$con= $conectando->conexion(); 

if (!isset($_SESSION['id_usuario'])) {
    header("Location:../../login.php"); 
}

include 'insertar_utilidad.php';

if(isset($_POST)){

  date_default_timezone_set("America/Matamoros");
  $hora = date("h:i a");

    //Variables para el historial venta
    $fecha = date("Y-m-d"); 
 

  $id_sucursal = $_POST["sucursal"];

  $querySuc = "SELECT nombre, hora_corte_normal, hora_corte_sabado FROM sucursal WHERE id =?";
  $resp=$con->prepare($querySuc);
  $resp->bind_param('i', $id_sucursal);
  $resp->execute();
  $resp->bind_result($sucursal, $hora_corte_normal, $hora_corte_sabado);
  $resp->fetch();
  $resp->close();
   
    $idUser =   $_SESSION['id_usuario'];
    $cliente =  $_POST["cliente"];
    $total =    $_POST["total"];
    
    /* print_r($_POST);
    die(); */
    if(isset($_POST["plazo"])){ 
      $estatus = "Abierta";
      $tipo = "Credito";
    }else{
      $estatus = "Pagado";
      $tipo = "Normal";
    } 
    $desc_metodos ='';
    $pago_efectivo=0;
    $pago_transferencia=0;
    $pago_tarjeta=0;
    $pago_cheque=0;
    $pago_deposito=0;
    $pago_sin_definir=0;
      foreach ($_POST["metodo_pago"] as $key => $value) {
      $metodo_id = isset($value['clave']) ? $value['clave']: $key;
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
  
        case 4:
        $pago_sin_definir = $value['monto'];
        break;
        
        case 5:
          $pago_deposito = $value['monto'];
        break;  
        
        default:
          break;
      }
      $monto_pago = $value['monto'];
      $metodo_pago = $value['metodo'];
      if($key != count($_POST["metodo_pago"]) - 1) {
        // Este código se ejecutará para todos menos el último
        $desc_metodos .= $metodo_pago . ", ";
      }else{
        $desc_metodos .= $metodo_pago . ". ";
      }
    }


    $datos = $_POST['data'];
   // $info_producto_individual = json_decode($datos);  
   $info_producto_individual = $datos;
   $comentario = $_POST["comentario"];

   $stock_ok = true;
   $error_llantas='';
   foreach ($info_producto_individual as $key => $value) { 
   
    $codigo = $value["codigo"];
    $subcadena = substr($codigo, 0, 4);
    $cantidad_post = $value['cantidad'];

        if($subcadena == "SERV") {

        }else{
            $ID = $con->prepare("SELECT id_Llanta, Stock FROM inventario WHERE Codigo = ?");
            $ID->bind_param('s', $codigo);
            $ID->execute();
            $ID->bind_result($id_Llanta, $stockActual);
            $ID->fetch();
            $ID->close();
    
             if($stockActual < $cantidad_post){
                $error_llantas .= $value['descripcion'] .', ';
                $stock_ok = false;
             }
          }
        
    
   }
   
if($stock_ok) {
    include '../helpers/verificar-hora-corte.php';


    $queryInsertar = "INSERT INTO ventas (id, Fecha, sucursal, id_sucursal, id_Usuarios, id_Cliente, pago_efectivo, pago_tarjeta, pago_transferencia, pago_cheque, pago_deposito, pago_sin_definir, Total, tipo, estatus, metodo_pago, hora, comentario, fecha_corte, hora_corte) VALUES (null,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
    $resultado = $con->prepare($queryInsertar);
    $resultado->bind_param('ssiisdddddddsssssss', $fecha, $sucursal, $id_sucursal, $idUser, $cliente, $pago_efectivo, $pago_tarjeta, $pago_transferencia, $pago_cheque, $pago_deposito, $pago_sin_definir, $total, $tipo, $estatus, $desc_metodos, $hora, $comentario, $fecha_corte, $hora_corte);
    $resultado->execute();
    $resultado->close();

    $sql = "SELECT id FROM ventas ORDER BY id DESC LIMIT 1";
    $resultado = mysqli_query($con, $sql);

    if(!$resultado) {
        echo "no se pudo realizar la consulta";

    } else {
        $dato =  mysqli_fetch_array($resultado, MYSQLI_ASSOC);


        $id_Venta = $dato["id"];

        foreach ($info_producto_individual as $key => $value) {

            $validacion = is_numeric($key);


            if($validacion) {
                $codigo = $value["codigo"];
                $descripcion = $value["descripcion"];
                $modelo = $value["modelo"];
                $cantidad = $value["cantidad"];
                $precio_unitario = $value["precio"];
                $importe = $value["importe"];


                $subcadena = substr($codigo, 0, 4);


                if($subcadena == "SERV") {

                    $ID = $con->prepare("SELECT id FROM servicios WHERE codigo LIKE ?");
                    $ID->bind_param('s', $codigo);
                    $ID->execute();
                    $ID->bind_result($id_Servicio);
                    $ID->fetch();
                    $ID->close();

                } else {
                    $ID = $con->prepare("SELECT id_Llanta, Stock FROM inventario WHERE Codigo = ?");
                    $ID->bind_param('s', $codigo);
                    $ID->execute();
                    $ID->bind_result($id_Llanta, $stockActual);
                    $ID->fetch();
                    $ID->close();

                    if($stockActual <= 0) {

                    } else {
                        $resultStock = $stockActual - $cantidad;

                        $updateStockSendero = $con->prepare("UPDATE inventario SET Stock = ? WHERE Codigo = ?");
                        $updateStockSendero->bind_param('is', $resultStock, $codigo);
                        $updateStockSendero->execute();
                        $updateStockSendero->close();
                    }

                }

                if($subcadena == "SERV") {
                    $unidad = "servicio";
                    $queryInsertar = "INSERT INTO detalle_venta (id, id_Venta, id_Llanta, Modelo, Cantidad, Unidad, precio_Unitario, Importe) VALUES (null,?,?,?,?,?,?,?)";
                    $resultado = $con->prepare($queryInsertar);
                    $resultado->bind_param('iisisdd', $id_Venta, $id_Servicio, $modelo, $cantidad, $unidad, $precio_unitario, $importe);
                    $resultado->execute();
                    $resultado->close();
                } else {
                    $unidad = "pieza";
                    $queryInsertar = "INSERT INTO detalle_venta (id, id_Venta, id_Llanta, Modelo, Cantidad, Unidad, precio_Unitario, Importe) VALUES (null,?,?,?,?,?,?,?)";
                    $resultado = $con->prepare($queryInsertar);
                    $resultado->bind_param('iisisdd', $id_Venta, $id_Llanta, $modelo, $cantidad, $unidad, $precio_unitario, $importe);
                    $resultado->execute();
                    $resultado->close();
                }


                //insertar utilidad
                $utlidad_res = insertarUtilidad($con, $id_Venta);


                //Notify system
                $iduser = $_SESSION["id_usuario"];

                $vaciarTabla = "TRUNCATE TABLE productos_temp$iduser";

                $consulta = mysqli_query($con, $vaciarTabla);


                $consulta = mysqli_query($con, "SELECT * FROM usuarios WHERE rol LIKE 1 OR rol LIKE 2");

                if($_POST["tipo"] == "vt-normal") {
                    $tipo = "vt-normal";
                    $complemento_desc = " realizo una venta";
                } elseif($_POST["tipo"] == "vt-credito") {
                    $tipo = "vt-credito";
                    $complemento_desc = " realizo una venta a credito";
                }

                // print_r($_POST["tipo"]);

               


            } else {
                echo "";
            }

        }

        $response = array('estatus' =>true, 'mensaje' =>'Venta realizada correctamente', 'folio' =>$id_Venta, 'utlidad_res'=>$utlidad_res);
        echo json_encode($response);
    }
}else{
        $response = array('estatus' =>false, 'mensaje' =>'El stock de la llantas: '. $error_llantas.' no es suficiente. Cantidad solicitada<b>'.$cantidad_post .'</b>  Stock actual:<b>'.$stockActual);
        echo json_encode($response);
}
  
}





?>