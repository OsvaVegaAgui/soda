document.addEventListener('DOMContentLoaded', function() {

    /* -----------------------------
       Auto-submit al cambiar fecha
    ----------------------------- */
    const formFecha = document.getElementById('formFecha');
    const inputFecha = formFecha ? formFecha.querySelector('input[name="fecha"]') : null;

    if (inputFecha) {
        inputFecha.addEventListener('change', function() {
            formFecha.submit();
        });
    }


    /* -----------------------------
       Buscador avanzado
       (nombre, cantidad, fecha)
    ----------------------------- */
    const buscador = document.getElementById('buscarTicket');
    if (buscador) {
        buscador.addEventListener('keyup', function() {

            let value = this.value.toLowerCase().trim();
            let filas = document.querySelectorAll('#tablaTickets tbody tr');

            filas.forEach(fila => {

                let nombre     = fila.querySelector('td:nth-child(1)')?.textContent.toLowerCase() || "";
                let cantidad   = fila.querySelector('td:nth-child(2)')?.textContent.toLowerCase() || "";
                let fechaRaw   = fila.querySelector('td:nth-child(3)')?.textContent.trim() || "";
                let fecha      = fechaRaw.toLowerCase();
                let fechaSimple = fechaRaw.replace(/\D/g, ""); // 20112025

                let coincide =
                    nombre.includes(value) ||
                    cantidad.includes(value) ||
                    fecha.includes(value) ||
                    fechaSimple.includes(value.replace(/\D/g, ""));

                fila.style.display = coincide ? "" : "none";
            });
        });
    }


    /* -----------------------------
       Modal de imprimir tickets
    ----------------------------- */
    const modalEl = document.getElementById('modalImprimir');
    const modal = modalEl ? new bootstrap.Modal(modalEl) : null;

    document.querySelectorAll('.btn-imprimir').forEach(btn => {
        btn.addEventListener('click', () => {

            const id = btn.dataset.id;
            const nombre = btn.dataset.nombre;
            const cantidad = btn.dataset.cantidad ?? 20;

            document.getElementById('modalId').value = id;
            document.getElementById('modalNombre').value = nombre;
            document.getElementById('modalCantidad').value = cantidad;

            // Ruta del controlador resolver
            document.getElementById('formImprimir').action =
                `/generar-ticketes/imprimir/${id}`;

            modal.show();
        });
    });


    /* -----------------------------
       Prevenir doble submit
    ----------------------------- */
    const formImprimir = document.getElementById('formImprimir');
    if (formImprimir) {
        formImprimir.addEventListener('submit', function() {
            this.querySelector('button[type="submit"]').disabled = true;
        });
    }

});
