<?php
    
    include 'conexion.php';
    $con= $conectando->conexion(); 

    if (!$con) {
        echo "Problemas con la conexion";
    }

    if (isset($_POST["searchTerm"])) {
      
        $term = $_POST["searchTerm"];
        $parametro = "%$term%";
        $total =0;
        $sqlContarLlantas= $con->prepare("SELECT COUNT(*) total FROM marcas WHERE Nombre LIKE ? 
                                                                             OR Imagen LIKE ?");

                                                                                 
       
       $sqlContarLlantas->bind_param('ss', $parametro, $parametro);
       $sqlContarLlantas->execute();
       $sqlContarLlantas->bind_result($total);
       $sqlContarLlantas->fetch();
       $sqlContarLlantas->close();
       

       if ($total > 0) { 
        
        $sqlTraerLlanta="SELECT * FROM marcas WHERE Nombre      LIKE '%$term%'    
                                                  OR Imagen       LIKE '%$term%'";
        
        $resultado = mysqli_query($con, $sqlTraerLlanta);
       
        while($fila= $resultado->fetch_assoc()){
            $id = $fila["id"];
            $nombre = $fila["Nombre"];
            $imagen = $fila["Imagen"];
            $data[] = array("id" => $id, "nombre"=>$nombre, "imagen" => $imagen);
        }

        echo json_encode($data, JSON_UNESCAPED_UNICODE);
       
    
    }else{ 
        
        $data[] = array("id" => 0, "nombre"=>"Sin resultados");
    }   
    

   
    

    }
    
    
    ?>