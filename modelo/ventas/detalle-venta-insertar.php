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

            /*if ($resultado) {
                echo "Se creo la tabla y se insertaron los datos";
            }else{
                echo "Se creo la tabla solamente los datos no se insertaron";
            }*/

        }else{
            echo "Se creo puro chile";
        }

       


    }else{
        
    $match = "SELECT COUNT(*) totales FROM productos_temp$iduser WHERE codigo = ?";
    $stmt = $con->prepare($match);
    $stmt->bind_param('s', $codigo);
    $stmt->execute();
    $stmt->bind_result($iguales);
    $stmt->fetch();
    $stmt->close();
    
    if ($iguales >= 1) {

                $obtenerCant = "SELECT cantidad, importe FROM productos_temp$iduser WHERE codigo = ?";
                $stmt = $con->prepare($obtenerCant);
                $stmt->bind_param('s', $codigo);
                $stmt->execute();
                $stmt->bind_result($cantidadActual, $importeActual);
                $stmt->fetch();
                $stmt->close();

                $subcadena = substr($codigo, 0, 4);

                if ($subcadena == "SEND") {
                    $suc = 2;
                }else if($subcadena == "PEDC"){
                 $suc = 1;
                }

                $revisarStock = "SELECT Stock FROM inventario_mat$suc WHERE Codigo = ?";
                $res = $con->prepare($revisarStock);
                $res->bind_param('s', $codigo);
                $res->execute();
                $res->bind_result($StockActual);
                $res->fetch();
                $res->close();

                $total = $cantidad + $cantidadActual;
                $importeTotal = doubleval($importe) + doubleval($importeActual);

                if ($total > $StockActual) {
                    print_r(2);

                }else{
                    $updateQuanty = $con->prepare("UPDATE productos_temp$iduser SET cantidad = ?, importe = ? WHERE codigo = ?");
                    $updateQuanty->bind_param('ids', $total, $importeTotal, $codigo);
                    $updateQuanty->execute();
                    $updateQuanty->close();
      
                    print_r(1);
                }

             

    }else if($iguales == 0){

            $insertar = "INSERT INTO productos_temp$iduser(id, codigo, descripcion, modelo, cantidad, precio, importe) VALUES(null,?,?,?,?,?,?)";
            $resultado = $con->prepare($insertar);
            $resultado->bind_param('sssidd', $codigo, $descripcion, $modelo, $cantidad, $precio ,$importe);
            $resultado->execute();
            $resultado->close();

            if ($resultado) {
                $fila = $result->fetch_assoc();
                //echo 'Número de total de registros: ' . $fila['total'];
                print_r(1);
           
            }else{
                echo "los datos no se insertaron";
            }
    }
    }
    

    //print_r("El id de sesion para el usuario " . $_SESSION["user"] . " es: " . $_SESSION["id_usuario"]);*/


}


?>