import * as bootstrap from 'bootstrap';
import DataTable from 'datatables.net-bs5';

window.bootstrap = bootstrap;

function showToast(message, variant = 'success') {
    const container = document.getElementById('toast-container');
    if (!container) return;

    const toastEl = document.createElement('div');
    toastEl.className = `toast align-items-center text-bg-${variant} border-0`;
    toastEl.setAttribute('role', 'alert');
    toastEl.setAttribute('aria-live', 'assertive');
    toastEl.setAttribute('aria-atomic', 'true');
    toastEl.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">${message}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    `;

    container.appendChild(toastEl);

    const toast = new bootstrap.Toast(toastEl, { delay: 5000 });
    toast.show();

    toastEl.addEventListener('hidden.bs.toast', () => toastEl.remove());
}

window.showToast = showToast;

function formatUltimaActualizacion(isoDate) {
    const date = new Date(isoDate);

    return new Intl.DateTimeFormat('es-MX', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    }).format(date);
}

document.addEventListener('DOMContentLoaded', () => {
    const tableEl = document.getElementById('estados-table');
    let dataTable = null;

    if (tableEl) {
        dataTable = new DataTable(tableEl, {
            processing: true,
            serverSide: true,
            ajax: tableEl.dataset.source,
            autoWidth: false,
            columns: [
                { data: 'cve_ent', name: 'cve_ent', title: 'Clave', width: '15%' },
                { data: 'nomgeo', name: 'nomgeo', title: 'Estado', width: '65%' },
                { data: 'pob_total', name: 'pob_total', title: 'Población total', width: '20%' },
            ],
            language: {
                url: 'https://cdn.datatables.net/plug-ins/2.1.8/i18n/es-MX.json',
            },
        });
    }

    const syncButtons = document.querySelectorAll('[data-sync-estados]');

    syncButtons.forEach((button) => {
        button.addEventListener('click', async () => {
            const originalText = button.innerHTML;
            button.disabled = true;
            button.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Sincronizando...';

            try {
                const response = await fetch(button.dataset.syncEstados, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        Accept: 'application/json',
                    },
                });

                const payload = await response.json();

                if (!response.ok) {
                    throw new Error(payload.message || 'No fue posible sincronizar.');
                }

                showToast(payload.message, 'success');

                if (dataTable) {
                    // La tabla ya existe: solo refrescamos sus datos vía el AJAX
                    // del propio DataTable, sin recargar la página ni perder
                    // la página/búsqueda/orden actuales.
                    dataTable.ajax.reload(null, false);

                    const ultimaActualizacionEl = document.getElementById('ultima-actualizacion');
                    if (ultimaActualizacionEl && payload.ultima_actualizacion) {
                        ultimaActualizacionEl.textContent =
                            'Última actualización: ' + formatUltimaActualizacion(payload.ultima_actualizacion);
                    }
                } else {
                    // Estado vacío inicial: el HTML de la tabla ni siquiera
                    // existe en el DOM todavía, así que sí necesitamos recargar
                    // la página para que el servidor la renderice.
                    setTimeout(() => window.location.reload(), 1200);
                }
            } catch (error) {
                showToast(error.message, 'danger');
            } finally {
                button.disabled = false;
                button.innerHTML = originalText;
            }
        });
    });
});
