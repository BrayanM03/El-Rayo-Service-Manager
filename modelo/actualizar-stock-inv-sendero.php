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
            $traerstockactual = "SELECT Stock FROM inventario_mat2 WHERE id_Llanta = ?";
            $result = $con->prepare($traerstockactual);
            $result->bind_param('i',$codigo);
            $result->execute();
            $result->bind_result($stock_actual);
            $result->fetch();
            $result->close();


           

            
        
       
            //Actualizamos stock de la llanta
            $editar_llanta= $con->prepare("UPDATE inventario_mat2 SET Stock = ? WHERE id_Llanta = ?");
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
                $palabra = "se agregarón " . $llantas_agregadas;
                $response ="Agregaste " . $llantas_agregadas . " llantas";
                $data = array("llantas_dif" => $response);
                echo json_encode($data, JSON_UNESCAPED_UNICODE); 

            }else if($stock < $stock_actual){
                $llantas_retiradas =   $stock_actual - $stock;
                
                $palabra = "se retirarón " . $llantas_retiradas;
                $response ="Retiraste " . $llantas_retiradas . " llantas";
                $data = array("llantas_dif" => $response);
                echo json_encode($data, JSON_UNESCAPED_UNICODE); 
            }else if($stock == $stock_actual){
                $palabra = "son iguales las cantidades, el inventario se queda igual, hay " . $stock;
                 $response ="La cantidad que ingresaste es lo mismo que hay en stock, por lo tanto se queda igual.";
                $data= array("llantas_dif" => $response);
                echo json_encode($data, JSON_UNESCAPED_UNICODE); 
                
            }
                
         
            $sucursal = "Sendero";  
            $fecha = date("Y-m-d");   
            $hora =date("h:i a");   
            $usuario = $_SESSION["nombre"] . " " . $_SESSION["apellidos"];
            $descripcion_movimiento = "Se actualizo el stock del inventario de " . $sucursal . ", de la llanta " . $descripcion_llanta . ", " . $palabra . " llantas";

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