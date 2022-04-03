<?php
    
    
    include '../conexion.php';
    $con= $conectando->conexion(); 

    if (!$con) {
        echo "maaaaal";
    }

    if (isset($_POST)) {


        $sucursal_id = $_POST["sucursal_id"];
        $parametro = $_POST["parametro"];
        $params = "llantas.".$parametro;
       
       
        $traerid = "SELECT DISTINCT llantas.Ancho FROM inventario INNER JOIN llantas 
                           ON inventario.id_Llanta = llantas.id WHERE inventario.id_sucursal = '$sucursal_id' ORDER BY llantas.Ancho";  

        $resultado = mysqli_query($con, $traerid);
        $data = array();
        while($row = $resultado->fetch_assoc()){
          
           /*  $id_llanta = $row["id_Llanta"]; */
            $ancho = $row["Ancho"];

            array_push($data, $ancho);
         /* 
            $proporcion = $row["Proporcion"];
            $diametro = $row["Diametro"];  */
            
            //$data[] = array($ancho, /* "proporcion"=>$proporcion, "diametro"=>$diametro */);

        }   
        

        echo json_encode($data, JSON_UNESCAPED_UNICODE);

      
       
    
    }else{
        print_r(2);
    }
        

    
    
    ?>