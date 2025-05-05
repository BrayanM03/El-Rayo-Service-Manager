<?php
   include '../conexion.php';
   include '../helpers/response_helper.php';
   $con= $conectando->conexion(); 
   session_start();
   date_default_timezone_set("America/Matamoros");
if($_SERVER['REQUEST_METHOD'] === 'POST'){  

     // Verificar si el token CSRF enviado está presente y coincide con el almacenado en la sesión
     if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        responder(false, 'Error: Token CSRF inválido', 'danger', [], true);
    }


    $usuario = $_POST['usuario'];
    $contraseña = $_POST['contraseña'];

    if(trim($usuario) == '' || trim($contraseña) == ''){
        responder(false, 'Ingrese una contraseña o un usuario', 'warning', [], true);
    }
    

    $query_mostrar = $con->prepare("SELECT * FROM usuarios WHERE usuario =?");
    $query_mostrar->bind_param('s', $usuario);
    $query_mostrar->execute();
    $query_mostrar->store_result();    
    $rows= $query_mostrar->num_rows();

    if ($rows > 0 ) {
        $query_mostrar->bind_result($id, $nombre, $apellidos, $user, $password, $cumple, 
        $rol, $numero, $direccion, $sucursal, $id_sucursal, $aperturado, $comision, $comision_credito, $id_departamento,
    $intentos_fallidos, $ultima_falla, $bloqueado_hasta, $estatus);
        $query_mostrar->fetch();

        // Verificar si el usuario está temporalmente bloqueado
        if ($bloqueado_hasta != null && strtotime($bloqueado_hasta) > time()) {
            responder(false,  "Tu cuenta está bloqueada hasta " . $bloqueado_hasta, 'danger', [], true);
        }

        if (password_verify($contraseña, $password)) {
             // Restablecer los intentos fallidos si el inicio de sesión es correcto
             $query_reset_intentos = $con->prepare("UPDATE usuarios SET intentos_fallidos = 0, bloqueado_hasta = NULL WHERE id = ?");
             $query_reset_intentos->bind_param('i', $id);
             $query_reset_intentos->execute();

            // Iniciar la sesión del usuario
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
            $_SESSION['id_departamento'] = $id_departamento;

            responder(true,  'Inicio de sesión exitoso', 'success',[], true);
        }else{
            // Incrementar el número de intentos fallidos
            $intentos_fallidos++;
            $tiempo_bloqueo = null;

             

            if ($intentos_fallidos >= 3) {  
                // Máximo de 3 intentos fallidos permitidos
                // Bloquear el usuario por 5 minutos
                $tiempo_bloqueo = date("Y-m-d H:i:s", strtotime("+5 minutes"));
                responder(false,  "Demasiados intentos fallidos. Tu cuenta está bloqueada por 5 minutos.", 'danger',[], false);
            } else {
                responder(false,  "Contraseña incorrecta. Intento fallido $intentos_fallidos de 3.",'warning', [], false);
            }
            // Actualizar los intentos fallidos y el tiempo de bloqueo en la base de datos
            $query_update_intentos = $con->prepare("UPDATE usuarios SET intentos_fallidos = ?, bloqueado_hasta = ? WHERE id = ?");
            $query_update_intentos->bind_param('isi', $intentos_fallidos, $tiempo_bloqueo, $id);
            $query_update_intentos->execute();
                }
    }else{
        responder(false, 'El usuario no existe', 'danger',[], true);
    }
}else{
    responder(false, 'No existe un metodo POST', 'danger',[], true);

}


?>