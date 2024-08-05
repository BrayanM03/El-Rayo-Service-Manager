
                        <div class="card">
                            <span href="#" class="list-group-item">
                                <b>Ajustes principales</b>
                            </span>
                            <!-- <a href="#" class="list-group-item list-group-item-action">Editar mis datos de usuario (inactivo)</a> -->
                            <a href="lista-marcas.php?id=0&nav=marcas" class="list-group-item list-group-item-action">Lista de marcas</a>
                            <a href="configuracion-stock.php?id=0&nav=confg-stock" class="list-group-item list-group-item-action">Configuración de stock</a>
                            <a href="comisiones.php?id=0&nav=comisiones" class="list-group-item list-group-item-action">Comisiones</a>
                            <a href="promociones.php?id=0&nav=promociones" class="list-group-item list-group-item-action">Promociones</a>
                           <!--  <a href="corte.php?id=0&nav=corte" class="list-group-item list-group-item-action">Realizar corte</a>
                            <a href="historial-cortes.php?id=0&nav=cortes" class="list-group-item list-group-item-action">Historial de cortes</a> -->
                            <?php 
                                if($_SESSION['user'] =="brayanm03"){
                            ?>
                            <span href="#" class="list-group-item">
                            <b>Ajustes de sucursales</b>
                            </span>
                                
                            <a href="sucursales.php?id=0&nav=sucursales" class="list-group-item list-group-item-action">Agregar sucursal</a>
                               <?php 
                                }
                                ?>
                         

                            <a href="#" class="list-group-item list-group-item-action">Cerrar sesión</a>
                            </div> 