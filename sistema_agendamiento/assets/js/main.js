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

/* ============================================
   MOBILE NAVIGATION (HAMBURGER MENU)
   ============================================ */

const MobileNav = {
    /**
     * Inicializar navegación móvil
     */
    init: function() {
        const toggle = document.querySelector('.navbar-toggle');
        const overlay = document.querySelector('.navbar-mobile-overlay');
        const menu = document.querySelector('.navbar-menu');
        const navItems = document.querySelectorAll('.navbar-item a');

        if (!toggle) return;

        // Toggle hamburger menu
        toggle.addEventListener('click', () => {
            this.toggleMenu();
        });

        // Cerrar menú al hacer clic en overlay
        if (overlay) {
            overlay.addEventListener('click', () => {
                this.closeMenu();
            });
        }

        // Cerrar menú al hacer clic en un item
        navItems.forEach(item => {
            item.addEventListener('click', () => {
                this.closeMenu();
            });
        });

        // Cerrar menú al presionar ESC
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                this.closeMenu();
            }
        });

        // Cerrar menú al redimensionar ventana si pasa de mobile a desktop
        window.addEventListener('resize', () => {
            if (window.innerWidth > 768) {
                this.closeMenu();
            }
        });
    },

    /**
     * Toggle del menú
     */
    toggleMenu: function() {
        const toggle = document.querySelector('.navbar-toggle');
        const menu = document.querySelector('.navbar-menu');
        const overlay = document.querySelector('.navbar-mobile-overlay');
        const userSection = document.querySelector('.navbar-user');

        if (toggle && menu) {
            toggle.classList.toggle('active');
            menu.classList.toggle('active');
            
            if (overlay) {
                overlay.classList.toggle('active');
            }

            if (userSection) {
                userSection.classList.toggle('active');
            }

            // Prevenir scroll cuando menú está abierto
            document.body.style.overflow = toggle.classList.contains('active') ? 'hidden' : '';
        }
    },

    /**
     * Abrir menú
     */
    openMenu: function() {
        const toggle = document.querySelector('.navbar-toggle');
        if (toggle && !toggle.classList.contains('active')) {
            this.toggleMenu();
        }
    },

    /**
     * Cerrar menú
     */
    closeMenu: function() {
        const toggle = document.querySelector('.navbar-toggle');
        if (toggle && toggle.classList.contains('active')) {
            this.toggleMenu();
        }
    }
};

// Calendar Responsiveness Module
const CalendarResponsive = {
    currentView: null, // 'desktop', '3day', '1day'
    currentDayOffset: 0, // Offset para la fecha inicial en vistas responsivas
    viewControls: null,
    originalDays: [],

    /**
     * Inicializar módulo de calendario responsivo
     */
    init: function() {
        this.detectView();
        window.addEventListener('resize', () => this.handleResize());
        
        // Crear controles de vista si es necesario
        this.createViewControls();
    },

    /**
     * Detectar tamaño de ventana y establecer vista
     */
    detectView: function() {
        const width = window.innerWidth;
        let newView;

        if (width > 768) {
            newView = 'desktop';
        } else if (width > 480) {
            newView = '3day';
        } else {
            newView = '1day';
        }

        if (newView !== this.currentView) {
            this.currentView = newView;
            this.currentDayOffset = 0;
            this.applyView();
        }
    },

    /**
     * Manejar evento de redimensionamiento
     */
    handleResize: function() {
        this.detectView();
    },

    /**
     * Aplicar vista actual
     */
    applyView: function() {
        const calendar = document.querySelector('.weekly-calendar');
        if (!calendar) return;

        // Limpiar clases anteriores
        calendar.classList.remove('calendar-1day', 'calendar-3day');

        if (this.currentView === 'desktop') {
            // Vista de escritorio: 6 días, sin cambios
            this.showAllDays();
        } else if (this.currentView === '3day') {
            // Vista tablet: 3 días
            calendar.classList.add('calendar-3day');
            this.show3Days();
        } else if (this.currentView === '1day') {
            // Vista móvil: 1 día
            calendar.classList.add('calendar-1day');
            this.show1Day();
        }

        this.updateViewControls();
    },

    /**
     * Mostrar todos los días (vista desktop)
     */
    showAllDays: function() {
        const columns = document.querySelectorAll('.calendar-day-column');
        columns.forEach(col => col.style.display = '');
    },

    /**
     * Mostrar 3 días (vista tablet)
     */
    show3Days: function() {
        const columns = document.querySelectorAll('.calendar-day-column');
        const startIndex = this.currentDayOffset;
        
        columns.forEach((col, index) => {
            if (index >= startIndex && index < startIndex + 3) {
                col.style.display = '';
            } else {
                col.style.display = 'none';
            }
        });
    },

    /**
     * Mostrar 1 día (vista móvil)
     */
    show1Day: function() {
        const columns = document.querySelectorAll('.calendar-day-column');
        const dayIndex = this.currentDayOffset;
        
        columns.forEach((col, index) => {
            if (index === dayIndex) {
                col.style.display = '';
            } else {
                col.style.display = 'none';
            }
        });
    },

    /**
     * Crear controles de navegación
     */
    createViewControls: function() {
        const calendar = document.querySelector('.calendar-container');
        if (!calendar || document.querySelector('.calendar-view-controls')) {
            return; // Ya existe o no hay contenedor
        }

        const controls = document.createElement('div');
        controls.className = 'calendar-view-controls';
        controls.id = 'calendarViewControls';
        controls.innerHTML = `
            <span class="calendar-view-label" id="calendarViewLabel"></span>
            <div class="calendar-view-buttons">
                <button class="btn btn-sm btn-outline" id="prevDayBtn" title="Día anterior">
                    ← Anterior
                </button>
                <button class="btn btn-sm btn-outline" id="nextDayBtn" title="Siguiente día">
                    Siguiente →
                </button>
            </div>
        `;

        // Insertar controles antes del calendario
        const calendarHeader = calendar.querySelector('.calendar-header');
        if (calendarHeader && calendarHeader.nextElementSibling) {
            calendarHeader.nextElementSibling.parentNode.insertBefore(controls, calendarHeader.nextElementSibling);
        } else {
            calendar.insertBefore(controls, calendar.querySelector('.weekly-calendar'));
        }

        // Agregar event listeners
        document.getElementById('prevDayBtn')?.addEventListener('click', () => this.previousDay());
        document.getElementById('nextDayBtn')?.addEventListener('click', () => this.nextDay());

        this.viewControls = controls;
    },

    /**
     * Ir al día anterior
     */
    previousDay: function() {
        const maxOffset = this.currentView === '3day' ? 4 : 5; // 6 días - 3 o 1
        if (this.currentDayOffset > 0) {
            this.currentDayOffset--;
            this.applyView();
        }
    },

    /**
     * Ir al siguiente día
     */
    nextDay: function() {
        const maxOffset = this.currentView === '3day' ? 4 : 5; // 6 días - 3 o 1
        if (this.currentDayOffset < maxOffset) {
            this.currentDayOffset++;
            this.applyView();
        }
    },

    /**
     * Actualizar etiqueta de controles
     */
    updateViewControls: function() {
        if (!this.viewControls) return;

        const label = document.getElementById('calendarViewLabel');
        const columns = document.querySelectorAll('.calendar-day-column');
        
        if (this.currentView === 'desktop') {
            this.viewControls.style.display = 'none';
        } else {
            this.viewControls.style.display = 'flex';
            
            if (this.currentView === '1day') {
                const dayCol = Array.from(columns)[this.currentDayOffset];
                if (dayCol) {
                    const dayName = dayCol.querySelector('.day-name')?.textContent || '';
                    const dayDate = dayCol.querySelector('.day-date')?.textContent || '';
                    label.textContent = `${dayName} ${dayDate}`;
                }
            } else if (this.currentView === '3day') {
                const startCol = Array.from(columns)[this.currentDayOffset];
                const endCol = Array.from(columns)[Math.min(this.currentDayOffset + 2, columns.length - 1)];
                
                if (startCol && endCol) {
                    const startDate = startCol.querySelector('.day-date')?.textContent || '';
                    const endDate = endCol.querySelector('.day-date')?.textContent || '';
                    label.textContent = `${startDate} - ${endDate}`;
                }
            }
        }

        // Deshabilitar botones si es necesario
        const prevBtn = document.getElementById('prevDayBtn');
        const nextBtn = document.getElementById('nextDayBtn');
        
        if (prevBtn) prevBtn.disabled = this.currentDayOffset === 0;
        if (nextBtn) {
            const maxOffset = this.currentView === '3day' ? 4 : 5;
            nextBtn.disabled = this.currentDayOffset >= maxOffset;
        }
    }
};

// Inicializar navegación móvil cuando el DOM está listo
document.addEventListener('DOMContentLoaded', function() {
    MobileNav.init();
    
    // Inicializar calendario responsivo si está en la página calendario
    if (document.body.getAttribute('data-page') === 'calendario') {
        CalendarResponsive.init();
    }
});
