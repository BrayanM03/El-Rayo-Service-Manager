<?php
    
    
    include '../conexion.php';
    $con= $conectando->conexion(); 

    if (!$con) {
        echo "maaaaal";
    }

    if (isset($_POST)) {


       $codigo = $_POST["codigo"];
       $sucursal_id = $_POST["sucursal_id"];


       $sqlContarLlantas= $con->prepare("SELECT * FROM inventario WHERE id_Llanta = ? AND id_sucursal =?");
       $sqlContarLlantas->bind_param('ii', $codigo, $sucursal_id);
       $sqlContarLlantas->execute();
       $resultado = $sqlContarLlantas->get_result();
       $fila = $resultado->fetch_assoc();
       $sqlContarLlantas->close();
    
            $id = $fila["id_Llanta"];
            $codigo = $fila["Codigo"];
            $sucursal = $fila["Sucursal"];
            $stock = $fila["Stock"];
            

        $count = "SELECT COUNT(*) FROM proveedores";
        $res = $con->prepare($count);
        $res->execute();
        $res->bind_result($no_proveedores);
        $res->fetch();
        $res->close();
        if($no_proveedores > 0){
        $sqlTraerProveedores= $con->prepare("SELECT * FROM proveedores");
        $sqlTraerProveedores->execute();
        $resultado = $sqlTraerProveedores->get_result();
        
        $proveedores = array(); // Inicializa el arreglo

        while ($row = $resultado->fetch_assoc()) {
            $proveedores[] = $row; // Agrega cada fila al arreglo
        }

        $sqlTraerProveedores->close();
        }else{
            $proveedores = [];
        }
       $data = array("id" => $codigo, "id_llanta" => $id, "Sucursal" => $sucursal, "stock" => $stock, 'proveedores' => $proveedores);

        echo json_encode($data, JSON_UNESCAPED_UNICODE);

      
       
    
    }else{
        print_r(2);
    }
        

    
    
    ?>