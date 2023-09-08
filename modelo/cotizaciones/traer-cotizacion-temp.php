<?php


session_start();
include '../conexion.php';
$con= $conectando->conexion(); 

if (!isset($_SESSION['id_usuario'])) {
    header("Location:../../login.php");
}


if(isset($_POST)){

    $iduser = $_SESSION["id_usuario"];
    $tipo_cotizacion = $_POST["tipo_cotizacion"];
    
    $consultar = "SELECT * FROM detalle_nueva_cotizacion WHERE id_usuario = $iduser AND tipo = $tipo_cotizacion";
    $result = $con->query($consultar);

    if($result){
        while($fila= $result->fetch_assoc()){
        
            $id           =            $fila['id'];
            $codigo       =        $fila['codigo'];
            $descripcion  =   $fila['descripcion'];
            $modelo       =        $fila['modelo'];
            $cantidad     =      $fila['cantidad'];
            $precio       =        $fila['precio'];
            $importe      =       $fila['importe'];
            $id_usuario   =    $fila['id_usuario'];
            $tipo         =          $fila['tipo'];
    
    
            $data["data"][] = array('id' => $id, 
                            'codigo' => $codigo,
                            'descripcion'=>$descripcion, 
                            'modelo' => $modelo, 
                            'cantidad' => $cantidad,
                            'precio'=>$precio, 
                            'importe'=>$importe,
                            'id_usuario' => $id_usuario,
                            'tipo' => $tipo);
        }

        if (isset($data)) {
            echo json_encode($data, JSON_UNESCAPED_UNICODE);
        }else{
            print_r('Sin datos');
        }
    
        
    }else{
        print_r("");
    }

   


}


?>