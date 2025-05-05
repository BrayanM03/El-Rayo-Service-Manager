const monthYearElement1 = document.getElementById('monthYear1');
const monthYearElement2 = document.getElementById('monthYear2');
const datesElement1 = document.getElementById('dates1');
const datesElement2 = document.getElementById('dates2');
const prevBtn = document.getElementById('prevBtn');
const nextBtn = document.getElementById('nextBtn');
const fechaInicioInput = document.getElementById('fecha-inicio');
const fechaFinalInput = document.getElementById('fecha-final');
const calendar = document.querySelector(".calendar");
//Variables para la animación si el calendario esta oculto
const calendario = document.querySelector(".contenedor-calendario");
const btnMostrar = document.querySelector(".form-field");
const btnCerrar = document.createElement("button");
const btnReset = document.createElement("button");

let isAnimating = false; // Para evitar clics rápidos que rompan la animación

let currentMonthYear = new Date();
let selectedStartDate = null;
let selectedEndDate = null;
let hoveringDate = null;
let selectedDates = new Set(); // Guardamos las fechas seleccionadas

const updateCalendar = () => {
    const firstMonth = new Date(currentMonthYear);
    const secondMonth = new Date(currentMonthYear);
    secondMonth.setMonth(secondMonth.getMonth() + 1);

    renderCalendar(firstMonth, monthYearElement1, datesElement1);
    renderCalendar(secondMonth, monthYearElement2, datesElement2);

    // Reasignar eventos después de actualizar el calendario
    document.querySelectorAll('.date:not(.inactive)').forEach(dateElement => {
        dateElement.addEventListener('click', handleDateClick);
        dateElement.addEventListener('mouseover', handleDateHover);
    });

    // 🔥 Restaurar los estilos después de actualizar el calendario
    restoreSelectedDates();
};

const renderCalendar = (date, monthYearElement, datesElement, tipo_evento='c') => {
    const currentYear = date.getFullYear();
    const currentMonth = date.getMonth();
    const today = new Date().toISOString().split('T')[0];

    const firstDay = new Date(currentYear, currentMonth, 1);
    const lastDay = new Date(currentYear, currentMonth + 1, 0);
    const totalDays = lastDay.getDate();
    const firstDayIndex = (firstDay.getDay() + 6) % 7;

    const monthYearString = date.toLocaleString('default', { month: 'long', year: 'numeric' });
    monthYearElement.textContent = monthYearString;

    let datesHtml = '';

    // Días del mes anterior
    const prevLastDay = new Date(currentYear, currentMonth, 0).getDate();
    for (let i = firstDayIndex; i > 0; i--) {
        datesHtml += `<div class="date inactive">${prevLastDay - i + 1}</div>`;
    }

    // Días del mes actual
    for (let i = 1; i <= totalDays; i++) {
        const fullDate = new Date(currentYear, currentMonth, i);
        const isoDate = fullDate.toISOString().split('T')[0];

        let extraClass = '';
        if (isoDate === today) {
            extraClass = 'today'; // Día actual
        }
       /*  if (isoDate === selectedStartDate) {
         
            extraClass = 'selected-start'; // Primer día seleccionado (verde)
        }
        
        if (isoDate === selectedEndDate) {
            extraClass = 'selected-end'; // Último día seleccionado (tomato)
        }
        if (selectedStartDate && selectedEndDate && isoDate > selectedStartDate && isoDate < selectedEndDate) {
            
            extraClass = 'selected-range'; // Días dentro del rango (azul claro)
        }
        if (selectedStartDate && !selectedEndDate && hoveringDate && isoDate > selectedStartDate && isoDate <= hoveringDate) {
            extraClass = 'hover-range'; // Hover sobre fechas entre la selección
        } */

        datesHtml += `<div class="date ${extraClass}" data-date="${isoDate}">${i}</div>`;
    }

    // Días del mes siguiente
    const nextDays = 7 - ((firstDayIndex + totalDays) % 7);
    if (nextDays < 7) {
        for (let i = 1; i <= nextDays; i++) {
            datesHtml += `<div class="date inactive">${i}</div>`;
        }
    }

    datesElement.innerHTML = datesHtml;

    // Eventos para selección y hover
    datesElement.querySelectorAll('.date:not(.inactive)').forEach(dateElement => {
        dateElement.addEventListener('click', handleDateClick);
        dateElement.addEventListener('mouseover', handleDateHover);
    });
};

const handleDateClick = (event) => {
    const selectedDate = event.target.getAttribute('data-date');

    if (!selectedStartDate || (selectedStartDate && selectedEndDate)) {
        // Primer clic: establecer la fecha de inicio y resetear la fecha final
        selectedStartDate = selectedDate;
        selectedEndDate = null;
        hoveringDate = null;
        selectedDates.clear();

        document.querySelectorAll('.date').forEach(el => {
            el.classList.remove('selected-start', 'selected-end', 'selected-range', 'hover-range');
        });

        event.target.classList.add('selected-start');
        selectedDates.add(selectedStartDate);
    } else if (selectedStartDate && !selectedEndDate) {

        document.querySelectorAll('.date').forEach(el => {
            el.classList.remove('hover-range');
        });
        const selectedDateObj = new Date(selectedDate + 'T00:00:00'); // Agrego el + 'T00:00:00' para que se mantenga la fecha sin cambios en el objeto
        const startDateObj = new Date(selectedStartDate + 'T00:00:00');
       

        if (selectedDateObj >= startDateObj) {
            // Segundo clic: establecer la fecha final
            selectedEndDate = selectedDate;
                // 🔥 Agregar TODAS las fechas del rango (aunque no estén renderizadas)
                let tempDate = new Date(selectedStartDate+ 'T00:00:00');
                while (tempDate <= selectedDateObj) {
                    selectedDates.add(tempDate.toISOString().split('T')[0]);
                    tempDate.setDate(tempDate.getDate() + 1);
                }
                restoreSelectedDates();
           /*  const dateElements = document.querySelectorAll('.date:not(.inactive)');

            dateElements.forEach(el => {
                const elDate = new Date(el.getAttribute('data-date') + 'T00:00:00');
                elementosDates.add(el.getAttribute('data-date'))
                if (elDate >= startDateObj && elDate <= selectedDateObj) {
                    el.classList.add('selected-range');
                    selectedDates.add(el.getAttribute('data-date'));
                }
            });
            console.log(elementosDates); */
           // event.target.classList.add('selected-end');
        }
    }

    // Guardar las fechas en los inputs
    fechaInicioInput.value = selectedStartDate || '';
    fechaFinalInput.value = selectedEndDate || '';
    var event_change = new Event('change');

    btnMostrar.textContent =''
    fechaInicioInput.dispatchEvent(event_change);
    fechaFinalInput.dispatchEvent(event_change)
};


const handleDateHover = (event) => {
    if (selectedStartDate && !selectedEndDate) {
        hoveringDate = event.target.getAttribute('data-date');

        const dateElements = document.querySelectorAll('.date:not(.inactive)');

        // Convertimos a Date para comparaciones seguras
        const startDateObj = new Date(selectedStartDate + 'T00:00:00');
        const hoverDateObj = new Date(hoveringDate + 'T00:00:00');

        // Remover todos los estilos de hover, excepto el de la fecha de inicio
        dateElements.forEach(el => {
            if (el.getAttribute('data-date') !== selectedStartDate) {
                el.classList.remove('hover-range');
            }
        });

        // Aplicar el rango si la fecha del hover es mayor o igual a la fecha inicial
        if (hoverDateObj >= startDateObj) {
            dateElements.forEach(el => {
                const elDate = new Date(el.getAttribute('data-date') + 'T00:00:00');
                if (elDate >= startDateObj && elDate <= hoverDateObj) {
                    el.classList.add('hover-range');
                }
            });
        }
    }
};

function establecerFecha(event, tipo){
    
    if(event.target.value){
        if(event.target.id=='fecha-final' && fechaInicioInput.value == event.target.value){
            let txt=btnMostrar.innerHTML
            btnMostrar.textContent =txt.slice(0, -3); 
            return false
        }
        let texto_adicional
    if(fechaFinalInput.value =='' && fechaInicioInput!=''){
         texto_adicional = ''
    }else{
       texto_adicional = ' al '
    }

        let fecha_obj = new Date(event.target.value + 'T00:00:00') 
        let formato = fecha_obj.toLocaleDateString('es-ES', { day: 'numeric', month: 'short' }) + texto_adicional;
        let fecha_ft = formato.replace('.', '')
        console.log(event.target.id);
        if(fechaFinalInput.value !='' && fechaInicioInput!=''){
            if(event.target.id=='fecha-final'){
                fecha_ft = fecha_ft.slice(0, -3); 
            } 
       }
       btnMostrar.append(fecha_ft)
    }
}

function resetearCalendario(e){
    e.preventDefault();
      // 1️⃣ Resetear variables de selección
      selectedStartDate = null;
      selectedEndDate = null;
      hoveringDate = null;
      selectedDates.clear();
      btnMostrar.textContent ='Selecciona una fecha'
      // 2️⃣ Eliminar todas las clases de fechas seleccionadas
      document.querySelectorAll('.date').forEach(el => {
          el.classList.remove('selected-start', 'selected-end', 'selected-range', 'hover-range');
      });
  
      // 3️⃣ Resetear los inputs de fecha
      fechaInicioInput.value = '';
      fechaFinalInput.value = '';
  
      // 4️⃣ Volver a renderizar el calendario
      updateCalendar();
}

// 🔄 Función para restaurar los estilos después de actualizar el calendario

const restoreSelectedDates = () => {
    document.querySelectorAll('.date:not(.inactive)').forEach(el => {
        const date = el.getAttribute('data-date');
        
       
        if (selectedStartDate === date) {
            el.classList.add('selected-range');//start
        } else if (selectedEndDate === date) {
            el.classList.add('selected-range');//end
        } else if (selectedDates.has(date)) {
            el.classList.add('selected-range');
        }
    });
};

// Botones de navegación
prevBtn.addEventListener('click', (e) => {
    e.preventDefault();

    currentMonthYear.setMonth(currentMonthYear.getMonth() - 1);
    changeMonth("prev")
    updateCalendar();
});

nextBtn.addEventListener('click', (e) => {
    e.preventDefault();

    currentMonthYear.setMonth(currentMonthYear.getMonth() + 1);
    changeMonth("next")
    updateCalendar();
});

updateCalendar('s');


//Animaciones
function changeMonth(direction) {
    if (isAnimating) return;
    isAnimating = true;

    if (direction === "next") {
        calendar.style.animation = "slide-left 0.3s forwards";
    } else {
        calendar.style.animation = "slide-right 0.3s forwards";
    }

    setTimeout(() => {
        // Reset animación y mover al nuevo mes
        calendar.style.animation = "";

        if (direction === "next") {
            calendar.style.animation = "slide-in-left 0.3s forwards";
        } else {
            calendar.style.animation = "slide-in-right 0.3s forwards";
        }

        setTimeout(() => {
            isAnimating = false;
        }, 70);
    }, 70);
}

// Configurar botón de cerrar
btnCerrar.classList.add("btn-cerrar");
btnReset.classList.add("btn-reset");
btnCerrar.innerHTML = "<b class='m-2'>X</b>";
btnReset.innerHTML = "<b class='m-2'><i class='fas fa-sync-alt'></i></b>";

btnCerrar.onclick = ocultarCalendario;
btnReset.onclick = resetearCalendario;

calendario.appendChild(btnCerrar);
calendario.appendChild(btnReset);

//Solo aplica si queremos mostrar el calendario
function mostrarCalendarioDinamico(){

    calendario.classList.add("mostrar");
}

// Función para ocultar con el mismo efecto
function ocultarCalendario(e) {
    e.preventDefault();
    
    if(fechaInicioInput.value !='' && fechaFinalInput.value ==''){
        toastr.error('Seleccione una fecha final o resete el calendario', 'Error')
    }else{
        calendario.classList.remove("mostrar");
    }
}

