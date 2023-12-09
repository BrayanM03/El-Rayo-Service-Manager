(function (Vue) {
    function onDocumentReady(callback) {
        if (document.readyState === "complete" || (document.readyState !== "loading" && !document.documentElement.doScroll)) {
          callback()
        } else {
          document.addEventListener("DOMContentLoaded", callback)
        }
      }
    
      function getUserFilter() {
    
        return {
          data: function () {
            return {
              results: []
            }
          },
          props: ['tabla', 'columnas', 'columna_id', 'modo_usuario', 'rol_sesion', 'id_sesion', 'nombre_sesion', 'id_sucursal'],
    
          template: `
               <div>
                 <select :id="'buscador-' + modo_usuario" class="form-control selectpicker" multiple data-live-search="true">
                 </select>
               </div>
             `,
          mounted() {
            const vm = this; // Almacenamos una referencia al componente Vue
            let buscador = $(`#buscador-${this.modo_usuario}`);
            buscador.selectpicker('refresh');
            let input_buscador = buscador.parent().find('.bs-searchbox > input');
            let timeoutId;
            vm.id_sucursal = document.getElementById('titulo-hv').getAttribute('id_sucursal');
            if (vm.rol_sesion !=78) {
              input_buscador.on('keyup', (e) => {
                clearTimeout(timeoutId);
                //buscador.selectpicker('refresh')
                timeoutId = setTimeout(function () {
                  // Aquí se puede hacer la solicitud a la base de datos
                  $.ajax({
                    type: "post",
                    url: "./modelo/filtros/busqueda-select.php",
                    data: { 'term': input_buscador.val(), 'page': 1, 'tabla': vm.tabla, 'columnas': vm.columnas, 'columna_id': vm.columna_id, 'rol_sesion': vm.rol_sesion },
                    dataType: 'json',
                    success: function (response) {
                      if (response.estatus) {
                        vm.results = response.datos; // Usamos la referencia a vm para actualizar results
                        vm.results.forEach(element => {
                          buscador.append(`
                                <option value="${element.id}">${element.nombre} ${element.apellidos}</option>
                            `)
                        });
                        buscador.selectpicker('refresh');
                      }
                    }
                  });
                }, 200);
              });
            } else { //Este codigo sera temporal en lo que encuentro una forma de hacer mejor el filtrado
             // buscador.prop('disabled', true);
         
                  buscador.empty().append(`
                           <option value="${vm.id_sesion}">${vm.nombre_sesion}</option>
                       `)
                  buscador.selectpicker('refresh');
                  $(`.selectpicker[data-id='buscador-${this.modo_usuario}']`).addClass("disabled");
    
                  if(vm.modo_usuario=='asesor'){
                    buscador.on('change', ()=>{
                      // Obtén el valor de la opción seleccionada actualmente
                      var valorActualSeleccionado = buscador.val();
                      if(valorActualSeleccionado.length ==0){
                        $('#buscador-sucursal').empty();
                        sucursales_actuales.forEach(element => {
                          $('#buscador-sucursal').append(`
                           <option value="${element.id}">${element.nombre}</option>
                       `)
    
                       $('#buscador-sucursal').val(vm.id_sucursal)
                       $('#buscador-sucursal').selectpicker('refresh');
                        });
                       buscador.selectpicker('refresh');
                      }else{
                        $('#buscador-sucursal').val('');
                        $(`#buscador-sucursal`).selectpicker('refresh');
                      }
                     })
                  }  
            }
    
          }
    
        }
    
      }

      function getProviderFilter(){
        return {
          data: function () {
            return {
              results: []
            }
          },
          props: ['tabla', 'columnas', 'columna_id', 'modo_usuario', 'rol_sesion', 'id_sesion', 'nombre_sesion', 'id_sucursal'],
    
          template: `
               <div>
                 <select id="buscador-proveedor" class="form-control selectpicker" multiple data-live-search="true">
                 </select>
               </div>
             `,
          mounted() {
            const vm = this; // Almacenamos una referencia al componente Vue
            let buscador = $(`#buscador-proveedor`);
            buscador.selectpicker('refresh');
            let input_buscador = buscador.parent().find('.bs-searchbox > input');
            let timeoutId;
            vm.id_sucursal = document.getElementById('titulo-hv').getAttribute('id_sucursal');
            if (vm.rol_sesion !=78) {
              input_buscador.on('keyup', (e) => {
                clearTimeout(timeoutId);
                //buscador.selectpicker('refresh')
                timeoutId = setTimeout(function () {
                  // Aquí se puede hacer la solicitud a la base de datos
                  $.ajax({
                    type: "post",
                    url: "./modelo/filtros/busqueda-select.php",
                    data: { 'term': input_buscador.val(), 'page': 1, 'tabla': vm.tabla, 'columnas': vm.columnas, 'columna_id': vm.columna_id, 'rol_sesion': vm.rol_sesion },
                    dataType: 'json',
                    success: function (response) {
                      if (response.estatus) {
                        vm.results = response.datos; // Usamos la referencia a vm para actualizar results
                        vm.results.forEach(element => {
                          buscador.append(`
                                <option value="${element.id}">${element.nombre}</option>
                            `)
                        });
                        buscador.selectpicker('refresh');
                      }
                    }
                  });
                }, 200);
              });
            } else { //Este codigo sera temporal en lo que encuentro una forma de hacer mejor el filtrado
             // buscador.prop('disabled', true);
         
                  buscador.empty().append(`
                           <option value="${vm.id_sesion}">${vm.nombre_sesion}</option>
                       `)
                  buscador.selectpicker('refresh');
                  $(`.selectpicker[data-id='buscador-${this.modo_usuario}']`).addClass("disabled");
    
                  if(vm.modo_usuario=='asesor'){
                    buscador.on('change', ()=>{
                      // Obtén el valor de la opción seleccionada actualmente
                      var valorActualSeleccionado = buscador.val();
                      if(valorActualSeleccionado.length ==0){
                        console.log(sucursales_actuales);
                        console.log(vm.id_sucursal);
                        $('#buscador-sucursal').empty();
                        sucursales_actuales.forEach(element => {
                          $('#buscador-sucursal').append(`
                           <option value="${element.id}">${element.nombre}</option>
                       `)
    
                       $('#buscador-sucursal').val(vm.id_sucursal)
                       $('#buscador-sucursal').selectpicker('refresh');
                        });
                       buscador.selectpicker('refresh');
                      }else{
                        $('#buscador-sucursal').val('');
                        $(`#buscador-sucursal`).selectpicker('refresh');
                      }
                     })
                  }  
            }
    
          }
    
        }
      }
    
      function getBrandFilter() {
    
        return {
          data: function () {
            return {
              results: []
            }
          },
          props: ['tabla', 'columnas', 'columna_id'],
          template: `
            <div>
              <select id="buscador-marcas" class="form-control selectpicker" multiple data-live-search="true">
              </select>
            </div>
          `,//  <option v-for="(result, index) in results" :value="result.id">{{result.Nombre}}</option>
          mounted() {
            //getCustomers(this._props)
            const vm = this; // Almacenamos una referencia al componente Vue
            let buscador = $(`#buscador-${this.tabla}`);
            buscador.selectpicker('refresh');
            let input_buscador = buscador.parent().find('.bs-searchbox > input');
            let timeoutId;
    
            input_buscador.on('keyup', (e) => {
              //clearTimeout(timeoutId);
    
              //buscador.selectpicker('refresh')
              timeoutId = setTimeout(function () {
                // Aquí se puede hacer la solicitud a la base de datos
                $.ajax({
                  type: "post",
                  url: "./modelo/filtros/busqueda-select.php",
                  data: { 'term': input_buscador.val(), 'page': 1, 'tabla': vm.tabla, 'columnas': vm.columnas, 'columna_id': vm.columna_id },
                  dataType: 'json',
                  success: function (response) {
                    if (response.estatus) {
                      buscador.empty();
                      vm.results = response.datos;
                      vm.results.forEach(element => {
                        buscador.append(`
                          <option value="${element.id}">${element.Nombre}</option>
                      `)
                      });
                      buscador.selectpicker('refresh')
                    }
    
                  }
                });
              }, 400);
            });
          }
    
        }
    
      }
    
      function getStoreFilter() {
    
        return {
          data: function () {
            return {
              results: []
            }
          },
          props: ['tabla', 'columnas', 'columna_id', 'tipo_sucursal'],
    
          template: `
            <div>
              <select :id="'sucursal-' + tipo_sucursal" class="form-control selectpicker" data-live-search="true">
                  <option>Seleccione una sucursal</option>
                  <option value="0">Bodega</option>
                  <option v-for="(result, index) in results" :value="result.id">{{result.nombre}}</option>
              </select>
            </div>
          `,
          mounted() {
            const vm = this; // Almacenamos una referencia al componente Vue
            let buscador = $(`#sucursal-${this.tipo_sucursal}`);
            let input_buscador = buscador.parent().find('.bs-searchbox > input');
            // Aquí se puede hacer la solicitud a la base de datos
            $.ajax({
              type: "post",
              url: "./modelo/filtros/busqueda-select.php",
              data: { 'term': input_buscador.val(), 'page': 1, 'tabla': vm.tabla, 'columnas': vm.columnas, 'columna_id': vm.columna_id },
              dataType: 'json',
              success: function (response) {
                sucursales_actuales = response.datos;
                let rol_sesion = $('#titulo-hv').attr('rol')
                let id_sesion = document.getElementById('titulo-hv').getAttribute('id_usuario');
    
                if(rol_sesion == 78) {
                  buscador.prop('disabled', true);
                  let sucursal_nombre = $('#titulo-hv').attr('sucursal')
                  let sucursal_id = $('#titulo-hv').attr('id_sucursal')
                  
                  $('#buscador-sucursal').empty().
                  append(`
                        <option value="${sucursal_id}">${sucursal_nombre}</option>            
                    `)
                    $('#buscador-sucursal').selectpicker('refresh');
      
              }else{
                vm.results = response.datos;
                setTimeout(() => {
                  buscador.selectpicker('refresh')
                }, 100)
                buscador.selectpicker('render');
              }  
              }
            });
          }
    
        }
    
      }
    
      function getTyreSize() {
        return {
          data: function () {
            return {
              results: []
            }
          },
          props: ['tabla', 'columna', 'columna_id'],
    
          template: `
              <div>
                <select :id="columna" class="form-control">
                    <option value="null">Seleccione un {{columna}}</option>
                    <option v-for="(result, index) in results" :value="result">{{result}}</option>
                </select>
              </div>
            `,
          mounted() {
            const vm = this; // Almacenamos una referencia al componente Vue
            let buscador = $(`#filtro-medida-${vm.columna}`);
            $.ajax({
              type: "post",
              url: "./modelo/filtros/filtros-medidas.php",
              data: { 'page': 1, 'tabla': vm.tabla, 'columna': vm.columna, 'columna_id': vm.columna_id },
              dataType: 'json',
              success: function (response) {
                if (response.estatus) {
                  vm.results = response.medidas; // Usamos la referencia a vm para actualizar results
                  //buscador.selectpicker('refresh')
                }
              }
            });
    
    
          }
    
        }
      } 

      function inicializarFiltrosMovimientos(rol_sesion, id_sesion, nombre_sesion, id_sucursal) {
    return new Vue({
      el: '#filter-movimientos-container',
      components: {
        'filtro-busqueda-usuario': getUserFilter(),
        'filtro-busqueda-proveedor': getProviderFilter(),
        'filtro-medidas': getTyreSize(),
        'filtro-marcas': getBrandFilter(),
        'filtro-sucursales': getStoreFilter(),

      },
      data: {
        usuarios: {
          columnas: ['nombre', 'apellidos', 'usuario', 'id_sucursal'],
          columna_id: 'id',
          tabla: 'usuarios',
          rol_sesion: rol_sesion,
          id_sesion: id_sesion,
          id_sucursal: id_sucursal,
          nombre_sesion: nombre_sesion
        },
        medida: {
          //columnas: ['Ancho', 'Proporcion', 'Diametro'],
          tabla: 'llantas',
          columna_id: 'id',
        },
        marca: {
          tabla: 'marcas',
          columnas: ['Nombre'],
          columna_id: 'id',
        },
        sucursal: {
          tabla: 'sucursal',
          columnas: ['Nombre'],
          columna_id: 'id',
          tipo_ubicacion: 'ubicacion',
          tipo_destino: 'destino'
        },
        proveedor: {
          tabla: 'proveedores',
          columnas: ['nombre'],
          columna_id: 'id'
        },
      },
      template: `
      <div id="contenedor-filtros">
      <div class="titulo-inventario d-none" id="titulo-hv" ></div>
                        <div class="row">
                            <div class="col-md-6">
                                <h3><b>Historial de movimientos</b></h3>
                            </div>
                        </div>
                        <hr>
                        <!-- Filtros -->
                        <span style="font-size:16px">Filtros de busqueda</span>
                        <div class="row mt-3">
                            <div class="col-md-2">
                                <b>Folio</b>
                                <input type="text" class="form-control" placeholder="Folio" id="filtro-folio">
                            </div>
                            <div class="col-md-2">
                                <b>Factura</b>
                                <input type="text" class="form-control" placeholder="Factura" id="filtro-factura">
                            </div>
                            <div class="col-md-2">
                                <b>Fecha inicial</b>
                                <input type="date" id="filtro-fecha-inicial" class="form-control">
                            </div>
                            <div class="col-md-2">
                                <b>Fecha final</b>
                                <input type="date" id="filtro-fecha-final" class="form-control">
                            </div>
                            <div class="col-md-2">
                                <b>Ubicación</b>
                                <filtro-sucursales :tabla="sucursal.tabla" :columnas="sucursal.columnas" :tipo_sucursal="sucursal.tipo_ubicacion" :columna_id="sucursal.columna_id"></filtro-sucursales>
                            </div>
                            <div class="col-md-2">
                                <b>Sucursal destino</b>
                                <filtro-sucursales :tabla="sucursal.tabla" :columnas="sucursal.columnas" :tipo_sucursal="sucursal.tipo_destino" :columna_id="sucursal.columna_id"></filtro-sucursales>
                            </div>
                        </div>

                        <div class="row mt-3 mb-3">
                            <div class="col-md-2">
                                <b>Marca de la llanta</b>
                                <filtro-marcas :tabla="marca.tabla" :columnas="marca.columnas" :columna_id="marca.columna_id"></filtro-marcas>
                            </div>
                            <div class="col-md-2">
                                <b>Ancho</b>
                                <filtro-medidas :tabla="medida.tabla" :columna_id="medida.columna_id" columna="Ancho"></filtro-medidas>
                            </div>
                            <div class="col-md-2">
                                <b>Alto</b>
                                <filtro-medidas :tabla="medida.tabla" :columna_id="medida.columna_id" columna="Proporcion"></filtro-medidas>
                            </div>
                            <div class="col-md-2">
                                <b>Rin</b>
                                <filtro-medidas :tabla="medida.tabla" :columna_id="medida.columna_id" columna="Diametro"></filtro-medidas>
                            </div>
                            <div class="col-md-2">
                            <b>Usuario</b>
                                <filtro-busqueda-usuario :rol_sesion="usuarios.rol_sesion" :id_sesion="usuarios.id_sesion" :nombre_sesion="usuarios.nombre_sesion" :tabla="usuarios.tabla" :columnas="usuarios.columnas" :columna_id="usuarios.columna_id"></filtro-busqueda-usuario>
                            </div>
                            <div class="col-md-2">
                            <b>Proveedor</b>
                                <filtro-busqueda-proveedor :rol_sesion="usuarios.rol_sesion" :id_sesion="usuarios.id_sesion" :nombre_sesion="usuarios.nombre_sesion" :tabla="proveedor.tabla" :columnas="proveedor.columnas" :columna_id="proveedor.columna_id"></filtro-busqueda-proveedor>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-2">
                                <b>Tipo</b>
                                <select class="form-control selectpicker" multiple data-live-search="true" id="filtro-tipo">
                                    <option value="null">Selecciona un tipo</option>
                                    <option value="2">Movimiento</option>
                                    <option value="2">Ingreso</option>
                                    <option value="3">Retiro</option>
                                    <option value="5">Borrado</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <b>Estatus</b>
                                <select class="form-control selectpicker" multiple data-live-search="true" id="filtro-estatus">
                                    <option value="null">Selecciona un estatus</option>
                                    <option value="Completado">Completado</option>
                                    <option value="Pendiente">Pendiente</option>
                                    <option value="Cancelada">Cancelado</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <b>Estado factura</b>
                                <select class="form-control selectpicker" multiple data-live-search="true" id="filtro-estado">
                                    <option value="null">Selecciona un estatus</option>
                                    <option value="1">Sin factura</option>
                                    <option value="2">Factura completa</option>
                                    <option value="3">Factura incompleta</option>
                                </select>
                            </div>
                            <div class="col-md-1 mt-3 text-right">
                                <a href="#" onclick="procesarFiltrosMovimientos()">
                                    <div class="btn btn-info">
                                        <span class="icon text-white-50">
                                            <i class="fas fa-search"></i>
                                        </span>
                                        <span class="text">Filtrar</span>
                                    </div>
                                </a>
                            </div>
                           <!-- <div class="col-md-1 mt-3 text-left">
                                <a href="#" onclick="descargarReporteFiltro()">
                                    <div class="btn btn-success">
                                        <span class="icon text-white-50">
                                            <i class="fas fa-download"></i>
                                        </span>
                                        <span class="text">Descargar</span>
                                    </div>
                                </a>
                            </div>--->
                        </div>
        <hr>
        </div>
      `,

      mounted() {
        let filtro_tipo = $(`#filtro-tipo`);
        filtro_tipo.selectpicker('refresh');
        let filtro_estatus = $(`#filtro-estatus`);
        filtro_estatus.selectpicker('refresh');
        let filtro_estado = $(`#filtro-estado`);
        filtro_estado.selectpicker('refresh');
      },
    });
      }

  onDocumentReady(function () {
    // 2. Una vez listo, creamos nuestro widget con Vue.
    let rol_sesion = document.getElementById('titulo-hv').getAttribute('rol');
    let nombre_sesion = document.getElementById('titulo-hv').getAttribute('nombre_usuario');
    let id_sesion = document.getElementById('titulo-hv').getAttribute('id_usuario');
    let id_sucursal = document.getElementById('titulo-hv').getAttribute('id_sucursal');
    let commentsWidget =  inicializarFiltrosMovimientos(rol_sesion, id_sesion, nombre_sesion, id_sucursal)

    // Aquí tienes la referencia al widget completo. Puedes hacer con ella lo que gustes.
    // Yo he decidido exponerlo como una propiedad de objeto window.
    window.commentsWidget = commentsWidget

  })

})(window.Vue)