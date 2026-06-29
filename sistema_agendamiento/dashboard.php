<?php
/**
 * Dashboard Principal
 */

require_once 'config/config.php';
require_once 'config/session.php';
require_once 'models/Cita.php';
require_once 'models/Usuario.php';

requireAuth();

$conn = getDBConnection();
$citaModel = new Cita($conn);
$usuarioModel = new Usuario($conn);

// Obtener estadísticas
$today = date('Y-m-d');
$weekStart = date('Y-m-d', strtotime('monday this week'));
$weekEnd = date('Y-m-d', strtotime('sunday this week'));

$stats = $citaModel->getStats([
    'fecha_inicio' => $today,
    'fecha_fin' => $today
]);

// Contar citas por estado
$citasPendientes = 0;
$citasCompletadas = 0;
$citasInstalacion = 0;
$citasRetiro = 0;
$citasSoporte = 0;

foreach ($stats as $stat) {
    if ($stat['estado'] === 'pendiente') {
        $citasPendientes += $stat['total'];
    } elseif ($stat['estado'] === 'completada') {
        $citasCompletadas += $stat['total'];
    }

    if ($stat['tipo_cita'] === 'instalacion') {
        $citasInstalacion += $stat['total'];
    } elseif ($stat['tipo_cita'] === 'retiro') {
        $citasRetiro += $stat['total'];
    } elseif ($stat['tipo_cita'] === 'soporte') {
        $citasSoporte += $stat['total'];
    }
}

// Obtener citas de la semana
$citasSemanales = $citaModel->getAll([
    'fecha_inicio' => $weekStart,
    'fecha_fin' => $weekEnd
]);

closeDBConnection($conn);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
        <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="assets/images/favicon.ico">
</head>
<body data-page="dashboard">
    <!-- Navbar -->
    <nav class="navbar">
        <div class="navbar-container">
            <div class="navbar-brand">
                <span>🌐</span>
                <a href="dashboard.php">Internet Cordillera</a>
            </div>

            <ul class="navbar-menu">
                <li class="navbar-item active"><a href="dashboard.php">Dashboard</a></li>
                <li class="navbar-item"><a href="citas.php">Citas</a></li>
                <li class="navbar-item"><a href="calendario.php">Calendario</a></li>
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
        <div class="page-title">Dashboard</div>
        <div class="page-subtitle">Bienvenido, <?php echo $_SESSION['user_nombre']; ?>. Aquí está el resumen de hoy.</div>

        <!-- Grid de Estadísticas -->
        <div class="stats-grid">
            <div class="stat-card success">
                <div class="stat-label">Completadas Hoy</div>
                <div class="stat-value"><?php echo $citasCompletadas; ?></div>
                <div class="stat-change">Citas finalizadas</div>
            </div>

            <div class="stat-card warning">
                <div class="stat-label">Pendientes Hoy</div>
                <div class="stat-value"><?php echo $citasPendientes; ?></div>
                <div class="stat-change">Citas programadas</div>
            </div>

            <div class="stat-card info">
                <div class="stat-label">Instalaciones</div>
                <div class="stat-value"><?php echo $citasInstalacion; ?></div>
                <div class="stat-change">Servicios nuevos</div>
            </div>

            <div class="stat-card">
                <div class="stat-label">Retiros</div>
                <div class="stat-value"><?php echo $citasRetiro; ?></div>
                <div class="stat-change">Retiro de equipos</div>
            </div>

            <div class="stat-card">
                <div class="stat-label">Soportes</div>
                <div class="stat-value"><?php echo $citasSoporte; ?></div>
                <div class="stat-change">Visitas de soporte</div>
            </div>
        </div>

        <!-- Citas Recientes -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Citas de Esta Semana</h3>
                <p class="card-subtitle">Desde <?php echo date('d/m/Y', strtotime($weekStart)); ?> hasta <?php echo date('d/m/Y', strtotime($weekEnd)); ?></p>
            </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Hora</th>
                            <th>Cliente</th>
                            <th>Teléfono</th>
                            <th>Tipo</th>
                            <th>Técnico</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($citasSemanales)): ?>
                            <tr>
                                <td colspan="7" class="text-center">No hay citas para esta semana</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($citasSemanales as $cita): ?>
                                <tr>
                                    <td><?php echo date('d/m/Y', strtotime($cita['fecha_cita'])); ?></td>
                                    <td><?php echo $cita['hora_inicio']; ?></td>
                                    <td><?php echo htmlspecialchars($cita['cliente_nombre']); ?></td>
                                    <td><?php echo htmlspecialchars($cita['cliente_telefono']); ?></td>
                                    <td>
                                        <?php
                                        $tipos = [
                                            'instalacion' => 'Instalación',
                                            'retiro' => 'Retiro',
                                            'soporte' => 'Soporte'
                                        ];
                                        $badge_class = [
                                            'instalacion' => 'success',
                                            'retiro' => 'warning',
                                            'soporte' => 'info'
                                        ];
                                        $tipo = $cita['tipo_cita'];
                                        ?>
                                        <span class="badge badge-<?php echo $badge_class[$tipo] ?? 'primary'; ?>">
                                            <?php echo $tipos[$tipo] ?? $tipo; ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($cita['tecnico_nombre']); ?></td>
                                    <td>
                                        <?php
                                        $estado_badge = [
                                            'pendiente' => 'warning',
                                            'completada' => 'success',
                                            'cancelada' => 'danger'
                                        ];
                                        $estado_label = [
                                            'pendiente' => 'Pendiente',
                                            'completada' => 'Completada',
                                            'cancelada' => 'Cancelada'
                                        ];
                                        $estado = $cita['estado'];
                                        ?>
                                        <span class="badge badge-<?php echo $estado_badge[$estado] ?? 'primary'; ?>">
                                            <?php echo $estado_label[$estado] ?? $estado; ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="card-footer">
                <a href="citas.php" class="btn btn-primary">Ver todas las citas</a>
            </div>
        </div>
    </div>

    <script src="assets/js/main.js"></script>
    <script>
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
