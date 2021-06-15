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
    //$modelo = $_POST["modelo"];
    $cantidad = $_POST["cantidad"];
    $precio = $_POST["precio"];
    $importe = $_POST["importe"];

    $iduser = $_SESSION["id_usuario"];

    
    $comprobar = "SELECT COUNT(*) total FROM cotizacion_temp$iduser";
    $result = $con->query($comprobar);
    if($result == false){
        
        
        
        $crear = "CREATE TABLE cotizacion_temp$iduser (
            id INT NOT NULL AUTO_INCREMENT,
            codigo VARCHAR(255) NOT NULL,
            descripcion VARCHAR(255),
            cantidad INT,
            precio DECIMAL(10,2),
            importe DECIMAL(10,2),
            PRIMARY KEY (id)
        );";
        $result2 = $con->query($crear);
        
        if ($result2) {
            

            $insertar = "INSERT INTO cotizacion_temp$iduser(id, codigo, descripcion, cantidad, precio, importe) VALUES(null,?,?,?,?,?)";
            $resultado = $con->prepare($insertar);
            $resultado->bind_param('isidd', $id, $descripcion, $cantidad, $precio ,$importe);
            $resultado->execute();
            $resultado->close();

            /*if ($resultado) {
                echo "Se creo la tabla y se insertaron los datos";
            }else{
                echo "Se creo la tabla solamente los datos no se insertaron";
            }*/

        }else{
            echo "Se creo puro chile";
        }

       


    }else{
        
    $match = "SELECT COUNT(*) totales FROM cotizacion_temp$iduser WHERE codigo = ?";
    $stmt = $con->prepare($match);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->bind_result($iguales);
    $stmt->fetch();
    $stmt->close();
    
    if ($iguales >= 1) {

                $obtenerCant = "SELECT cantidad, importe FROM cotizacion_temp$iduser WHERE codigo = ?";
                $stmt = $con->prepare($obtenerCant);
                $stmt->bind_param('i', $id);
                $stmt->execute();
                $stmt->bind_result($cantidadActual, $importeActual);
                $stmt->fetch();
                $stmt->close();

                $cantidad_llantas_totales = $cantidadActual + $cantidad;
                $importeTotal = doubleval($importe) + doubleval($importeActual);

                $updateQuanty = $con->prepare("UPDATE cotizacion_temp$iduser SET cantidad = ?, importe = ? WHERE codigo = ?");
                $updateQuanty->bind_param('idi', $cantidad_llantas_totales, $importeTotal, $id);
                $updateQuanty->execute();
                $updateQuanty->close();
  
                print_r(1);
             

    }else if($iguales == 0){

            $insertar = "INSERT INTO cotizacion_temp$iduser(id, codigo, descripcion, cantidad, precio, importe) VALUES(null,?,?,?,?,?)";
            $resultado = $con->prepare($insertar);
            $resultado->bind_param('isidd', $id, $descripcion, $cantidad, $precio ,$importe);
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