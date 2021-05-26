<?php
    session_start();
    include '../conexion.php';
    $con= $conectando->conexion(); 
    
date_default_timezone_set("America/Matamoros");

    if (!$con) {
        echo "Problemas con la conexion";
    }

    if (!isset($_SESSION['id_usuario'])) {
        header("Location:../login.php");
    }

    if (isset($_POST)) {

        $fecha_actual = date("d-m-Y");

        $validar_lapso = $con->prepare("SELECT COUNT(*) total FROM creditos WHERE fecha_final = ?");
        $validar_lapso->bind_param('s', $fecha_actual);
        $validar_lapso->execute();
        $validar_lapso->bind_result($total);
        $validar_lapso->fetch();
        $validar_lapso->close();

        if ($total == 0) {
            print_r($total);
            print_r(0);

        }else{

        
            $query="SELECT creditos.id, creditos.id_cliente, creditos.pagado, creditos.restante, creditos.total, creditos.estatus, creditos.fecha_inicio, creditos.fecha_final, creditos.plazo, clientes.Nombre_Cliente FROM creditos INNER JOIN clientes ON creditos.id_cliente = clientes.id WHERE fecha_final = $fecha_actual";

            $resultado = mysqli_query($con, $query);
    
        
                while($fila = $resultado->fetch_assoc()){
                    $id = $fila["id"];
                    $cliente = $fila["Nombre_Cliente"];
                    $pagado = $fila["pagado"];
                    $restante = $fila["restante"];
                    $total = $fila["total"];
                    $estatus = $fila["estatus"];
                    $fecha_inicio = $fila["fecha_inicio"];
                    $fecha_final = $fila["fecha_final"];
                    $plazo = $fila["plazo"];
                
                    $data["data"][] = array("id" => $id, "fecha_inicial"=>$fecha_inicio,"fecha_final"=>$fecha_final, "restante" => $restante,
                                    "pagado" => $pagado, "cliente"=>$cliente, "total"=>$total, "plazo"=>$plazo, "estatus"=>$estatus);
                
                    
            }
            
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        }


    }

    ?>