
let salarioBase
let formDatas = new FormData()
document.getElementById("empleado").addEventListener("change", function(e) {
    let selectedOption = e.target.selectedOptions[0]; // Obtener el option seleccionado
    salarioBase = parseFloat(selectedOption.getAttribute("salario_base")) || 0; // Obtener y convertir salario_base

});

function aplicarCambiosExtras(e){
    
    let id_categoria = $("#categoria").val()
    let area = $("#area-cambios-extras")
    if(id_categoria==1){
        area.empty()
        area.append(`
        <div class="row m-3">
            <div class="col-6 col-md-3">
                <label for="nombre">¿Que dias faltó el empleado?</label>
                <select class="form-control selectpicker" onchange="calcularDescuentoFaltas(event)" multiple id="dias-faltas">
                    <option value="1">Lunes</option>
                    <option value="2">Martes</option>
                    <option value="3">Miercoles</option>
                    <option value="4">Jueves</option>
                    <option value="5">Viernes</option>
                    <option value="6">Sabado</option>
                    <option value="7">Domingo</option>
                </select>
            </div>
            <div class="col-6 col-md-3" id="monto-sugerido-descontar">
                Monto sugerido a descontar al empleado:<br>
                <span><b id="descuento-total">$0.00</b><span>
            </div>            
        </div>            
        `)
        $("#dias-faltas").selectpicker()
    }else if(id_categoria==5){
        area.empty()
        area.append(`
        <div class="row m-3">
            <div class="col-6 col-md-3">
                <label for="monto-prestamo">Monto del prestamo</label>
                <input class="form-field" placeholder="0.00" id="monto-prestamo" type="number">
            </div>
            <div class="col-6 col-md-3" id="area-monto-descontar-periodo">
                 <span>La incidencia quedará activa en caso de que el campo <b>Periocidad</b> sea establecida <i>Solo esta semana</i>
                 y que el <b>Monto</b> sea <u>menor</u> al <b>Monto del prestamo</b>.</span>
            </div>            
        </div>            
        `)

        
    }else{
        area.empty()
    }
}

function cambiarPeriocidad(){
    if($("#periocidad").val()!=1){
        /* $("#area-monto-descontar-periodo").append(`
             <label for="monto-periodo">Monto a descontar por periodo</label>
             <input class="form-field" placeholder="0.00" id="monto-periodo" type="number">
        `) */
        $("#label-monto").text('Monto a descontar por periodo')
    }else{
        $("#label-monto").text('Monto')
    }
}

function calcularDescuentoFaltas(e){
    let diasFaltados_arr = $("#dias-faltas").val()
    let diasFaltados=diasFaltados_arr.length         
    let selectedOption = $("#empleado option:selected")[0]; // Obtener option seleccionado
    let salarioBase = parseFloat(selectedOption.getAttribute("salario_base")) || 0;
    let descuentoPorDia = salarioBase / 7; // Suponiendo salario semanal
    let descuentoTotal = descuentoPorDia * diasFaltados;

    $("#descuento-total").text(`$${descuentoTotal.toFixed(2)}`); // Mostrar descuento
    $("#monto").val(parseFloat(descuentoTotal.toFixed(2)))
}

function registrarIncidencia(){
  

    let formulario = document.querySelectorAll('#formulario-nueva-incidencia input, #formulario-nueva-incidencia select, #formulario-nueva-incidencia textarea')
    let valido = true;
    formulario.forEach(element => {
        id_elemento = element.id
        value_elemento = element.value
      
        if(validarFormulario(id_elemento, value_elemento, element)){
            formDatas.append(id_elemento, value_elemento)
        }else{
            valido = false;
        }
    });
    if(valido){
        $.ajax({
            type: "post",
            url: "./modelo/empleados/registrar-incidencia.php",
            contentType: false,
            processData: false,
            data: formDatas,
            dataType: "json",
            success: function (response) {
                if(response.estatus){
                  icon_registro='success' 
                }else{icon_registro='error'}
                Swal.fire({
                    icon: icon_registro,
                    title: response.mensaje
                })
            }
        });
    }
}

function validarFormulario(element_id, valor, element){
    let valido = true;
    switch (element_id) {
            case 'empleado':
                
                select_actual = element.nextSibling
                if(!valor){
                    select_actual.classList.remove('btn-light')
                    select_actual.style.border = "1px solid red";
                    let adv = document.getElementById('empleado-adv')
                    if(adv!=null){adv.classList.remove('d-none')}
                    valido=false;
                }else{
                    select_actual.classList.add('btn-light')
                    select_actual.style.border=''
                    let adv = document.getElementById('empleado-adv')
                    if(!adv.classList.contains('d-none')){
                        adv.classList.add('d-none')
                    }
                }
                break;
            case 'categoria':
                    adv = document.getElementById(id_elemento+'-adv')
                    select_actual = element.nextSibling
                    if(!valor){
                        select_actual.classList.remove('btn-light')
                        select_actual.style.border = "1px solid red";
                        let adv = document.getElementById(id_elemento+'-adv')
                        if(adv!=null){adv.classList.remove('d-none')}
                        valido=false;

                    }else{
                        select_actual.classList.add('btn-light')
                    select_actual.style.border=''
                        let adv = document.getElementById(id_elemento+'-adv')
                        if(!adv.classList.contains('d-none')){
                            adv.classList.add('d-none')
                        }
                    }
                    break;
            case 'fecha-inicio':
    
                    select_actual = document.getElementById('fechas-incidencia')
                        if(!valor){
                            select_actual.classList.remove('btn-light')
                            select_actual.style.border = "1px solid red";
                            let adv = document.getElementById('fechas-incidencia-adv')
                            if(adv!=null){adv.classList.remove('d-none')}
                            valido=false;

                        }else{
                            select_actual.classList.add('btn-light')
                            select_actual.style.border=''
                            let adv = document.getElementById('fechas-incidencia-adv')
                            if(!adv.classList.contains('d-none')){
                                adv.classList.add('d-none')
                            }
                        }
                    break;        
            case 'monto':
                    select_actual = element
                    if(!valor){
                        select_actual.classList.remove('btn-light')
                        select_actual.style.border = "1px solid red";
                        let adv = document.getElementById('monto-adv')
                        if(adv!=null){adv.classList.remove('d-none')}
                        valido=false;
                    }else{
                        select_actual.classList.add('btn-light')
                        select_actual.style.border=''
                        let adv = document.getElementById('monto-adv')
                        if(!adv.classList.contains('d-none')){
                            adv.classList.add('d-none')
                        }
                    }
                    break;       
            default:
                break;
        }
    return valido
}