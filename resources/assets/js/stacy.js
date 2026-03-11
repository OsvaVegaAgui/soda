document.addEventListener('DOMContentLoaded', () => {

    const formEditar = document.getElementById('formEditar');
    const msg = document.getElementById('mensaje');

    const showMessage = (type = 'info', text = '') => {
        if (!msg) return;
        const titles = { success: 'Éxito', danger: 'Error', warning: 'Atención', info: 'Información' };
        msg.className = `alert alert-${type}`;
        msg.innerHTML = `<strong>${titles[type] ?? 'Info'}</strong> ${text}`;
        msg.style.display = '';
        setTimeout(() => { msg.style.display = 'none'; }, 3500);
    };

    if (formEditar) {
        formEditar.addEventListener('submit', async (e) => {
            e.preventDefault();

            try {
                const resp = await fetch(formEditar.action, {
                    method: 'POST',
                    body: new FormData(formEditar),
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    credentials: 'same-origin',
                });

                const data = await resp.json();

                if (resp.status === 422) {
                    const firstError =
                        Object.values(data.errors ?? {})[0]?.[0] ??
                        'Debe completar todos los campos.';
                    showMessage('warning', firstError);
                    return;
                }

                if (!resp.ok || !data.ok) {
                    showMessage('danger', data.message ?? 'Error al guardar el menú.');
                    return;
                }

                showMessage('success', data.message);

                setTimeout(() => {
                    window.location.href = data.redirect;
                }, 1500);

            } catch (error) {
                console.error(error);
                showMessage('danger', 'Error de conexión. Intenta nuevamente.');
            }
        });
    }

});
