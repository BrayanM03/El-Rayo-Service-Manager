<?php

class Clientes {
    private $con;
    private $id_usuario;
    private $rol_usuario;
    // Constructor para recibir la conexión de base de datos
    public function __construct($conexion) {
        $this->con = $conexion;
        $this->id_usuario = $_SESSION['id_usuario'];
        $this->rol_usuario = $_SESSION['rol'];
    }

    // Método para obtener un producto (llanta) y sus imágenes
    public function obtenerCliente($id) {
        // Contar si la llanta existe
        $total_registros=0;
        $count = 'SELECT count(*) FROM clientes WHERE id = ?';
        $stmt = $this->con->prepare($count);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->bind_result($total_registros);
        $stmt->fetch();
        $stmt->close();

        if ($total_registros > 0) {
            // Obtener información de la llanta
            $sqlContarLlantas = $this->con->prepare("SELECT * FROM clientes WHERE id = ?");
            $sqlContarLlantas->bind_param('i', $id);
            $sqlContarLlantas->execute();
            $resultado = $sqlContarLlantas->get_result();
            $fila = $resultado->fetch_assoc();
            $sqlContarLlantas->close();

            // Devolver información de la llanta y las imágenes
            return [
                'estatus' => true,
                'cliente' => $fila,
                'mensaje' => 'Cliente encontrado'
            ];
        
        } else {
            return [
            'estatus' => false,
            'producto' => [],
            'mensaje' => 'Producto no encontrado'
            ];
        }
    }

    public function traer_creditos_vencidos($id_cliente){
        $fecha_actual = date("d-m-Y");
        $count =0;
        $con =$this->con;
        $id_usuario = $this->id_usuario;
        $rol =$this->rol_usuario;
        $total =0;
      
    function form_moneda($numero_original){
        $numero_formateado = number_format($numero_original, 2, '.', ',');
        return $numero_formateado;
    }
    function form_date($fecha_original){
        $timestamp = strtotime($fecha_original);
        $fecha_formateada = date("d-M-y", $timestamp);
        return $fecha_formateada;
    }

    if (!$con) {
        echo "Problemas con la conexion";
    }

    if (!isset($_SESSION['id_usuario'])) {
        header("Location:../login.php");
    }


    

    $fecha_hoy = date("Y-m-d");
  //Actualizamos el estatus de los credtios que ya esta vencidos
    $estatusvencido = 4;
    $res = 0.00;
    /* $update = "UPDATE `creditos` SET estatus = ? WHERE pagado <> total AND restante <> ? AND DATE(fecha_final) <= ?";
    $result = $con->prepare($update);
    $result->bind_param('sss', $estatusvencido, $res, $fecha_hoy);
    $result->execute();
    $result->close(); */

    $traer = "SELECT c.*, v.hora FROM `creditos` as c INNER JOIN ventas v ON c.id_venta = v.id  WHERE c.estatus =? AND c.id_cliente =?";
    $result = $con->prepare($traer);
    if ($result) {
     
    $result->bind_param('ss', $estatusvencido, $id_cliente);
    $result->execute();
    $array_resultados = $result->get_result();
    $result->close();
    $arreglo =[];
    $suma_restante=0;
    while($fila = $array_resultados->fetch_assoc()){
        
        $cliente_id = $fila['id_cliente'];
        $pagado = $fila['pagado'];
        $pagado_f = form_moneda($pagado);
        $restante = $fila['restante'];
        $restante_f = form_moneda($restante);

        $total = $fila['total'];
        $total_f = form_moneda($total);

        $fecha_inicio = $fila['fecha_inicio'] .' '. $fila['hora'];
        $fecha_inicio_f = form_date($fecha_inicio);
        $fecha_final = $fila['fecha_final'] .' '. $fila['hora'];
        $fecha_final_f = form_date($fecha_final);
        $id_cred = $fila['id']; 
        $id_venta = $fila['id_venta']; 

        $cliente='';
        $sql_customer_name = "SELECT Nombre_Cliente FROM `clientes` WHERE id = ?";
        $result = $con->prepare($sql_customer_name);
        $result->bind_param('s', $cliente_id);
        $result->execute();
        $result->bind_result($cliente);
        $result->fetch();
        $result->close();
        $suma_restante += $restante;
        $arreglo[] = array('id_venta'=>$id_venta, 'cliente'=> $cliente,'total'=>$total_f, 'pagado'=> $pagado_f, 'restante'=> $restante_f, 'fecha_inicio'=>$fecha_inicio, 'fecha_final'=> $fecha_final, 'id_cred'=> $id_cred);
    }

    $suma_restante_ = form_moneda($suma_restante);
    if(count($arreglo)==0){
        return [
            'estatus' => false,
            'credito_vencidos' => [],
            'mensaje' => 'Cliente con credito OK'
            ];
    }else{
        return [
            'estatus' => true,
            'credito_vencidos' => $arreglo,
            'sumatoria_deuda'=>$suma_restante_,
            'mensaje' => 'Cliente con credito vencido'
            ];
    }

    }


    }
}

?>
