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

        $tipo = $_POST["tipo"];
        $codigo = $_POST["codigo"];
        $sucursal_id = $_POST["sucursal_id"];
        $stock  = $_POST["stock"];
        $stock_actual  = $_POST["stock_actual"];
        $stock_total = $stock_actual + $stock;
        $id_usuario = $_SESSION["id_usuario"];

        if ($tipo == "aumentar") {
            # code...
        $stock_total = $stock_actual + $stock;
        $palabra_singular = "Se agregó ";
        $response_singular = "Agregaste ";
        $palabra_plural = "Se agregarón ";
        $response_plural = "Agregaste ";
        }else if ($tipo == "reducir"){
        $stock_total = $stock_actual - $stock;
        $palabra_singular = "Se retiró ";
        $response_singular = "Retiraste ";
        $palabra_plural = "Se retirarón ";
        $response_plural = "Retiraste ";
        }
        
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
                    $palabra = "". $palabra_singular . $llantas_agregadas . " llanta. Producto: " . $descripcion_llanta;
                    $response =$response_singular . $llantas_agregadas . " llanta. Producto: " . $descripcion_llanta;
                }else{
                    $palabra = "". $palabra_plural . $llantas_agregadas . " llantas. Producto: " . $descripcion_llanta;
                    $response =$response_plural . $llantas_agregadas . " llantas. Producto: " . $descripcion_llanta;
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
            $descripcion_movimiento = "Se actualizó el stock del inventario de " . $sucursal . ", " . $palabra . ".
            Stock anterior: " . $stock_actual_s . " - Stock actual: ". $stock_total;

         //Registramos el movimiento
            $tipo = 3;
            $insertar_movimi = "INSERT INTO movimientos(id, descripcion, mercancia, fecha, hora, usuario, tipo, sucursal)
            VALUES(null,?,?,?,?,?,?,?)";
            $resultado = $con->prepare($insertar_movimi);                     
            $resultado->bind_param('sssssss', $descripcion_movimiento, $descripcion_llanta, $fecha, $hora, $usuario, $tipo, $sucursal_id);
            $resultado->execute();
            $resultado->close();  

              //LAST ID
              $rs = mysqli_query($con, "SELECT MAX(id) AS id FROM movimientos");
              if ($rowss = mysqli_fetch_row($rs)) {
              $id_movimiento = trim($rowss[0]);
              }
         
            //Registrar detalle de edición
            $insertar = "INSERT INTO historial_detalle_cambio(id, 
            id_llanta, 
            id_ubicacion, 
            id_destino, 
            cantidad, 
            id_usuario,
            id_movimiento,
            stock_actual,
            stock_anterior) VALUES(null, ?,?,?,?,?,?,?,?)";
            $result = $con->prepare($insertar);
            $result->bind_param('ssssssss',$codigo, $sucursal_id, $sucursal_id, $stock, $id_usuario, $id_movimiento, $stock_total, $stock_actual_s);
            $result->execute();
            $result->close();
       


}else{
    print_r(0);
}


?>