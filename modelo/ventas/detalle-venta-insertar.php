<?php


session_start();
include '../conexion.php';
$con= $conectando->conexion(); 

if (!isset($_SESSION['id_usuario'])) {
    header("Location:../../login.php");
}


if(isset($_POST)){

    $codigo = $_POST["codigo"];
    $descripcion = $_POST["descripcion"];
    $modelo = $_POST["modelo"];
    $cantidad = $_POST["cantidad"];
    $precio = $_POST["precio"];
    $importe = $_POST["subtotal"];

    $iduser = $_SESSION["id_usuario"];
    $comprobar = "SELECT COUNT(*) total FROM productos_temp$iduser";
    $result = $con->query($comprobar);
    if($result == false){
        
        
        $crear = "CREATE TABLE productos_temp$iduser (
            id INT NOT NULL AUTO_INCREMENT,
            codigo VARCHAR(255) NOT NULL,
            descripcion VARCHAR(255),
            modelo VARCHAR(255),
            cantidad INT,
            precio DECIMAL(10,2),
            importe DECIMAL(10,2),
            PRIMARY KEY (id)
        );";
        $result2 = $con->query($crear);
        
        if ($result2) {
            

            $insertar = "INSERT INTO productos_temp$iduser(id, codigo, descripcion, modelo, cantidad, precio, importe) VALUES(null,?,?,?,?,?,?)";
            $resultado = $con->prepare($insertar);
            $resultado->bind_param('sssidd', $codigo, $descripcion, $modelo, $cantidad, $precio ,$importe);
            $resultado->execute();
            $resultado->close();

            if ($resultado) {
                echo "Se creo la tabla y se insertaron los datos";
            }else{
                echo "Se creo la tabla solamente los datos no se insertaron";
            }

        }else{
            echo "Se creo puro chile";
        }

       


    }else{
       

            $insertar = "INSERT INTO productos_temp$iduser(id, codigo, descripcion, modelo, cantidad, precio, importe) VALUES(null,?,?,?,?,?,?)";
            $resultado = $con->prepare($insertar);
            $resultado->bind_param('sssidd', $codigo, $descripcion, $modelo, $cantidad, $precio ,$importe);
            $resultado->execute();
            $resultado->close();

            if ($resultado) {
                $fila = $result->fetch_assoc();
                echo 'Número de total de registros: ' . $fila['total'];
           
            }else{
                echo "los datos no se insertaron";
            }
    }
    

    //print_r("El id de sesion para el usuario " . $_SESSION["user"] . " es: " . $_SESSION["id_usuario"]);*/


}


?>