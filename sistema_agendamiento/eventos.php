<?php
/**
 * Página de gestión de eventos y compromisos del equipo
 */

require_once 'config/config.php';
require_once 'config/session.php';
require_once 'models/Usuario.php';

requireAuth();

$conn = getDBConnection();
$usuarioModel = new Usuario($conn);
$usuarios = $usuarioModel->getAll();
closeDBConnection($conn);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eventos - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo filemtime(__DIR__ . '/assets/css/style.css'); ?>">
    <link rel="icon" type="image/x-icon" href="assets/images/favicon.ico">
</head>
<body data-page="eventos">
    <nav class="navbar">
        <div class="navbar-container">
            <div class="navbar-brand">
                <a href="dashboard.php"><img src="assets/images/logo.png" alt="Internet Cordillera" class="navbar-logo"></a>
            </div>
            <ul class="navbar-menu">
                <li class="navbar-item"><a href="dashboard.php">Dashboard</a></li>
                <li class="navbar-item"><a href="citas.php">Citas</a></li>
                <li class="navbar-item"><a href="calendario.php">Calendario</a></li>
                <li class="navbar-item active"><a href="eventos.php">Eventos</a></li>
                <li class="navbar-item"><a href="reportes.php">Reportes</a></li>
                <?php if (isAdmin()): ?>
                    <li class="navbar-item"><a href="usuarios.php">Usuarios</a></li>
                <?php endif; ?>
            </ul>
            <button class="navbar-toggle" aria-label="Abrir menú">
                <span></span>
                <span></span>
                <span></span>
            </button>
            <div class="navbar-user">
                <div class="navbar-user-profile"><?php echo strtoupper(substr($_SESSION['user_nombre'], 0, 1)); ?></div>
                <span><?php echo $_SESSION['user_nombre']; ?></span>
                <a href="#" onclick="logout(); return false;">Salir</a>
            </div>
        </div>
    </nav>

    <!-- Mobile menu overlay -->
    <div class="navbar-mobile-overlay"></div>

    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
            <div>
                <div class="page-title">Eventos y compromisos</div>
                <div class="page-subtitle">Registra reuniones, compromisos y actividades del equipo</div>
            </div>
            <button id="btnNewEvento" class="btn btn-primary btn-lg">+ Nuevo Evento</button>
        </div>

        <div class="card" style="margin-bottom: 24px;">
            <div class="card-body">
                <div class="form-row">
                    <div class="form-group">
                        <label for="filterTipoEvento">Tipo</label>
                        <select id="filterTipoEvento">
                            <option value="">Todos</option>
                            <option value="reunion">Reunión</option>
                            <option value="compromiso">Compromiso</option>
                            <option value="evento">Evento</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="filterEstadoEvento">Estado</label>
                        <select id="filterEstadoEvento">
                            <option value="">Todos</option>
                            <option value="programado">Programado</option>
                            <option value="completado">Completado</option>
                            <option value="cancelado">Cancelado</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Hora</th>
                            <th>Título</th>
                            <th>Tipo</th>
                            <th>Responsable</th>
                            <th>Lugar</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td colspan="8" class="text-center">Cargando eventos...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="modalEvento" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Nuevo Evento</h2>
                <button class="modal-close">×</button>
            </div>
            <div class="modal-body">
                <form id="formEvento">
                    <div class="form-group">
                        <label for="inputTitulo">Título *</label>
                        <input type="text" id="inputTitulo" name="titulo" required>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="inputTipoEvento">Tipo *</label>
                            <select id="inputTipoEvento" name="tipo_evento" required>
                                <option value="">Seleccionar...</option>
                                <option value="reunion">Reunión</option>
                                <option value="compromiso">Compromiso</option>
                                <option value="evento">Evento</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="inputResponsable">Responsable *</label>
                            <select id="inputResponsable" name="responsable_id" required>
                                <option value="">Seleccionar...</option>
                                <?php foreach ($usuarios as $usuario): ?>
                                    <option value="<?php echo $usuario['id']; ?>"><?php echo htmlspecialchars($usuario['nombre']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="inputFechaEvento">Fecha *</label>
                            <input type="date" id="inputFechaEvento" name="fecha_evento" required>
                        </div>
                        <div class="form-group">
                            <label for="inputHoraInicio">Hora inicio *</label>
                            <input type="time" id="inputHoraInicio" name="hora_inicio" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="inputHoraFin">Hora fin</label>
                            <input type="time" id="inputHoraFin" name="hora_fin">
                        </div>
                        <div class="form-group">
                            <label for="inputLugar">Lugar</label>
                            <input type="text" id="inputLugar" name="lugar">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputDescripcionEvento">Descripción</label>
                        <textarea id="inputDescripcionEvento" name="descripcion"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="inputEstadoEvento">Estado</label>
                        <select id="inputEstadoEvento" name="estado">
                            <option value="programado">Programado</option>
                            <option value="completado">Completado</option>
                            <option value="cancelado">Cancelado</option>
                        </select>
                    </div>
                    <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px;">
                        <button type="button" class="btn btn-secondary" onclick="Modal.close('modalEvento')">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar Evento</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="assets/js/main.js?v=<?php echo filemtime(__DIR__ . '/assets/js/main.js'); ?>"></script>
    <script src="assets/js/eventos.js?v=<?php echo filemtime(__DIR__ . '/assets/js/eventos.js'); ?>"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Eventos.loadEventos();
        });

        document.getElementById('filterTipoEvento').addEventListener('change', function() {
            Eventos.loadEventos({ tipo_evento: this.value, estado: document.getElementById('filterEstadoEvento').value });
        });

        document.getElementById('filterEstadoEvento').addEventListener('change', function() {
            Eventos.loadEventos({ tipo_evento: document.getElementById('filterTipoEvento').value, estado: this.value });
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
                    success: () => window.location.href = 'index.php',
                    error: () => alert('Error al cerrar sesión. Intente de nuevo.')
                });
            }
        }
    </script>
</body>
</html>
