<?php


session_start();
include '../conexion.php';
$con= $conectando->conexion(); 

if (!isset($_SESSION['id_usuario'])) {
    header("Location:../../login.php");
}


if(isset($_POST)){

    $id = $_POST["id"];
    $descripcion = $_POST["descripcion"];
    $modelo = $_POST["modelo"];
    $cantidad = $_POST["cantidad"];
    $precio = $_POST["precio"];
    $importe = $_POST["importe"];
    $tipo_cotizacion = $_POST["tipo_cotizacion"];

    $iduser = $_SESSION["id_usuario"];

    
    $comprobar = "SELECT COUNT(*) total FROM detalle_nueva_cotizacion WHERE id_usuario = $iduser AND tipo = $tipo_cotizacion";
    $result = $con->query($comprobar);
    if($result == false){

    }else{
        
    $match = "SELECT COUNT(*) totales FROM detalle_nueva_cotizacion WHERE codigo = ? AND id_usuario = ? AND tipo = ?";
    $stmt = $con->prepare($match);
    $stmt->bind_param('iii', $id, $iduser, $tipo_cotizacion);
    $stmt->execute();
    $stmt->bind_result($iguales);
    $stmt->fetch();
    $stmt->close();
    
    if ($iguales >= 1) {

                $obtenerCant = "SELECT cantidad, importe FROM detalle_nueva_cotizacion WHERE codigo = ? AND id_usuario = ? AND tipo = ?";
                $stmt = $con->prepare($obtenerCant);
                $stmt->bind_param('iii', $id, $iduser, $tipo_cotizacion);
                $stmt->execute();
                $stmt->bind_result($cantidadActual, $importeActual);
                $stmt->fetch();
                $stmt->close();

                $cantidad_llantas_totales = $cantidadActual + $cantidad;
                $importeTotal = doubleval($importe) + doubleval($importeActual);

                $updateQuanty = $con->prepare("UPDATE detalle_nueva_cotizacion SET cantidad = ?, importe = ? WHERE codigo = ? AND id_usuario = ? AND tipo = ?");
                $updateQuanty->bind_param('idiii', $cantidad_llantas_totales, $importeTotal, $id, $iduser, $tipo_cotizacion);
                $updateQuanty->execute();
                $updateQuanty->close();
  
                print_r(1);
             

    }else if($iguales == 0){

            $insertar = "INSERT INTO detalle_nueva_cotizacion (id, codigo, descripcion, modelo, cantidad, precio, importe, id_usuario, tipo) VALUES(null,?,?,?,?,?,?,?,?)";
            $resultado = $con->prepare($insertar);
            $resultado->bind_param('ssssssss', $id, $descripcion, $modelo, $cantidad, $precio ,$importe, $iduser, $tipo_cotizacion);
            $resultado->execute();
            $resultado->close();

            if ($resultado) {
                $fila = $result->fetch_assoc();
                //echo 'Número de total de registros: ' . $fila['total'];
                print_r(2);
           
            }else{
                echo "los datos no se insertaron";
            }
    }
    }
    

    //print_r("El id de sesion para el usuario " . $_SESSION["user"] . " es: " . $_SESSION["id_usuario"]);*/


}


?>