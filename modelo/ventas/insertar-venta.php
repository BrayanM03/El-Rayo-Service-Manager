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

  if ($_POST["sucursal"] ==0) {
    $sucursal = "Pedro";

  }else if($_POST["sucursal"] == 1){
    $sucursal = "Sendero";
  }else{
    $sucursal = $_SESSION['sucursal'];
  }
   
    $idUser =   $_SESSION['id_usuario'];
    $cliente =  $_POST["cliente"];
    $total =    $_POST["total"];
    

    if(isset($_POST["plazo"])){ 
      $estatus = "Abierta";
      $tipo = "Credito";
    }else{
      $estatus = "Pagado";
      $tipo = "Normal";
    }

    
   

    switch ($_POST["metodo_pago"]) {
      case 0:
       $metodo_pago = "Efectivo";
        break;
      
      case 1:
      $metodo_pago = "Tarjeta";
      break;
      
      case 2:
      $metodo_pago = "Transferencia";
      break;

      case 3:
      $metodo_pago = "Cheque";
          break;

      case 4:
      $metodo_pago = "Por definir";
      break;     
      
      default:
        $metodo_pago = "Sin valor";
        break;
    }

   
    $datos = $_POST['data'];
   // $info_producto_individual = json_decode($datos);  
   $info_producto_individual = $datos;

   
    $queryInsertar = "INSERT INTO ventas (id, Fecha, id_Sucursal, id_Usuarios, id_Cliente, Total, tipo, estatus, metodo_pago, hora) VALUES (null,?,?,?,?,?,?,?,?,?)";
    $resultado = $con->prepare($queryInsertar);
    $resultado->bind_param('ssiidssss', $fecha, $sucursal, $idUser, $cliente , $total, $tipo, $estatus, $metodo_pago, $hora);
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


            if ($subcadena == "SEND") {
               
              
               $ID = $con->prepare("SELECT id_Llanta, Stock FROM inventario_mat2 sendero INNER JOIN llantas ON sendero.id_Llanta = llantas.id WHERE Codigo LIKE ?");
               $ID->bind_param('s', $codigo);
               $ID->execute();
               $ID->bind_result($id_Llanta, $stockActual);
               $ID->fetch();
               $ID->close();

               $resultStock = $stockActual - $cantidad;

              $updateStockSendero = $con->prepare("UPDATE inventario_mat2 SET Stock = ? WHERE id_Llanta = ?");
              $updateStockSendero->bind_param('ii', $resultStock, $id_Llanta);
              $updateStockSendero->execute();
              $updateStockSendero->close();

               


            }else if($subcadena == "PEDC"){
              $ID = $con->prepare("SELECT id_Llanta, Stock FROM inventario_mat1 pedro INNER JOIN llantas ON pedro.id_Llanta = llantas.id WHERE Codigo LIKE ?");
              $ID->bind_param('s', $codigo);
              $ID->execute();
              $ID->bind_result($id_Llanta, $stockActual);
              $ID->fetch();
              $ID->close();

              $resultStock = $stockActual - $cantidad;

              $updateStockSendero = $con->prepare("UPDATE inventario_mat1 SET Stock = ? WHERE id_Llanta = ?");
              $updateStockSendero->bind_param('ii', $resultStock, $id_Llanta);
              $updateStockSendero->execute();
              $updateStockSendero->close();

             
            }else if($subcadena == "SERV"){

              $ID = $con->prepare("SELECT id FROM servicios WHERE codigo LIKE ?");
              $ID->bind_param('s', $codigo);
              $ID->execute();
              $ID->bind_result($id_Servicio);
              $ID->fetch();
              $ID->close();

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