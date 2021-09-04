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
  $semana = date("W");
  $hoy = date("w") - 1;

  if($_POST){

    $tipo = "Normal";
    $estatus ="Cancelada";
    $sucursalP = "Pedro";
    $sucursalS = "Sendero";
    $unidad = "pieza";

               $traer_id = $con->prepare("SELECT id FROM `ventas` WHERE WEEK(Fecha) = ? AND YEAR(Fecha) = ? AND tipo = ? AND estatus <> ? AND WEEKDAY(Fecha) =? AND id_Sucursal =?");
               $traer_id->bind_param('ssssss', $semana, $año, $tipo, $estatus, $hoy, $sucursalP);
               $traer_id->execute();
               $resultado = $traer_id->get_result();
                $costo_acumulado = 0;
                if($resultado->num_rows < 1){
                    //echo "sin valores"; 
                }else{
                    while($fila = $resultado->fetch_assoc()){
                    $id_venta = $fila["id"];
                   

                    $traer_idLlanta = $con->prepare("SELECT id_Llanta, Cantidad FROM `detalle_venta` WHERE id_Venta = ? AND Unidad = ?");
                    $traer_idLlanta->bind_param('ss', $id_venta, $unidad);
                    $traer_idLlanta->execute();
                    $array_id_llanta = $traer_idLlanta->get_result();

                    if($array_id_llanta->num_rows < 1){
                       // echo "sin valores"; 
                    }else{
                        while($fila2 = $array_id_llanta->fetch_assoc()){

                        $id_llanta = $fila2["id_Llanta"];
                        $cantidad = $fila2["Cantidad"];
                        
                        $traer_Costo = $con->prepare("SELECT precio_Inicial FROM `llantas` WHERE id = ?");
                        $traer_Costo->bind_param('s', $id_llanta);
                        $traer_Costo->execute();
                        $array_costos = $traer_Costo->get_result();
                        if($array_costos->num_rows < 1){
                            //echo "sin valores"; 
                        }else{
                            while($fila3 = $array_costos->fetch_assoc()){
                                $costo_unitario = $fila3["precio_Inicial"];
                                $costo_total = $costo_unitario * $cantidad;
                                $costo_acumulado = $costo_acumulado + $costo_total;
                                
            
                        }

                        $traer_Costo->close();
                        
                    }
                    };
                    $traer_idLlanta->close();
                    
                }
                }
              
               $traer_id->close();
               
               
            }
             


               $ganancia_domingo_sql = $con->prepare("SELECT SUM(Total) FROM `ventas` WHERE WEEK(Fecha) = ? AND YEAR(Fecha) = ? AND estatus <> ? AND WEEKDAY(Fecha) =? AND id_Sucursal =?");
               $ganancia_domingo_sql->bind_param('sssss', $semana, $año, $estatus, $hoy, $sucursalP);
               $ganancia_domingo_sql->execute();
               $ganancia_domingo_sql->bind_result($ganancia_pedro);
               $ganancia_domingo_sql->fetch();
               $ganancia_domingo_sql->close();

               if($ganancia_pedro == null){
                   $ganancia_pedro = 0;
               }else{
                   $ganancia_pedro = $ganancia_pedro - $costo_acumulado;
               }

               
               $ganancia_domingo_sql = $con->prepare("SELECT SUM(Total) FROM `ventas` WHERE WEEK(Fecha) = ? AND YEAR(Fecha) = ? AND estatus <> ? AND WEEKDAY(Fecha) =? AND id_Sucursal =?");
               $ganancia_domingo_sql->bind_param('sssss', $semana, $año, $estatus, $hoy, $sucursalS);
               $ganancia_domingo_sql->execute();
               $ganancia_domingo_sql->bind_result($ganancia_sendero);
               $ganancia_domingo_sql->fetch();
               $ganancia_domingo_sql->close();

               if($ganancia_sendero == null){
                   $ganancia_sendero = 0;
               } 

               $ganancia_domingo_sql = $con->prepare("SELECT COUNT(*) FROM `ventas` WHERE WEEK(Fecha) = ? AND YEAR(Fecha) = ? AND estatus <> ? AND WEEKDAY(Fecha) =?");
               $ganancia_domingo_sql->bind_param('ssss', $semana, $año, $estatus, $hoy);
               $ganancia_domingo_sql->execute();
               $ganancia_domingo_sql->bind_result($ventas_Hoy);
               $ganancia_domingo_sql->fetch();
               $ganancia_domingo_sql->close();

               if($ventas_Hoy == null){
                   $ventas_Hoy = 0;
               } 


               $data = array("ganancia_pedro"=> $ganancia_pedro,"ganancia_sendero"=> $ganancia_sendero, "ventas_hoy"=> $ventas_Hoy);
        

        if (isset($data)) {
            echo json_encode($data, JSON_UNESCAPED_UNICODE);
        }else{
            print_r("Sin datos");
        } 
    

  }

  /*SELECT Total, Fecha, (ELT(WEEKDAY(Fecha) + 1, 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado', 'Domingo')) AS DIA_SEMANA
FROM ventas WHERE WEEK(Fecha) = 26 AND YEAR(Fecha) =2021 AND WEEKDAY(Fecha) =0*/


?>