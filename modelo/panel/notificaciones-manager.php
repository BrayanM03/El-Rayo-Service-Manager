<?php
session_start();
include '../conexion.php';
$con= $conectando->conexion(); 
date_default_timezone_set("America/Matamoros");

if (!isset($_SESSION['id_usuario'])) {
    header("Location:../../login.php");
}

if (!$con) {
    echo "maaaaal";
}

if($_SESSION["rol"] !==1 ){

    if(isset($_POST)){

        $id_user = $_SESSION["id_usuario"];
    
        $comprobar_notificaciones = $con->prepare("SELECT COUNT(*) total FROM registro_notificaciones WHERE id_usuario LIKE ?");
        $comprobar_notificaciones->bind_param('i', $id_user);
        $comprobar_notificaciones->execute();
        $comprobar_notificaciones->bind_result($total);
        $comprobar_notificaciones->fetch();
        $comprobar_notificaciones->close();
    
        if ($total > 0) {
            
            
            $query="SELECT * FROM registro_notificaciones WHERE id_usuario LIKE '$id_user'";
        
            $resultado = mysqli_query($con, $query);
    
        
                while($fila = $resultado->fetch_assoc()){
    
                    $id = $fila["id"];
                    $id_usuario = $fila["id_usuario"];
                    $descripcion= $fila["descripcion"];
                    $estatus = $fila["estatus"];
                    $fecha = $fila["fecha"];
                    $hora = $fila["hora"];
                    $refe = $fila["refe"];
                
                   
                    
                    $data[] = array("id" => $id,"id_usuario" => $id_usuario, "descripcion"=>$descripcion, "state"=>$estatus,"fecha"=>$fecha,
                    "hora" => $hora, "refe"=>$refe);
    
                        }
    
                        if(isset($data)){
                            echo json_encode($data, JSON_UNESCAPED_UNICODE); 
                        }
    
                   
                    }else{
                        print_r("No se econtro nada");
                    }             
                
                    
            }
}



?>