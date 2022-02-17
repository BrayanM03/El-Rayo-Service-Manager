<?php
session_start();
include '../conexion.php';
$con= $conectando->conexion(); 

if (!isset($_SESSION['id_usuario'])) {
    header("Location:../../login.php");
}

date_default_timezone_set("America/Matamoros");
  $hora = date("h:i a");
  $fecha = date("Y-m-d"); 
  $mes = date("m");
  $año = date("Y");

  if($_POST){
    $estatus ="Cancelada";
    $suc_pedro = "Pedro";

    $traer="SELECT COUNT(*) FROM sucursal";
    $res= $con->prepare($traer);
    $res->execute();
    $res->bind_result($tot);
    $res->fetch();
    $res->close();

    $ventas_totales = [];
    if($tot > 0){

        $query = "SELECT * FROM sucursal";
        $resp = mysqli_query($con, $query);

        while ($fila = $resp->fetch_assoc()) {
            $id = $fila["id"];
            $nombre_suc = $fila["nombre"];

            $ganancia_pedro_sql = $con->prepare("SELECT SUM(Total) FROM `ventas` WHERE id_sucursal = ? AND YEAR(Fecha) = ? AND estatus <> ?");
            $ganancia_pedro_sql->bind_param('sss', $id, $año, $estatus);
            $ganancia_pedro_sql->execute();
            $ganancia_pedro_sql->bind_result($ventas);
            $ganancia_pedro_sql->fetch();
            $ganancia_pedro_sql->close();

            if($ventas == null){
                $ventas =0;
            }else{
                $ventas = floatval($ventas);
            }
            $ventas_totales[] = array("sucursal" => $nombre_suc, "venta_total" => $ventas);

        }

    }


    

  

    
        

        if (isset($ventas_totales)) {
            echo json_encode($ventas_totales, JSON_UNESCAPED_UNICODE);
        }else{
            print_r("Sin datos");
        }

  }


  ?>