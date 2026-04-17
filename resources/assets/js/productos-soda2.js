document.addEventListener('DOMContentLoaded', () => {

    // ── Utilidad: mostrar alerta en #mensaje ──────────────────────────────────
    const msg = document.getElementById('mensaje');

    const showMessage = (type = 'info', text = '') => {
        if (!msg) return;
        const labels = { success: 'Éxito', danger: 'Error', warning: 'Atención', info: 'Información' };
        msg.className = `alert alert-${type}`;
        msg.innerHTML = `<strong>${labels[type] ?? 'Info'}</strong> ${text}`;
        msg.style.display = '';
        setTimeout(() => { msg.style.display = 'none'; }, 3500);
    };

    // ── Errores de validación 422 ─────────────────────────────────────────────
    const handleValidationError = async (resp) => {
        const data = await resp.json();
        const firstError = Object.values(data.errors ?? {})[0]?.[0] ?? 'Revisa los campos del formulario.';
        showMessage('danger', firstError);
    };

    // ── Formulario CREAR ──────────────────────────────────────────────────────
    const formCrear = document.getElementById('formCrearProducto');

    if (formCrear) {
        formCrear.addEventListener('submit', async (e) => {
            e.preventDefault();

            try {
                const resp = await fetch(formCrear.action, {
                    method: 'POST',
                    body: new FormData(formCrear),
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    credentials: 'same-origin',
                });

                if (resp.status === 422) { await handleValidationError(resp); return; }
                if (!resp.ok) { showMessage('danger', 'Error al enviar el formulario.'); return; }

                const data = await resp.json();

                if (data.ok) {
                    showMessage('success', data.message ?? 'Producto guardado correctamente.');
                    if (data.redirect) {
                        setTimeout(() => { window.location.href = data.redirect; }, 1200);
                    }
                } else {
                    showMessage('warning', 'Solicitud procesada, pero sin confirmación.');
                }

            } catch {
                showMessage('danger', 'Error de conexión. Intenta nuevamente.');
            }
        });
    }

    // ── Formulario EDITAR ─────────────────────────────────────────────────────
    const formEditar = document.getElementById('formEditarProducto');

    if (formEditar) {
        formEditar.addEventListener('submit', async (e) => {
            e.preventDefault();

            if (!formEditar.checkValidity()) { formEditar.reportValidity(); return; }

            const csrf = document.querySelector('meta[name="csrf-token"]')?.content
                ?? document.querySelector('input[name="_token"]')?.value
                ?? '';

            try {
                const resp = await fetch(formEditar.action, {
                    method: 'POST',
                    body: new FormData(formEditar),
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                    },
                    credentials: 'same-origin',
                });

                if (resp.status === 422) { await handleValidationError(resp); return; }
                if (!resp.ok) { showMessage('danger', 'Error al actualizar el producto.'); return; }

                const data = await resp.json();

                if (data.ok) {
                    showMessage('success', data.message ?? 'Producto actualizado correctamente.');
                    if (data.redirect) {
                        setTimeout(() => { window.location.href = data.redirect; }, 1200);
                    }
                } else {
                    showMessage('warning', 'Solicitud procesada, pero sin confirmación.');
                }

            } catch {
                showMessage('danger', 'Error de conexión. Intenta nuevamente.');
            }
        });
    }

    // ── Códigos de barras adicionales ────────────────────────────────────────
    const listaCodigos  = document.getElementById('listaCodigosAdicionales');
    const btnAgregar    = document.getElementById('btnAgregarCodigo');

    function agregarFilaCodigo(valor = '') {
        if (!listaCodigos) return;
        const div = document.createElement('div');
        div.className = 'd-flex gap-2 mb-2 fila-codigo-adicional';
        div.innerHTML =
            `<input type="text" name="codigos_adicionales[]" class="form-control" ` +
            `placeholder="Código de barras" maxlength="50" value="${valor.replace(/"/g, '&quot;')}">` +
            `<button type="button" class="btn btn-outline-danger btn-sm btn-quitar-codigo">` +
            `<i class="bi bi-trash"></i></button>`;
        listaCodigos.appendChild(div);
        div.querySelector('input').focus();
    }

    if (btnAgregar) {
        btnAgregar.addEventListener('click', () => agregarFilaCodigo());
    }

    // Delegación: quitar fila
    if (listaCodigos) {
        listaCodigos.addEventListener('click', (e) => {
            const btn = e.target.closest('.btn-quitar-codigo');
            if (btn) btn.closest('.fila-codigo-adicional').remove();
        });
    }

    // ── Formulario ELIMINAR (delegado desde lista) ────────────────────────────
    document.addEventListener('submit', async (e) => {
        const formEliminar = e.target.closest('.form-eliminar-producto');
        if (!formEliminar) return;

        e.preventDefault();

        const nombre = formEliminar.querySelector('.btn-eliminar-producto')?.dataset.nombre ?? 'este producto';
        if (!confirm(`¿Seguro que desea eliminar "${nombre}"?`)) return;

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
                showMessage('success', data.message ?? 'Producto eliminado correctamente.');
                setTimeout(() => location.reload(), 1200);
            } else {
                showMessage('danger', data.message ?? 'No se pudo eliminar el producto.');
            }

        } catch {
            showMessage('danger', 'Error de conexión al intentar eliminar el producto.');
        }
    });

});
