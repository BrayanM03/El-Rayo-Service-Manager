<?php

include '../conexion.php';
include 'Empleado.php';
include '../helpers/response_helper.php';
$con= $conectando->conexion(); 
ini_set('display_errors', 1); 
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
if (!$con) {
    echo "Problemas con la conexion";
}
$empleado = new Empleado($con);
$nombre = $_POST['nombre'];
$apellidos = $_POST['apellidos'];
$sucursal = $_POST['sucursal'];
$puesto = $_POST['puesto'];
$rfc = isset($_POST['rfc']) ? $_POST['rfc'] : '';
$telefono = isset($_POST['telefono']) ? $_POST['telefono'] : '';
$horario = isset($_POST['horario']) ? $_POST['horario'] : '';
$correo = isset($_POST['correo']) ? $_POST['correo'] : '';
$fecha_nacimiento = isset($_POST['fecha-cumple']) ?  date('Y-m-d', strtotime($_POST['fecha-cumple'])) : null;
$fecha_ingreso = isset($_POST['fecha-ingreso']) ? date('Y-m-d', strtotime($_POST['fecha-ingreso'])): null;
$genero = isset($_POST['genero']) ? $_POST['genero'] : '';
$salario_base = isset($_POST['salario-base']) ? floatval($_POST['salario-base']) : '';
$direccion = isset($_POST['direccion']) ? $_POST['direccion']: '';
$id_usuario = isset($_POST['usuario']) ? $_POST['usuario']: 0;
//Archivos adjuntos
/* foto_perfil = isset($_FILES['documento_adjunto']) ? $_FILES['documento_adjunto'] : [];
$cv = isset($_FILES['cv']) ? $_FILES['cv'] : [];
$curp = isset($_FILES['curp']) ? $_FILES['curp'] : [];
$combrobante_domicilio = isset($_FILES['domicilio']) ? $_FILES['domicilio'] : [];
$nss = isset($_FILES['nss']) ? $_FILES['nss'] : [];
$identificacion = isset($_FILES['id']) ? $_FILES['id'] : [];
$contrato = isset($_FILES['contrato']) ? $_FILES['contrato'] : [];
$bancarios = isset($_FILES['bancarios']) ? $_FILES['bancarios'] : []; */

$respuesta_empleado = $empleado->insertarEmpleado($nombre, $apellidos, $fecha_nacimiento, $genero, $direccion, 
    $telefono, $correo, $fecha_ingreso, $puesto, $horario, $sucursal, $salario_base, 1, $id_usuario, $rfc);

$id_empleado = $respuesta_empleado['data']['id_empleado'];
$uploads = [
    1 => ['id'=>1, 'nombre'=> 'foto_perfil','file' => $_FILES['documento_adjunto'] ?? null, 'path' => "../../src/img/fotos_empleados/", 'prefix' => 'E'],
    2 => ['id'=>2, 'nombre'=>'cv', 'file' => $_FILES['cv'] ?? null, 'path' => "../../src/docs/cv/",  'prefix' => 'D'],
    3 => ['id'=>3, 'nombre'=>'curp', 'file' => $_FILES['curp'] ?? null, 'path' => "../../src/docs/curp/", 'prefix' => 'D'],
    4 => ['id'=>4, 'nombre'=>'comprobante_domicilio', 'file' => $_FILES['domicilio'] ?? null, 'path' => "../../src/docs/domicilio/", 'prefix' => 'D'],
    5 => ['id'=>5, 'nombre'=>'nss', 'file' => $_FILES['nss'] ?? null, 'path' => "../../src/docs/nss/", 'prefix' => 'D'],
    6 => ['id'=>6, 'nombre'=>'identificacion', 'file' => $_FILES['id'] ?? null, 'path' => "../../src/docs/identificacion/", 'prefix' => 'D'],
    7 => ['id'=>7, 'nombre'=>'contrato', 'file' => $_FILES['contrato'] ?? null, 'path' => "../../src/docs/contrato/", 'prefix' => 'D'],
    8 => ['id'=>8, 'nombre'=>'bancarios', 'file' => $_FILES['bancarios'] ?? null, 'path' => "../../src/docs/bancarios/", 'prefix' => 'D']
];


$documentos_guardados = []; 
foreach ($uploads as $key => $upload) {
    if ($upload['file']) {
        $file = $upload['file'];
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $prefix = $upload['prefix'] ?? '';
        $filename = $upload['path'] . $prefix . $id_empleado . '.' . $extension;
       
        if (!is_dir($upload['path'])) {
            mkdir($upload['path'], 0777, true);
        }
        if (move_uploaded_file($file['tmp_name'], $filename)) {
            $empleado->actualizarDocumentosEmpleado($id_empleado, $upload, $extension);
            $documentos_guardados[$key] = 1;
        } else {
            $documentos_guardados[$key] = 0;
        }
    } else {
        $documentos_guardados[$key] = 0;
    }
}

responder(true, 'Exito',1, []);
die();
//Eliminar
$mensaje_eliminacion ='';
if($_POST['eliminar_documento']=='true'){
    $targetDir = '../../src/docs/facturas_compras/';
    if (unlink($targetDir . 'FAC-' . $id_movimiento.'.' .$_POST['extension_archivo'])) {
        $mensaje_eliminacion .= "El archivo se ha eliminado correctamente.";
        $update = 'UPDATE movimientos SET archivo = 0, extension_archivo = null WHERE id = ?';
        $stmt = $con->prepare($update);
        $stmt->bind_param('s', $id_movimiento);
        $stmt->execute();
        $stmt->close();
      } else {
        $mensaje_eliminacion .=  "Error al eliminar el archivo.";
      }
}

?>