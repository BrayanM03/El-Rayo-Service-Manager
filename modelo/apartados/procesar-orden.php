<?php

session_start();
include '../conexion.php';
$con= $conectando->conexion(); 

if (!isset($_SESSION['id_usuario'])) {
    header("Location:../../login.php"); 
}


if(isset($_POST)){

  date_default_timezone_set("America/Matamoros");
  $hora = date("h:i a");

    //Variables para el historial venta
  if ($_POST['fecha'] == "") {
    $fecha = date("Y-m-d"); 
  }else{
    $fecha = $_POST['fecha'];
  }

  //Sacamos la informacion del apartado para insertarla en las ventas

$ID = $con->prepare("SELECT a.sucursal, a.id_sucursal, a.id_usuario, c.Nombre_Cliente, c.Telefono, a.primer_abono, a.restante, a.total,  a.tipo, a.estatus, a.metodo_pago, a.hora, a.comentario, a.plazo, a.fecha_inicial, a.fecha_final FROM apartados a INNER JOIN clientes c ON a.id_cliente = c.id WHERE a.id = ?");
$ID->bind_param('i', $id);
$ID->execute();
$ID->bind_result($sucursal, $id_sucursal, $vendedor_id, $cliente, $telefono_cliente, $primer_abono, $restante, $total, $tipo, $estatus, $metodo_pago, $hora, $comentario, $plazo, $fecha_inicio, $fecha_final);
$ID->fetch();
$ID->close();

  $querySuc = "SELECT nombre FROM sucursal WHERE id =?";
  $resp=$con->prepare($querySuc);
  $resp->bind_param('i', $id_sucursal);
  $resp->execute();
  $resp->bind_result($sucursal);
  $resp->fetch();
  $resp->close();
   
    $idUser =   $_SESSION['id_usuario']; //Preguntar si el usuario que hizo la venta es el mismo que esta logueado
    $estatus = "Pagado";
    $tipo = "Normal";
    
    $desc_metodos ='';
    $pago_efectivo=0;
    $pago_transferencia=0;
    $pago_tarjeta=0;
    $pago_cheque=0;
    $pago_sin_definir=0;
      foreach ($_POST["metodo_pago"] as $key => $value) {
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

    $queryInsertar = "INSERT INTO ventas (id, Fecha, sucursal, id_sucursal, id_Usuarios, id_Cliente, pago_efectivo, pago_tarjeta, pago_transferencia, pago_cheque, pago_sin_definir, Total, tipo, estatus, metodo_pago, hora, comentario) VALUES (null,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
    $resultado = $con->prepare($queryInsertar);
    $resultado->bind_param('ssiisddddddsssss', $fecha, $sucursal, $id_sucursal, $idUser, $cliente , $pago_efectivo, $pago_tarjeta, $pago_transferencia, $pago_cheque, $pago_sin_definir, $total, $tipo, $estatus, $desc_metodos, $hora, $comentario);
    $resultado->execute();
    $resultado->close();

    $sql = "SELECT id FROM ventas ORDER BY id DESC LIMIT 1";
    $resultado = mysqli_query($con, $sql);
    
    if(!$resultado){
      echo "no se pudo realizar la consulta";

    }else{
      $dato =  mysqli_fetch_array($resultado, MYSQLI_ASSOC);

        
        $id_Venta = $dato["id"];

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
              $queryInsertar = "INSERT INTO detalle_venta (id, id_Venta, id_Llanta, Modelo, Cantidad, Unidad, precio_Unitario, Importe) VALUES (null,?,?,?,?,?,?,?)";
              $resultado = $con->prepare($queryInsertar);
              $resultado->bind_param('iisisdd',$id_Venta, $id_Servicio, $modelo, $cantidad, $unidad, $precio_unitario, $importe);
              $resultado->execute();
              $resultado->close();
             }else{
              $unidad = "pieza";
              $queryInsertar = "INSERT INTO detalle_venta (id, id_Venta, id_Llanta, Modelo, Cantidad, Unidad, precio_Unitario, Importe) VALUES (null,?,?,?,?,?,?,?)";
              $resultado = $con->prepare($queryInsertar);
              $resultado->bind_param('iisisdd',$id_Venta, $id_Llanta, $modelo, $cantidad, $unidad, $precio_unitario, $importe);
              $resultado->execute();
              $resultado->close();
             }

              
            
            

            //Notify system
            $iduser = $_SESSION["id_usuario"];

            $vaciarTabla = "TRUNCATE TABLE productos_temp$iduser";

            $consulta = mysqli_query($con, $vaciarTabla);
           

            $consulta = mysqli_query($con, "SELECT * FROM usuarios WHERE rol LIKE 1 OR rol LIKE 2");

            if($_POST["tipo"] == "vt-normal"){
              $tipo = "vt-normal";
              $complemento_desc = " realizo una venta";
            }else if($_POST["tipo"] == "vt-credito"){
              $tipo = "vt-credito";
              $complemento_desc = " realizo una venta a credito";
            }

           // print_r($_POST["tipo"]);

            if ($consulta) {
                while($row = mysqli_fetch_assoc($consulta))
                {
                  $id_usuario_admi = $row['id'] . " " . $row["nombre"] ; // Sumar variable $total + resultado de la consulta 
                  $desc_notifi = $_SESSION['nombre'] . $complemento_desc;
                  $estatus = 1; 
                  $fecha = date("d-m-Y"); 
                  $hora = date("h:i a");
                  $refe = 0;  
                  $alertada = "NO";
                  
                  $queryInsertarNoti = "INSERT INTO registro_notificaciones (id, id_usuario, descripcion, estatus, fecha, hora, refe, alertada, tipo) VALUES (null,?,?,?,?,?,?,?,?)";
                        $resultados = $con->prepare($queryInsertarNoti);
                        $resultados->bind_param('isississ',$id_usuario_admi, $desc_notifi, $estatus, $fecha, $hora, $refe, $alertada, $tipo);
                        $resultados->execute();
                        $resultados->close();
                }
                
                
            }
           

          }else{
            echo "";
          }

        }


      print_r($id_Venta);
    /*usar esta consulta:  select * from ventas ORDER BY id DESC LIMIT 1 para escoger el ultimo id

          

   // echo json_encode($codigo_llanta, JSON_UNESCAPED_UNICODE);*/

    //Aqui //}

    
  }
  
}

?>