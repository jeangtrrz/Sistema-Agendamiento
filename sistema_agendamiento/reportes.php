<?php
/**
 * Página de Reportes
 */

require_once 'config/config.php';
require_once 'config/session.php';

requireAuth();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
        <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="assets/images/favicon.ico">
</head>
<body data-page="reportes">
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
                <li class="navbar-item"><a href="calendario.php">Calendario</a></li>
                <li class="navbar-item active"><a href="reportes.php">Reportes</a></li>
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
        <div class="page-title">Reportes</div>
        <div class="page-subtitle">Análisis y estadísticas de citas</div>

        <!-- Filtros -->
        <div class="card" style="margin-bottom: 24px;">
            <div class="card-header">
                <h3 class="card-title">Filtros de Reporte</h3>
            </div>
            <div class="card-body">
                <div class="form-row">
                    <div class="form-group">
                        <label for="reportFechaInicio">Fecha Inicio</label>
                        <input type="date" id="reportFechaInicio" value="<?php echo date('Y-m-01'); ?>">
                    </div>
                    <div class="form-group">
                        <label for="reportFechaFin">Fecha Fin</label>
                        <input type="date" id="reportFechaFin" value="<?php echo date('Y-m-d'); ?>">
                    </div>
                </div>
                <div style="display: flex; gap: 12px;">
                    <button class="btn btn-primary" onclick="generateReport()">Generar Reporte</button>
                    <button class="btn btn-secondary" onclick="exportPDF()">Descargar PDF</button>
                    <button class="btn btn-secondary" onclick="exportExcel()">Descargar Excel</button>
                </div>
            </div>
        </div>

        <!-- Estadísticas -->
        <div class="stats-grid" id="reportStats">
            <div class="stat-card success">
                <div class="stat-label">Completadas</div>
                <div class="stat-value" id="citasCompletadas">0</div>
            </div>

            <div class="stat-card warning">
                <div class="stat-label">Pendientes</div>
                <div class="stat-value" id="citasPendientes">0</div>
            </div>

            <div class="stat-card danger">
                <div class="stat-label">Canceladas</div>
                <div class="stat-value" id="citasCanceladas">0</div>
            </div>

            <div class="stat-card info">
                <div class="stat-label">Total</div>
                <div class="stat-value" id="citasTotal">0</div>
            </div>
        </div>

        <!-- Gráficos y Tablas -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-top: 24px;">
            <!-- Por Tipo -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Citas por Tipo</h3>
                </div>
                <div class="card-body" id="porTipo">
                    <!-- Se carga dinámicamente -->
                </div>
            </div>

            <!-- Por Estado -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Citas por Estado</h3>
                </div>
                <div class="card-body" id="porEstado">
                    <!-- Se carga dinámicamente -->
                </div>
            </div>
        </div>

        <!-- Tabla Detallada -->
        <div class="card" style="margin-top: 24px;">
            <div class="card-header">
                <h3 class="card-title">Detalle de Citas</h3>
            </div>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Cliente</th>
                            <th>Tipo</th>
                            <th>Técnico</th>
                            <th>Estado</th>
                            <th>Horas</th>
                        </tr>
                    </thead>
                    <tbody id="reportTableBody">
                        <tr>
                            <td colspan="6" class="text-center">Selecciona un rango de fechas</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="assets/js/main.js"></script>
    <script>
        function generateReport() {
            const fechaInicio = document.getElementById('reportFechaInicio').value;
            const fechaFin = document.getElementById('reportFechaFin').value;

            if (!fechaInicio || !fechaFin) {
                Utils.showAlert('Por favor selecciona ambas fechas', 'warning');
                return;
            }

            const formData = new FormData();
            formData.append('action', 'getCitas');
            formData.append('fecha_inicio', fechaInicio);
            formData.append('fecha_fin', fechaFin);

            fetch('controllers/citas.api.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    updateReportStats(result.data);
                    updateReportTable(result.data);
                } else {
                    Utils.showAlert(result.error, 'danger');
                }
            });
        }

        function updateReportStats(citas) {
            let completadas = 0, pendientes = 0, canceladas = 0;
            let tiposCount = { instalacion: 0, retiro: 0, soporte: 0 };
            let estadosCount = { pendiente: 0, completada: 0, cancelada: 0 };

            citas.forEach(cita => {
                if (cita.estado === 'completada') completadas++;
                else if (cita.estado === 'pendiente') pendientes++;
                else if (cita.estado === 'cancelada') canceladas++;

                tiposCount[cita.tipo_cita]++;
                estadosCount[cita.estado]++;
            });

            document.getElementById('citasCompletadas').textContent = completadas;
            document.getElementById('citasPendientes').textContent = pendientes;
            document.getElementById('citasCanceladas').textContent = canceladas;
            document.getElementById('citasTotal').textContent = citas.length;

            // Por tipo
            const tiposContent = `
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid #e2e8f0;">
                    <span>Instalaciones</span>
                    <strong>${tiposCount.instalacion}</strong>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid #e2e8f0;">
                    <span>Retiros</span>
                    <strong>${tiposCount.retiro}</strong>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px 0;">
                    <span>Soportes</span>
                    <strong>${tiposCount.soporte}</strong>
                </div>
            `;
            document.getElementById('porTipo').innerHTML = tiposContent;

            // Por estado
            const estadosContent = `
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid #e2e8f0;">
                    <span>Pendientes</span>
                    <strong>${estadosCount.pendiente}</strong>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid #e2e8f0;">
                    <span>Completadas</span>
                    <strong>${estadosCount.completada}</strong>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px 0;">
                    <span>Canceladas</span>
                    <strong>${estadosCount.cancelada}</strong>
                </div>
            `;
            document.getElementById('porEstado').innerHTML = estadosContent;
        }

        function updateReportTable(citas) {
            const tbody = document.getElementById('reportTableBody');

            if (citas.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center">No hay citas en este período</td></tr>';
                return;
            }

            const tipos = { 'instalacion': 'Instalación', 'retiro': 'Retiro', 'soporte': 'Soporte' };
            const estados = { 'pendiente': 'Pendiente', 'completada': 'Completada', 'cancelada': 'Cancelada' };
            const badgeClass = {
                'instalacion': 'success', 'retiro': 'warning', 'soporte': 'info',
                'pendiente': 'warning', 'completada': 'success', 'cancelada': 'danger'
            };

            tbody.innerHTML = citas.map(cita => {
                const horaInicio = cita.hora_inicio.substring(0, 5);
                const horaFin = cita.hora_fin.substring(0, 5);
                return `
                    <tr>
                        <td>${new Date(cita.fecha_cita).toLocaleDateString('es-CL')}</td>
                        <td>${cita.cliente_nombre}</td>
                        <td><span class="badge badge-${badgeClass[cita.tipo_cita]}">${tipos[cita.tipo_cita]}</span></td>
                        <td>${cita.tecnico_nombre}</td>
                        <td><span class="badge badge-${badgeClass[cita.estado]}">${estados[cita.estado]}</span></td>
                        <td>${horaInicio} - ${horaFin}</td>
                    </tr>
                `;
            }).join('');
        }

        function exportPDF() {
            alert('La funcionalidad de exportación PDF será habilitada en la siguiente versión');
        }

        function exportExcel() {
            alert('La funcionalidad de exportación Excel será habilitada en la siguiente versión');
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Cargar reporte inicial
            generateReport();
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
