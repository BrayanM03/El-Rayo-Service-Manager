<?php


session_start();
include '../conexion.php';
$con= $conectando->conexion(); 

if (!isset($_SESSION['id_usuario'])) {
    header("Location:../../login.php");
}

$iduser = $_SESSION["id_usuario"];

if ($_SESSION["rol"] == 1) {
    
    if(isset($_POST)){
        $id_notify = $_POST["id"];
        $alertada = "SI";
         $actualizar_notificacion= $con->prepare("UPDATE registro_notificaciones SET alertada = ? WHERE id = ?");
         $actualizar_notificacion->bind_param('si', $alertada, $id_notify);
         $actualizar_notificacion->execute();
         $actualizar_notificacion->close();
        
         if($actualizar_notificacion){
             print_r(1);
         }else{
             print_r(0);
         }
        
        
        }

}



?>