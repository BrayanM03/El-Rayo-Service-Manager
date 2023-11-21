<?php
     
    include 'conexion.php';
    $con= $conectando->conexion(); 
   /*  ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL); */
    if (!$con) {
        echo "maaaaal";
    }
 
    if(isset($_POST["traer-token"])){

        $sqltoken="SELECT * FROM token";

        $result = mysqli_query($con, $sqltoken);
        while ($datas=mysqli_fetch_assoc($result)){
    
        $arrayInven = $datas;
        }
    
        echo json_encode($arrayInven, JSON_UNESCAPED_UNICODE);
        
    }


    ///A partir de aqui ponerlo en otro archivo para que el token no se muestre en la pantall de venta

    if(isset($_POST["token"])){

        $token = $_POST["token"];
        $tipo_token = $_POST["tipo_token"];
        if($tipo_token==1){
            $columna_token = 'codigo';
        }else if($tipo_token==2){
            $columna_token = 'codigo_administrativo';
        }

        $sqlUpdatetoken="UPDATE token SET $columna_token = ? WHERE id = 1";
        $stmt = $con->prepare($sqlUpdatetoken);
        $stmt->bind_param('s', $token);
        $stmt->execute();
        $stmt->close();

        $sqlComprobartoken= $con->prepare("SELECT codigo, codigo_administrativo FROM token");
        $sqlComprobartoken->execute();
        $sqlComprobartoken->bind_result($token_op_actual, $token_admin_actual);
        $sqlComprobartoken->fetch();
        $sqlComprobartoken->close();
        
        $res = array('estatus'=>true, 'token_admin'=>$token_admin_actual, 'token_op'=>$token_op_actual);
        echo json_encode($res);
    } 


    if (isset($_POST["comprobar-token"])) {

        $token_in = $_POST["comprobar-token"];
        $new_token = $_POST["nuevo-token"];
        $tipo_token = $_POST["tipo-token"];
        if($tipo_token==1){
            $columna_token = 'codigo';
        }else if($tipo_token==2){
            $columna_token = 'codigo_administrativo';
        }
        $sqlComprobartoken= $con->prepare("SELECT $columna_token FROM token WHERE $columna_token = ?");
        $sqlComprobartoken->bind_param('s', $token_in);
        $sqlComprobartoken->execute();
        $sqlComprobartoken->bind_result($resultado);
        $sqlComprobartoken->fetch();
        $sqlComprobartoken->close();

        /* print_r($columna_token . ' - ');
        print_r($token_in . ' - ');
        print_r($resultado);
        die(); */
        if ($token_in == $resultado) {
           
            $sqlUpdatetoken="UPDATE token SET $columna_token = '$new_token'";
            $result = mysqli_query($con, $sqlUpdatetoken);
            print_r(3);

        }else{
            print_r(4);
        }


    }
    

    
    ?>