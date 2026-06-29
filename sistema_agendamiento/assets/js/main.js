/**
 * Script principal de la aplicación
 * Internet Cordillera - Sistema de Agendamiento
 */

// Utilidades
const Utils = {
    /**
     * Mostrar alerta
     */
    showAlert: function(message, type = 'info', element = null) {
        const container = element || document.body;
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type}`;
        alertDiv.innerHTML = `
            <span>
                ${type === 'success' ? '✓' : type === 'danger' ? '✕' : type === 'warning' ? '⚠' : 'ℹ'}
            </span>
            <span>${message}</span>
        `;
        
        if (element) {
            element.insertBefore(alertDiv, element.firstChild);
        } else {
            document.body.insertBefore(alertDiv, document.body.firstChild);
        }
        
        setTimeout(() => {
            alertDiv.remove();
        }, 5000);
    },

    /**
     * Hacer petición AJAX
     */
    ajax: function(options) {
        const {
            url,
            method = 'POST',
            data = null,
            contentType = 'application/json',
            success = null,
            error = null,
            complete = null
        } = options;

        let headers = {};
        let body = null;

        if (data) {
            if (contentType === false) {
                // FormData - no agregar Content-Type, el navegador lo hará automáticamente
                body = data;
            } else if (contentType === 'application/json') {
                headers['Content-Type'] = 'application/json';
                body = JSON.stringify(data);
            } else {
                headers['Content-Type'] = contentType;
                body = new URLSearchParams(data);
            }
        }

        fetch(url, {
            method: method,
            headers: headers,
            body: body
        })
        .then(response => response.json())
        .then(result => {
            if (success) success(result);
            if (complete) complete();
        })
        .catch(err => {
            console.error('Error:', err);
            if (error) error(err);
            if (complete) complete();
        });
    },

    /**
     * Formatear fecha
     */
    formatDate: function(date, format = 'dd/mm/yyyy') {
        const d = new Date(date);
        const day = String(d.getDate()).padStart(2, '0');
        const month = String(d.getMonth() + 1).padStart(2, '0');
        const year = d.getFullYear();
        const hours = String(d.getHours()).padStart(2, '0');
        const minutes = String(d.getMinutes()).padStart(2, '0');

        return format
            .replace('dd', day)
            .replace('mm', month)
            .replace('yyyy', year)
            .replace('hh', hours)
            .replace('ii', minutes);
    },

    /**
     * Obtener nombre del día
     */
    getDayName: function(date) {
        const days = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
        return days[new Date(date).getDay()];
    },

    /**
     * Validar email
     */
    isValidEmail: function(email) {
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return regex.test(email);
    },

    /**
     * Validar teléfono
     */
    isValidPhone: function(phone) {
        const regex = /^\+?[\d\s\-\(\)]{9,}$/;
        return regex.test(phone);
    },

    /**
     * Loading spinner
     */
    showLoading: function(element) {
        element.classList.add('loading');
    },

    hideLoading: function(element) {
        element.classList.remove('loading');
    }
};

// Modal Manager
const Modal = {
    /**
     * Abrir modal
     */
    open: function(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('show');
        }
    },

    /**
     * Cerrar modal
     */
    close: function(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('show');
        }
    },

    /**
     * Cerrar todos los modales
     */
    closeAll: function() {
        document.querySelectorAll('.modal.show').forEach(modal => {
            modal.classList.remove('show');
        });
    },

    /**
     * Inicializar listeners de modales
     */
    init: function() {
        // Cerrar modal al hacer clic en el botón de cerrar
        document.querySelectorAll('.modal-close').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const modal = e.target.closest('.modal');
                if (modal) {
                    modal.classList.remove('show');
                }
            });
        });

        // Cerrar modal al hacer clic fuera del contenido
        document.querySelectorAll('.modal').forEach(modal => {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.classList.remove('show');
                }
            });
        });
    }
};

// Inicializar cuando el DOM está listo
document.addEventListener('DOMContentLoaded', function() {
    Modal.init();

    // Manejar navegación activa
    const currentPage = document.body.getAttribute('data-page');
    if (currentPage) {
        document.querySelectorAll('.navbar-item').forEach(item => {
            const link = item.querySelector('a');
            if (link && link.getAttribute('href').includes(currentPage)) {
                item.classList.add('active');
            } else {
                item.classList.remove('active');
            }
        });

        document.querySelectorAll('.sidebar-menu li').forEach(item => {
            const link = item.querySelector('a');
            if (link && link.getAttribute('href').includes(currentPage)) {
                item.classList.add('active');
            } else {
                item.classList.remove('active');
            }
        });
    }
});
