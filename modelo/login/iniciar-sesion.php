<?php
   include '../conexion.php';
   $con= $conectando->conexion(); 

if(isset($_POST["user"])){

    $usuario = $_POST["user"];
    $contraseña = $_POST["pass"];


    $query_mostrar = $con->prepare("SELECT * FROM usuarios WHERE usuario = ?, contraseña = ?");

//-----------------------------------------------------------------------------------------------------//
//-----------------------------------------------------------------------------------------------------//

    $query_mostrar->bind_param('ss', $usuario, $contraseña);
    $query_mostrar->execute();
    //$query_mostrar->bind_result($total);
    $query_mostrar->fetch();
    $total = $query_mostrar->num_rows();
    $query_mostrar->close();

    if($total == 0){
        print_r(0);
    }else if($total >= 1){
        print_r(1);
    }

}


?>