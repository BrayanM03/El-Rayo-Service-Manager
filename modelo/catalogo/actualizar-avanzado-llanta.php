 <?php
    
    include '../conexion.php';
    $con= $conectando->conexion(); 

    if (!$con) {
        echo "No existe un objecto de conexión";
        die();
    }

    if($_POST){
        
        $id_llanta = $_POST['id_llanta'];
        $marca = $_POST['marca'];
        $modelo = $_POST['modelo'];
        $ancho = $_POST['ancho'];
        $alto = $_POST['alto'];
        $construccion = $_POST['construccion']; 
        $diametro = $_POST['diametro'];
        $descripcion = $_POST['descripcion'];
        $costo = $_POST['costo'];
        $precio = $_POST['precio'];
        $precio_mayoreo = $_POST['precio_mayoreo'];
        $promocion = $_POST['promocion'];
        $precio_promocion = $_POST['precio_promocion'];
        $aplicacion = $_POST['aplicacion'];
        $tipo_vehiculo = $_POST['tipo_vehiculo'];
        $arreglo_permisos = $_POST['arreglo_permisos'];
        
      /*   echo json_encode($_POST);
        die(); */

        $select = "SELECT count(*) FROM llantas WHERE id = ?";
        $stmt = $con->prepare($select);
        $stmt->bind_param('s', $id_llanta);
        $stmt->execute();
        $stmt->bind_result($total_llantas);
        $stmt->fetch();
        $stmt->close();

        if($total_llantas>0){
            $ancho = strval($ancho);
            $aplicacion = intval($aplicacion);
            $tipo_vehiculo = intval($tipo_vehiculo);
         
            $update = "UPDATE llantas SET Ancho = ?, Proporcion = ?, Diametro = ?, Descripcion = ?, Marca = ?,
             Modelo = ?, precio_Inicial = ?, Precio_Venta = ?, precio_Mayoreo = ?, construccion = ?,
            id_aplicacion = ?, id_tipo_vehiculo = ?, promocion = ?, precio_promocion = ?  WHERE id = ?";
            $stmt_ = $con->prepare($update);
            $stmt_->bind_param('sssssssssssssss', $ancho, $alto, $diametro, $descripcion, $marca, $modelo, $costo, $precio, $precio_mayoreo,
            $construccion, $aplicacion, $tipo_vehiculo, $promocion, $precio_promocion, $id_llanta);
            $stmt_->execute();
            $stmt_->close();
        }
        $arreglo_permisos = json_decode($arreglo_permisos, true);
        /* print_r($_FILES);
        die();  */
        $mensaje = array();
        foreach ($arreglo_permisos as $key => $value) {
            # code...
            $mensaje[]=    seteandoImagenes($con, $value, $id_llanta);
        }

        echo json_encode(array('estatus'=>true, 'mensaje' => 'Llanta actualizada correctamente', 'Arreglo'=>$mensaje));

    }

    //Setando imagenes
    function seteandoImagenes($con, $element, $id_llanta){
        $total_llantas_img=0;
        $tipo_llanta = $element['indice'];
        $permiso_eliminar = $element['eliminar'];
        $url_col = $element['url'];
        $id_img = $element['id_img'];

        if(empty($_FILES["file_$tipo_llanta"])){
            $mensaje_archivo = 'No se subio un archivo';
    
        }else{
            $comprobante = 1;
            $targetDir = '../../src/img/neumaticos/';
            $targetFile = $targetDir . basename($_FILES["file_$tipo_llanta"]['name']);
            $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
            if ($imageFileType != 'jpg' && $imageFileType != 'png' && $imageFileType != 'jpeg') {
                return json_encode(["error" => "El archivo no es una imagen válida."]);
                exit;
            }
    
            // Mover el archivo al directorio de destino
            $llanta_url = 'llanta_' . $id_llanta . '_' . $id_img .'.' . $imageFileType;
            if (move_uploaded_file($_FILES["file_$tipo_llanta"]["tmp_name"], $targetDir . $llanta_url)) {
                $mensaje_archivo ="El archivo se ha subido correctamente.";

                $count = 'SELECT COUNT(*) FROM llantas_imagenes WHERE id_llanta=?';
                $stmt=$con->prepare($count);
                $stmt->bind_param('s', $id_llanta);
                $stmt->execute();
                $stmt->bind_result($total_llantas_img);
                $stmt->fetch();
                $stmt->close();

                if($total_llantas_img>0){
                    $update = "UPDATE llantas_imagenes SET $url_col = ? WHERE id_llanta = ?";
                    $stmt = $con->prepare($update);
                    $stmt->bind_param('ss', $llanta_url, $id_llanta);
                    $stmt->execute();
                    $stmt->close();
                }else{
                    $insert = "INSERT INTO llantas_imagenes($url_col, id_llanta) VALUES(?,?)";
                    $stmt = $con->prepare($insert);
                    $stmt->bind_param('ss', $llanta_url, $id_llanta);
                    $stmt->execute();
                    $stmt->close();
                }
                
              
            } else {
                $mensaje_archivo ="Hubo un error al subir el archivo.";
            }
            
        }

        //Eliminado img 
        $mensaje_eliminacion ='';
       
        if(strtolower($permiso_eliminar) !== 'false'){

            $count = 'SELECT COUNT(*) FROM llantas_imagenes WHERE id_llanta=?';
            $stmt=$con->prepare($count);
            $stmt->bind_param('s', $id_llanta);
            $stmt->execute();
            $stmt->bind_result($total_llantas_img);
            $stmt->fetch();
            $stmt->close();

            if($total_llantas_img>0){
                $llanta_url_='';
                $count = "SELECT $url_col FROM llantas_imagenes WHERE id_llanta=?";
                $stmt=$con->prepare($count);
                $stmt->bind_param('s', $id_llanta);
                $stmt->execute();
                $stmt->bind_result($llanta_url_);
                $stmt->fetch();
                $stmt->close();
                /* print_r($llanta_url_);
                die(); */
                $targetDir = '../../src/img/neumaticos/'.$llanta_url_;
                    if (unlink($targetDir)) {
                        $mensaje_eliminacion .= "El archivo se ha eliminado correctamente.";
                        $update = "UPDATE llantas_imagenes SET $url_col = ? WHERE id_llanta = ?";
                        $stmt = $con->prepare($update);
                        $stmt->bind_param('ss', $llanta_url, $id_llanta);
                        $stmt->execute();
                        $stmt->close();
                      } else {
                        $mensaje_eliminacion .=  "Error al eliminar el archivo.";
                      }
            }else{
                $mensaje_eliminacion .=  "No se encontro ID_llanta en LLantas_imagenes.";
            }
            }
            return array('mensaje_archivo'=>$mensaje_archivo, 'mensaje_eliminacion'=>$mensaje_eliminacion);
     
    }
    ?>