<?php
 session_start();
include '../conexion.php';
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
        $sucursal_id = $_POST["sucursal_id"];
        $stock  = $_POST["stock"];
        $stock_actual  = $_POST["stock_actual"];
        $stock_total = $stock_actual + $stock;
        
           //Traer stock  actual
            $traerstockactual = "SELECT id, Stock FROM inventario WHERE id_Llanta = ? AND id_sucursal = ?";
            $result = $con->prepare($traerstockactual);
            $result->bind_param('ss',$codigo, $sucursal_id);
            $result->execute();
            $result->bind_result($this_tyre, $stock_actual_s);
            $result->fetch();
            $result->close();

            
        
            //Actualizamos stock de la llanta
            $editar_llanta= $con->prepare("UPDATE inventario SET Stock = ? WHERE id = ?");
            $editar_llanta->bind_param('ii', $stock_total, $this_tyre);
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

            
                $llantas_agregadas =  $stock;
                if($llantas_agregadas == 1){
                    $palabra = "se agregó " . $llantas_agregadas . " llanta.";
                    $response ="Agregaste " . $llantas_agregadas . " llanta.";
                }else{
                    $palabra = "se agregarón " . $llantas_agregadas . " llantas.";
                    $response ="Agregaste " . $llantas_agregadas . " llantas.";
                }
               
                
                $data = array("llantas_agregadas" => $response);
                echo json_encode($data, JSON_UNESCAPED_UNICODE); 
                
         
            $traerSuc = "SELECT nombre FROM sucursal WHERE id=?";
            $r = $con->prepare($traerSuc);
            $r->bind_param('i', $sucursal_id);
            $r->execute();
            $r->bind_result($sucursal);
            $r->fetch();
            $r->close();

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