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

    $id_usuario = $_SESSION["id_usuario"];
    $rol = $_SESSION["rol"];

    $fecha_hoy = date("Y-m-d");    

    $estatusvencido = 4;
    $res =0.00;

    //Codifo para cancelar credito con ventas canceladas
     /*  $treaer = mysqli_query($con, "SELECT * FROM ventas WHERE estatus = 'Cancelada'");

      while ($fila = $treaer->fetch_assoc()){
          $id_vent = $fila["id"];

          $contar = "SELECT COUNT(*) FROM creditos WHERE id_venta = ?";
          $response = $con->prepare($contar);
          $response->bind_param('s', $id_vent);
          $response->execute();
          $response->bind_result($total_matches);
          $response->fetch();
          $response->close();

          if($total_matches > 0){

            $update = "UPDATE creditos SET estatus = 5 WHERE id_venta= ?";
            $result = $con->prepare($update);
            $result->bind_param('s', $id_vent);
            $result->execute();
            $result->close();

            $data[] = array("id"=>$id_vent, "OK"=>true);
          }else{
              $data[] = array("id"=>$id_vent, "OK"=>false);
          }

      } 
 */
      echo json_encode($data, JSON_UNESCAPED_UNICODE);

    $update = "UPDATE creditos SET estatus = ? WHERE estatus <> 5 AND pagado <> total AND restante <> ? AND fecha_final <= ?";
    $result = $con->prepare($update);
    $result->bind_param('sss', $estatusvencido, $res, $fecha_hoy);
    $result->execute();
    $result->close();
   

    ?>