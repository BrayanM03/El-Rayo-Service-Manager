<?php
 session_start();
include 'conexion.php';
$con= $conectando->conexion(); 

if (!$con) {
    echo "maaaaal";
}

if (!isset($_SESSION['id_usuario'])) {
    header("Location:../../login.php");
}
date_default_timezone_set("America/Matamoros");
if (isset($_POST)) {


        $codigo = $_POST["codigo"];

        $stock  = $_POST["stock"];

            //Traer stock  actual
            $traerstockactual = "SELECT Stock FROM inventario_mat1 WHERE id_Llanta = ?";
            $result = $con->prepare($traerstockactual);
            $result->bind_param('i',$codigo);
            $result->execute();
            $result->bind_result($stock_actual);
            $result->fetch();
            $result->close();

            
        
       
            //Actualizamos stock de la llanta
            $editar_llanta= $con->prepare("UPDATE inventario_mat1 SET Stock = ? WHERE id_Llanta = ?");
            $editar_llanta->bind_param('ii', $stock, $codigo);
            $editar_llanta->execute();
            $editar_llanta->close();

            //Traemos descripcion del producto
            $traerdatadenew = "SELECT Descripcion FROM llantas WHERE id = ?";
            $result = $con->prepare($traerdatadenew);
            $result->bind_param('i',$codigo);
            $result->execute();
            $result->bind_result($descripcion_llanta);
            $result->fetch();
            $result->close(); 

            if($stock > $stock_actual){
                $llantas_agregadas =  $stock - $stock_actual;
                if($llantas_agregadas == 1){
                    $palabra = "se agregó " . $llantas_agregadas . " llanta.";
                    $response ="Agregaste " . $llantas_agregadas . " llanta.";
                }else{
                    $palabra = "se agregarón " . $llantas_agregadas . " llantas.";
                    $response ="Agregaste " . $llantas_agregadas . " llantas.";
                }
               
                
                $data = array("llantas_dif" => $response);
                echo json_encode($data, JSON_UNESCAPED_UNICODE); 

            }else if($stock < $stock_actual){
                $llantas_retiradas =   $stock_actual - $stock;

                if($llantas_retiradas == 1){
                    $palabra = "se descontó " . $llantas_retiradas . " llanta.";
                    $response ="Descontaste " . $llantas_retiradas . " llanta.";
                }else{
                    $palabra = "se descontarón " . $llantas_retiradas . " llantas.";
                    $response ="Descontaste " . $llantas_retiradas . " llantas.";
                }
               
                $data = array("llantas_dif" => $response);
                echo json_encode($data, JSON_UNESCAPED_UNICODE); 
            }else if($stock == $stock_actual){
                if($stock == 1){
                    $palabrilla = "llanta.";
                }else{
                    $palabrilla = "llantas.";
                }
                $palabra = "pero las cantidades eran iguales, por lo tanto se quedó igual el stock con un total de " . $stock . " " . $palabrilla;
                 $response ="La cantidad que ingresaste es lo mismo que hay en stock, por lo tanto se queda igual.";
                $data= array("llantas_dif" => $response);
                echo json_encode($data, JSON_UNESCAPED_UNICODE); 
                
            }
                
         
            $sucursal = "Pedro Cardenas";  
            $fecha = date("Y-m-d");   
            $hora =date("h:i a");   
            $usuario = $_SESSION["nombre"] . " " . $_SESSION["apellidos"];
            $descripcion_movimiento = "Se actualizó el stock del inventario de " . $sucursal . ", " . $palabra;

         //Registramos el movimiento
            $insertar_movimi = "INSERT INTO movimientos(id, descripcion, mercancia, fecha, hora, usuario)
            VALUES(null,?,?,?,?,?)";
            $resultado = $con->prepare($insertar_movimi);                     
            $resultado->bind_param('sssss', $descripcion_movimiento, $descripcion_llanta, $fecha, $hora, $usuario);
            $resultado->execute();
            $resultado->close(); 
         
       



}else{
    print_r(0);
}


?>