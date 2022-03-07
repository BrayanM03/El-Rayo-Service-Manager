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

    $traer="SELECT COUNT(*) FROM sucursal";
    $res= $con->prepare($traer);
    $res->execute();
    $res->bind_result($tot);
    $res->fetch();
    $res->close();

    $cred_totales = [];
    if($tot > 0){

        $query = "SELECT * FROM sucursal";
        $resp = mysqli_query($con, $query);

        while ($fila = $resp->fetch_assoc()) {
            $id = $fila["id"];
            $nombre_suc = $fila["nombre"];


            $estatus =5;
            $ganancia_pedro_sql = $con->prepare("SELECT COUNT(*) FROM `creditos` INNER JOIN ventas ON creditos.id_venta = ventas.id WHERE creditos.estatus <> ? AND ventas.id_sucursal = ? AND YEAR(ventas.Fecha) =?");
            $ganancia_pedro_sql->bind_param('sss', $estatus, $id, $año);
            $ganancia_pedro_sql->execute();
            $ganancia_pedro_sql->bind_result($total_cred);
            $ganancia_pedro_sql->fetch();
            $ganancia_pedro_sql->close();


            if($total_cred == null){
                $total_cred =0;
            }else{
                $total_cred = floatval($total_cred);
            }

            $tarer_colores = $con->prepare("SELECT color_out, color_hover FROM `colores_sucursales` WHERE id_suc = ?");
            $tarer_colores->bind_param('i', $id);
            $tarer_colores->execute();
            $tarer_colores->bind_result($background, $hover);
            $tarer_colores->fetch();
            $tarer_colores->close();

            $cred_totales[] = array("id"=>$id,
                                      "sucursal" => $nombre_suc, 
                                      "total_cred" => $total_cred,
                                      "color_back"=>$background,
                                      "color_hover"=>$hover);

        }

    }


        

        if (isset($cred_totales)) {
            echo json_encode($cred_totales, JSON_UNESCAPED_UNICODE);
        }else{
            print_r("Sin datos");
        }

  }


  ?>