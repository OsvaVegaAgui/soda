
document.addEventListener('DOMContentLoaded', () => {

    const msg = document.getElementById('mensaje');

    const showMessage = (type = 'info', text = '') => {
        if (!msg) return;
        msg.className = `alert alert-${type}`;
        msg.innerHTML = `<strong>${{ success: 'Éxito', danger: 'Error', warning: 'Atención', info: 'Información' }[type] || 'Info'}</strong> ${text}`;
        msg.style.display = '';
        setTimeout(() => msg.style.display = 'none', 3500);
    };

    // ── Formulario CREAR ──────────────────────────────────────────────────────
    const form = document.getElementById('formUsuarios');

    if (form) {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            const fd = new FormData(form);
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

                if (resp.status === 422) {
                    const data = await resp.json();
                    const firstError =
                        Object.values(data.errors || {})[0]?.[0] ||
                        'Revisa los campos del formulario.';
                    showMessage('warning', firstError);
                    return;
                }

                if (!resp.ok) {
                    showMessage('danger', 'Error al guardar el usuario.');
                    return;
                }

                const data = await resp.json();

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

    // ── Formulario EDITAR ─────────────────────────────────────────────────────
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

    // ── Formulario ELIMINAR (delegado desde lista) ────────────────────────────
    document.addEventListener('submit', async (e) => {
        const formEliminar = e.target.closest('.form-eliminar-usuario');
        if (!formEliminar) return;

        e.preventDefault();

        const nombre = formEliminar.querySelector('.btn-eliminar-usuario')?.dataset.nombre ?? 'este usuario';
        if (!confirm(`¿Seguro que desea eliminar a "${nombre}"?`)) return;

        try {
            const resp = await fetch(formEliminar.action, {
                method: 'POST',
                body: new FormData(formEliminar),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                credentials: 'same-origin',
            });

            const data = await resp.json();

            if (data.ok) {
                showMessage('success', data.message ?? 'Usuario eliminado correctamente.');
                setTimeout(() => location.reload(), 1200);
            } else {
                showMessage('danger', data.message ?? 'No se pudo eliminar el usuario.');
            }

        } catch {
            showMessage('danger', 'Error de conexión al intentar eliminar el usuario.');
        }
    });

});


document.addEventListener('DOMContentLoaded', () => {

    // Form Login
    const formLogin = document.getElementById('formLog');
    if (!formLogin) return;

    const emailInput = formLogin.querySelector('input[name="email"]');
    const passInput = formLogin.querySelector('input[name="password"]');
    const btnLogin = document.getElementById('click');

    if (!emailInput || !passInput || !btnLogin) return;

    const validarCorreo = (correo) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(correo);

    btnLogin.addEventListener('click', async (e) => {
        e.preventDefault();
        let valido = true;
        let mensaje = '';

        if (emailInput.value.trim() === '') {
            valido = false;
            mensaje = 'El correo es obligatorio.';
        } else if (!validarCorreo(emailInput.value.trim())) {
            valido = false;
            mensaje = 'Ingresa un correo electrónico válido.';
        }

        if (valido && passInput.value.trim() === '') {
            valido = false;
            mensaje = 'La contraseña es obligatoria.';
        }

        if (!valido) {
            Swal.fire({
                icon: 'warning',
                title: 'Validación',
                text: mensaje,
                confirmButtonColor: '#3085d6',
            });
            return;
        }

        const datos = new FormData(formLogin);

        try {
            const response = await fetch(window.rutaLoginConfirmacion, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document
                        .querySelector('meta[name="csrf-token"]')
                        .getAttribute('content'),
                },
                body: datos,
            });

            const data = await response.json();

            if (data.ok) {
                Swal.fire({
                    icon: 'success',
                    title: 'Éxito',
                    text: data.mensaje || 'Inicio de sesión exitoso.',
                    showConfirmButton: false,
                    timer: 2000,
                }).then(() => {
                    window.location.href = data.redirect || '/';
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error de acceso',
                    text: data.mensaje || 'Credenciales incorrectas.',
                    confirmButtonColor: '#d33',
                });
            }

        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'Error de servidor',
                text: 'Ocurrió un problema al procesar la solicitud.',
                confirmButtonColor: '#d33',
            });
            console.error('Error en fetch:', error);
        }
    });

});


document.addEventListener('DOMContentLoaded', () => {

    document.addEventListener('click', (event) => {
        if (event.target && event.target.id === 'btnEnviarRecuperacion') {
            enviarCodigoRecuperacion(event.target);
        }
    });

    async function enviarCodigoRecuperacion(btnEnviar) {
        const formRecuperar1 = document.getElementById('formRecuperar');
        const inputEmail = document.getElementById('recuperarEmail');

        if (!formRecuperar1 || !inputEmail) return;

        const email = inputEmail.value.trim();

        if (email === '' || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            Swal.fire({
                icon: 'warning',
                title: 'Correo inválido',
                text: 'Por favor ingresa un correo electrónico válido.',
                confirmButtonColor: '#3085d6'
            });
            return;
        }

        btnEnviar.disabled = true;
        btnEnviar.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Enviando...';

        try {
            const response = await fetch(window.rutaSendCode, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ email })
            });

            const data = await response.json();

            if (response.ok) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Código enviado!',
                    text: data.message || 'Revisa tu correo, te hemos enviado el código de recuperación.',
                    showConfirmButton: false,
                    timer: 1100
                });

                inputEmail.value = '';

                setTimeout(() => {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('modalRecuperar'));
                    if (modal) modal.hide();
                }, 1500);

                setTimeout(() => {
                    window.location.href = window.rutaLogin;
                }, 1800);

            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message || 'Hubo un problema al enviar el correo.',
                    confirmButtonColor: '#d33'
                });
            }

        } catch (error) {
            console.error(error);
            Swal.fire({
                icon: 'error',
                title: 'Error de conexión',
                text: 'No se pudo conectar con el servidor. Inténtalo de nuevo.',
                confirmButtonColor: '#d33'
            });
        } finally {
            btnEnviar.disabled = false;
            btnEnviar.textContent = 'Enviar enlace';
        }
    }

});


document.addEventListener('DOMContentLoaded', () => {

    const btnCambiar = document.getElementById('btnCambiar');
    if (!btnCambiar) return;

    function mostrarMensaje(texto, tipo = 'error') {
        const div = document.getElementById('mensaje');
        if (!div) return;
        div.style.color = tipo === 'error' ? 'red' : 'green';
        div.innerText = texto;
    }

    btnCambiar.addEventListener('click', async () => {

        const email = document.getElementById('email')?.value;
        const token = document.getElementById('token')?.value;
        const pass1 = document.getElementById('newPassword')?.value;
        const pass2 = document.getElementById('confirmPassword')?.value;

        if (pass1 !== pass2) {
            mostrarMensaje('Las contraseñas no coinciden', 'error');
            return;
        }

        const csrf = document.querySelector('meta[name="csrf-token"]').content;

        // 1. VALIDAR TOKEN
        let validar = await fetch(window.rutaValidarToken, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf
            },
            body: JSON.stringify({ email, token })
        });

        let r1 = await validar.json();

        if (!r1.ok) {
            mostrarMensaje(r1.mensaje, 'error');
            return;
        }

        // 2. CAMBIAR CONTRASEÑA
        let cambiar = await fetch(window.rutaCambiarContrasena, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf
            },
            body: JSON.stringify({
                email,
                token,
                password: pass1
            })
        });

        let r2 = await cambiar.json();

        if (!r2.ok) {
            mostrarMensaje(r2.mensaje, 'error');
            return;
        }

        mostrarMensaje(r2.mensaje, 'success');

        setTimeout(() => {
            window.location.href = window.rutaLogin;
        }, 2000);

    });

});
