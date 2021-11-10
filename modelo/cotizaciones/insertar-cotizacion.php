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
  $fecha= date("Y-m-d");

    //Variables para el historial venta
  if ($_POST['comentario'] == "") {
    $comentario = "Sin comentario"; 
  }else{
    $comentario = $_POST['comentario'];
    
  }


    $sucursal = $_SESSION['sucursal'];
    $idUser =   $_SESSION['id_usuario'];
    $cliente =  $_POST["cliente"];
    $total =    $_POST["total"];
    

  $estatus = "OK";

    

   
    $datos = $_POST['data'];
   // $info_producto_individual = json_decode($datos);  
   $info_producto_individual = $datos;

   
    $queryInsertar = "INSERT INTO cotizaciones (id, Fecha, id_Sucursal, id_Usuarios, id_Cliente, Total, estatus, hora, comentario) VALUES (null,?,?,?,?,?,?,?,?)";
    $resultado = $con->prepare($queryInsertar);
    $resultado->bind_param('ssssssss', $fecha, $sucursal, $idUser, $cliente , $total, $estatus,$hora, $comentario);
    $resultado->execute();
    $resultado->close();

    $sql = "SELECT id FROM cotizaciones ORDER BY id DESC LIMIT 1";
    $resultado = mysqli_query($con, $sql);
    
    if(!$resultado){
      echo "no se pudo realizar la consulta";

    }else{
      $dato =  mysqli_fetch_array($resultado, MYSQLI_ASSOC);

        
      $id_Cotizacion = $dato["id"];
        
        

        foreach ($info_producto_individual as $key => $value) { 
          
          $validacion = is_numeric($key);

          
          if($validacion){
            
            
            $id_llanta = $value["codigo"];
            $cantidad = $value["cantidad"];
            $precio_unitario = $value["precio"];
            $importe = $value["importe"];
            $modelo= $value["modelo"];
            
 
              $unidad = "pieza";
              $queryInsertar = "INSERT INTO detalle_cotizacion (id, id_Llanta, id_Cotiza, Cantidad, Unidad, precio_Unitario, Importe) VALUES (null,?,?,?,?,?,?)";
              $resultado = $con->prepare($queryInsertar);
              $resultado->bind_param('ssssss',$id_llanta, $id_Cotizacion, $cantidad, $unidad, $precio_unitario, $importe);
              $resultado->execute();
              $resultado->close();
             

              
            
            
            //Notify system

            $vaciarTabla = "TRUNCATE TABLE cotizacion_temp$idUser";

            $consulta = mysqli_query($con, $vaciarTabla);
           

        
               /* 
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
                        $resultados->close(); */
                
                
                
            

          }else{
            echo "";
          }

        }


      print_r($id_Cotizacion);
    /*usar esta consulta:  select * from ventas ORDER BY id DESC LIMIT 1 para escoger el ultimo id

          

   // echo json_encode($codigo_llanta, JSON_UNESCAPED_UNICODE);*/

    //Aqui //}

    
  }
  
}





?>