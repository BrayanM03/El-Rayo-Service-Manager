let bandera_token_credito_incorrecto=false;
let bandera_cliente_aceptado=false;
function controlCodeInputs(tipo, clase, id_etiqueta_error){
  const inputs = document.querySelectorAll(clase);
  const totalInputs = inputs.length;
  
  inputs.forEach((input, index) => {
          input.addEventListener('input', (e) => {
            e.target.value = e.target.value.toUpperCase();
              // Limitar a un solo carácter
              if (e.target.value.length > 1) {
                  e.target.value = e.target.value.slice(0, 1);
              }
  
              // Mover al siguiente input si no es el último
              if (e.target.value && index < totalInputs - 1) {
                  inputs[index + 1].focus();
              }
  
              // Si todos están completos, enviar los datos
              if (Array.from(inputs).every(input => input.value !== '')) {
                  return enviarDatos(tipo, clase,id_etiqueta_error);
              }
          });

          // Manejar pegado
        input.addEventListener('paste', (e) => {
          const clipboardData = e.clipboardData || window.clipboardData;
          const pastedData = clipboardData.getData('text').toUpperCase();

          // Validar que son exactamente 5 caracteres
          if (pastedData.length === totalInputs) {
              e.preventDefault(); // Evitar el comportamiento predeterminado
              // Llenar los inputs con los caracteres pegados
              for (let i = 0; i < totalInputs; i++) {
                  inputs[i].value = pastedData[i] || '';
              }
              inputs[totalInputs - 1].focus(); // Mover el foco al último input

              // Si todos están completos, enviar los datos
              if (Array.from(inputs).every(input => input.value !== '')) {
                  enviarDatos(tipo,clase,id_etiqueta_error);
              }
          }
      });

      // Manejar retroceso para mover al input anterior
      input.addEventListener('keydown', (e) => {
        if (e.key === 'Backspace' && !input.value && index > 0) {
            inputs[index - 1].focus();
        }
    });

  
          input.addEventListener('focus', (e) => {
            $(id_etiqueta_error).empty()
              e.target.placeholder = ''; // Ocultar placeholder al enfocarse
              if(bandera_token_credito_incorrecto){
                Array.from(inputs).map((input)=>{
                  input.value=''
                  e.target.placeholder = '';
                 input.placeholder = '0';
                  input.style.border = "1px solid #e2e2e2";
                })
                bandera_token_credito_incorrecto=false
              }
          });
  
          input.addEventListener('blur', (e) => {
              // Restaurar placeholder si no hay valor
              if (!e.target.value) {
                  e.target.placeholder = '0';
              }
          });
      });

}

function enviarDatos(tipo, clase, id_etiqueta_error) {
        const inputs = document.querySelectorAll(clase);
        const codigo = Array.from(inputs).map(input => input.value).join('');
        let tipo_token = tipo=='credito' ? 2 : 1
        // Realizar la petición
        $.ajax({
          type: "POST",
          url: "./modelo/token.php",
          data: { 'comprobar-token':codigo, tipo_token},
          dataType: "JSON",
          success: function (response) {
       
            let color_border_input;
           
            if(response.estatus){
              color_border_input='#29ba68';
              $(id_etiqueta_error).append(`
              <span style="font-size:13px; color: ${color_border_input}">Token correcto</span>
              `)
              audio.play();
              if(tipo_token==2){
                $("#forma-pago").prop('disabled', false)
                $("#plazo-credito").prop('disabled', false)
                $("#pagare").prop('disabled', false)
                $("#forma-pago").selectpicker('refresh')
                $("#plazo-credito").selectpicker('refresh')
                $("#pagare").selectpicker('refresh')
                $("#importe-total-confg").attr("mensaje_error", 'Selecciona una forma de pago');
                $("#importe-total-confg").attr("is-valid", "false")
              }else{
                $('#tbody-clientes-encontrados').attr("is-valid", "true")
                $("#importe-total-confg").attr("is-valid", "true")
                let button_confirm = document.querySelector('.swal2-confirm');
                button_confirm.style.backgroundColor = '#1cc88a';
              }
              
            }else{
              color_border_input='tomato';
              $(id_etiqueta_error).append(`
              <span style="font-size:13px; color: ${color_border_input}">Token incorrecto</span>
              `)
              bandera_token_credito_incorrecto=true
              $("#importe-total-confg").attr("is-valid", "false")
              let button_confirm = document.querySelector('.swal2-confirm');
              button_confirm.style.backgroundColor = '#858796';
              audio_error.play();
              
            }
            Array.from(inputs).map((input) => {
              document.activeElement.blur()
              input.style.border = `1px solid ${color_border_input}`
             // if(!data.estatus) input.value =''
            })

            if(tipo=='clientes'){
              bandera_cliente_aceptado = response.estatus;
            }


        }})
}