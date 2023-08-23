<?php

session_start();
include '../conexion.php';
$con= $conectando->conexion(); 

if (!isset($_SESSION['id_usuario'])) {
    header("Location:../../login.php"); 
}


if(isset($_POST)) {

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

      $pago_efectivo=0;
      $pago_transferencia=0;
      $pago_tarjeta=0;
      $pago_cheque=0;
      $pago_sin_definir=0;
        foreach ($_POST['metodos_pago'] as $key => $value) {
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
    
          case 4:
          $pago_sin_definir = $value['monto'];
          break;     
          
          default:
            break;
        }
        $monto_pago = $value['monto'];
        $metodo_pago = $value['metodo'];
        $desc_metodos ='';
        if($key != count($_POST["metodos_pago"]) - 1) {
          // Este código se ejecutará para todos menos el último
          $desc_metodos .= $metodo_pago . ", ";
        }else{
          $desc_metodos .= $metodo_pago . ". ";
        }
      }
      
      $id_sucursal = $_POST["sucursal"];

      $vendedor_id = $_SESSION['id_usuario'];
      $ID = $con->prepare("SELECT nombre, apellidos FROM usuarios WHERE id = ?");
      $ID->bind_param('i', $vendedor_id);
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

    $idUser =   $_SESSION['id_usuario'];
    $cliente =  $_POST['cliente'];
    $total =    $_POST['total'];

    $restante = $_POST['restante'];
    $adelanto = floatval($total) - floatval($restante); 

    $datos = $_POST['data'];
    // $info_producto_individual = json_decode($datos);  
    $info_producto_individual = $datos;
    $comentario = $_POST['comentario'];

    $queryInsertar = "INSERT INTO apartados (id, sucursal, id_sucursal, id_usuario, id_cliente, pago_efectivo, pago_tarjeta, pago_transferencia, pago_cheque, pago_sin_definir, primer_abono, restante, total, tipo, estatus, metodo_pago, hora, comentario, plazo, fecha_inicial, fecha_final) VALUES (null,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
    $resultado = $con->prepare($queryInsertar);
    $resultado->bind_param('siisddddddddsssssiss', $sucursal, $id_sucursal, $idUser, $cliente , $pago_efectivo, $pago_tarjeta, $pago_transferencia, $pago_cheque, $pago_sin_definir, $adelanto, $restante, $total, $tipo, $estatus, $desc_metodos, $hora, $comentario, $plazo, $fecha_inicial, $fecha_final);
    $resultado->execute();
    
    $resultado->close();

    $sql = "SELECT id FROM apartados ORDER BY id DESC LIMIT 1";
    $resultado = mysqli_query($con, $sql);
    
    if(!$resultado){
      echo "no se pudo realizar la consulta";

    }else{
      $dato =  mysqli_fetch_array($resultado, MYSQLI_ASSOC);
        
        $id_apartado = $dato["id"];

        //Insertar abono
        $estado =1; 
        $queryInsertar = "INSERT INTO abonos_apartados (id, id_apartado, fecha, hora, abono, metodo_pago, pago_efectivo, pago_tarjeta, pago_transferencia, pago_cheque, pago_sin_definir, usuario, estado, sucursal, id_sucursal) VALUES (null,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
        $resultado = $con->prepare($queryInsertar);
        $resultado->bind_param('issdsdddddssss', $id_apartado, $fecha_inicial, $hora, $adelanto, $desc_metodos, $pago_efectivo, $pago_tarjeta, $pago_transferencia, $pago_cheque, $pago_sin_definir, $vendedor_usuario, $estado, $sucursal, $id_sucursal);
        $resultado->execute();
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

            //Notify system
            $iduser = $_SESSION["id_usuario"];

            $vaciarTabla = "TRUNCATE TABLE productos_temp$iduser";

            $consulta = mysqli_query($con, $vaciarTabla);
          }else{
            echo "";
          }
        }

      print_r($id_apartado);
    
  }
//    print_r($_POST);
}

?>