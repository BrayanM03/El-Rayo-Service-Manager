<?php
session_start();
include '../conexion.php';
$con= $conectando->conexion(); 

if (!isset($_SESSION['id_usuario'])) {
    header("Location:../../login.php");
}

date_default_timezone_set("America/Matamoros");

$id_sucursal = $_POST['id_sucursal'];
$tipo_filtro = $_POST['tipo_filtro'];
$mes = $_POST['mes'];
$fecha_inicial = $_POST['fecha_inicial'];
$fecha_final = $_POST['fecha_final'];
$year = $_POST['year'];
$semana = $_POST['semana'];

$utilidad = traerUtilidad($id_sucursal, $tipo_filtro, $mes, $fecha_inicial, $fecha_final, $year, $semana, $con);
$entrada = traerEntrada($id_sucursal, $tipo_filtro, $mes, $fecha_inicial, $fecha_final, $year, $semana, $con);
$creditos_por_cobrar = traerCreditosPorCobrar($id_sucursal, $tipo_filtro, $mes, $fecha_inicial, $fecha_final, $year, $semana, $con);
$dinero_mercancia = traerDineroEnMercancia($id_sucursal, $con);

$resp = array(
    'estatus'=>true, 
    'utilidad'=>$utilidad, 
    'entrada'=> $entrada,
    'creditos_por_cobrar'=>$creditos_por_cobrar, 
    'dinero_mercancia'=>$dinero_mercancia, 'post'=>$_POST);
echo json_encode($resp);

function traerUtilidad($id_sucursal, $tipo_filtro, $mes, $fecha_inicial, $fecha_final, $year, $semana, $conexion) {
    // Construir la consulta SQL base
    $consulta_base = "SELECT SUM(v.utilidad) AS utilidad_total FROM ventas v WHERE 1=1";
    $consulta_base_creditos = "SELECT SUM(a.utilidad) AS utilidad FROM abonos a INNER JOIN creditos c ON c.id = a.id_credito
    INNER JOIN ventas v ON v.id = c.id_venta WHERE 1=1";
    $consulta_base_pedidos = "SELECT SUM(a.utilidad) AS utilidad FROM abonos_pedidos a INNER JOIN pedidos p ON p.id = a.id_pedido WHERE 1=1";
    $consulta_base_apartados = "SELECT SUM(a.utilidad) AS utilidad FROM abonos_apartados a INNER JOIN apartados ap ON ap.id = a.id_apartado WHERE 1=1";
    // Agregar condiciones según el tipo de filtro
    switch ($tipo_filtro) {
        case 1: // Año
            $consulta_base .= " AND YEAR(v.Fecha) = ?";
            $consulta_base_creditos .= " AND YEAR(a.fecha_corte) = ?";
            $consulta_base_apartados .= " AND YEAR(a.fecha_corte) = ?";
            $consulta_base_pedidos .= " AND YEAR(a.fecha_corte) = ?";
                                $param = $year;
            break;
        case 2: // Semana
            $consulta_base .= " AND WEEK(v.Fecha) = ? AND YEAR(v.Fecha) = ?";
            $consulta_base_creditos .= " AND  WEEK(a.fecha_corte) = ? AND YEAR(a.fecha_corte) = ?";
            $consulta_base_apartados .= " AND  WEEK(a.fecha_corte) = ? AND YEAR(a.fecha_corte) = ?";
            $consulta_base_pedidos .= " AND  WEEK(a.fecha_corte) = ? AND YEAR(a.fecha_corte) = ?";
                                $param = intval($semana) -1;
                                $param2 = $year;
            break;
        case 3: // Mes
            $consulta_base .= " AND MONTH(v.Fecha) = ? AND YEAR(v.Fecha) = ?";
            $consulta_base_creditos .= " AND MONTH(a.fecha_corte) = ? AND YEAR(a.fecha_corte) = ?";
            $consulta_base_apartados .= " AND MONTH(a.fecha_corte) = ? AND YEAR(a.fecha_corte) = ?";
            $consulta_base_pedidos .= " AND MONTH(a.fecha_corte) = ? AND YEAR(a.fecha_corte) = ?";
                                $param = $mes;
                                $param2 = $year;
            break;
        case 4: // Día
            $consulta_base .= " AND v.Fecha = ?";
            $consulta_base_creditos .= " AND a.fecha_corte = ?";
            $consulta_base_apartados .= " AND a.fecha_corte = ?";
            $consulta_base_pedidos .= " AND a.fecha_corte = ?";
                                $param = $fecha_inicial;
            break;
        case 5: // Rango
            $consulta_base .= " AND v.Fecha BETWEEN ? AND ?";
            $consulta_base_creditos .=  " AND a.fecha_corte BETWEEN ? AND ?";
            $consulta_base_apartados .=  " AND a.fecha_corte BETWEEN ? AND ?";
            $consulta_base_pedidos .=  " AND a.fecha_corte BETWEEN ? AND ?";
            break;
        default:
            // Si el tipo de filtro no es válido, se devuelve un mensaje de error
            return "Tipo de filtro no válido";
    }

    // Finalizar la consulta base
    if($id_sucursal ==0){
        $consulta_base .= " AND (v.tipo='Normal')";
       /*  print_r($consulta_base_creditos);
        die(); */
        $stmt = $conexion->prepare($consulta_base);
        $stmt_c = $conexion->prepare($consulta_base_creditos);
        $stmt_a = $conexion->prepare($consulta_base_apartados);
        $stmt_p = $conexion->prepare($consulta_base_pedidos);
    if($tipo_filtro ==5){
        $stmt->bind_param('ss', $fecha_inicial, $fecha_final);
        $stmt_c->bind_param('ss', $fecha_inicial, $fecha_final);
        $stmt_a->bind_param('ss', $fecha_inicial, $fecha_final);
        $stmt_p->bind_param('ss', $fecha_inicial, $fecha_final);
    }else if($tipo_filtro ==2 || $tipo_filtro ==3){
        $stmt->bind_param('ss', $param, $param2);
        $stmt_c->bind_param('ss', $param, $param2);
        $stmt_a->bind_param('ss', $param, $param2);
        $stmt_p->bind_param('ss', $param, $param2);
    }else{
        $stmt->bind_param('s', $param);
        $stmt_c->bind_param('s', $param);
        $stmt_a->bind_param('s', $param);
        $stmt_p->bind_param('s', $param);
    }
    }else{
        $consulta_base .= " AND (v.tipo='Normal' OR v.tipo = 'Apartado' OR v.tipo='Pedido') AND v.id_sucursal =?";
        $stmt = $conexion->prepare($consulta_base);
       
    if($tipo_filtro ==5){
        $consulta_base_creditos.= ' AND v.id_sucursal =?';
        $consulta_base_apartados.= ' AND ap.id_sucursal =?';
        $consulta_base_pedidos.= ' AND p.id_sucursal =?';
        $stmt_c = $conexion->prepare($consulta_base_creditos);
        $stmt_a = $conexion->prepare($consulta_base_apartados);
        $stmt_p = $conexion->prepare($consulta_base_pedidos);
        $stmt->bind_param('sss', $fecha_inicial, $fecha_final, $id_sucursal);
        $stmt_c->bind_param('sss', $fecha_inicial, $fecha_final, $id_sucursal);
        $stmt_a->bind_param('sss', $fecha_inicial, $fecha_final, $id_sucursal);
        $stmt_p->bind_param('sss', $fecha_inicial, $fecha_final, $id_sucursal);
    }else if($tipo_filtro ==2 || $tipo_filtro ==3){
        $consulta_base_creditos .= " AND v.id_sucursal= ?";
        $consulta_base_apartados .= " AND ap.id_sucursal= ?";
        $consulta_base_pedidos .= " AND p.id_sucursal= ?";
        $stmt_c = $conexion->prepare($consulta_base_creditos);
        $stmt_a = $conexion->prepare($consulta_base_apartados);
        $stmt_p = $conexion->prepare($consulta_base_pedidos);
        $stmt->bind_param('sss', $param, $param2, $id_sucursal);
        $stmt_c->bind_param('sss', $param, $param2, $id_sucursal);
        $stmt_a->bind_param('sss', $param, $param2, $id_sucursal);
        $stmt_p->bind_param('sss', $param, $param2, $id_sucursal);
    }else{
        $consulta_base_creditos .= " AND v.id_sucursal= ?";
        $consulta_base_apartados .= " AND ap.id_sucursal= ?";
        $consulta_base_pedidos .= " AND p.id_sucursal= ?";
        $stmt_c = $conexion->prepare($consulta_base_creditos);
        $stmt_a = $conexion->prepare($consulta_base_apartados);
        $stmt_p = $conexion->prepare($consulta_base_pedidos);

        $stmt->bind_param('ss', $param, $id_sucursal);
        $stmt_c->bind_param('ss', $param, $id_sucursal);
        $stmt_a->bind_param('ss', $param, $id_sucursal);
        $stmt_p->bind_param('ss', $param, $id_sucursal);
    }
    }
    $utilidad=0;
    $utilidad_abonos =0;
    $utilidad_abonos_apartados =0;
    $utilidad_abonos_pedidos =0;
    $stmt->execute();
    $stmt->bind_result($utilidad);
    $stmt->fetch();
    $stmt->close();

    $stmt_c->execute();
    $stmt_c->bind_result($utilidad_abonos);
    $stmt_c->fetch();
    $stmt_c->close();

    $stmt_a->execute();
    $stmt_a->bind_result($utilidad_abonos_apartados);
    $stmt_a->fetch();
    $stmt_a->close();

    $stmt_p->execute();
    $stmt_p->bind_result($utilidad_abonos_pedidos);
    $stmt_p->fetch();
    $stmt_p->close();

    if(!$utilidad){
        $utilidad =0;
    }
    if(!$utilidad_abonos){
        $utilidad_abonos =0;
    }

    if(!$utilidad_abonos_apartados){
        $utilidad_abonos =0;
    }

    if(!$utilidad_abonos_pedidos){
        $utilidad_abonos =0;
    }

   $sumatoria_utilidad = $utilidad_abonos + $utilidad_abonos_apartados + $utilidad_abonos_pedidos + $utilidad;
    
    return $sumatoria_utilidad;
    /* // Verificar si se obtuvieron resultados
    if ($resultado) {
        // Obtener el resultado de la consulta
        $fila = $resultado->fetch_assoc();

        // Retornar la utilidad total
        return $fila['utilidad_total'];
    } else {
        // Si la consulta no se ejecuta correctamente, se devuelve un mensaje de error
        return "Error al ejecutar la consulta: " . $conexion->error;
    }
 */
}
function traerEntrada($id_sucursal, $tipo_filtro, $mes, $fecha_inicial, $fecha_final, $year, $semana, $conexion) {
     // Construir la consulta SQL base
     $tipo_filtro = intval($tipo_filtro);
     $consulta_base = "SELECT SUM(v.Total) AS total FROM ventas v WHERE v.estatus != 'Cancelada'";
     $consulta_base_creditos = "SELECT SUM(a.abono) AS total FROM abonos a INNER JOIN creditos c ON c.id = a.id_credito
     INNER JOIN ventas v ON v.id = c.id_venta WHERE c.estatus != 4";
     $consulta_base_pedidos = "SELECT SUM(a.abono) AS total FROM abonos_pedidos a INNER JOIN pedidos p ON p.id = a.id_pedido WHERE p.estatus != 'Cancelado'";
     $consulta_base_apartados = "SELECT SUM(a.abono) AS total FROM abonos_apartados a INNER JOIN apartados ap ON ap.id = a.id_apartado WHERE ap.estatus != 'Cancelado'";
     // Agregar condiciones según el tipo de filtro
     switch ($tipo_filtro) {
         case 1: // Año
             $consulta_base .= " AND YEAR(v.fecha_corte) = ?";
             $consulta_base_creditos .= " AND YEAR(a.fecha_corte) = ?";
                                 $param = $year;
            $consulta_base_pedidos .= " AND YEAR(a.fecha_corte) = ?";
                                 $param = $year;    
            $consulta_base_apartados .= " AND YEAR(a.fecha_corte) = ?";
                                 $param = $year;                 
             break;
         case 2: // Semana
             $consulta_base .= " AND WEEK(v.Fecha) = ? AND YEAR(v.Fecha) = ?";
             $consulta_base_creditos .= " AND  WEEK(a.fecha_corte) = ? AND YEAR(a.fecha_corte) = ?";
             $consulta_base_pedidos .= " AND  WEEK(a.fecha_corte) = ? AND YEAR(a.fecha_corte) = ?";
             $consulta_base_apartados .= " AND  WEEK(a.fecha_corte) = ? AND YEAR(a.fecha_corte) = ?";
                                 $param = intval($semana) - 1;
                                 $param2 = $year;
             break;
         case 3: // Mes
             $consulta_base .= " AND MONTH(v.Fecha) = ? AND YEAR(v.Fecha) = ?";
             $consulta_base_creditos .= " AND MONTH(a.fecha_corte) = ? AND YEAR(a.fecha_corte) = ?";
             $consulta_base_pedidos .= " AND MONTH(a.fecha_corte) = ? AND YEAR(a.fecha_corte) = ?";
             $consulta_base_apartados .= " AND MONTH(a.fecha_corte) = ? AND YEAR(a.fecha_corte) = ?";
                                 $param = $mes;
                                 $param2 = $year;
             break;
         case 4: // Día
             $consulta_base .= " AND v.Fecha = ?";
             $consulta_base_creditos .= " AND a.fecha_corte = ?";
             $consulta_base_pedidos .= " AND a.fecha_corte = ?";
             $consulta_base_apartados .= " AND a.fecha_corte = ?";
                                 $param = $fecha_inicial;
             break;
         case 5: // Rango
             $consulta_base .= " AND v.Fecha BETWEEN ? AND ?";
             $consulta_base_creditos .=  " AND a.fecha BETWEEN ? AND ?";
             $consulta_base_pedidos .=  " AND a.fecha BETWEEN ? AND ?";
             $consulta_base_apartados .=  " AND a.fecha BETWEEN ? AND ?";
             break;
         default:
             // Si el tipo de filtro no es válido, se devuelve un mensaje de error
             return "Tipo de filtro no válido";
     }
 
     // Finalizar la consulta base
     if($id_sucursal ==0){
         $consulta_base .= " AND (v.tipo='Normal')";
         $stmt = $conexion->prepare($consulta_base);
         $stmt_c = $conexion->prepare($consulta_base_creditos);
         $stmt_p = $conexion->prepare($consulta_base_pedidos);
         $stmt_a = $conexion->prepare($consulta_base_apartados);
     if($tipo_filtro ==5){
         $stmt->bind_param('ss', $fecha_inicial, $fecha_final);
         $stmt_c->bind_param('ss', $fecha_inicial, $fecha_final);
         $stmt_p->bind_param('ss', $fecha_inicial, $fecha_final);
         $stmt_a->bind_param('ss', $fecha_inicial, $fecha_final);
     }else if($tipo_filtro ==2 || $tipo_filtro ==3){
         $stmt->bind_param('ss', $param, $param2);
         $stmt_c->bind_param('ss', $param, $param2);
         $stmt_p->bind_param('ss', $param, $param2);
         $stmt_a->bind_param('ss', $param, $param2);
     }else{
         $stmt->bind_param('s', $param);
         $stmt_c->bind_param('s', $param);
         $stmt_p->bind_param('s', $param);
         $stmt_a->bind_param('s', $param);
     }
     }else{
         $consulta_base .= " AND (v.tipo='Normal') AND v.id_sucursal =?";
         $stmt = $conexion->prepare($consulta_base);
        
     if($tipo_filtro ==5){
         $consulta_base_creditos.= ' AND v.id_sucursal =?';
         $consulta_base_pedidos.= ' AND p.id_sucursal =?';
         $consulta_base_apartados.= ' AND ap.id_sucursal =?';
         $stmt_c = $conexion->prepare($consulta_base_creditos);
         $stmt_p = $conexion->prepare($consulta_base_pedidos);
         $stmt_a = $conexion->prepare($consulta_base_apartados);
         $stmt->bind_param('sss', $fecha_inicial, $fecha_final, $id_sucursal);
         $stmt_c->bind_param('sss', $fecha_inicial, $fecha_final, $id_sucursal);
         $stmt_p->bind_param('sss', $fecha_inicial, $fecha_final, $id_sucursal);
         $stmt_a->bind_param('sss', $fecha_inicial, $fecha_final, $id_sucursal);
     }else if($tipo_filtro ==2 || $tipo_filtro ==3){
         $consulta_base_creditos .= " AND v.id_sucursal= ?";
         $consulta_base_pedidos .= " AND p.id_sucursal= ?";
         $consulta_base_apartados .= " AND a.id_sucursal= ?";

         $stmt_c = $conexion->prepare($consulta_base_creditos);
         $stmt_p = $conexion->prepare($consulta_base_pedidos);
         $stmt_a = $conexion->prepare($consulta_base_apartados);
         $stmt->bind_param('sss', $param, $param2, $id_sucursal);
         $stmt_c->bind_param('sss', $param, $param2, $id_sucursal);
        
         
         $stmt_p->bind_param('sss', $param, $param2, $id_sucursal);
         $stmt_a->bind_param('sss', $param, $param2, $id_sucursal);
     }else{
         $consulta_base_creditos .= " AND v.id_sucursal= ?";
         $consulta_base_pedidos .= " AND p.id_sucursal= ?";
         $consulta_base_apartados .= " AND a.id_sucursal= ?";
         $stmt_c = $conexion->prepare($consulta_base_creditos);
         $stmt_p = $conexion->prepare($consulta_base_pedidos);
         $stmt_a = $conexion->prepare($consulta_base_apartados);
         $stmt->bind_param('ss', $param, $id_sucursal);
         $stmt_c->bind_param('ss', $param, $id_sucursal);
         $stmt_p->bind_param('ss', $param, $id_sucursal);
         $stmt_a->bind_param('ss', $param, $id_sucursal);
     }
     }
     //print_r($consulta_base);
     /* print_r($consulta_base_apartados);
     die(); */
     $entrada=0;
     $entrada_abonos =0;
     $entrada_abonos_pedidos =0;
     $entrada_abonos_apartados =0;
     //print_r($consulta_base_apartados);
     $stmt->execute();
     $stmt->bind_result($entrada);
     $stmt->fetch();
     $stmt->close();
     $stmt_c->execute();
     $stmt_c->bind_result($entrada_abonos);
     $stmt_c->fetch();
     $stmt_c->close();

     $stmt_p->execute();
     $stmt_p->bind_result($entrada_abonos_pedidos);
     $stmt_p->fetch();
     $stmt_p->close();

     $stmt_a->execute();
     $stmt_a->bind_result($entrada_abonos_apartados);
     $stmt_a->fetch();
     $stmt_a->close();

     if(!$entrada){
         $entrada =0;
     }
     if(!$entrada_abonos){
         $entrada_abonos =0;
     }
     if(!$entrada_abonos_pedidos){
        $entrada_abonos_pedidos =0;
    }
    if(!$entrada_abonos_apartados){
        $entrada_abonos_apartados =0;
    }
   /*  print_r('Normal:' .$entrada . ' --- ');
    print_r('A. Creditos:'.$entrada_abonos . ' --- ');
    print_r('A. Apartados:'.$entrada_abonos_apartados . ' --- ');
    print_r('A. Pedidos:'.$entrada_abonos_pedidos . ' --- ');
    die(); */
    $sumatoria_entrada = $entrada_abonos + $entrada_abonos_apartados + $entrada_abonos_pedidos + $entrada;
    return $sumatoria_entrada;
}
function traerCreditosPorCobrar($id_sucursal, $tipo_filtro, $mes, $fecha_inicial, $fecha_final, $year, $semana, $conexion) {
    $consulta_base_creditos = "SELECT SUM(c.restante) AS deuda FROM creditos c 
    INNER JOIN ventas v ON c.id_Venta = v.id WHERE (c.estatus != 3 AND c.estatus != 5)";
    // Agregar condiciones según el tipo de filtro
    switch ($tipo_filtro) {
        case 1: // Año
            $consulta_base_creditos .= " AND YEAR(c.fecha_inicio) = ?";
                                $param = $year;
            break;
        case 2: // Semana
            $consulta_base_creditos .= " AND  WEEK(c.fecha_inicio) = ? AND YEAR(c.fecha_inicio) = ?";
                                $param = intval($semana) -1;
                                $param2 = $year;
            break;
        case 3: // Mes
            $consulta_base_creditos .= " AND MONTH(c.fecha_inicio) = ? AND YEAR(c.fecha_inicio) = ?";
                                $param = $mes;
                                $param2 = $year;
            break;
        case 4: // Día
            $consulta_base_creditos .= " AND c.fecha_inicio = ?";
                                $param = $fecha_inicial;
            break;
        case 5: // Rango
            $consulta_base_creditos .=  " AND c.fecha_inicio BETWEEN ? AND ?";
            break;
        default:
            // Si el tipo de filtro no es válido, se devuelve un mensaje de error
            return "Tipo de filtro no válido";
    }

    // Finalizar la consulta base
    if($id_sucursal ==0){
        $stmt_c = $conexion->prepare($consulta_base_creditos);
    if($tipo_filtro ==5){
        $stmt_c->bind_param('ss', $fecha_inicial, $fecha_final);
    }else if($tipo_filtro ==2 || $tipo_filtro ==3){
        $stmt_c->bind_param('ss', $param, $param2);
    }else{
        $stmt_c->bind_param('s', $param);
    }
    }else{
       
    if($tipo_filtro ==5){
        $consulta_base_creditos.= ' AND v.id_sucursal =?';
        $stmt_c = $conexion->prepare($consulta_base_creditos);
        $stmt_c->bind_param('sss', $fecha_inicial, $fecha_final, $id_sucursal);
    }else if($tipo_filtro ==2 || $tipo_filtro ==3){
        $consulta_base_creditos .= " AND v.id_sucursal= ?";
        $stmt_c = $conexion->prepare($consulta_base_creditos);
        $stmt_c->bind_param('sss', $param, $param2, $id_sucursal);
    }else{
        $consulta_base_creditos .= " AND v.id_sucursal= ?";
        $stmt_c = $conexion->prepare($consulta_base_creditos);
        $stmt_c->bind_param('ss', $param, $id_sucursal);
    }
    }
    $entrada_abonos =0;
    $stmt_c->execute();
    $stmt_c->bind_result($entrada_abonos);
    $stmt_c->fetch();
    $stmt_c->close();
    if(!$entrada_abonos){
        $entrada_abonos =0;
    }

   $sumatoria_entrada = $entrada_abonos;
   return floatval($sumatoria_entrada);
}
function traerDineroEnMercancia($id_sucursal, $con) {
    $dinero_mercancia =0;
    $consulta_base = "SELECT SUM(l.precio_Inicial) FROM inventario i INNER JOIN llantas l ON i.id_Llanta = l.id WHERE 1=1";
    if($id_sucursal ==0){
        $stmt = $con->prepare($consulta_base); 
    }else{
        $consulta_base.= " AND i.id_sucursal =?";
        $stmt = $con->prepare($consulta_base);
        $stmt->bind_param('s', $id_sucursal);
    }
    $stmt->execute();
    $stmt->bind_result($dinero_mercancia);
    $stmt->fetch();
    $stmt->close();

    return floatval($dinero_mercancia);
}