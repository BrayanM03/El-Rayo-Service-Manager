<?php

class Empleado
{
    private $con;

    // Constructor para recibir la conexión de base de datos
    public function __construct($conexion)
    {
        $this->con = $conexion;
    }

    // Método para obtener un producto (llanta) y sus imágenes

    public function obtenerEmpleados($sucursal = [])
    {
        // Verificar si $sucursal es un array y tiene elementos
        $sql_adicional = "";
        $params = [];

        if (!empty($sucursal) && is_array($sucursal)) {
            // Generar placeholders para la consulta preparada
            $placeholders = implode(',', array_fill(0, count($sucursal), '?'));
            $sql_adicional = " WHERE id_sucursal IN ($placeholders)";
            $params = $sucursal;
        }

        // Consulta para obtener empleados filtrados por sucursal si aplica
        $sql = "SELECT * FROM empleados" . $sql_adicional . " ORDER BY id_sucursal ASC";
        $stmt = $this->con->prepare($sql);

        if (!empty($params)) {
            // Generar tipos de datos para bind_param (asumiendo que id_sucursal es un entero)
            $types = str_repeat('i', count($params));
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $resultado = $stmt->get_result();
        $fila = $resultado->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        // Verificar si hay resultados
        if (!empty($fila)) {
            return [
                'estatus' => true,
                'data' => $fila,
                'mensaje' => 'Empleados encontrados'
            ];
        } else {
            return [
                'estatus' => false,
                'data' => [],
                'mensaje' => 'No se encontraron empleados'
            ];
        }
    }

    public function obtenerSucursalEmpleado() {}


    public function obtenerPuestos()
    {
        // Contar si la llanta existe
        $total_sucursales = 0;
        $count = 'SELECT count(*) FROM puestos WHERE estatus = 1';
        $stmt = $this->con->prepare($count);
        $stmt->execute();
        $stmt->bind_result($total_sucursales);
        $stmt->fetch();
        $stmt->close();

        if ($total_sucursales > 0) {
            // Obtener información de la llanta
            $sqlContarLlantas = $this->con->prepare("SELECT * FROM puestos WHERE estatus = 1");
            $sqlContarLlantas->execute();
            $resultado = $sqlContarLlantas->get_result();
            $fila = $resultado->fetch_all();
            $sqlContarLlantas->close();


            // Devolver información de la llanta y las imágenes
            return [
                'estatus' => true,
                'data' => $fila,
                'mensaje' => 'Producto encontrado'
            ];
        } else {
            return [
                'estatus' => false,
                'data' => [],
                'mensaje' => 'Producto no encontrado'
            ];
        }
    }

    public function insertarEmpleado(
        $nombre,
        $apellidos,
        $fecha_nacimiento,
        $genero,
        $direccion,
        $telefono,
        $correo,
        $fecha_ingreso,
        $id_puesto,
        $id_horario,
        $id_sucursal,
        $salario_base,
        $estatus,
        $id_usuario,
        $rfc
    ) {

        $insert = "INSERT INTO empleados(
            nombre, apellidos, fecha_nacimiento, genero, direccion, telefono, correo, fecha_ingreso,
            id_puesto, id_horario, id_sucursal, salario_base, estatus, id_usuario, rfc) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
        $stmt = $this->con->prepare($insert);
        $stmt->bind_param(
            'sssssssssssssss',
            $nombre,
            $apellidos,
            $fecha_nacimiento,
            $genero,
            $direccion,
            $telefono,
            $correo,
            $fecha_ingreso,
            $id_puesto,
            $id_horario,
            $id_sucursal,
            $salario_base,
            $estatus,
            $id_usuario,
            $rfc
        );
        $stmt->execute();
        $id_empleado = $stmt->insert_id;
        $stmt->close();


        return [
            'estatus' => true,
            'data' => ['id_empleado' => $id_empleado],
            'mensaje' => 'Empleado registrado con exito'
        ];
    }


    public function actualizarDocumentosEmpleado($id_empleado, $documento, $ext)
    {
        if ($documento['id'] == 1) {
            $sql = "UPDATE empleados SET foto_perfil = 1, extension = ? WHERE id = ?";
            $stmt = $this->con->prepare($sql);
            $stmt->bind_param(
                'si',
                $ext,
                $id_empleado
            );
        } else {

            switch ($documento['id']) {
                case 2:
                    $col = 'cv';
                    break;
                case 3:
                    $col = 'curp';
                    break;
                case 4:
                    $col = 'comprobante_domicilio';
                    break;
                case 5:
                    $col = 'nss';
                    break;
                case 6:
                    $col = 'identificacion';
                    break;
                case 7:
                    $col = 'contrato';
                    break;
                case 7:
                    $col = 'bancarios';
                    break;

                default:
                    # code...
                    break;
            }

            $upd = "UPDATE empleados SET $col = 1 WHERE id =?";
            $stmt = $this->con->prepare($upd);
            $stmt->bind_param('i', $id_empleado);
            $stmt->execute();
            $stmt->close();

            $sql = "INSERT INTO documentos_empleados(id_documento, id_empleado, extension) VALUES(?,?,?)";
            $stmt = $this->con->prepare($sql);
            $stmt->bind_param(
                'iis',
                $documento['id'],
                $id_empleado,
                $ext
            );
        }



        if ($stmt->execute()) {
            $estatus = true;
            $mensaje = "Registro actualizado correctamente.";
        } else {
            $estatus = false;
            $mensaje = "Error al actualizar el registro: " . $stmt->error;
        }

        $stmt->close();
        return [
            'estatus' => $estatus,
            'data' => [],
            'mensaje' => $mensaje
        ];
    }

    public function obtenerDocumentos()
    {
        // Contar si la llanta existe
        $total_sucursales = 0;
        $count = 'SELECT count(*) FROM documentos WHERE estatus = 1';
        $stmt = $this->con->prepare($count);
        $stmt->execute();
        $stmt->bind_result($total_sucursales);
        $stmt->fetch();
        $stmt->close();

        if ($total_sucursales > 0) {
            // Obtener información de la llanta
            $sqlContarLlantas = $this->con->prepare("SELECT * FROM documentos WHERE estatus = 1");
            $sqlContarLlantas->execute();
            $resultado = $sqlContarLlantas->get_result();
            $fila = $resultado->fetch_all();
            $sqlContarLlantas->close();


            // Devolver información de la llanta y las imágenes
            return [
                'estatus' => true,
                'data' => $fila,
                'mensaje' => 'Documento encontrado'
            ];
        } else {
            return [
                'estatus' => false,
                'data' => [],
                'mensaje' => 'Documento no encontrado'
            ];
        }
    }

    public function obtenerIncidencia($id_incidencia)
    {
        // Contar si la llanta existe
        $total_sucursales = 0;
        $count = 'SELECT count(*) FROM incidencias WHERE id = ?';
        $stmt = $this->con->prepare($count);
        $stmt->bind_param('i', $id_incidencia);
        $stmt->execute();
        $stmt->bind_result($total_sucursales);
        $stmt->fetch();
        $stmt->close();

        if ($total_sucursales > 0) {
            // Obtener información de la llanta
            $sqlContarLlantas = $this->con->prepare("SELECT * FROM incidencias WHERE id = ?");
            $sqlContarLlantas->bind_param('i', $id_incidencia);
            $sqlContarLlantas->execute();
            $resultado = $sqlContarLlantas->get_result();
            //$fila = $resultado->fetch_all();
            foreach ($resultado as $key => $value) {
                $row[] = $value;
            }
            $sqlContarLlantas->close();


            // Devolver información de la llanta y las imágenes
            return [
                'estatus' => true,
                'data' => $row,
                'mensaje' => 'Incidencia encontrada'
            ];
        } else {
            return [
                'estatus' => false,
                'data' => [],
                'mensaje' => 'Incidencia no encontrada'
            ];
        }
    }

    public function actualizarIncidencia($datos)
    {

        // Verificar si la categoría es 5 para incluir los montos adicionales
        if ($datos['categoria'] == 5) {
            $pagado = 0;
            $sel = "SELECT SUM(monto) FROM abonos_prestamos WHERE id_incidencia =?";
            $stmt = $this->con->prepare($sel);
            $stmt->bind_param('i', $datos['id_incidencia']);
            $stmt->execute();
            $stmt->bind_result($pagado);
            $stmt->fetch();
            $stmt->close();

            $restante = !$pagado ? $datos['monto-prestamo'] : floatval($datos['monto-prestamo']) - floatval($pagado);
            /*  print_r($pagado);
                die(); */

            $upd = "UPDATE incidencias SET concepto = ?, id_categoria = ?, id_empleado = ?,
                fecha_inicio = ?, fecha_final = ?, monto = ?, tipo = ?, periocidad = ?,
                monto_prestamo = ?, restante = ? WHERE id = ?";

            $stmt = $this->con->prepare($upd);
            $stmt->bind_param(
                'ssssssssssi',
                $datos['descripcion'],
                $datos['categoria'],
                $datos['empleado'],
                $datos['fecha-inicio'],
                $datos['fecha-final'],
                $datos['monto'],
                $datos['tipo'],
                $datos['periocidad'],
                $datos['monto-prestamo'],
                $restante,
                $datos['id_incidencia']
            );
        } else {
            $upd = "UPDATE incidencias SET concepto = ?, id_categoria = ?, id_empleado = ?,
                fecha_inicio = ?, fecha_final = ?, monto = ?, tipo = ?, periocidad = ? 
                WHERE id = ?";

            $stmt = $this->con->prepare($upd);
            $stmt->bind_param(
                'ssssssssi',
                $datos['descripcion'],
                $datos['categoria'],
                $datos['empleado'],
                $datos['fecha-inicio'],
                $datos['fecha-final'],
                $datos['monto'],
                $datos['tipo'],
                $datos['periocidad'],
                $datos['id_incidencia']
            );
        }

        $stmt->execute();
        $stmt->close();
    }


    public function obtenerDocumentosXEmpleado($id_empleado)
    {
        // Contar si la llanta existe
        $total_docs = 0;
        $count = 'SELECT count(*) FROM documentos_empleados WHERE id_empleado = ?';
        $stmt = $this->con->prepare($count);
        $stmt->bind_param('i', $id_empleado);
        $stmt->execute();
        $stmt->bind_result($total_docs);
        $stmt->fetch();
        $stmt->close();

        if ($total_docs > 0) {
            // Obtener información de la llanta
            $sqlContarLlantas = $this->con->prepare("SELECT * FROM documentos_empleados WHERE id_empleado = ?");
            $sqlContarLlantas->bind_param('i', $id_empleado);
            $sqlContarLlantas->execute();
            $resultado = $sqlContarLlantas->get_result();
            $fila = $resultado->fetch_all();
            $sqlContarLlantas->close();


            // Devolver información de la llanta y las imágenes
            return [
                'estatus' => true,
                'data' => $fila,
                'mensaje' => 'Documentos encontrados'
            ];
        } else {
            return [
                'estatus' => false,
                'data' => [],
                'mensaje' => 'Documentos no encontrados'
            ];
        }
    }

    public function obtenerDatosEmpleado($id_empleado)
    {
        // Contar si la llanta existe
        $total_emp = 0;
        $count = 'SELECT count(*) FROM empleados WHERE id = ?';
        $stmt = $this->con->prepare($count);
        $stmt->bind_param('i', $id_empleado);
        $stmt->execute();
        $stmt->bind_result($total_emp);
        $stmt->fetch();
        $stmt->close();
        $row = [];
        if ($total_emp > 0) {
            $estatus = true;
            $mensaje = 'Datos obtenidos del empleado';
            // Obtener información de la llanta
            $sqlContarLlantas = $this->con->prepare("SELECT * FROM empleados WHERE id = ?");
            $sqlContarLlantas->bind_param('i', $id_empleado);
            $sqlContarLlantas->execute();
            $resultado = $sqlContarLlantas->get_result();
            //$fila = $resultado->fetch_all();
            foreach ($resultado as $key => $value) {
                $row = $value;
            }
            $sqlContarLlantas->close();
        } else {
            $estatus = false;
            $mensaje = 'No se encontrarón datos del empleado';
        }
        return [
            'estatus' => $estatus,
            'data' => $row,
            'mensaje' => $mensaje
        ];
    }


    public function actualizarDatosGenerales($datos, $carga, $ext)
    {
        $id_empleado = $datos['id_empleado'];
        $nombre = $datos['nombre'];
        $apellidos = $datos['apellidos'];
        $sucursal = $datos['sucursal'];
        $puesto = $datos['puesto'];
        $salario_base = $datos['salario-base'];
        $telefono = $datos['telefono'];
        $correo = $datos['correo'];
        $genero = $datos['genero'];
        $fecha_cumple = $datos['fecha-cumple'];
        $estatus = $datos['estatus'];
        $fecha_ingreso = $datos['fecha-ingreso'];
        $usuario_enlazado = $datos['usuario'];
        $direccion = $datos['direccion'];
        $rfc = $datos['rfc'];

        $cargar_foto = $_POST['cargar_foto'];
        $eliminar_foto = $_POST['eliminar_foto'];
       
        $archivo = '../../src/img/fotos_empleados/E'.$id_empleado.'.'.$ext;

        //Revisar 
      
        if($eliminar_foto=='true' AND $cargar_foto =='false'){
            if (file_exists($archivo)) {
        
                if (unlink($archivo)) {
                    $mensaje_actualizacion_fp = "El archivo fue eliminado exitosamente.";
                    $upda = 'UPDATE empleados SET foto_perfil = 0 AND extension = null WHERE id = ?';
                    $stmt = $this->con->prepare($upda);
                    $stmt->bind_param('s', $id_empleado);
                    $stmt->execute();
                    $stmt->close();
                } else {
                    $mensaje_actualizacion_fp= "Error al intentar eliminar el archivo.";
                }

            } else {
                $mensaje_actualizacion_fp = "El archivo no existe.";
            }
        }else if($eliminar_foto=='true' AND $cargar_foto =='true'){
                     $file = $carga['foto_perfil'];
                    $mensaje_actualizacion_fp = "El archivo fue cargado exitosamente.";
            
                    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                    $path = '../../src/img/fotos_empleados/';
                    $filename = $path . 'E' . $id_empleado . '.' . $extension;

                    if (file_exists($archivo)) {
                        unlink($archivo);
                    }

                    if (!is_dir($path)) {
                        mkdir($path, 0777, true);
                    }
                    if (move_uploaded_file($file['tmp_name'], $filename)) {
                        $d=['id'=>1 ];
                        $this->actualizarDocumentosEmpleado($id_empleado, $d, $extension);
                    } else {
                        $mensaje_actualizacion_fp ='No se pudo mover el archivo perrito';
                    }

                    $upda = 'UPDATE empleados SET foto_perfil = 1 AND extension = ? WHERE id = ?';
                    $stmt = $this->con->prepare($upda);
                    $stmt->bind_param('ss', $extension, $id_empleado);
                    $stmt->execute();
                    $stmt->close();
               

           
        }else{
            $mensaje_actualizacion_fp = "No se adjunto foto de perfil";
        }
        $upd = 'UPDATE empleados SET nombre=?, apellidos=?, fecha_nacimiento=?, genero=?, direccion=?, telefono=?, correo=?,
    fecha_ingreso=?, id_puesto=?, id_sucursal=?, salario_base=?, estatus=?, id_usuario=?, rfc=? WHERE id =?';
        $stmt = $this->con->prepare($upd);
        $stmt->bind_param(
            'sssssssssssssss',
            $nombre,
            $apellidos,
            $fecha_cumple,
            $genero,
            $direccion,
            $telefono,
            $correo,
            $fecha_ingreso,
            $puesto,
            $sucursal,
            $salario_base,
            $estatus,
            $usuario_enlazado,
            $rfc,
            $id_empleado
        );
        $res = $stmt->execute();
        $stmt->close();

        return [
            'estatus' => $res,
            'data' => [],
            'mensaje_fp'=> $mensaje_actualizacion_fp,
            'mensaje' => !$res ? 'No se pudo actualizar el empleado' : 'Empleado actualizado correctamente'
        ];
    }
}
