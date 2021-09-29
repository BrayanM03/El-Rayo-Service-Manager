<?php
session_start();
include '../conexion.php';
$con= $conectando->conexion(); 

if (!isset($_SESSION['id_usuario'])) {
    header("Location:../../login.php");
}


$comprobar = $con->prepare("SELECT * FROM `sucursal`");
              // $comprobar->bind_param('s', $id_sucursal);
               $comprobar->execute();
               $resultado = $comprobar->get_result();
              

               if($resultado->num_rows < 1){
                 echo "sin valores"; 
             }else{

                 while($fila = $resultado->fetch_assoc()){
                    $corte = $fila["corte"];
                    $id= $fila["id"];
                    $nombre = $fila["nombre"];
                    $data[] = array("id"=>$id, "corte"=> $corte, "nombre"=> $nombre);

                   
                   

                }           

             }

             $comprobar->close();
             echo json_encode($data, JSON_UNESCAPED_UNICODE); 

?>