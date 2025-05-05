<?php
session_start();
class Nomina {
    private $con;

    // Constructor
    public function __construct($conexion) {
        $this->con = $conexion;
    }

    public function obtenerPrenomina($fecha_inicio, $fecha_final, $semana, $sucursales, $empleados = []) {
        $data = [];
        $id_usuario=$_SESSION['id_usuario'];
        $filtroEmpleados = "";
        $params = [$fecha_final, $fecha_inicio];
    
        if (!empty($empleados) && is_array($empleados)) {
            $empleadosIds = array_column($empleados, 'id');
            if (!empty($empleadosIds)) {
                $placeholders = implode(',', array_fill(0, count($empleadosIds), '?'));
                $filtroEmpleados = " AND e.id IN ($placeholders)";
                $params = array_merge($params, $empleadosIds);
            }
        }

        $del = 'DELETE FROM prenomina_activa WHERE id_usuario = ?';
        $stmt = $this->con->prepare($del);
        $stmt->bind_param('s', $id_usuario);
        $stmt->execute();
        $stmt->close();

        $sucursales_str = implode(',', $sucursales);
        $insert = 'INSERT INTO prenomina_activa(semana, id_usuario, id_sucursales) VALUES(?,?,?)';
        $stmt = $this->con->prepare($insert);
        $stmt->bind_param('sss', $semana, $id_usuario, $sucursales_str);
        $stmt->execute();
        $stmt->close();
    
        // Consulta principal EXCLUYENDO incidencias con id_categoria = 5 y periocidad = 2
        $sql = "
            SELECT 
                e.id AS id_empleado, 
                e.nombre AS empleado, 
                e.extension,
                e.salario_base, 
                i.id AS id_incidencia,
                i.concepto,
                i.monto,
                i.tipo
            FROM empleados e
            LEFT JOIN incidencias i 
                ON e.id = i.id_empleado 
                AND (i.fecha_inicio <= ? AND i.fecha_final >= ?) 
                AND NOT (i.id_categoria = 5 OR i.periocidad = 2) -- Excluimos estos casos
            WHERE 1=1 $filtroEmpleados
            ORDER BY e.id, i.id
        ";
    
        $stmt = $this->con->prepare($sql);
        if (!empty($params)) {
            $types = str_repeat('s', 2) . str_repeat('i', count($params) - 2);
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $resultado = $stmt->get_result();
    
        $empleadosData = [];
        while ($fila = $resultado->fetch_assoc()) {
            $id_empleado = $fila['id_empleado'];
    
            if (!isset($empleadosData[$id_empleado])) {
                $empleadosData[$id_empleado] = [
                    'id_empleado'   => $id_empleado,
                    'empleado'      => $fila['empleado'],
                    'extension'     => $fila['extension'],
                    'sueldo_base'   => $fila['salario_base'],
                    'percepciones'  => 0,
                    'deducciones'   => 0,
                    'total_pagar'   => 0,
                    'incidencias'   => []
                ];
            }
    
            if (!empty($fila['id_incidencia'])) {
                $incidencia = [
                    'id_incidencia' => $fila['id_incidencia'],
                    'concepto'      => $fila['concepto'],
                    'monto'         => $fila['monto'],
                    'tipo'          => $fila['tipo']
                ];
    
                $empleadosData[$id_empleado]['incidencias'][] = $incidencia;
    
                if ($fila['tipo'] == 2) {
                    $empleadosData[$id_empleado]['percepciones'] += $fila['monto'];
                } elseif ($fila['tipo'] == 1) {
                    $empleadosData[$id_empleado]['deducciones'] += $fila['monto'];
                }
            }
        }
        $stmt->close();
    
        // OBTENER INCIDENCIAS RECURRENTES (id_categoria = 5, periocidad = 2, restante > 0)
        if (!empty($empleadosIds)) {
            $params_ = $empleadosIds;
            $placeholders_recurrentes = implode(',', array_fill(0, count($empleadosIds), '?'));
    
            /* $select_incidencias_recurrentes = "
                SELECT i.*, e.id AS id_empleado 
                FROM incidencias i
                INNER JOIN empleados e ON i.id_empleado = e.id
                WHERE i.id_categoria = 5 
                AND i.periocidad = 2 
                AND i.restante > 0
                AND i.id_empleado IN ($placeholders_recurrentes)
            "; */
            $select_incidencias_recurrentes = "
                SELECT DISTINCT i.*, e.id AS id_empleado 
                FROM incidencias i
                INNER JOIN empleados e ON i.id_empleado = e.id
                WHERE 
                    i.periocidad = 2
                    AND (
                        (i.id_categoria = 5 AND i.restante > 0)
                        OR (i.id_categoria != 5)
                    )
                    AND i.id_empleado IN ($placeholders_recurrentes)
            ";

    
            $stmt = $this->con->prepare($select_incidencias_recurrentes);
            $types_ = str_repeat('i', count($params_));
            $stmt->bind_param($types_, ...$params_);
            $stmt->execute();
            $resultado_recurrentes = $stmt->get_result();
            $stmt->close();
    
            // Agregar incidencias recurrentes a los empleados
            while ($fila = $resultado_recurrentes->fetch_assoc()) {
                $id_empleado = $fila['id_empleado'];
    
                if (isset($empleadosData[$id_empleado])) {
                    $incidencia = [
                        'id_incidencia' => $fila['id'],
                        'concepto'      => $fila['concepto'],
                        'monto'         => $fila['monto'],
                        'tipo'          => $fila['tipo']
                    ];
    
                    $empleadosData[$id_empleado]['incidencias'][] = $incidencia;
    
                    if ($fila['tipo'] == 2) {
                        $empleadosData[$id_empleado]['percepciones'] += $fila['monto'];
                    } elseif ($fila['tipo'] == 1) {
                        $empleadosData[$id_empleado]['deducciones'] += $fila['monto'];
                    }
                }
            }
        }
    
        // Calcular total a pagar
        foreach ($empleadosData as &$empleado) {
            $empleado['total_pagar'] = $empleado['sueldo_base'] + $empleado['percepciones'] - $empleado['deducciones'];
            $data[] = $empleado;
        }

        $this->guardarPrenomina($data);
        $prenomina_guardada = $this->obtenerPrenominaGuardada();
        
        return [
            'estatus' => !empty($data),
            'data' => $prenomina_guardada['data'],
            'prenomina_actual' => $prenomina_guardada['prenomina_actual'],
            'total_prenomina' => $prenomina_guardada['total_prenomina'],
            'mensaje' => !empty($data) ? 'Prenómina generada exitosamente' : 'No se encontraron registros'
        ];
    }

    public function obtenerPrenominaGuardada(){
        $id_usuario = $_SESSION['id_usuario'];
        $hay_prenomina=0;
        $count = 'SELECT count(*) FROM prenomina WHERE id_usuario = ?';
        $stmt = $this->con->prepare($count);
        $stmt->bind_param('i', $id_usuario);
        $stmt->execute();
        $stmt->bind_result($hay_prenomina);
        $stmt->fetch();
        $stmt->close();

        /* print_r($id_usuario. ' -');
        print_r($hay_prenomina); */
        if($hay_prenomina>0){
            $sel = 'SELECT * FROM prenomina WHERE id_usuario = ?';
            $stmt = $this->con->prepare($sel);
            $stmt->bind_param('i', $id_usuario);
            $stmt->execute();
            $resultado = $stmt->get_result();
            $stmt->close();
            $array_response=[];
            $total_prenomina = 0;
            while ($fila = $resultado->fetch_assoc()) {
                $id_prenomina = $fila['id'];
                $empleado = $fila['empleado'];
                $id_empleado = $fila['id_empleado'];
                $sueldo_base = $fila['sueldo_base'];
                $concepto_percepciones = $fila['concepto_percepciones'];
                $concepto_deducciones = $fila['concepto_deducciones'];
                $percepciones = $fila['percepciones'];
                $deducciones = $fila['deducciones'];
                $total_pagar = $fila['total_pagar'];
                $id_usuario_ = $fila['id_usuario'];
                $extension = $fila['extension'];
                $total_prenomina += $total_pagar;

                $no_incidencias=0;
                $count = 'SELECT count(*) FROM incidencias_prenomina WHERE id_prenomina = ?';
                $stmt = $this->con->prepare($count);
                $stmt->bind_param('i', $id_prenomina);
                $stmt->execute();
                $stmt->bind_result($no_incidencias);
                $stmt->fetch();
                $stmt->close();

                $incidencias_pren=[];
                if($no_incidencias>0){
                    $sel = 'SELECT * FROM incidencias_prenomina WHERE id_prenomina = ?';
                    $stmt = $this->con->prepare($sel);
                    $stmt->bind_param('i', $id_prenomina);
                    $stmt->execute();
                    $resultado_incd = $stmt->get_result();
                    $stmt->close();
                    while ($value = $resultado_incd->fetch_assoc()) {
                        $incidencias_pren[] = $value;
                    }
                }

                $prenomina_registro = array(
                    'empleado' => $empleado,
                    'id_empleado' => $id_empleado,
                    'sueldo_base' => $sueldo_base,
                    'concepto_percepciones'=>$concepto_percepciones,
                    'concepto_deducciones' => $concepto_deducciones,
                    'incidencias' => $incidencias_pren,
                    'percepciones' => $percepciones,
                    'deducciones' => $deducciones,
                    'total_pagar' => $total_pagar,
                    'extension' => $extension,
                    'id_usuario' => $id_usuario_

                );
                $array_response[] = $prenomina_registro;
            }

            $sel = 'SELECT * FROM prenomina_activa WHERE id_usuario = ?';
            $stmt = $this->con->prepare($sel);
            $stmt->bind_param('i', $id_usuario);
            $stmt->execute();
            $resultado_pre = $stmt->get_result();
            $stmt->close();
            $prenomina_actual = [];
            while ($row = $resultado_pre->fetch_assoc()) {
                $prenomina_actual = $row;
            }
        }else{
            $array_response =[];
            $prenomina_actual = [];
            $total_prenomina=0;
        }
        return [
            'estatus' => !empty($array_response),
            'data' => $array_response,
            'prenomina_actual' => $prenomina_actual,
            'total_prenomina' => $total_prenomina,
            'mensaje' => !empty($array_response) ? 'Prenómina generada exitosamente' : 'No se encontraron registros'
        ];
    }

    public function limpiarPrenomina(){
        $id_usuario = $_SESSION['id_usuario'];
        $del = "DELETE FROM prenomina WHERE id_usuario =?";
        $stmt = $this->con->prepare($del);
                $stmt->bind_param('i', $id_usuario);
                $res = $stmt->execute();
                $stmt->close(); 

                if($res){
                    return array(
                        'estatus' => $res,
                        'data' => [],
                        'mensaje'=> 'Incidencia actualizada'
                    );
                }
    }

    public function guardarPrenomina($datos){
       /*  echo json_encode($datos);
        die(); */
        $id_usuario = $_SESSION['id_usuario'];
        $del = 'DELETE FROM prenomina WHERE id_usuario = ?';
                $stmt = $this->con->prepare($del);
                $stmt->bind_param('i', $id_usuario);
                $stmt->execute();
                $stmt->close();
        $del = 'DELETE FROM incidencias_prenomina WHERE id_usuario = ?';
                $stmt = $this->con->prepare($del);
                $stmt->bind_param('i', $id_usuario);
                $stmt->execute();
                $stmt->close(); 
                
        $reinicio = 'ALTER TABLE prenomina AUTO_INCREMENT = 1';
        $stmt = $this->con->prepare($reinicio);
        $stmt->execute();
        $stmt->close();
        $reinicio = 'ALTER TABLE incidencias_prenomina AUTO_INCREMENT = 1';
        $stmt = $this->con->prepare($reinicio);
        $stmt->execute();
        $stmt->close();

        if(count($datos)>0){
            foreach ($datos as $key => $value) {
                $id_empleado = $value['id_empleado'];
                $empleado = $value['empleado'];
                $sueldo_base = $value['sueldo_base'];
                $percepciones = floatval($value['percepciones']);
                $deducciones = floatval($value['deducciones']);
                $total_pagar = floatval($value['total_pagar']);
                $extension = $value['extension'];
                $insert = 'INSERT INTO prenomina(empleado, id_empleado, extension, sueldo_base, percepciones,
                deducciones, total_pagar, id_usuario) VALUES(?,?,?,?,?,?,?,?)';
                $stmt = $this->con->prepare($insert);
                $stmt->bind_param('ssssssss', $empleado, $id_empleado, $extension, $sueldo_base, $percepciones, $deducciones, $total_pagar, $id_usuario);
                $stmt->execute();
                $id_prenomina = $this->con->insert_id;
                $stmt->close();               

                if(count($value['incidencias'])>0){
                    foreach ($value['incidencias'] as $key => $element) {
                        $concepto = $element['concepto'];
                        $id_incidencia = $element['id_incidencia'];
                        $monto = $element['monto'];
                        $tipo = $element['tipo'];
                       
                        $insert = 'INSERT INTO incidencias_prenomina(concepto, id_incidencia, monto, tipo,
                        id_prenomina, id_usuario) VALUES(?,?,?,?,?,?)';
                        $stmt = $this->con->prepare($insert);
                        $stmt->bind_param('ssssss', $concepto, $id_incidencia, $monto, $tipo, $id_prenomina, $id_usuario);
                        $stmt->execute();
                        $stmt->close();
                    }
                }

            }
        }
    }

    public function actualizarIncidenciaPrenomina($id, $id_prenomina, $descripcion, $monto, $tipo_incd){

        $id_usuario = $_SESSION['id_usuario'];
        $updt = 'UPDATE incidencias_prenomina SET concepto = ?, monto = ?, tipo = ? WHERE id = ?';
        $stmt = $this->con->prepare($updt);
        $stmt->bind_param('sssi', $descripcion, $monto, $tipo_incd, $id);
        $res=$stmt->execute();
        $stmt->close();

        //Obtenemos la suma de las deducciones y percepciones
        $deducciones_actuales = 0;
        $sel = 'SELECT SUM(monto) FROM incidencias_prenomina WHERE id_usuario = ? AND id_prenomina = ? AND tipo =1';
        $stmt = $this->con->prepare($sel);
        $stmt->bind_param('ii', $id_usuario, $id_prenomina);
        $stmt->execute();
        $stmt->bind_result($deducciones_actuales);
        $stmt->fetch();
        $stmt->close();

        $percepciones_actuales = 0;
        $sel = 'SELECT SUM(monto) FROM incidencias_prenomina WHERE id_usuario = ? AND id_prenomina = ? AND tipo =2';
        $stmt = $this->con->prepare($sel);
        $stmt->bind_param('ii', $id_usuario, $id_prenomina);
        $stmt->execute();
        $stmt->bind_result($percepciones_actuales);
        $stmt->fetch();
        $stmt->close();

        $sel = 'SELECT * FROM prenomina WHERE id = ?';
        $stmt = $this->con->prepare($sel);
        $stmt->bind_param('i',$id_prenomina);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $stmt->close();

        while ($fila = $resultado->fetch_assoc()) {
            
            $sueldo_base_actual = $fila['sueldo_base'];
            $total_pagar = ($sueldo_base_actual + $percepciones_actuales) - $deducciones_actuales ;
        }

        $deducciones_actuales=floatval($deducciones_actuales);
        $percepciones_actuales = floatval($percepciones_actuales);
        $total_pagar = floatval($total_pagar);
        
        $upd = 'UPDATE prenomina SET percepciones =?, deducciones = ?, total_pagar = ? WHERE id = ?';
        $stmt = $this->con->prepare($upd);
        $stmt->bind_param('dddi', $percepciones_actuales, $deducciones_actuales, $total_pagar, $id_prenomina);
        $stmt->execute();
        $stmt->close();

        $sel = 'SELECT * FROM prenomina WHERE id = ?';
        $stmt = $this->con->prepare($sel);
        $stmt->bind_param('i',$id_prenomina);
        $stmt->execute();
        $reg_prenomina_actual = $stmt->get_result();
        $stmt->close();

        $total_prenomina = $this->obtenerTotalPagarPrenomina();
        while($fil = $reg_prenomina_actual->fetch_assoc()){
            $data = $fil;
        }

        if($res){
            return array(
                'estatus' => $res,
                'data' => $data,
                'total_pagar' => $total_prenomina['data'],
                'mensaje'=> 'Incidencia actualizada'
            );
        }
    }

    public function obtenerTotalPagarPrenomina(){
        $id_usuario = $_SESSION['id_usuario'];
        $sel = 'SELECT * FROM prenomina WHERE id_usuario = ?';
        $stmt = $this->con->prepare($sel);
        $stmt->bind_param('i',$id_usuario);
        $res = $stmt->execute();
        $resultado = $stmt->get_result();
        $error_mensaje = $stmt->error;
        $stmt->close();

        $total_prenomina =0;
        while ($fila = $resultado->fetch_assoc()) {
            $total_prenomina += floatval($fila['total_pagar']);
        }
        return array(
            'estatus'=> $res,
            'estatus' => $res ? 'Consulta obtenida con exito' : $error_mensaje,
            'data'=>$total_prenomina
        );
        
    }

    
    
    
    
}


?>