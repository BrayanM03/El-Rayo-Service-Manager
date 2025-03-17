<div class="row mt-5">
    <div class="col-12 col-md-12 col-sm-12">
        <div class="card" style="margin-bottom: 2vh; padding-bottom: 5vh;">
            <div class="row justify-content-center">
                <div class="col-12 col-md-3 text-center">
                    <img src="./src/img/logo.jpg" alt="logo" class="mt-4" style="width: 140px;">
                </div>
                <div class="col-12 col-md-6 text-center">
                    <div class="row justify-content-center">
                        <div class="col-12 col-md-5 text-center">
                            <h3 class="titulo-nueva-venta">Nueva venta</h3>
                            <p class="ml-4" id="texto-modo-venta">Modo de venta:
                                <span>
                                    <a style="color: tomato; text-shadow:#ff4a65 3px 0 10px; cursor:pointer; text-decoration: none;" href="punto-venta.php">Servicios</a>
                                </span>
                            </p>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-11">
                            <label for="cliente">Cliente</label>
                            <select id="cliente" onchange="setLocalStorageCliente(2)" class="form-control selectpicker" data-live-search="true">

                            </select>
                        </div> 
                        <div class="col-1">   
                        <label for="">&ensp;<span style="color:white">.LOL</span></label>
                            <div class="btn btn-info" onclick="agregarCliente()"><i class="fas fa-user-plus"></i></div>
                        </div> 
                    </div>
                    <div class="row justify-content-center">
                        <div class="col-12 col-md-5 m-3 text-center">
                            <span>Elija el servicio requerido</span>
                        </div>
                    </div>
                    <div class="row justify-content-center">
                        <div class="col-10 col-md-12 col-sm-10">
                            <div class="row">
                                <div class="col-12">
                                    <input class="form-field" type="text" id="buscador-servicios" placeholder="Balanceo, Instalación, Valvula, etc...">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-3 text-center">
                    <img src="./src/img/mascota.png" alt="logo" class="mt-4" style="width: 190px;">
                </div>
            </div>



        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-6 col-12" id="area-resultados">
        <?php
        $count = "SELECT COUNT(*) FROM servicios s WHERE s.estatus = 'activo'";
        $stmt = $con->prepare($count);
        $stmt->execute();
        $stmt->bind_result($numero_servicios);
        $stmt->fetch();
        $stmt->close();

        if ($numero_servicios > 0) {
            $sel = "SELECT * FROM servicios s WHERE s.estatus = 'activo'";
            $stmt = $con->prepare($sel);
            $stmt->execute();
            $resultado_ = $stmt->get_result();
            while ($row = $resultado_->fetch_assoc()) {
        ?>
                <div class="row">
                    <div class="card mb-3 card_busqueda" onclick="previsualizarNeumaptico(<?= $row['id'] ?>)">
                        <article class="tire-teaser">
                            <div class="row">
                                <div class="col-12 col-md-2">
                                    <a>
                                        <img alt="" src="src/img/services/<?= $row['img'] ?>.png" class="mt-4 ml-4" style="width:100px; border-radius:10px">
                                    </a>
                                </div>
                                <div class="col-12 col-md-10 tire-teaser__right mt-3 mb-3">
                                    <div class="row">
                                        <div class="col-12 col-md-7">
                                            <h4><b><?= $row['descripcion'] ?></b></h4>
                                        </div>
                                        <div class="col-12 col-md-3">
                                            <div style="display:flex">
                                                <div class="btn btn-info" style="border-radius:10px 0px 0px 10px !important;" onclick="aumentarCantidadServicio(0,'cantidad_id_<?=$row['codigo']?>',event)"><b>-</b></div>
                                                <input type="number" id="cantidad_id_<?=$row['codigo']?>" style="border-radius:0px !important;" class="form-control" placeholder="0" value="1">
                                                <div class="btn btn-info" style="border-radius:0px 10px 10px 0px !important;" onclick="aumentarCantidadServicio(1,'cantidad_id_<?=$row['codigo']?>',event)"><b>+</b></div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-2 text-right">
                                            <div class="btn btn-warning mr-3" onclick="agregarPreventa(<?=$row['id']?>, '<?=$row['codigo']?>', 0, 2, 'cantidad_id_<?=$row['codigo']?>', event, 0, 0,0)">Agregar</div>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-12 col-md-9">
                                            <!-- <span><b>Sucursal:</b> ${element.nombre}</span><br>
                                            <span><b>Stock:</b> ${element.Stock}</span> -->
                                            <span class="ml-3"><b>Codigo:</b> <?=$row['codigo']?></span>
                                            <div class="row p-2 mt-2">
                                                <div class="col-6 col-md-6 text-center">
                                                    <div style="background-color:#FF7F50; width: 80%; color:white; border-radius:8px" class="p-2">
                                                        <span>Precio normal</span><br>
                                                        <h3><b><?=$row['precio']?></b></h3>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- <div class="col-12 col-md-3 text-right mt-3">
                                            <div class="div mr-3">
                                                <span><b>Marca:</b> ${element.Marca}</span>
                                                <img alt="" src="./src/img/logos/${element.Marca}.jpg" style="width:110px; border-radius:10px">
                                            </div>
                                        </div> -->
                                    </div>
                                </div>
                            </div>
                        </article>
                    </div>
                </div>
        <?php
            }
        }
        ?>

    </div>
</div>