<?php

session_start();
include '../conexion.php';
$con= $conectando->conexion(); 

require_once('../movimientos/mover_clientes.php');

if (!isset($_SESSION['id_usuario'])) {
    header("Location:../../login.php");
}

if($_POST){ 


    $nombre    = $_POST["nombre"];  
    $credito   = $_POST["credito"];   
    $telefono  = $_POST["telefono"];   
    $correo    = $_POST["correo"];   
    $rfc       = $_POST["rfc"];   
    $direccion = $_POST["direccion"];   
    $latitud   = $_POST["latitud"];  
    $longitud  = $_POST["longitud"];
    $asesor = $_POST["asesor"];
    $intento = $_POST["intento"];

    if($intento == 1){

        $comp = comprobar_existencia_cliente($nombre, $telefono, $correo, $rfc, $con);
    }else{
        $comp = false;
    }
  
    if($comp){
        $res = array("status"=> false, "msg"=>"Se encontraron coincidencias en el nombre del cliente", "data"=>$comp);
        echo json_encode($res, JSON_UNESCAPED_UNICODE);
    }else{

    $asesor_nombre = traerAsesor($asesor, $con);
    $cambios = "Se agregó nuevo cliente: $nombre - Asesor: $asesor_nombre";
    InsertarMovimiento("insercion", $cambios, $con);


    $insertar_cliente = "INSERT INTO clientes(id, Nombre_Cliente, Telefono, Direccion, Correo, Credito, RFC, Latitud, Longitud, id_asesor, credito_vencido) VALUES(null, ?,?,?,?,?,?,?,?,?,0)";
    $resultado = $con->prepare($insertar_cliente);
    $resultado->bind_param('sisssssss', $nombre, $telefono, $direccion, $correo, $credito, $rfc, $latitud, $longitud, $asesor);
    $resultado->execute();

    if ($resultado) {
         $res = array("status"=> true, "msg"=>"Cliente registrado correctamente", "data"=>$resultado);
        echo json_encode($res, JSON_UNESCAPED_UNICODE);
    }else {
         $res = array("status"=> false, "msg"=>"Hubo un error al insertar los datos", "data"=>null);
        echo json_encode($res, JSON_UNESCAPED_UNICODE);
    }

}
   
}

function comprobar_existencia_cliente($nombre, $telefono, $correo, $rfc, $con){
    $nombre= "%$nombre%";
    $consulta = "SELECT * FROM clientes WHERE Nombre_Cliente LIKE ?";
    $resultado = $con->prepare($consulta);
    $resultado->bind_param('s', $nombre);
    $resultado->execute();
    $resultados = $resultado->get_result();
    $concidenciasEncontradas = $resultados->fetch_all(MYSQLI_ASSOC);
    $numero_filas = count($concidenciasEncontradas);
    
    if ($numero_filas > 0) {
        return $concidenciasEncontradas;
    }else{
        return false;
      
    }
    
}


?>
