
<?php                      
                            $mes = date('m');
                            $sel = "CALL llantas_vendidas_por_mes(YEAR(CURDATE()));";
                            $stmt = $con->prepare($sel);
                            $stmt->execute();
                            $resultado_ = $stmt->get_result();
                            $piezas_contado = 0;
                            $piezas_credito = 0;
                            while($row = $resultado_->fetch_assoc()){
                                $id_sucursal = $row['id_sucursal'];
                                $mes_sql = isset($row['mes']) ? $row['mes'] : 0;
                                if($mes_sql==$mes && $_SESSION['id_sucursal'] == $id_sucursal){
                                    $piezas_contado += $row['camion_contado'];
                                    $piezas_credito += $row['camion_credito'];
                                }else{
                                    $piezas_contado += 0;
                                    $piezas_credito += 0;
                                }
                             
                            };
                            $stmt->close();
                            
                            $suma = $piezas_contado + $piezas_credito;
                            $meta = 200;

                            if($suma >= $meta){
                                $color_text = 'green';
                            }else{
                                $color_text = 'orange';
                            }
                        ?>
<div class="row mt-3">
    <div class="col-12 col-md-12 col-sm-12">
        <div class="card p-3">
            <div class="row">
            <div class="col-2">
                <label><b>Llantas de camión vendidas</b></label>
            </div>
            <div class="col-3">
                <label for="">Llantas a contado:</label>
                <h3><?= $piezas_contado?></h3>
            </div>

            <div class="col-3">
                <label for="">Llantas a credito:</label>
                <h3><?= $piezas_credito?></h3>
            </div>

            <div class="col-3">
                <label for="">META: ⏳🏆</label>
                <h3><b><span style="color:<?= $color_text?>"><?= $suma?></span> / <span style="color:blue"><?=$meta?></span></b></h3>
            </div>
            </div>
            
        </div>
    </div>
</div>
<div class="row mt-2">
    <div class="col-12 col-md-12 col-sm-12">
        <div class="card" style="margin-bottom: 2vh; padding-bottom: 5vh; padding-left:1rem; padding-right:1rem;">
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

                    <div class="row mt-3">
                        <div class="mr-3 col-md-10 col-12">
                            <label for="cliente">Cliente</label>
                            <select id="cliente" onchange="setLocalStorageCliente()" class="form-control selectpicker" data-live-search="true">

                            </select>
                        </div> 
                        <div class="col-2 col-md-1" style="margin:auto">   
                        <label for="">&ensp;<span style="color:white">.LOL</span></label>
                            <div class="btn btn-info" onclick="agregarCliente()"><i class="fas fa-user-plus"></i></div>
                        </div> 
                    </div>
                    <div class="row mt-2 justify-content-center">
                        <div class="col-12 col-md-5 m-3 text-center">
                            <span>Eliga la medida de su llanta con estos filtros</span>
                        </div>
                    </div>
                    <div class="row justify-content-center">
                        <div class="col-12 col-md-12 col-sm-8">
                            <div class="row">
                                <div class="col-md-4 col-12 d-flex flex-column">
                                    <label for="ancho" class="">Ancho</label>
                                    <select type="text" id="ancho" class="selectpicker w-100" data-live-search="true" onchange="cargarAltos()"></select>
                                </div>
                                <div class="col-md-4 col-12 d-flex flex-column">
                                    <label for="alto" class="ml-2">Alto</label>
                                    <select type="text" id="alto" class="form-field ml-2 disabled" disabled>
                                        <option value="">Primero seleccione un ancho</option>
                                    </select>
                                </div>
                                <div class="col-md-4 col-12 d-flex flex-column">
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
            <div class="col-12 col-md-3 col-sm-8">
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
                    <ul class="filtros_principales filtros-aplicaciones mt-2">
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
                    <span><b>Tipo de carga</b></span>
                    <ul class="filtros_principales mt-2">
                         <?php 
                            $sel = "SELECT * FROM tipo_cargas";
                            $stmt = $con->prepare($sel);
                            $stmt->execute();
                            $resultado_ = $stmt->get_result();
                            while($row = $resultado_->fetch_assoc()){
                                $id_tipo_carga = $row['id'];
                                $tipo_carga = $row['nombre'];
                                print_r("<li><input type='checkbox' id='tipo_carga_id_$id_tipo_carga' class='checkbox_grande'>$tipo_carga</li>");
                            };
                        ?>
                    
                    </ul>
                </div>
            </div> 
            <div class="col-12 col-md-9 col-sm-8">
                <h4 id="titulo-busqueda">Promociones:</h4>
                <div id="contenedor-resultados-llantas">
                <div class="row">
                    <?php
                        $count = "SELECT COUNT(*) FROM vista_promociones v INNER JOIN inventario i ON i.id_Llanta = v.id WHERE v.promocion = 1 AND i.Stock > 0";
                        $stmt = $con->prepare($count);
                        $stmt->execute();
                        $stmt->bind_result($numero_promociones);
                        $stmt->fetch();
                        $stmt->close();

                        if($numero_promociones>0){
                            $sel = "SELECT v.*,l.url_principal, i.Stock, i.Sucursal, i.Codigo, i.id_Sucursal FROM vista_promociones v LEFT JOIN llantas_imagenes l ON
                            l.id_llanta = v.id INNER JOIN inventario i ON i.id_Llanta = v.id WHERE v.promocion = 1 AND i.Stock > 0";
                            $stmt = $con->prepare($sel);
                            $stmt->execute();
                            $resultado_ = $stmt->get_result();
                            while($row = $resultado_->fetch_assoc()){
                                $precio_promo = '$' . number_format($row['precio_promocion'], 2);
                                $display_button = $_SESSION['id_sucursal'] != $row['id_Sucursal'] ? 'd-none' : '';
                                if($row['url_principal']!=null){
                                    $url_imagen = $row['url_principal'];
                                }else{
                                    $url_imagen = 'NA.JPG';
                                }
                                echo '
<div class="col-12 col-sm-6 col-md-4">
    <div onclick="previsualizarNeumatico('.$row['id'].')" class="card card_promocion p-3 mb-3">
        <div class="precio-promocion">'.$precio_promo.'</div>
        <img src="./src/img/neumaticos/'.$url_imagen.'" class="mb-2 img-fluid" alt="Imagen del neumático">
        <div class="p-2">
            <div class="row mb-2">
                <div class="col-12">
                    <div class="text-dark"><b>'.$row['Descripcion'].'</b></div>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-6">
                    <div class="text">'.$row['Marca'].'</div>
                </div>
                <div class="col-6">
                    <div class="text">Stock: <b>'.$row['Stock'].'</b></div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="text">Sucursal: '.$row['Sucursal'].'</div>
                </div>
            </div>
        </div>
        <button class="mt-2 '. $display_button .' btn btn-danger w-100" 
                onclick="agregarPreventa('.$row['id'].', `'.$row['Codigo'].'`, '.$row['id_Sucursal'].', 1, `cantidad_id_'.$row['Codigo'].'`, event, 0, 1, 1)">
            Agregar
        </button>
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