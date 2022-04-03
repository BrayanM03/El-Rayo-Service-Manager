<?php
    
    
    include '../conexion.php';
    $con= $conectando->conexion(); 

    if (!$con) {
        echo "maaaaal";
    }

    if (isset($_POST)) {

       
        $traerid = "SELECT DISTINCT llantas.Proporcion FROM inventario INNER JOIN llantas 
                           ON inventario.id_Llanta = llantas.id ORDER BY llantas.Proporcion";  

        $resultado = mysqli_query($con, $traerid);
        $data = array();
        while($row = $resultado->fetch_assoc()){
            
            $ancho = $row["Proporcion"];
            array_push($data, $ancho);

        }   
        

        echo json_encode($data, JSON_UNESCAPED_UNICODE);

      
       
    
    }else{
        print_r(2);
    }
        

    
    
    ?>