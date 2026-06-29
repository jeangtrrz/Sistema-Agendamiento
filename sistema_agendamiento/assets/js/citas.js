/**
 * Script para gestión de citas
 */

const Citas = {
    /**
     * Cargar lista de citas
     */
    loadCitas: function(filtros = {}) {
        const data = new FormData();
        data.append('action', 'getCitas');
        Object.keys(filtros).forEach(key => {
            data.append(key, filtros[key]);
        });

        Utils.ajax({
            url: 'controllers/citas.api.php',
            method: 'POST',
            data: data,
            contentType: false,
            success: (response) => {
                if (response.success) {
                    this.displayCitas(response.data);
                } else {
                    Utils.showAlert(response.error, 'danger');
                }
            }
        });
    },

    /**
     * Mostrar citas en la tabla
     */
    displayCitas: function(citas) {
        const tbody = document.querySelector('table tbody');
        if (!tbody) return;

        if (citas.length === 0) {
            tbody.innerHTML = '<tr><td colspan="8" class="text-center">No hay citas disponibles</td></tr>';
            return;
        }

        tbody.innerHTML = citas.map(cita => `
            <tr>
                <td>${this.formatDate(cita.fecha_cita)}</td>
                <td>${cita.hora_inicio}</td>
                <td>${cita.cliente_nombre}</td>
                <td>${cita.cliente_telefono}</td>
                <td><span class="badge badge-${this.getTipoBadgeClass(cita.tipo_cita)}">${this.getTipoLabel(cita.tipo_cita)}</span></td>
                <td>${cita.tecnico_nombre}</td>
                <td><span class="badge badge-${this.getEstadoBadgeClass(cita.estado)}">${this.getEstadoLabel(cita.estado)}</span></td>
                <td>
                    <div class="d-flex gap-1">
                        ${this.canEdit(cita) ? `<button class="btn btn-sm btn-primary" onclick="Citas.editCita(${cita.id})">Editar</button>` : ''}
                        ${this.canMarkCompleted(cita) ? `<button class="btn btn-sm btn-success" onclick="Citas.markCompleted(${cita.id})">Completar</button>` : ''}
                        ${this.canDelete(cita) ? `<button class="btn btn-sm btn-danger" onclick="Citas.deleteCita(${cita.id})">Eliminar</button>` : ''}
                    </div>
                </td>
            </tr>
        `).join('');
    },

    /**
     * Crear nueva cita
     */
    createCita: function() {
        const form = document.getElementById('formCita');
        if (!form) return;

        // Capturar valores explícitamente del formulario
        const formData = new FormData();
        formData.append('action', 'createCita');
        formData.append('cliente_nombre', document.getElementById('inputClienteNombre').value);
        formData.append('cliente_telefono', document.getElementById('inputClienteTelefono').value);
        formData.append('cliente_email', document.getElementById('inputClienteEmail').value);
        formData.append('cliente_direccion', document.getElementById('inputClienteDireccion').value);
        formData.append('tipo_cita', document.getElementById('inputTipoCita').value);
        formData.append('tecnico_id', document.getElementById('inputTecnico').value);
        formData.append('fecha_cita', document.getElementById('inputFechaCita').value);
        formData.append('hora_inicio', document.getElementById('inputHoraCita').value);
        formData.append('descripcion', document.getElementById('inputDescripcion').value);

        Utils.showLoading(form);

        Utils.ajax({
            url: 'controllers/citas.api.php',
            method: 'POST',
            data: formData,
            contentType: false,
            success: (response) => {
                Utils.hideLoading(form);
                if (response.success) {
                    Utils.showAlert('Cita creada correctamente', 'success');
                    form.reset();
                    Modal.close('modalCita');
                    this.loadCitas();
                } else {
                    Utils.showAlert(response.error, 'danger');
                }
            },
            error: (err) => {
                Utils.hideLoading(form);
                Utils.showAlert('Error al crear la cita', 'danger');
            }
        });
    },

    /**
     * Editar cita
     */
    editCita: function(id) {
        const formData = new FormData();
        formData.append('action', 'getCita');
        formData.append('id', id);

        Utils.ajax({
            url: 'controllers/citas.api.php',
            method: 'POST',
            data: formData,
            contentType: false,
            success: (response) => {
                if (response.success) {
                    const cita = response.data;
                    const form = document.getElementById('formCita');
                    form.dataset.citaId = id;
                    document.getElementById('inputClienteNombre').value = cita.cliente_nombre;
                    document.getElementById('inputClienteTelefono').value = cita.cliente_telefono;
                    document.getElementById('inputClienteEmail').value = cita.cliente_email || '';
                    document.getElementById('inputClienteDireccion').value = cita.cliente_direccion || '';
                    document.getElementById('inputTipoCita').value = cita.tipo_cita;
                    document.getElementById('inputFechaCita').value = cita.fecha_cita;
                    // Normalizar hora: si viene como HH:MM:SS, convertir a HH:MM
                    document.getElementById('inputHoraCita').value = cita.hora_inicio.substring(0, 5);
                    document.getElementById('inputTecnico').value = cita.tecnico_id;
                    document.getElementById('inputDescripcion').value = cita.descripcion || '';
                    document.querySelector('#modalCita .modal-title').textContent = 'Editar Cita';
                    form.dataset.action = 'update';
                    Modal.open('modalCita');
                } else {
                    Utils.showAlert(response.error, 'danger');
                }
            }
        });
    },

    /**
     * Actualizar cita
     */
    updateCita: function() {
        const form = document.getElementById('formCita');
        const citaId = form.dataset.citaId;

        // Capturar valores explícitamente del formulario
        const formData = new FormData();
        formData.append('action', 'updateCita');
        formData.append('id', citaId);
        formData.append('cliente_nombre', document.getElementById('inputClienteNombre').value);
        formData.append('cliente_telefono', document.getElementById('inputClienteTelefono').value);
        formData.append('cliente_email', document.getElementById('inputClienteEmail').value);
        formData.append('cliente_direccion', document.getElementById('inputClienteDireccion').value);
        formData.append('tipo_cita', document.getElementById('inputTipoCita').value);
        formData.append('tecnico_id', document.getElementById('inputTecnico').value);
        formData.append('fecha_cita', document.getElementById('inputFechaCita').value);
        formData.append('hora_inicio', document.getElementById('inputHoraCita').value);
        formData.append('descripcion', document.getElementById('inputDescripcion').value);

        Utils.showLoading(form);

        Utils.ajax({
            url: 'controllers/citas.api.php',
            method: 'POST',
            data: formData,
            contentType: false,
            success: (response) => {
                Utils.hideLoading(form);
                if (response.success) {
                    Utils.showAlert('Cita actualizada correctamente', 'success');
                    form.reset();
                    delete form.dataset.citaId;
                    delete form.dataset.action;
                    Modal.close('modalCita');
                    this.loadCitas();
                } else {
                    Utils.showAlert(response.error, 'danger');
                }
            },
            error: (err) => {
                Utils.hideLoading(form);
                Utils.showAlert('Error al actualizar la cita', 'danger');
            }
        });
    },

    /**
     * Eliminar cita
     */
    deleteCita: function(id) {
        if (!confirm('¿Está seguro que desea eliminar esta cita?')) {
            return;
        }

        const formData = new FormData();
        formData.append('action', 'deleteCita');
        formData.append('id', id);

        Utils.ajax({
            url: 'controllers/citas.api.php',
            method: 'POST',
            data: formData,
            contentType: false,
            success: (response) => {
                if (response.success) {
                    Utils.showAlert('Cita eliminada correctamente', 'success');
                    this.loadCitas();
                } else {
                    Utils.showAlert(response.error, 'danger');
                }
            }
        });
    },

    /**
     * Marcar cita como completada
     */
    markCompleted: function(id) {
        const observaciones = prompt('Observaciones (opcional):');
        if (observaciones === null) return;

        const formData = new FormData();
        formData.append('action', 'markCompleted');
        formData.append('id', id);
        formData.append('observaciones', observaciones);

        Utils.ajax({
            url: 'controllers/citas.api.php',
            method: 'POST',
            data: formData,
            contentType: false,
            success: (response) => {
                if (response.success) {
                    Utils.showAlert('Cita marcada como completada', 'success');
                    this.loadCitas();
                } else {
                    Utils.showAlert(response.error, 'danger');
                }
            }
        });
    },

    /**
     * Auxiliares
     */
    formatDate: function(date) {
        return new Date(date).toLocaleDateString('es-CL');
    },

    getTipoLabel: function(tipo) {
        const tipos = {
            'instalacion': 'Instalación',
            'retiro': 'Retiro',
            'soporte': 'Soporte'
        };
        return tipos[tipo] || tipo;
    },

    getTipoBadgeClass: function(tipo) {
        const clases = {
            'instalacion': 'success',
            'retiro': 'warning',
            'soporte': 'info'
        };
        return clases[tipo] || 'primary';
    },

    getEstadoLabel: function(estado) {
        const estados = {
            'pendiente': 'Pendiente',
            'completada': 'Completada',
            'cancelada': 'Cancelada'
        };
        return estados[estado] || estado;
    },

    getEstadoBadgeClass: function(estado) {
        const clases = {
            'pendiente': 'warning',
            'completada': 'success',
            'cancelada': 'danger'
        };
        return clases[estado] || 'primary';
    },

    canEdit: function(cita) {
        // Verificar permisos desde servidor (se asume que es manejado en PHP)
        return cita.can_edit !== false;
    },

    canMarkCompleted: function(cita) {
        return cita.estado === 'pendiente' && (cita.can_complete !== false);
    },

    canDelete: function(cita) {
        return cita.can_delete !== false;
    }
};

// Inicializar cuando el DOM está listo
document.addEventListener('DOMContentLoaded', function() {
    // Cargar citas si estamos en la página de citas
    if (document.querySelector('table tbody')) {
        Citas.loadCitas();
    }

    // Manejar formulario de cita
    const formCita = document.getElementById('formCita');
    if (formCita) {
        const btnSubmit = formCita.querySelector('button[type="submit"]');
        if (btnSubmit) {
            btnSubmit.addEventListener('click', function(e) {
                e.preventDefault();
                const action = formCita.dataset.action || 'create';
                if (action === 'create') {
                    Citas.createCita();
                } else {
                    Citas.updateCita();
                }
            });
        }

        // Limpiar formulario al cerrar modal
        const modal = document.getElementById('modalCita');
        if (modal) {
            modal.addEventListener('hidden.bs.modal', function() {
                formCita.reset();
                delete formCita.dataset.citaId;
                delete formCita.dataset.action;
                document.querySelector('#modalCita .modal-title').textContent = 'Nueva Cita';
            });
        }
    }

    // Listeners para botones de acción en formulario
    const btnNewCita = document.getElementById('btnNewCita');
    if (btnNewCita) {
        btnNewCita.addEventListener('click', function() {
            document.getElementById('formCita').reset();
            delete document.getElementById('formCita').dataset.citaId;
            delete document.getElementById('formCita').dataset.action;
            document.querySelector('#modalCita .modal-title').textContent = 'Nueva Cita';
            Modal.open('modalCita');
        });
    }
});
