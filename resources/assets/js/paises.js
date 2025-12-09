document.addEventListener('DOMContentLoaded', () => {

    const msg = document.getElementById('mensaje');

    const showMessage = (type='info', text='') => {
        if (!msg) return;
        msg.className = `alert alert-${type}`;
        msg.innerHTML = `<strong>${{success:'Éxito',danger:'Error',warning:'Atención',info:'Información'}[type]||'Info'}</strong> ${text}`;
        msg.style.display = '';
        setTimeout(()=> msg.style.display='none', 3500);
    };

    // SI ESTAMOS TRABAJANDO CON EL FORMULARIO DE CREAR
    const form = document.getElementById('formCrear');
    if (form){
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

                // Errores de validación (422)
                if (resp.status === 422) {
                    const data = await resp.json();
                    const firstError =
                        Object.values(data.errors || {})[0]?.[0] ||
                        'Revisa los campos del formulario.';
                    
                    
                    if (typeof showMessage === 'function') {
                        showMessage('danger', firstError);
                    } else {
                        alert(firstError);
                    }
                    return;
                }

                if (!resp.ok) {
                    if (typeof showMessage === 'function') {
                        showMessage('danger', 'Error al guardar el país.');
                    } else {
                        alert('Error al guardar el país.');
                    }
                    return;
                }

                const data = await resp.json();

                if (data.ok) {
                    if (typeof showMessage === 'function') {
                        showMessage('success', data.message || 'País guardado correctamente.');
                    } else {
                        alert(data.message || 'País guardado correctamente.');
                    }

                    // Se limpia
                    form.reset();
                } else {
                    if (typeof showMessage === 'function') {
                        showMessage('warning', 'Solicitud procesada, pero sin confirmación explícita.');
                    } else {
                        alert('Solicitud procesada, pero sin confirmación explícita.');
                    }
                }

            } catch (err) {
                console.error(err);
                if (typeof showMessage === 'function') {
                    showMessage('danger', 'Error de conexión. Intenta nuevamente.');
                } else {
                    alert('Error de conexión. Intenta nuevamente.');
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
                        alert(firstError);
                    }
                    return;
                }

                if (!resp.ok) {
                    if (typeof showMessage === 'function') {
                        showMessage('danger', 'Error al actualizar el país.');
                    } else {
                        alert('Error al actualizar el país.');
                    }
                    return;
                }

                const data = await resp.json();

                if (data.ok) {
                    if (typeof showMessage === 'function') {
                        showMessage('success', data.message || 'País actualizado correctamente.');
                    } else {
                        alert(data.message || 'País actualizado correctamente.');
                    }

                    // Si quieres redirigir de vuelta a la lista:
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    }
                } else {
                    if (typeof showMessage === 'function') {
                        showMessage('warning', 'Solicitud procesada, pero sin confirmación explícita.');
                    } else {
                        alert('Solicitud procesada, pero sin confirmación explícita.');
                    }
                }

            } catch (err) {
                console.error(err);
                if (typeof showMessage === 'function') {
                    showMessage('danger', 'Error de conexión. Intenta nuevamente.');
                } else {
                    alert('Error de conexión. Intenta nuevamente.');
                }
            }
        });
    }

    document.addEventListener('submit', async (e) => {
        const formEliminar = e.target.closest('.form-eliminar-pais');
        if (formEliminar){

            e.preventDefault();

            const btn = formEliminar.querySelector('.btn-eliminar-pais');
            const nombre = btn?.dataset.nombre || 'este país';

            const confirmar = confirm(`¿Seguro que desea eliminar ${nombre}?`);
            if (!confirmar) return;

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
                        showMessage('danger', 'No se pudo eliminar el país.');
                    } else {
                        alert('No se pudo eliminar el país.');
                    }
                    return;
                }

                const data = await resp.json();

                if (data.ok) {
                    if (typeof showMessage === 'function') {
                        showMessage('success', data.message || 'País eliminado correctamente.');
                        location.reload();
                    } else {
                        alert(data.message || 'País eliminado correctamente.');
                        location.reload();
                    }

                    const fila = form.closest('tr');
                    if (fila) fila.remove();
                } else {
                    if (typeof showMessage === 'function') {
                        showMessage('warning', data.message || 'No se pudo eliminar el país.');
                    } else {
                        alert(data.message || 'No se pudo eliminar el país.');
                    }
                }

            } catch (err) {
                console.error(err);
                if (typeof showMessage === 'function') {
                    showMessage('danger', 'Error de conexión al intentar eliminar el país.');
                } else {
                    alert('Error de conexión al intentar eliminar el país.');
                }
            }
        }
    });
});

