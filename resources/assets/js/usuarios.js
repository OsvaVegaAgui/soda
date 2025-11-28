


document.addEventListener('DOMContentLoaded', () => {


    const form = document.getElementById('formUsuarios');

    const msg = document.getElementById('mensaje');

    const showMessage = (type='info', text='') => {
        if (!msg) return;
        msg.className = `alert alert-${type}`;
        msg.innerHTML = `<strong>${{success:'Éxito',danger:'Error',warning:'Atención',info:'Información'}[type] || 'Info'}</strong> ${text}`;
        msg.style.display = '';
        setTimeout(() => msg.style.display = 'none', 3500);
    };

    if (form) {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            // Validación HTML5
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            const fd = new FormData(form);

            // Token CSRF
            const csrf =
                document.querySelector('meta[name="csrf-token"]')?.content ||
                document.querySelector('input[name="_token"]')?.value ||
                '';

            try {
                const resp = await fetch(form.action, {
                    method: 'POST',
                    body: fd,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrf
                    },
                    credentials: 'same-origin'
                });

                // Errores de validación
                if (resp.status === 422) {
                    const data = await resp.json();
                    const firstError =
                        Object.values(data.errors || {})[0]?.[0] ||
                        'Revisa los campos del formulario.';

                    showMessage('warning', firstError);
                    return;
                }

                // Error general del servidor
                if (!resp.ok) {
                    showMessage('danger', 'Error al guardar el usuario.');
                    return;
                }

                const data = await resp.json();

                // Todo OK
                if (data.ok) {
                    showMessage('success', data.message || 'Usuario creado correctamente.');

                    setTimeout(() => {
                        window.location.href = window.rutaUsuariosLista;
                    }, 1200);

                } else {
                    showMessage('info', data.message || 'La solicitud se procesó, pero sin confirmación.');
                }

            } catch (err) {
                console.error(err);
                showMessage('danger', 'Error de conexión. Intenta nuevamente.');
            }

        });
    }

    
    const formEditar = document.getElementById('formEditarUsuarios');

    if (formEditar) {
        

        formEditar.addEventListener('submit', async (e) => {
            e.preventDefault();

            if (!formEditar.checkValidity()) {
                formEditar.reportValidity();
                return;
            }

            const fd = new FormData(formEditar);

            const csrf =
                document.querySelector('meta[name="csrf-token"]')?.content ||
                document.querySelector('input[name="_token"]')?.value ||
                '';

            try {
                const resp = await fetch(formEditar.action, {
                    method: 'POST',
                    body: fd,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrf
                    },
                    credentials: 'same-origin'
                });

                if (resp.status === 422) {
                    const data = await resp.json();
                    const firstError =
                        Object.values(data.errors || {})[0]?.[0] ||
                        'Revisa los campos del formulario.';
                    showMessage('danger', firstError);
                    return;
                }

                if (!resp.ok) {
                    showMessage('danger', 'Error al actualizar el usuario.');
                    return;
                }

                const data = await resp.json();

                if (data.ok) {

                    showMessage('success', data.message);

                    // 🔥 REDIRECCIÓN FINAL — usando la URL REAL que devuelve Laravel
                    if (data.redirect) {
                        setTimeout(() => {
                            window.location.href = data.redirect;
                        }, 1200);
                    }

                } else {
                    showMessage('warning', data.message || 'Solicitud procesada sin confirmación clara.');
                }

            } catch (err) {
                console.error(err);
                showMessage('danger', 'Error de conexión. Intenta nuevamente.');
            }
        });
    }

});



document.addEventListener('DOMContentLoaded', () => {

    //Form Login
    const formLogin = document.getElementById("formLog");
    const emailInput = formLogin.querySelector('input[name="email"]');
    const passInput = formLogin.querySelector('input[name="password"]');
    const btnLogin = document.getElementById("click");

    // Validaciones básicas
    const validarCorreo = (correo) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(correo);
    const validarContra = (contra) => /^(?=.*[A-Z])(?=.*\d).{8,}$/.test(contra);

    btnLogin.addEventListener("click", async (e) => {
        e.preventDefault();
        let valido = true;
        let mensaje = "";

        if (emailInput.value.trim() === "") {
            valido = false;
            mensaje = "El correo es obligatorio.";
        } else if (!validarCorreo(emailInput.value.trim())) {
            valido = false;
            mensaje = "Ingresa un correo electrónico válido.";
        }

        if (valido && passInput.value.trim() === "") {
            valido = false;
            mensaje = "La contraseña es obligatoria.";
        } else if (valido && !validarContra(passInput.value.trim())) {
            valido = false;
            mensaje = "La contraseña debe tener al menos 8 caracteres, una mayúscula y un número.";
        }

        if (!valido) {
            Swal.fire({
                icon: "warning",
                title: "Validación",
                text: mensaje,
                confirmButtonColor: "#3085d6",
            });
            return;
        }

        const datos = new FormData(formLogin);

        try {

            const response = await fetch(window.rutaLoginConfirmacion, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document
                        .querySelector('meta[name="csrf-token"]')
                        .getAttribute("content"),
                },
                body: datos,
            });

            const data = await response.json();

            if (data.ok) {
                Swal.fire({
                    icon: "success",
                    title: "Éxito",
                    text: data.mensaje || "Inicio de sesión exitoso.",
                    showConfirmButton: false,
                    timer: 2000,
                }).then(() => {
                    window.location.href = data.redirect || "/";
                });
            } else {
                Swal.fire({
                    icon: "error",
                    title: "Error de acceso",
                    text: data.mensaje || "Credenciales incorrectas.",
                    confirmButtonColor: "#d33",
                });
            }

        } catch (error) {
            Swal.fire({
                icon: "error",
                title: "Error de servidor",
                text: "Ocurrió un problema al procesar la solicitud.",
                confirmButtonColor: "#d33",
            });
            console.error("Error en fetch:", error);
        }
    });



});




document.addEventListener('DOMContentLoaded', () => {

  document.addEventListener("click", (event) => {
    if (event.target && event.target.id === "btnEnviarRecuperacion") {
      enviarCodigoRecuperacion(event.target);
    }
  });

  async function enviarCodigoRecuperacion(btnEnviar) {
    const formRecuperar1 = document.getElementById("formRecuperar");
    const inputEmail = document.getElementById("recuperarEmail");

    if (!formRecuperar1 || !inputEmail) return console.error("Formulario o input no encontrado");

    const email = inputEmail.value.trim();

    if (email === "" || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
      Swal.fire({
        icon: "warning",
        title: "Correo inválido 😕",
        text: "Por favor ingresa un correo electrónico válido.",
        confirmButtonColor: "#3085d6"
      });
      return;
    }

    btnEnviar.disabled = true;
    btnEnviar.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Enviando...';

    try {

       const response = await fetch(window.rutaSendCode, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ email })
      });

      const data = await response.json();

      if (response.ok) {
        Swal.fire({
          icon: "success",
          title: "¡Código enviado! ✉️",
          text: data.message || "Revisa tu correo, te hemos enviado el código de recuperación.",
          showConfirmButton: false,
          timer: 1100
        });

        inputEmail.value = "";

        setTimeout(() => {
          const modal = bootstrap.Modal.getInstance(document.getElementById('modalRecuperar'));
          if (modal) modal.hide();
        }, 1500);

         setTimeout(() => {
          window.location.href = window.rutaLogin;
        }, 1800);

      } else {
        Swal.fire({
          icon: "error",
          title: "Error 🚫",
          text: data.message || "Hubo un problema al enviar el correo.",
          confirmButtonColor: "#d33"
        });
      }

    } catch (error) {
      console.error(error);
      Swal.fire({
        icon: "error",
        title: "Error de conexión ⚡",
        text: "No se pudo conectar con el servidor. Inténtalo de nuevo.",
        confirmButtonColor: "#d33"
      });
    } finally {
      btnEnviar.disabled = false;
      btnEnviar.textContent = "Enviar enlace";
    }
  }


});


function mostrarMensaje(texto, tipo = "error") {
              const div = document.getElementById("mensaje");

              if (tipo === "error") {
                  div.style.color = "red";
              } else {
                  div.style.color = "green";
              }

              div.innerText = texto;
          }


          
document.getElementById("btnCambiar").addEventListener("click", async () => {

    const email = document.getElementById("email").value;
    const token = document.getElementById("token").value;
    const pass1 = document.getElementById("newPassword").value;
    const pass2 = document.getElementById("confirmPassword").value;

    if (pass1 !== pass2) {
        mostrarMensaje("Las contraseñas no coinciden", "error");
        return;
    }

    const csrf = document.querySelector('meta[name="csrf-token"]').content;

    // ------------------------------
    // 1️⃣ VALIDAR TOKEN
    // ------------------------------
    let validar = await fetch(window.rutaValidarToken, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": csrf
        },
        body: JSON.stringify({ email, token })
    });

    let r1 = await validar.json();

    if (!r1.ok) {
        mostrarMensaje(r1.mensaje, "error");
        return;
    }

    // ------------------------------
    // 2️⃣ CAMBIAR CONTRASEÑA
    // ------------------------------
    let cambiar = await fetch(window.rutaCambiarContrasena, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": csrf
        },
        body: JSON.stringify({
            email,
            token,
            password: pass1
        })
    });

    let r2 = await cambiar.json();

    if (!r2.ok) {
        mostrarMensaje(r2.mensaje, "error");
        return;
    }

    mostrarMensaje(r2.mensaje, "success");

    setTimeout(() => {
       window.location.href = window.rutaLogin;
    }, 2000);

});









