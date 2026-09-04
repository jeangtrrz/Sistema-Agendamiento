/**
 * Script para gestión de eventos y compromisos del equipo
 */

const Eventos = {
    loadEventos: function(filtros = {}) {
        const data = new FormData();
        data.append('action', 'getEventos');
        Object.keys(filtros).forEach(key => data.append(key, filtros[key]));

        Utils.ajax({
            url: 'controllers/eventos.api.php',
            method: 'POST',
            data: data,
            contentType: false,
            success: (response) => {
                if (response.success) {
                    this.displayEventos(response.data);
                } else {
                    Utils.showAlert(response.error, 'danger');
                }
            }
        });
    },

    displayEventos: function(eventos) {
        const tbody = document.querySelector('table tbody');
        if (!tbody) return;

        if (eventos.length === 0) {
            tbody.innerHTML = '<tr><td colspan="8" class="text-center">No hay eventos registrados</td></tr>';
            return;
        }

        tbody.innerHTML = eventos.map(evento => `
            <tr>
                <td>${this.formatDate(evento.fecha_evento)}</td>
                <td>${evento.hora_inicio}</td>
                <td>${evento.titulo}</td>
                <td><span class="badge badge-${this.getTipoBadgeClass(evento.tipo_evento)}">${this.getTipoLabel(evento.tipo_evento)}</span></td>
                <td>${evento.responsable_nombre || '-'}</td>
                <td>${evento.lugar || '-'}</td>
                <td><span class="badge badge-${this.getEstadoBadgeClass(evento.estado)}">${this.getEstadoLabel(evento.estado)}</span></td>
                <td>
                    <div class="d-flex gap-1">
                        <button class="btn btn-sm btn-primary" onclick="Eventos.editEvento(${evento.id})">Editar</button>
                        <button class="btn btn-sm btn-danger" onclick="Eventos.deleteEvento(${evento.id})">Eliminar</button>
                    </div>
                </td>
            </tr>
        `).join('');
    },

    createEvento: function() {
        const form = document.getElementById('formEvento');
        if (!form) return;
        const formData = new FormData(form);
        formData.append('action', 'createEvento');

        Utils.showLoading(form);
        Utils.ajax({
            url: 'controllers/eventos.api.php',
            method: 'POST',
            data: formData,
            contentType: false,
            success: (response) => {
                Utils.hideLoading(form);
                if (response.success) {
                    Utils.showAlert('Evento creado correctamente', 'success');
                    form.reset();
                    Modal.close('modalEvento');
                    this.loadEventos();
                } else {
                    Utils.showAlert(response.error, 'danger');
                }
            },
            error: () => {
                Utils.hideLoading(form);
                Utils.showAlert('Error al crear el evento', 'danger');
            }
        });
    },

    editEvento: function(id) {
        const formData = new FormData();
        formData.append('action', 'getEvento');
        formData.append('id', id);

        Utils.ajax({
            url: 'controllers/eventos.api.php',
            method: 'POST',
            data: formData,
            contentType: false,
            success: (response) => {
                if (response.success) {
                    const evento = response.data;
                    const form = document.getElementById('formEvento');
                    form.dataset.eventoId = id;
                    document.getElementById('inputTitulo').value = evento.titulo || '';
                    document.getElementById('inputTipoEvento').value = evento.tipo_evento || '';
                    document.getElementById('inputResponsable').value = evento.responsable_id || '';
                    document.getElementById('inputFechaEvento').value = evento.fecha_evento || '';
                    document.getElementById('inputHoraInicio').value = (evento.hora_inicio || '').substring(0, 5);
                    document.getElementById('inputHoraFin').value = (evento.hora_fin || '').substring(0, 5);
                    document.getElementById('inputLugar').value = evento.lugar || '';
                    document.getElementById('inputDescripcionEvento').value = evento.descripcion || '';
                    document.getElementById('inputEstadoEvento').value = evento.estado || 'programado';
                    document.querySelector('#modalEvento .modal-title').textContent = 'Editar Evento';
                    form.dataset.action = 'update';
                    Modal.open('modalEvento');
                } else {
                    Utils.showAlert(response.error, 'danger');
                }
            }
        });
    },

    updateEvento: function() {
        const form = document.getElementById('formEvento');
        const eventoId = form.dataset.eventoId;
        const formData = new FormData(form);
        formData.append('action', 'updateEvento');
        formData.append('id', eventoId);

        Utils.showLoading(form);
        Utils.ajax({
            url: 'controllers/eventos.api.php',
            method: 'POST',
            data: formData,
            contentType: false,
            success: (response) => {
                Utils.hideLoading(form);
                if (response.success) {
                    Utils.showAlert('Evento actualizado correctamente', 'success');
                    form.reset();
                    delete form.dataset.eventoId;
                    delete form.dataset.action;
                    Modal.close('modalEvento');
                    this.loadEventos();
                } else {
                    Utils.showAlert(response.error, 'danger');
                }
            },
            error: () => {
                Utils.hideLoading(form);
                Utils.showAlert('Error al actualizar el evento', 'danger');
            }
        });
    },

    deleteEvento: function(id) {
        if (!confirm('¿Está seguro que desea eliminar este evento?')) return;
        const formData = new FormData();
        formData.append('action', 'deleteEvento');
        formData.append('id', id);

        Utils.ajax({
            url: 'controllers/eventos.api.php',
            method: 'POST',
            data: formData,
            contentType: false,
            success: (response) => {
                if (response.success) {
                    Utils.showAlert('Evento eliminado correctamente', 'success');
                    this.loadEventos();
                } else {
                    Utils.showAlert(response.error, 'danger');
                }
            }
        });
    },

    formatDate: function(date) {
        return Utils.parseLocalDate(date).toLocaleDateString('es-CL');
    },

    getTipoLabel: function(tipo) {
        const tipos = { reunion: 'Reunión', compromiso: 'Compromiso', evento: 'Evento' };
        return tipos[tipo] || tipo;
    },

    getTipoBadgeClass: function(tipo) {
        const clases = { reunion: 'info', compromiso: 'warning', evento: 'primary' };
        return clases[tipo] || 'primary';
    },

    getEstadoLabel: function(estado) {
        const estados = { programado: 'Programado', completado: 'Completado', cancelado: 'Cancelado' };
        return estados[estado] || estado;
    },

    getEstadoBadgeClass: function(estado) {
        const clases = { programado: 'warning', completado: 'success', cancelado: 'danger' };
        return clases[estado] || 'primary';
    }
};

document.addEventListener('DOMContentLoaded', function() {
    const formEvento = document.getElementById('formEvento');
    if (formEvento) {
        formEvento.addEventListener('submit', function(e) {
            e.preventDefault();
            const action = formEvento.dataset.action || 'create';
            if (action === 'create') {
                Eventos.createEvento();
            } else {
                Eventos.updateEvento();
            }
        });
    }

    const btnNewEvento = document.getElementById('btnNewEvento');
    if (btnNewEvento) {
        btnNewEvento.addEventListener('click', function() {
            const form = document.getElementById('formEvento');
            form.reset();
            delete form.dataset.eventoId;
            delete form.dataset.action;
            document.querySelector('#modalEvento .modal-title').textContent = 'Nuevo Evento';
            Modal.open('modalEvento');
        });
    }
});
