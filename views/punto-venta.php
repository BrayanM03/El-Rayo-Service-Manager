<div class="row mt-5">
    <div class="col-12 col-md-12 col-sm-12">
        <div class="card" style="margin-bottom: 2vh; padding-bottom: 5vh; padding-right: 30px;">
            <div class="row justify-content-center">
                <div class="col-12 col-md-3 text-center">
                   <img src="./src/img/logo.jpg" alt="logo" class="mt-4" style="width: 140px;">
                </div>
                <div class="col-12 col-md-6 text-center">
                    <div class="row justify-content-center">
                    <div class="col-12 col-md-5 text-center">
                        <h3 class="titulo-nueva-venta">Nueva venta</h3>
                        <p class="ml-4" id="texto-modo-venta">Modo de venta: <span 
                       >
                        <a  style="color: green; text-shadow:#00a000 3px 0 10px; cursor:pointer; text-decoration: none;" href="punto-venta-servicios.php">Neumaticos</a></span></p>
                    </div>
                    </div>
                    <div class="row justify-content-center">
                        <div class="col-12 col-md-5 m-3 text-center">
                            <span>Eliga la medida de su llanta con estos filtros</span>
                        </div>
                    </div>
                    <div class="row justify-content-center">
                        <div class="col-12 col-md-12 col-sm-8">
                            <div class="row">
                                <div class="col-4 d-flex flex-column">
                                    <label for="ancho" class="">Ancho</label>
                                    <select type="text" id="ancho" class="selectpicker w-100" data-live-search="true" onchange="cargarAltos()"></select>
                                </div>
                                <div class="col-4 d-flex flex-column">
                                    <label for="alto" class="ml-2">Alto</label>
                                    <select type="text" id="alto" class="form-field ml-2 disabled" disabled>
                                        <option value="">Primero seleccione un ancho</option>
                                    </select>
                                </div>
                                <div class="col-4 d-flex flex-column">
                                    <label for="rin" class="ml-2">Rin</label>
                                    <select type="text" id="rin" class="form-field ml-2 disabled" disabled>
                                        <option value="">Primero seleccione un alto</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-3 text-center">
                    <img src="./src/img/rayin_like.png" alt="logo" class="mt-4" style="width: 190px;">
                </div>
            </div>
            

            
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-10" id="area-resultados">
        <div class="row">
            <div class="col-12 col-md-4 col-sm-8">
                <h4>Filtrar</h4>
                <div class="card p-3" style="border-radius: 15px !important;">
                <span><b>Sucursales</b></span>
                    <ul class="filtros_principales mt-2">
                        <?php 
                            $sel = "SELECT * FROM sucursal";
                            $stmt = $con->prepare($sel);
                            $stmt->execute();
                            $resultado_ = $stmt->get_result();
                            while($row = $resultado_->fetch_assoc()){
                                $id_sucursal = $row['id'];
                                $nombre_sucursal = $row['nombre'];
                                print_r("<li><input type='checkbox' id='sucursal_id_$id_sucursal' class='checkbox_grande'>$nombre_sucursal</li>");
                            };
                        ?>
                            
                    </ul> 
                    <hr class="m-0">

                    <span><b>Aplicación</b></span>
                    <ul class="filtros_principales mt-2">
                        <?php 
                            $sel = "SELECT * FROM aplicacion";
                            $stmt = $con->prepare($sel);
                            $stmt->execute();
                            $resultado_ = $stmt->get_result();
                            while($row = $resultado_->fetch_assoc()){
                                $id_aplicacion = $row['id'];
                                $aplicacion = $row['nombre'];
                                print_r("<li><input type='checkbox' id='aplicacion_id_$id_aplicacion' class='checkbox_grande'>$aplicacion</li>");
                            };
                        ?>
                            
                    </ul> 
                    <hr class="m-0">
                    <span><b>Tipo de vehiculo</b></span>
                    <ul class="filtros_principales mt-2">
                         <?php 
                            $sel = "SELECT * FROM tipo_vehiculos";
                            $stmt = $con->prepare($sel);
                            $stmt->execute();
                            $resultado_ = $stmt->get_result();
                            while($row = $resultado_->fetch_assoc()){
                                $id_tipo_vehiculo = $row['id'];
                                $tipo_vehiculo = $row['nombre'];
                                print_r("<li><input type='checkbox' id='tipo_vehiculo_id_$id_tipo_vehiculo' class='checkbox_grande'>$tipo_vehiculo</li>");
                            };
                        ?>
                    
                    </ul>
                </div>
            </div>
            <div class="col-12 col-md-8 col-sm-8">
                <h4 id="titulo-busqueda">Promociones:</h4>
                <div id="contenedor-resultados-llantas">
                <div class="row">
                    <?php
                        $count = "SELECT COUNT(*) FROM vista_promociones v INNER JOIN inventario i ON i.id_Llanta = v.id WHERE v.promocion = 1";
                        $stmt = $con->prepare($count);
                        $stmt->execute();
                        $stmt->bind_result($numero_promociones);
                        $stmt->fetch();
                        $stmt->close();

                        if($numero_promociones>0){
                            $sel = "SELECT v.*,l.url_principal, i.Stock, i.Sucursal FROM vista_promociones v LEFT JOIN llantas_imagenes l ON
                            l.id_llanta = v.id INNER JOIN inventario i ON i.id_Llanta = v.id WHERE v.promocion = 1";
                            $stmt = $con->prepare($sel);
                            $stmt->execute();
                            $resultado_ = $stmt->get_result();
                            while($row = $resultado_->fetch_assoc()){
                        
                                if($row['url_principal']!=null){
                                    $url_imagen = $row['url_principal'];
                                }else{
                                    $url_imagen = 'NA.jpg';
                                }
                                echo '
                                <div class="col-4">
                                    <div onclick="previsualizarNeumatico('.$row['id'].')" class="card card_promocion p-3 mb-3">
                                        <img src="./src/img/neumaticos/'.$url_imagen.'" class="mb-2">
                                        <div class="p-2">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="text-dark"><b>'.$row['Descripcion'].'</b></div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class"col-md-6">
                                                    <div class="text">'.$row['Marca'].' ---- </div>
                                                </div>
                                                <div class"col-md-6">    
                                                    <div class="text"> Stock:<b>'.$row['Stock'].'</b></div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class"col-md-12">
                                                    <div class="text">Sucursal: '.$row['Sucursal'].'</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mt-2 btn btn-danger">Agregar</div>
                                    </div>
                                </div>';
                            }
                        }
                    ?>
                    
                </div>
                <!-- <div class="card" style="border-radius: 15px !important; box-shadow: rgba(0,0,0,0.1) 0 0 30px 0;">
                    <div class="m-3">No se encontraron resultados para esta busqueda.</div>
                </div> -->
                </div>
            </div>
        </div>
    </div>
</div>