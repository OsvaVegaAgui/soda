document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('formCrear');

    const msg = document.getElementById('mensaje');

    const showMessage = (type='info', text='') => {
        if (!msg) return;
        msg.className = `alert alert-${type}`;
        msg.innerHTML = `<strong>${{success:'Éxito',danger:'Error',warning:'Atención',info:'Información'}[type]||'Info'}</strong> ${text}`;
        msg.style.display = '';
        setTimeout(()=> msg.style.display='none', 3500);
    };

    function mostrarMensaje(tipo = "info", texto = "Información almacenada satisfactoriamente.") {
    const mensaje = document.getElementById("mensaje-dinamico");

    // Actualiza el contenido dinámicamente
    mensaje.innerHTML = `<span class="icono">ℹ️</span> <strong>${tipo.toUpperCase()}:</strong> ${texto}`;

    // Muestra el mensaje (tu CSS se encargará del estilo)
    mensaje.style.display = "flex";

    // Oculta automáticamente después de 3 segundos
    setTimeout(() => {
        mensaje.style.display = "none";
    }, 5000);
    }

    if (form) {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            // Validación HTML5 básica (required, type number, etc.)
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            const fd = new FormData(form);

            // Tomamos el token CSRF desde el input oculto o meta
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

                    if (typeof showMessage === 'function') {
                        showMessage('danger', firstError);
                    } else {
                        mostrarMensaje('danger', firstError);
                    }
                    return;
                }

                const data = await resp.json();

                if (resp.ok) {
                    showMessage('success', data.message || 'Ticket creado correctamente.');
                    form.reset();
                    location.reload(); // opcional
                } else {
                    mostrarMensaje('warning', data.message || 'Solicitud procesada sin confirmación.');
                }
            } catch (err) {
                console.error(err);
                if (typeof showMessage === 'function') {
                    showMessage('danger', 'Error. Intenta nuevamente.');
                } else {
                    mostrarMensaje('danger', 'Error. Intenta nuevamente.');
                }
            }
        });
    }

    // SI ESTAMOS TRABAJANDO CON EL FORMULARIO DE EDITAR
    const formEditar = document.getElementById('formEditar');
    if (formEditar){
        formEditar.addEventListener('submit', async (e) => {
            e.preventDefault();

            // Validación HTML5
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
                    method: 'POST', // usamos POST hacia la acción 'editar'
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

                    if (typeof showMessage === 'function') {
                        showMessage('danger', firstError);
                    } else {
                        mostrarMensaje('danger', firstError);
                    }
                    return;
                }

                if (!resp.ok) {
                    if (typeof showMessage === 'function') {
                        showMessage('danger', 'Error al actualizar el ticket.');
                    } else {
                        mostrarMensaje('danger', 'Error al actualizar el ticket.');
                    }
                    return;
                }

                const data = await resp.json();

                if (data.ok) {
                    if (typeof showMessage === 'function') {
                        showMessage('success', data.message || 'Ticket actualizado correctamente.');
                        this.location.reload();
                    } else {
                        mostrarMensaje('success', data.message || 'Ticket actualizado correctamente.');
                        this.location.reload();
                    }

                    // Si quieres redirigir de vuelta a la lista:
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    }

                } else {
                    if (typeof showMessage === 'function') {
                        showMessage('warning', data.message || 'Solicitud procesada sin confirmación.');
                    } else {
                        mostrarMensaje('warning', data.message || 'Solicitud procesada sin confirmación.');
                    }
                }

            } catch (err) {
                console.error(err);
                if (typeof showMessage === 'function') {
                    showMessage('danger', 'Error de conexión. Intenta nuevamente.');
                } else {
                   mostrarMensaje('danger', 'Error de conexión. Intenta nuevamente.');
                }
            }
        });
    }

    document.addEventListener('submit', async (e) => {

        e.preventDefault();

        function mostrarMensaje(texto) {
        const toast = document.getElementById("toastMensaje");
        toast.textContent = texto;
        toast.classList.add("mostrar");

        setTimeout(() => {
            toast.classList.remove("mostrar");
        }, 4500); // desaparece en 2.5s
        }

        const formEliminar = e.target.closest(".form-eliminar-ticket");
        if (formEliminar) {

        const btn = formEliminar.querySelector(".btn-eliminar-ticket");
        const nombre = btn?.dataset.nombre || "este ticket";

        // Mostrar mensaje de confirmación simple
        mostrarMensaje(`Para eliminar ${nombre}, vuelve a presionar el botón.`);

        // Cambiar comportamiento del botón por 3 segundos
        btn.disabled = true;
        btn.textContent = "Confirmar eliminación";

        setTimeout(() => {
            btn.disabled = false;
            btn.textContent = "Eliminar";
        }, 4000);

        // Si el usuario vuelve a hacer submit dentro de esos 3 segundos ► eliminar
        btn.onclick = () => {
            formEliminar.submit();
        };

        const fd = new FormData(formEliminar);

        try {
            const resp = await fetch(formEliminar.action, {
                method: 'POST',
                body: fd, // aquí ya viaja _token
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                credentials: 'same-origin'
            });

            if (!resp.ok) {
                if (typeof showMessage === 'function') {
                    showMessage('danger', 'No se pudo eliminar el ticket.');

                } else {
                    mostrarMensaje('danger', 'No se pudo eliminar el ticket.');
                }
                return;
            }

            const data = await resp.json();

            if (data.ok) {
                if (typeof showMessage === 'function') {
                    showMessage('success', data.message || 'Ticket eliminado correctamente.');
                    location.reload();
                } else {
                    mostrarMensaje('success', data.message || 'Ticket eliminado correctamente.');
                    location.reload();
                }

                const fila = form.closest('tr');
                if (fila) fila.remove();
            } else {
                if (typeof showMessage === 'function') {
                    showMessage('danger', data.message || 'No se pudo eliminar el ticket.');

                } else {
                    mostrarMensaje('danger', data.message || 'No se pudo eliminar el ticket.');
                }
            }

        } catch (err) {
            console.error(err);
            if (typeof showMessage === 'function') {
                showMessage('danger', 'Error de conexión al intentar eliminar el ticket.');
            } else {
                mostrarMensaje('danger', 'Error de conexión al intentar eliminar el ticket.');
            }
        }
        }
    });
});



