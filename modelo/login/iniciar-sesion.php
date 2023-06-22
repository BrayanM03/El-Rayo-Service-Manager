<?php
   include '../conexion.php';
   $con= $conectando->conexion(); 

if(isset($_POST)){

    $usuario = $_POST["usuario"];
    $contraseña = $_POST["contraseña"];
    

    $query_mostrar = $con->prepare("SELECT * FROM usuarios WHERE usuario =?");
    $query_mostrar->bind_param('s', $usuario);
    $query_mostrar->execute();
    $query_mostrar->store_result();    
    $rows= $query_mostrar->num_rows();

    if ($rows > 0 ) {
        $query_mostrar->bind_result($id, $nombre, $apellidos, $user, $password, $cumple, $rol, $numero, $direccion, $sucursal, $id_sucursal, $aperturado, $comision);
        $query_mostrar->fetch();
        $validar_pass = password_verify($contraseña, $password);
        if ($validar_pass) {

            session_start();
            
            $_SESSION['id_usuario'] = $id;
            $_SESSION['nombre'] = $nombre;
            $_SESSION['apellidos'] = $apellidos;
            $_SESSION['user'] = $user;
            $_SESSION['cumple'] = $cumple;
            $_SESSION['rol'] = $rol;
            $_SESSION['numero'] = $numero;
            $_SESSION['direccion'] = $direccion;
            $_SESSION['sucursal'] = $sucursal;
            $_SESSION['id_sucursal'] = $id_sucursal;
            $_SESSION['aperturado'] = $aperturado;

            print_r(1);
        }else{
            print_r(0);
        }
    }else{
        print_r(2);
    }
 

}else{

    print_r("no existe la variable data");

}


?>