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
    $sucursal = $_SESSION['sucursal'];
    $idUser = $_SESSION['id_usuario'];
    $cliente = $_POST["cliente"];
    $total = $_POST["total"];
    $estatus = "Activo";
    $unidad = "pieza";

    switch ($_POST["metodo_pago"]) {
      case 0:
       $metodo_pago = "Efectivo";
        break;
      
      case 1:
      $metodo_pago = "Targeta";
      break;
      
      case 2:
      $metodo_pago = "Transferencia";
      break;

      case 3:
      $metodo_pago = "Cheque";
          break;
      
      default:
        $metodo_pago = "Sin valor";
        break;
    }

   
    $datos = $_POST['data'];
   // $info_producto_individual = json_decode($datos);  
   $info_producto_individual = $datos;

   
    $queryInsertar = "INSERT INTO ventas (id, Fecha, id_Sucursal, id_Usuarios, id_Cliente, Total, estatus, metodo_pago, hora) VALUES (null,?,?,?,?,?,?,?,?)";
    $resultado = $con->prepare($queryInsertar);
    $resultado->bind_param('ssiidsss', $fecha, $sucursal, $idUser, $cliente ,$total, $estatus, $metodo_pago, $hora);
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

             
            }
 
           

            

             

              
            
            $queryInsertar = "INSERT INTO detalle_venta (id, id_Venta, id_Llanta, Modelo, Cantidad, Unidad, precio_Unitario, Importe) VALUES (null,?,?,?,?,?,?,?)";
            $resultado = $con->prepare($queryInsertar);
            $resultado->bind_param('iisisdd',$id_Venta, $id_Llanta, $modelo, $cantidad, $unidad, $precio_unitario, $importe);
            $resultado->execute();
            $resultado->close();

            $iduser = $_SESSION["id_usuario"];

            $vaciarTabla = "TRUNCATE TABLE productos_temp$iduser";

            $consulta = mysqli_query($con, $vaciarTabla);
           

              
           
          
          


          
           

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