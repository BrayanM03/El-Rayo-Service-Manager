<div class="row">
    <div class="col-12">
        <h3><b>Reporte de llantas vendidas</b></h3>
    </div>
    <div class="col-3">
        <label>Fecha inicio</label>
        <input class="form-control" type="date" id="fecha-inicio">
    </div>

    <div class="col-3">
        <label>Fecha Fin</label>
        <input class="form-control" type="date" id="fecha-final">
    </div>

    <div class="col-3">
        <label>Sucursales</label><br>
        <?php 
        if($_SESSION['rol']==1){
            $disabled ='';
        }else{
            $disabled = 'disabled';
        }
        ?>
        <select class="selectpicker form-control" id="id-sucursal" multiple data-actions-box="true" <?= $disabled; ?>>
            
        <?php
                $select = "SELECT COUNT(*) FROM sucursal";
                $res = $con->prepare($select);
                $res->execute();
                $res->bind_result($total_s);
                $res->fetch();
                $res->close();

                if ($total_s > 0) {

                    $consultar = "SELECT id, nombre FROM sucursal";
                    $resp = $con->prepare($consultar);
                    $resp->execute();
                    $resultado = $resp->get_result();
                    while ($data = $resultado->fetch_assoc()) {
                        $session = $_SESSION['id_sucursal'];
                        if($session==$data['id']){
                            $selected = 'selected';
                        }else{
                            $selected ='';
                        }
                       
                        echo '<option value="' . $data['id'] . '" '.$selected.'>' . $data['nombre'] . '</option>';
                    }
                    $resp->close();
                }
        ?>
        </select>
    </div>

    <div class="col-3">
<!--         <div class="btn btn-success" style="margin-top:30px" onclick="reporteLlantasVendidas()">Buscar</div> -->
    </div>
</div>   
<div class="row mt-2">
    <div class="col-3">
        <label for="id-marca">Marca</label><br>
        <div class="form-group">
        <select class="selectpicker form-control" data-width="auto" data-live-search="true" id="id-marca" multiple <?= $disabled; ?>>
        <?php
                $select = "SELECT COUNT(*) FROM marcas";
                $res = $con->prepare($select);
                $res->execute();
                $res->bind_result($total_s);
                $res->fetch();
                $res->close();

                if ($total_s > 0) {

                    $consultar = "SELECT id, nombre, imagen FROM marcas";
                    $resp = $con->prepare($consultar);
                    $resp->execute();
                    $resultado = $resp->get_result();
                    while ($data = $resultado->fetch_assoc()) {
                       
                        echo '<option value="' . $data['imagen'] . '">' . $data['nombre'] . '</option>';
                    }
                    $resp->close();
                }
        ?>
        </select>
        </div>
    </div>
    <div class="col-3">
        <label for="ancho">Ancho</label>
        <input type="number" id="ancho" class="form-control" placeholder="Ejem. 285"/>
    </div>
    <div class="col-2">
    <label for="ancho">Alto</label>
        <input type="number" id="alto" class="form-control" placeholder="Ejem. 60"/>
    </div>
    <div class="col-2">
    <label for="ancho">Rin</label>
        <input type="number" id="rin" class="form-control" placeholder="Ejem. 17"/>
    </div>
    <div class="col-2">
        <div class="btn btn-success" style="margin-top:30px" onclick="reporteLlantasVendidas()">Buscar</div>
    </div>
</div>
<div class="row mt-3">
    <div class="col-12">
        <table class="table" id="llantas-vendidas">

            <thead class="bg-dark text-white">
                <th>#</th>
                <th>Descripción</th>
                <th>Marca</th>
                <th>Pz vendidas</th>
                <th>RAY</th>
                <th>Sucursal</th>
                <th>Vendedor</th>
                <th>Cliente</th>
                <th>Fecha</th>
                <th>Hora</th>
            </thead>

            <tbody id = "tbody-llantas-vendidas">

                <tr style="background-color:white">
                    <th colspan="10" class="text-center p-3">Sin datos</th>
                </tr>

            </tbody>

        </table>
    </div>
</div>

<div class="row mt-3">
    <div class="col-4">
        <table class="table" id="llantas-vendidas-x-marca">

            <thead class="bg-dark text-white">
                <th>#</th>
                <th>Marca</th>
                <th>Pz vendidas</th>
            </thead>

            <tbody id = "tbody-llantas-vendidas-x-marca">

                <tr style="background-color:white">
                    <th colspan="10" class="text-center p-3">Sin datos</th>
                </tr>

            </tbody>

        </table>
    </div>
</div>
