<?php
    
    include 'conexion.php';
    $con= $conectando->conexion(); 

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

        $sqlUpdatetoken="UPDATE token SET codigo = '$token'";

        $result = mysqli_query($con, $sqlUpdatetoken);
        
    
        print_r(1);
    } 


    if (isset($_POST["comprobar-token"])) {

        $token_in = $_POST["comprobar-token"];
        $new_token = $_POST["nuevo-token"];

        $sqlComprobartoken= $con->prepare("SELECT codigo FROM token WHERE codigo = ?");
        $sqlComprobartoken->bind_param('s', $token_in);
        $sqlComprobartoken->execute();
        $sqlComprobartoken->bind_result($resultado);
        $sqlComprobartoken->fetch();
        $sqlComprobartoken->close();

        if ($token_in == $resultado) {
           

            $sqlUpdatetoken="UPDATE token SET codigo = '$new_token'";
            $result = mysqli_query($con, $sqlUpdatetoken);
            print_r(3);

        }else{
            print_r(4);
        }


    }
    

    
    ?>