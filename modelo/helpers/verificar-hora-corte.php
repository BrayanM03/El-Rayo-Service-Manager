<?php
 //Validar hora de cortes

 $fecha_actual = date('Y-m-d');
 $hora_actual = date("h:i a");
 $dia_de_la_semana = date("l");

 $hora_corte_normal = '';
 $hora_corte_sabado = '';
 $querySuc = "SELECT nombre, hora_corte_normal, hora_corte_sabado FROM sucursal WHERE id = ?";
 $resp = $con->prepare($querySuc);
 $resp->bind_param('i', $id_sucursal);
 $resp->execute();
 $resp->bind_result($sucursal, $hora_corte_normal, $hora_corte_sabado);
 $resp->fetch();
 $resp->close();

 $hora_a_comparar = $dia_de_la_semana == 'Saturday' ? $hora_corte_sabado : $hora_corte_normal;

 if ($hora_actual < $hora_a_comparar) {
     $fecha_corte = $fecha_actual;
     $hora_corte = $hora_actual;
 } else {
 if ($dia_de_la_semana == 'Saturday') {
     // Crear un objeto DateTime a partir de la cadena de fecha
     $fecha_obj = new DateTime($fecha_actual);
     $fecha_obj->modify('+2 day');
     $fecha_corte = $fecha_obj->format('Y-m-d');
     $hora_corte = '08:30 am';
 } else {
     
     $fecha_obj = new DateTime($fecha_actual);
     $fecha_obj->modify('+1 day');
     $fecha_corte = $fecha_obj->format('Y-m-d');
     $hora_corte = '08:30 am';
 }
 }

?>