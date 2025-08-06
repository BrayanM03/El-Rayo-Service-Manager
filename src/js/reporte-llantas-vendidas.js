let fecha = document.getElementById('fecha-inicio').valueAsDate = new Date();

reporteLlantasVendidas()
function reporteLlantasVendidas(){
    var tabla_ventas = $('#llantas-vendidas');

    fecha_inicio= $("#fecha-inicio").val()
    fecha_final= $("#fecha-final").val()

    id_marca= $("#id-marca").val()
    ancho= $("#ancho").val()
    alto= $("#alto").val()
    rin= $("#rin").val()

    let sucursal= $("#id-sucursal").val()
   /*  table.destroy()
    if ($.fn.DataTable.isDataTable( '#llantas-vendidas' ) ) {
        tabla_ventas.destroy();
      } */
    tabla_ventas.empty();
    tabla_ventas.append(`
    <div class="row" style="background-color:white !important;">
        <div class="col-12 col-md-12 text-center"><img src="./src/img/preload.gif" style="width:70px;"><br></img>Buscando...</div>
    </div>
    `);
    $.ajax({
        type: "post",
        url: "./modelo/panel/reporte-llantas-vendidas.php",
        data: {fecha_inicio, fecha_final, sucursal, id_marca, ancho, alto, rin},
        dataType: "JSON",
        success: function (response) {

            if(response.estatus){
                
                //Conversion de arreglo de objectos a arreglos de arrays
                response.data = response.data.length == 0 ? [] : response.data;
                const data_convertida = response.data.map(objeto => [
                    '', 
                    objeto.Descripcion,
                    objeto.Marca,
                    objeto.Cantidad,
                    objeto.ray,
                    objeto.sucursal,
                    objeto.vendedor,
                    objeto.Nombre_cliente,
                    objeto.Fecha,
                    objeto.hora

                ]);
                clearTimeout();
                setTimeout(function(){
                tabla_ventas.empty();
                tabla_ventas.DataTable({    
                rowCallback: function(row, data, index) {
                    var info = this.api().page.info();
                    var page = info.page;
                    var length = info.length;
                    var columnIndex = 0; // Índice de la primera columna a enumerar
                    row.style.backgroundColor = 'white'
                    $('td:eq(' + columnIndex + ')', row).html(page * length + index + 1);
                  },
                "bDestroy": true,
                columns: [   
                { title: '#' },
                { title: 'Descripción'},
                {title:  'Marca'},
                { title: 'Pz vendidas'},
                { title: 'RAY'},
                { title: 'Sucursal'},
                { title: 'Vendedor'},
                { title: 'Cliente'},
                { title: 'Fecha'},
                { title: 'Hora'},
                ],
                data: data_convertida,
                paging: true,
                searching: true,
               // scrollY: "50vh",
                info: false,
                responsive: false,
                ordering: "enable",
                multiColumnSort: true,
              });
            
              $("table.dataTable thead").addClass("table-dark")
              $("table.dataTable thead").addClass("text-white")
            
            },500);


            //Generar los datos de la tabla para el llenado de información de marcas x llanta
            tablaBody= document.getElementById('tbody-llantas-vendidas-x-marca')
            $("#tbody-llantas-vendidas-x-marca").empty()
            let contador_ =0;
          /*   let contador_ =0;
            response.tipo.forEach(element => {
              contador_++
              $("#tbody-marca-x-llanta").append(`
              <tr>
                <th>${contador_}</th>
                <th>${}</th>
                <th>Pz vendidas</th>
              </tr>
              `)
            }); */
            let pz_x_marca= response.tipo;
            for (const marca in pz_x_marca) {
              contador_++
              const cantidad = pz_x_marca[marca];
          
              const fila = document.createElement("tr");
              fila.style.backgroundColor='white'
          
              const celdaMarca = document.createElement("td");
              celdaMarca.textContent = marca;
          
              const celdaContador= document.createElement("td");
              const celdaCantidad = document.createElement("td");
              celdaContador.textContent = contador_;
              celdaCantidad.textContent = cantidad;
          
              fila.appendChild(celdaContador);
              fila.appendChild(celdaMarca);
              fila.appendChild(celdaCantidad);
          
              tablaBody.appendChild(fila);
            }

          }else{
            tabla_ventas.empty()
            .append(`
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
              `);
          
              $("#tbody-llantas-vendidas-x-marca").empty().append(`
              <tr style="background-color:white">
              <th colspan="10" class="text-center p-3">Sin datos</th>
          </tr>
              `)
          }
        }
    });

    
}