
toastr.options = {
    "closeButton": true,
    "debug": false,
    "newestOnTop": false,
    "progressBar": true,
    "positionClass": "toast-bottom-right",
    "preventDuplicates": false,
    "onclick": null,
    "showDuration": "300",
    "hideDuration": "1000",
    "timeOut": "5000",
    "extendedTimeOut": "1000",
    "showEasing": "swing",
    "hideEasing": "linear",
    "showMethod": "fadeIn",
    "hideMethod": "fadeOut"
  } 

  //Funcion que carga el loader
function load(flag) {
    if (flag) {
      $("#contenedor-loader").removeClass("d-none"); //True desaparece animacion
    } else {
      $("#contenedor-loader").addClass("d-none");
    }
  }

function generarPrenomina(){
    load(true);
    let sucursales = $("#sucursales").val()
    let semana = $("#semana").val()
    
    if(sucursales.length==0){
        toastr.error('Selecciona una sucursal', 'Error')
        load(false);
    }else if(!semana){
        toastr.error('Selecciona una semana', 'Error')
        load(false);

    }
    const range = getWeekRange(semana);
    let fecha_inicio = range.start.toISOString().split('T')[0]
    let fecha_final = range.end.toISOString().split('T')[0]
    $.ajax({
        type: "post",
        url: "./modelo/nomina/generar-prenomina.php",
        data: {sucursales, fecha_inicio, fecha_final, semana},
        dataType: "json",
        success: function (response) {
                dibujarTabla(response)
        }
    });
}

cargarPrenominaGuardada()
function cargarPrenominaGuardada(){
    $.ajax({
        type: "post",
        url: "./modelo/nomina/opciones-prenomina.php",
        data: {tipo: 'carga'},
        dataType: "json",
        success: function (response) {
            if(response.estatus){
                toastr.success('Se cargó prenomina guardada', 'Exito')
                dibujarTabla(response)
            }
        }
    });
}

function limpiarPrenomina(){
    $.ajax({
        type: "post",
        url: "./modelo/nomina/opciones-prenomina.php",
        data: {'tipo':'limpiar'},
        dataType: "json",
        success: function (response) {
            if(response.estatus){
                location.reload();

            }
        }
    });
}

function dibujarTabla(response){
    if(response.estatus){

        $("#semana").val(response.prenomina_actual['semana'])
        $("#sucursales").val(response.prenomina_actual['id_sucursales'].split(''))
        $("#sucursales").selectpicker('refresh')
        $("#card-body-nomina").attr('style', '')
        $("#card-body-nomina").empty().append(`
        <table class="table table-condensed">
                
            <thead class="bg-dark text-white">
                <tr>
                    <td></td>
                    <td>#</td>
                    <td>Empleado</td>
                    <td>Foto</td>
                    <td>Salario base</td>
                    <td>Concepto</td>
                    <td>Percepción</td>
                    <td>Concepto</td>
                    <td>Deduccion</td>
                    <td>Pagar</td>
                </tr>
            </thead>
            <tbody id="tbody-prenomina">
            </tbody>
        </table>

        <div class="row justify-content-center">
            <div class="col-10 text-center">
                <div class="btn-info btn">Generar nomina</div>
            </div>
        </div>
        `)
        let contador=0;
        response.data.forEach(element => {
            contador++;
            const sueldoformatoMoneda = parseFloat(element.sueldo_base).toLocaleString('es-MX', {
                style: 'currency',
                currency: 'MXN',
              });
              console.log(sueldoformatoMoneda);
              const deduccionesformatoMoneda =  parseFloat(element.deducciones).toLocaleString('es-MX', {
                style: 'currency',
                currency: 'MXN',
              });
              const percepcionesformatoMoneda =  parseFloat(element.percepciones).toLocaleString('es-MX', {
                style: 'currency',
                currency: 'MXN',
              });
              const pagarformatoMoneda =  parseFloat(element.total_pagar).toLocaleString('es-MX', {
                style: 'currency',
                currency: 'MXN',
              });
            $("#tbody-prenomina").append(`
                    <tr data-toggle="collapse" id="contador_${contador}" data-target="#demo${contador}" class="accordion-toggle">
                        <td><button class="btn btn-info btn-xs"  id="btn-eyes-${contador}"><i class="fas fa-eye"></i></button></td>
                        <td>${contador}</td>
                        <td>${element.empleado}</td>
                        <td><img onerror="this.src='./src/img/neumaticos/NA.JPG';" class="foto-incidencia" src="./src/img/fotos_empleados/E${element.id_empleado}.${element.extension}"></img></td>
                        <td>${sueldoformatoMoneda}</td>
                        <td>-</td>
                        <td>${percepcionesformatoMoneda}</td>
                        <td>-</td>
                        <td>${deduccionesformatoMoneda}</td>
                        <td>${pagarformatoMoneda}</td>
                    </tr>
                    <tr>
                    <td colspan="10" class="hiddenRow"><div id="demo${contador}" class="accordian-body collapse">
                    
                        <ul class="list-group w-80 m-auto">
                            
                            <li class="list-group-item mt-3">
                                <div class="row">
                                    <div class="col-1"><b>#</b></div>
                                    <div class="col-3"><b>Concepto</b></div>
                                    <div class="col-3"><b>Monto</b></div>
                                    <div class="col-3"><b>Tipo</b></div>
                                    <div class="col-2"><b></b></div>
                                </div>
                            </li>
                            <div id="incidencias-${contador}" class="mb-3">
                            <li class="list-group-item" style="background-color:whitesmoke !important;">
                                <div class="row">
                                    <div class="col-12 text-center">Sin incidencias registradas</div>
                                </div>
                            </li>
                            </div>
                        </ul>
                    </div></td>
                    </tr>
                    `)

                    if(element.incidencias.length >0){
                        $(`#incidencias-${contador}`).empty()
                        let contador_2 =0
                        element.incidencias.forEach(incidencia => {
                            contador_2++
                            let tipo_incd = incidencia.tipo == 1 ? 'Deducción' : 'Percepción'
                            let bg_tipo_incd = incidencia.tipo == 1 ? 'tomato' : 'green'
                            const montoformatoMoneda = parseFloat(incidencia.monto).toLocaleString('es-MX', {
                                style: 'currency',
                                currency: 'MXN',
                              });
                            $(`#incidencias-${contador}`).append(
                                `
                                <li class="list-group-item" style="background-color:whitesmoke !important;" id="incidencia_${incidencia.id}" id_contador="${contador}"> 
                                    <div class="row">
                                        <div class="col-1">${contador_2}</div>
                                        <div class="col-3">${incidencia.concepto}</div>
                                        <div class="col-3">${montoformatoMoneda }</div>
                                        <div class="col-3" style="color: ${bg_tipo_incd}">${tipo_incd}</div>
                                        <div class="col-2" style="color:#1E90FF; cursor:pointer;" onclick="editarIncidenciaEmpleado(${incidencia.id}, ${incidencia.id_prenomina})">Editar</div>
                                    </div>
                                </li>
                                `
                            )
                        });
                    }
        }); 

        const totalformatoMoneda =  parseFloat(response.total_prenomina).toLocaleString('es-MX', {
            style: 'currency',
            currency: 'MXN',
          });
        $("#tbody-prenomina").append(`
                    <tr data-toggle="collapse" id="contador_${contador}" data-target="#demo${contador}" class="accordion-toggle">
                        <td colspan="8"></td>
                        <td><b>Total a pagar</b></td>
                        <td id="total_pagar">${totalformatoMoneda}</td>
                    </tr>`)
        
        
    }else{
        toastr.warning(response.mensaje, 'Error')
    }

    setTimeout(function () {
        load(false);
      }, 700);
}

bandera_eyes= false
function eyesClosed(id_contador){
    bandera_eyes = bandera_eyes== false ? true: false;
    console.log(id_contador);
    $(`#btn-eyes-${id_contador}`).empty().append('<i class="fas fa-eye-slash"></i>')
}

function getWeekRange(weekInput) {
    const [year, week] = weekInput.split('-W').map(Number);

    // Obtener el primer día del año
    const firstDayOfYear = new Date(year, 0, 1);

    // Calcular el primer sábado del año
    const dayOfWeek = firstDayOfYear.getDay();
    const diff = dayOfWeek <= 6 ? 6 - dayOfWeek : 0; // Encuentra el primer sábado
    const firstSaturday = new Date(year, 0, 1 + diff);

    // Ajustar para que la "semana 1" comience en el sábado de la semana anterior
    firstSaturday.setDate(firstSaturday.getDate() - 7);

    // Calcular el inicio de la semana seleccionada
    const startDate = new Date(firstSaturday);
    startDate.setDate(firstSaturday.getDate() + (week - 1) * 7);

    // Calcular el final de la semana (viernes)
    const endDate = new Date(startDate);
    endDate.setDate(startDate.getDate() + 6); 

    return {
        start: startDate,//.toISOString().split('T')[0], // YYYY-MM-DD
        end: endDate//.toISOString().split('T')[0]
    };
}


document.getElementById("semana").addEventListener("change", function() {
    const selectedWeek = this.value; 
    if (selectedWeek) {
        const range = getWeekRange(selectedWeek);
        document.getElementById("weekRange").textContent = `${formatDate(range.start)} - ${formatDate(range.end)}`;
    }
});

document.getElementById("sucursales").addEventListener("change", function() {

        document.getElementById("sucursales").nextSibling.classList.remove('btn-light')
    
});

// Función para formatear la fecha a "DD Mes. YY"
function formatDate(date) {
    const months = ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"];
    const day = date.getDate();
    const month = months[date.getMonth()];
    const year = date.getFullYear().toString().slice(-2); // Tomamos solo los últimos 2 dígitos del año
    return `${day} ${month}. ${year}`;
}


function editarIncidenciaEmpleado(id, id_prenomina) {
    const li = $("#incidencia_" + id);

    // Obtener los textos actuales
    const cols = li.find(".col-3, .col-2"); // Obtenemos los elementos de texto (posiciones 2, 3, 4, 5)

    const descripcion = $(cols[0]).text().trim();
    const monto = $(cols[1]).text().trim().replace('$', '');
    const tipoTexto = $(cols[2]).text().trim();

    // Determinar valor para el select
    const tipoValor = tipoTexto.toLowerCase() === 'deducción' ? 1 : 2;

    // Reemplazar contenido con inputs
    li.empty().append(`
        <div class="row">
            <div class="col-1">${id}</div>
            <div class="col-3">
                <input type="text" class="form-control" id="desc_${id}" value="${descripcion}">
            </div>
            <div class="col-3">
                <input type="number" class="form-control" id="monto_${id}" value="${monto}">
            </div>
            <div class="col-3">
            <select class="form-control" id="tipo_${id}">
            <option value="1" ${tipoValor === 1 ? 'selected' : ''}>Deducción</option>
            <option value="2" ${tipoValor === 2 ? 'selected' : ''}>Percepción</option>
        </select>
            </div>
            <div class="col-2" style="color:#1E90FF; cursor:pointer;" onclick="guardarIncidenciaEmpleado(${id}, ${id_prenomina})">
                Guardar
            </div>
        </div>
    `);

    
}

function guardarIncidenciaEmpleado(id, id_prenomina) {
    const nuevaDesc = $("#desc_" + id).val();
    const nuevoMonto = $("#monto_" + id).val();
    const nuevoTipo = $("#tipo_" + id).val();

    const tipoTexto = nuevoTipo === "1" ? "Deducción" : "Percepción";
    const tipoColor = nuevoTipo === "1" ? "tomato" : "green";

    $.ajax({
        type: "post",
        url: "./modelo/nomina/opciones-prenomina.php",
        data: {id, 'tipo':'edicion', descripcion: nuevaDesc, monto: nuevoMonto, tipo_incidencia: nuevoTipo,
        id_prenomina
        },
        dataType: "json",
        success: function (response) {
            if(response.estatus){
                toastr.success(response.mensaje, 'Exito')
                $("#incidencia_" + id).empty().append(`
                <div class="row">
                    <div class="col-1">${id}</div>
                    <div class="col-3">${nuevaDesc}</div>
                    <div class="col-3">$${parseFloat(nuevoMonto).toFixed(2)}</div>
                    <div class="col-3" style="color: ${tipoColor}">${tipoTexto}</div>
                    <div class="col-2" style="color:#1E90FF; cursor:pointer;" onclick="editarIncidenciaEmpleado(${id}, ${id_prenomina})">
                        Editar
                    </div>
                </div>
            `);

            let id_contador = $("#incidencia_" + id).attr('id_contador')
            const sueldoformatoMoneda = parseFloat(response.data.sueldo_base).toLocaleString('es-MX', {
                style: 'currency',
                currency: 'MXN',
              });
              const percepcionesformatoMoneda = parseFloat(response.data.percepciones).toLocaleString('es-MX', {
                style: 'currency',
                currency: 'MXN',
              });
              const deduccionesformatoMoneda = parseFloat(response.data.deducciones).toLocaleString('es-MX', {
                style: 'currency',
                currency: 'MXN',
              });

              const pagarformatoMoneda = parseFloat(response.data.total_pagar).toLocaleString('es-MX', {
                style: 'currency',
                currency: 'MXN',
              });

              console.log(id_contador);
              
            $("#contador_"+id_contador).empty().append(`
                        <td><button class="btn btn-info btn-xs"  id="btn-eyes-${id_contador}"><i class="fas fa-eye"></i></button></td>
                        <td>${id_contador}</td>
                        <td>${response.data.empleado}</td>
                        <td><img onerror="this.src='./src/img/neumaticos/NA.JPG';" class="foto-incidencia" src="./src/img/fotos_empleados/E${response.data.id_empleado}.${response.data.extension}"></img></td>
                        <td>${sueldoformatoMoneda}</td>
                        <td>-</td>
                        <td>${percepcionesformatoMoneda}</td>
                        <td>-</td>
                        <td>${deduccionesformatoMoneda}</td>
                        <td>${pagarformatoMoneda}</td>
            
            `)

            const totalPagarformatoMoneda = parseFloat(response.total_pagar).toLocaleString('es-MX', {
                style: 'currency',
                currency: 'MXN',
              });
            $("#total_pagar").text(totalPagarformatoMoneda)
            }
        }
    });

    
}

