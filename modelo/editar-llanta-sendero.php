<?php
    
    
    include 'conexion.php';
    $con= $conectando->conexion(); 

    if (!$con) {
        echo "maaaaal";
    }

    if (isset($_POST)) {


       $codigo = $_POST["codigo"];


       $sqlContarLlantas= $con->prepare("SELECT * FROM inventario_mat2 WHERE id_Llanta = ?");
       $sqlContarLlantas->bind_param('i', $codigo);
       $sqlContarLlantas->execute();
       $resultado = $sqlContarLlantas->get_result();
       $fila = $resultado->fetch_assoc();
       $sqlContarLlantas->close();
    
            $id = $fila["id_Llanta"];
            $codigo = $fila["Codigo"];
            $sucursal = $fila["Sucursal"];
            $stock = $fila["Stock"];
            

            $data = array("id" => $codigo, "id_llanta" => $id, "Sucursal" => $sucursal, "stock" => $stock);
        
            
               
            
          //echo $fila["Descripcion"];  
             //print_r(" la segunda iteracion de la llanta ID " . $id . " tiene de stock ". $TotalStock);
        

        echo json_encode($data, JSON_UNESCAPED_UNICODE);

      
       
    
    }else{
        print_r(2);
    }
        

    
    
    ?>