<?php

session_start();
include '../conexion.php';
$con= $conectando->conexion(); 

if (!isset($_SESSION['id_usuario'])) {
    header("Location:../../login.php");
}


if(isset($_POST)){

  date_default_timezone_set('UTC');

  date_default_timezone_set("America/Mexico_City");

    //Variables para el historial venta
  if ($_POST['fecha'] == "") {
    $fecha = date("Y-m-d"); 
  }else{
    $fecha = $_POST['fecha'];
  }
    $sucursal = $_SESSION['sucursal'];
    $idUser = $_SESSION['id_usuario'];
    $cliente = 1;
    
    $total = $_POST["total"];
    $estatus = "Activo";
    $unidad = "pieza";

   
    $datos = $_POST['data'];
    $info_producto_individual = json_decode($datos);  


   
    $queryInsertar = "INSERT INTO ventas (id, Fecha, id_Sucursal, id_Usuarios, id_Cliente, Total, estatus) VALUES (null,?,?,?,?,?,?)";
    $resultado = $con->prepare($queryInsertar);
    $resultado->bind_param('ssiids', $fecha, $sucursal, $idUser, $cliente ,$total, $estatus);
    $resultado->execute();
    $resultado->close();

    $sql = "SELECT id FROM ventas ORDER BY id DESC LIMIT 1";
    $resultado = mysqli_query($con, $sql);
    
    if(!$resultado){
      echo "no se pudo realizar la consulta";

    }else{
      while ($dato=mysqli_fetch_assoc($resultado)) {

        $arreglo["id"] = $dato;
        $id_Venta = $arreglo["id"];
        
        

        foreach ($info_producto_individual as $key => $value) {
          
          $validacion = is_numeric($key);

          if($validacion){
            
            $codigo = $value[0];
            $descripcion = $value[1];
            $modelo = $value[2];
            $cantidad = $value[3];
            $precio_unitario = $value[4];
            $importe = $value[5];

            $subcadena = substr($codigo, 0, 4);
            if ($subcadena == "SEND") {
               
              
               $ID = $con->prepare("SELECT id_Llanta, Stock FROM inventario_mat2 sendero INNER JOIN llantas ON sendero.id_Llanta = llantas.id WHERE Codigo LIKE ?");
               $ID->bind_param('s', $codigo);
               $ID->execute();
               $ID->bind_result($id_Llanta, $stockActual);
               $ID->fetch();
               $ID->close();

               $resultStock = $stockActual - $cantidad;

               


            }else if($subcadena == "PEDC"){
              $ID = $con->prepare("SELECT id_Llanta, Stock FROM inventario_mat1 pedro INNER JOIN llantas ON pedro.id_Llanta = llantas.id WHERE Codigo LIKE ?");
              $ID->bind_param('s', $codigo);
              $ID->execute();
              $ID->bind_result($id_Llanta, $stockActual);
              $ID->fetch();
              $ID->close();

              

             
            }

           

            foreach($id_Venta as $keys => $value2){

              $resultStock = $stockActual - $cantidad;

              $updateStockSendero = $con->prepare("UPDATE inventario_mat1 SET Stock = ? WHERE Codigo = ?");
              $updateStockSendero->bind_param('ii', $resultStock, $codigo);
              $updateStockSendero->execute();
              $updateStockSendero->close();
            
            $queryInsertar = "INSERT INTO detalle_venta (id, id_Venta, id_Llanta, Cantidad, Unidad, precio_Unitario, Importe) VALUES (null,?,?,?,?,?,?)";
            $resultado = $con->prepare($queryInsertar);
            $resultado->bind_param('iiisdd',$value2, $id_Llanta, $cantidad, $unidad, $precio_unitario, $importe);
            $resultado->execute();
            $resultado->close();

              
            }
          
          


          
           

          }else{
            echo "";
          }

        }

    /*usar esta consulta:  select * from ventas ORDER BY id DESC LIMIT 1 para escoger el ultimo id

          

   // echo json_encode($codigo_llanta, JSON_UNESCAPED_UNICODE);*/

    }

    
  }
  
}





?>