<?php
/**
 * Página de Citas
 */

require_once 'config/config.php';
require_once 'config/session.php';
require_once 'models/Usuario.php';

requireAuth();

$conn = getDBConnection();
$usuarioModel = new Usuario($conn);
$tecnicos = $usuarioModel->getTechnicians();
closeDBConnection($conn);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Citas - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
        <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="assets/images/favicon.ico">
</head>
<body data-page="citas">
    <!-- Navbar -->
    <nav class="navbar">
        <div class="navbar-container">
            <div class="navbar-brand">
                <span>🌐</span>
                <a href="dashboard.php">Internet Cordillera</a>
            </div>

            <ul class="navbar-menu">
                <li class="navbar-item"><a href="dashboard.php">Dashboard</a></li>
                <li class="navbar-item active"><a href="citas.php">Citas</a></li>
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
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
            <div>
                <div class="page-title">Gestión de Citas</div>
                <div class="page-subtitle">Administra todas las citas del sistema</div>
            </div>
            <?php if (isAdmin()): ?>
                <button id="btnNewCita" class="btn btn-primary btn-lg">+ Nueva Cita</button>
            <?php endif; ?>
        </div>

        <!-- Filtros -->
        <div class="card" style="margin-bottom: 24px;">
            <div class="card-body">
                <div class="form-row">
                    <div class="form-group">
                        <label for="filterTipo">Tipo de Cita</label>
                        <select id="filterTipo">
                            <option value="">Todos</option>
                            <option value="instalacion">Instalación</option>
                            <option value="retiro">Retiro</option>
                            <option value="soporte">Soporte</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="filterEstado">Estado</label>
                        <select id="filterEstado">
                            <option value="">Todos</option>
                            <option value="pendiente">Pendiente</option>
                            <option value="completada">Completada</option>
                            <option value="cancelada">Cancelada</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de Citas -->
        <div class="card">
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
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="8" class="text-center">Cargando citas...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Nueva/Editar Cita -->
    <div id="modalCita" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Nueva Cita</h2>
                <button class="modal-close">×</button>
            </div>

            <div class="modal-body">
                <form id="formCita">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="inputClienteNombre">Nombre del Cliente *</label>
                            <input type="text" id="inputClienteNombre" name="cliente_nombre" required>
                        </div>
                        <div class="form-group">
                            <label for="inputClienteTelefono">Teléfono *</label>
                            <input type="tel" id="inputClienteTelefono" name="cliente_telefono" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="inputClienteEmail">Email</label>
                            <input type="email" id="inputClienteEmail" name="cliente_email">
                        </div>
                        <div class="form-group">
                            <label for="inputClienteDireccion">Dirección *</label>
                            <input type="text" id="inputClienteDireccion" name="cliente_direccion" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="inputTipoCita">Tipo de Cita *</label>
                            <select id="inputTipoCita" name="tipo_cita" required>
                                <option value="">Seleccionar...</option>
                                <option value="instalacion">Instalación de Servicio</option>
                                <option value="retiro">Retiro de Equipamiento</option>
                                <option value="soporte">Visita de Soporte</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="inputTecnico">Técnico Asignado *</label>
                            <select id="inputTecnico" name="tecnico_id" required>
                                <option value="">Seleccionar...</option>
                                <?php foreach ($tecnicos as $tecnico): ?>
                                    <option value="<?php echo $tecnico['id']; ?>">
                                        <?php echo htmlspecialchars($tecnico['nombre']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="inputFechaCita">Fecha de Cita *</label>
                            <input type="date" id="inputFechaCita" name="fecha_cita" required>
                        </div>
                        <div class="form-group">
                            <label for="inputHoraCita">Hora de Cita *</label>
                            <input type="time" id="inputHoraCita" name="hora_inicio" required>
                        </div>
                    </div>

                    <div class="form-group full">
                        <label for="inputDescripcion">Descripción</label>
                        <textarea id="inputDescripcion" name="descripcion" placeholder="Notas adicionales sobre la cita"></textarea>
                    </div>

                    <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px;">
                        <button type="button" class="btn btn-secondary" onclick="Modal.close('modalCita')">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar Cita</button>
                    </div>
                </form>
            </div>

            <div class="modal-footer" style="display: none;">
                <!-- Botones movidos dentro del formulario -->
            </div>
        </div>
    </div>

    <script src="assets/js/main.js"></script>
    <script src="assets/js/citas.js"></script>
    <script>
        // Cargar citas al iniciar
        document.addEventListener('DOMContentLoaded', function() {
            Citas.loadCitas();
        });

        // Filtros
        document.getElementById('filterTipo').addEventListener('change', function() {
            const filtros = {
                tipo_cita: this.value,
                estado: document.getElementById('filterEstado').value
            };
            Citas.loadCitas(filtros);
        });

        document.getElementById('filterEstado').addEventListener('change', function() {
            const filtros = {
                tipo_cita: document.getElementById('filterTipo').value,
                estado: this.value
            };
            Citas.loadCitas(filtros);
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
