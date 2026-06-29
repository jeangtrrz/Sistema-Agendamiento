<?php
/**
 * Página de Calendario Semanal
 */

require_once 'config/config.php';
require_once 'config/session.php';

requireAuth();

// Obtener el lunes de la semana actual
$monday = date('Y-m-d', strtotime('monday this week'));
$days = [];

for ($i = 0; $i < 6; $i++) {
    $days[] = date('Y-m-d', strtotime($monday . ' +' . $i . ' days'));
}

// Nombres de días en español
$diasSemana = [
    1 => 'LUN',
    2 => 'MAR',
    3 => 'MIÉ',
    4 => 'JUE',
    5 => 'VIE',
    6 => 'SÁB',
    7 => 'DOM'
];

$horaInicio = intval(HORA_INICIO);
$horaFin = intval(HORA_FIN);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendario - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
        <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="assets/images/favicon.ico">
</head>
<body data-page="calendario">
    <!-- Navbar -->
    <nav class="navbar">
        <div class="navbar-container">
            <div class="navbar-brand">
                <span>🌐</span>
                <a href="dashboard.php">Internet Cordillera</a>
            </div>

            <ul class="navbar-menu">
                <li class="navbar-item"><a href="dashboard.php">Dashboard</a></li>
                <li class="navbar-item"><a href="citas.php">Citas</a></li>
                <li class="navbar-item active"><a href="calendario.php">Calendario</a></li>
                <li class="navbar-item"><a href="reportes.php">Reportes</a></li>
                <?php if (isAdmin()): ?>
                    <li class="navbar-item"><a href="usuarios.php">Usuarios</a></li>
                <?php endif; ?>
            </ul>

            <div class="navbar-user">
                <div class="navbar-user-profile">
                    <?php echo strtoupper(substr($_SESSION['user_nombre'], 0, 1)); ?>
                </div>
                <span><?php echo $_SESSION['user_nombre']; ?></span>
                <a href="#" onclick="logout(); return false;" style="margin-left: 12px;">Salir</a>
            </div>
        </div>
    </nav>

    <!-- Contenido Principal -->
    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
            <div>
                <div class="page-title">Calendario Semanal</div>
                <div class="page-subtitle">Visualiza todas las citas de la semana</div>
            </div>
            <div style="display: flex; gap: 12px;">
                <button class="btn btn-outline" onclick="previousWeek()">← Semana Anterior</button>
                <button class="btn btn-outline" onclick="currentWeek()">Hoy</button>
                <button class="btn btn-outline" onclick="nextWeek()">Semana Siguiente →</button>
            </div>
        </div>

        <!-- Calendario Semanal -->
        <div class="card">
            <div class="calendar-container">
                <div class="calendar-header">
                    <div class="calendar-controls">
                        <h2 class="calendar-title">
                            Semana del <span id="weekStart">
                                <?php echo date('d/m/Y', strtotime($monday)); ?>
                            </span>
                        </h2>
                    </div>
                </div>

                <!-- Tablero semanal -->
                <div class="weekly-calendar" id="weeklyCalendar">
                    <!-- Columna de horarios -->
                    <div class="weekly-calendar-times">
                        <!-- Encabezado vacío para alineación -->
                        <div class="time-slot-header"></div>
                        <?php for ($hour = $horaInicio; $hour < $horaFin; $hour++): ?>
                            <div class="time-slot">
                                <?php echo str_pad($hour, 2, '0', STR_PAD_LEFT); ?>:00
                            </div>
                        <?php endfor; ?>
                    </div>

                    <!-- Días de la semana -->
                    <div class="weekly-calendar-days">
                        <?php foreach ($days as $day): ?>
                            <div class="calendar-day-column">
                                <div class="calendar-day-header">
                                    <span class="day-name">
                                        <?php echo $diasSemana[intval(date('N', strtotime($day)))]; ?>
                                    </span>
                                    <span class="day-date">
                                        <?php echo date('d/m', strtotime($day)); ?>
                                    </span>
                                </div>
                                <div class="calendar-time-slots" data-date="<?php echo $day; ?>">
                                    <?php for ($hour = $horaInicio; $hour < $horaFin; $hour++): ?>
                                        <div class="calendar-slot" data-hour="<?php echo $hour; ?>">
                                            <!-- Las citas se cargarán aquí con JS -->
                                        </div>
                                    <?php endfor; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Leyenda de colores -->
        <div style="display: flex; gap: 24px; margin-top: 24px; justify-content: center;">
            <div style="display: flex; align-items: center; gap: 8px;">
                <div style="width: 20px; height: 20px; background-color: #4CAF50; border-radius: 4px;"></div>
                <span>Instalación</span>
            </div>
            <div style="display: flex; align-items: center; gap: 8px;">
                <div style="width: 20px; height: 20px; background-color: #FF9800; border-radius: 4px;"></div>
                <span>Retiro</span>
            </div>
            <div style="display: flex; align-items: center; gap: 8px;">
                <div style="width: 20px; height: 20px; background-color: #2196F3; border-radius: 4px;"></div>
                <span>Soporte</span>
            </div>
        </div>
    </div>

    <!-- Modal para detalles de cita -->
    <div id="modalCitaDetails" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Detalles de Cita</h2>
                <button class="modal-close">×</button>
            </div>
            <div class="modal-body" id="citaDetailsContent">
                <!-- Se carga dinámicamente -->
            </div>
        </div>
    </div>

    <script src="assets/js/main.js"></script>
    <script>
        let currentMonday = new Date('<?php echo $monday; ?>');

        function loadWeekCalendar() {
            const monday = currentMonday;
            const formData = new FormData();
            formData.append('action', 'getWeekCitas');

            fetch('controllers/citas.api.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    drawCalendar(result.data);
                    updateWeekDisplay();
                }
            });
        }

        function drawCalendar(citas) {
            // Limpiar calendarios
            document.querySelectorAll('.calendar-slot').forEach(slot => {
                slot.innerHTML = '';
            });

            // Dibujar citas
            citas.forEach(cita => {
                const col = document.querySelector(`[data-date="${cita.fecha_cita}"]`);
                if (col) {
                    const hour = parseInt(cita.hora_inicio.split(':')[0]);
                    const slot = col.querySelector(`[data-hour="${hour}"]`);
                    
                    if (slot) {
                        const citaDiv = document.createElement('div');
                        const tipo = cita.tipo_cita;
                        citaDiv.className = `calendar-slot-cita ${tipo}`;
                        citaDiv.innerHTML = `
                            <strong>${cita.cliente_nombre.substring(0, 10)}</strong><br>
                            <small>${cita.tecnico_nombre}</small>
                        `;
                        citaDiv.onclick = () => showCitaDetails(cita);
                        slot.appendChild(citaDiv);
                    }
                }
            });
        }

        function showCitaDetails(cita) {
            const tipos = {
                'instalacion': 'Instalación de Servicio',
                'retiro': 'Retiro de Equipamiento',
                'soporte': 'Visita de Soporte'
            };

            const estados = {
                'pendiente': 'Pendiente',
                'completada': 'Completada',
                'cancelada': 'Cancelada'
            };

            const content = `
                <div class="form-group">
                    <label>Cliente</label>
                    <p>${cita.cliente_nombre}</p>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Teléfono</label>
                        <p>${cita.cliente_telefono}</p>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <p>${cita.cliente_email || '-'}</p>
                    </div>
                </div>
                <div class="form-group">
                    <label>Dirección</label>
                    <p>${cita.cliente_direccion || '-'}</p>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Tipo de Cita</label>
                        <p>${tipos[cita.tipo_cita]}</p>
                    </div>
                    <div class="form-group">
                        <label>Estado</label>
                        <p><span class="badge badge-${getEstadoBadgeClass(cita.estado)}">${estados[cita.estado]}</span></p>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Fecha y Hora</label>
                        <p>${new Date(cita.fecha_cita).toLocaleDateString('es-CL')} ${cita.hora_inicio}</p>
                    </div>
                    <div class="form-group">
                        <label>Técnico</label>
                        <p>${cita.tecnico_nombre}</p>
                    </div>
                </div>
                <div class="form-group">
                    <label>Descripción</label>
                    <p>${cita.descripcion || '-'}</p>
                </div>
                <div class="form-group">
                    <label>Observaciones</label>
                    <p>${cita.observaciones || '-'}</p>
                </div>
            `;

            document.getElementById('citaDetailsContent').innerHTML = content;
            Modal.open('modalCitaDetails');
        }

        function getEstadoBadgeClass(estado) {
            const clases = {
                'pendiente': 'warning',
                'completada': 'success',
                'cancelada': 'danger'
            };
            return clases[estado] || 'primary';
        }

        function previousWeek() {
            currentMonday = new Date(currentMonday.getTime() - (7 * 24 * 60 * 60 * 1000));
            loadWeekCalendar();
        }

        function nextWeek() {
            currentMonday = new Date(currentMonday.getTime() + (7 * 24 * 60 * 60 * 1000));
            loadWeekCalendar();
        }

        function currentWeek() {
            currentMonday = new Date();
            currentMonday.setDate(currentMonday.getDate() - (currentMonday.getDay() + 6) % 7);
            loadWeekCalendar();
        }

        function updateWeekDisplay() {
            const weekStart = currentMonday;
            const formatted = weekStart.toLocaleDateString('es-CL');
            document.getElementById('weekStart').textContent = formatted;
        }

        // Cargar al inicio
        document.addEventListener('DOMContentLoaded', function() {
            loadWeekCalendar();
        });

        function logout() {
            if (confirm('¿Está seguro que desea cerrar sesión?')) {
                const formData = new FormData();
                formData.append('action', 'logout');

                Utils.ajax({
                    url: 'controllers/auth.api.php',
                    method: 'POST',
                    data: formData,
                    contentType: false,
                    success: (response) => {
                        window.location.href = 'index.php';
                    },
                    error: () => {
                        alert('Error al cerrar sesión. Intente de nuevo.');
                    }
                });
            }
        }
    </script>
</body>
</html>
