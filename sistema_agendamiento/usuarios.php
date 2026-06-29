<?php
/**
 * Página de Usuarios (Admin)
 */

require_once 'config/config.php';
require_once 'config/session.php';

requireAdmin();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuarios - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
        <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="assets/images/favicon.ico">
</head>
<body data-page="usuarios">
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
                <li class="navbar-item"><a href="reportes.php">Reportes</a></li>
                <li class="navbar-item active"><a href="usuarios.php">Usuarios</a></li>
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
                <div class="page-title">Gestión de Usuarios</div>
                <div class="page-subtitle">Administra los usuarios del sistema</div>
            </div>
            <button id="btnNewUsuario" class="btn btn-primary btn-lg">+ Nuevo Usuario</button>
        </div>

        <!-- Tabla de Usuarios -->
        <div class="card">
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Perfil</th>
                            <th>Estado</th>
                            <th>Creado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="usuariosTableBody">
                        <tr>
                            <td colspan="6" class="text-center">Cargando usuarios...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Nueva/Editar Usuario -->
    <div id="modalUsuario" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Nuevo Usuario</h2>
                <button class="modal-close">×</button>
            </div>

            <div class="modal-body">
                <form id="formUsuario">
                    <div class="form-group">
                        <label for="inputNombre">Nombre *</label>
                        <input type="text" id="inputNombre" name="nombre" required>
                    </div>

                    <div class="form-group">
                        <label for="inputEmail">Email *</label>
                        <input type="email" id="inputEmail" name="email" required>
                    </div>

                    <div class="form-group" id="passwordGroup">
                        <label for="inputPassword">Contraseña *</label>
                        <input type="password" id="inputPassword" name="password" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="inputPerfil">Perfil *</label>
                            <select id="inputPerfil" name="perfil" required>
                                <option value="">Seleccionar...</option>
                                <option value="tecnico">Técnico</option>
                                <option value="administrador">Administrador</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="inputEstado">Estado *</label>
                            <select id="inputEstado" name="estado" required>
                                <option value="activo">Activo</option>
                                <option value="inactivo">Inactivo</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="Modal.close('modalUsuario')">Cancelar</button>
                <button type="submit" form="formUsuario" class="btn btn-primary">Guardar Usuario</button>
            </div>
        </div>
    </div>

    <script src="assets/js/main.js"></script>
    <script>
        const Usuarios = {
            /**
             * Cargar lista de usuarios
             */
            loadUsuarios: function() {
                const formData = new FormData();
                formData.append('action', 'getUsuarios');

                fetch('controllers/usuarios.api.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        this.displayUsuarios(result.data);
                    } else {
                        Utils.showAlert(result.error, 'danger');
                    }
                });
            },

            /**
             * Mostrar usuarios en la tabla
             */
            displayUsuarios: function(usuarios) {
                const tbody = document.getElementById('usuariosTableBody');

                if (usuarios.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="6" class="text-center">No hay usuarios</td></tr>';
                    return;
                }

                tbody.innerHTML = usuarios.map(usuario => `
                    <tr>
                        <td>${usuario.nombre}</td>
                        <td>${usuario.email}</td>
                        <td>
                            <span class="badge badge-${usuario.perfil === 'administrador' ? 'primary' : 'info'}">
                                ${usuario.perfil === 'administrador' ? 'Administrador' : 'Técnico'}
                            </span>
                        </td>
                        <td>
                            <span class="badge badge-${usuario.estado === 'activo' ? 'success' : 'danger'}">
                                ${usuario.estado === 'activo' ? 'Activo' : 'Inactivo'}
                            </span>
                        </td>
                        <td>${new Date(usuario.created_at).toLocaleDateString('es-CL')}</td>
                        <td>
                            <div class="d-flex gap-1">
                                <button class="btn btn-sm btn-primary" onclick="Usuarios.editUsuario(${usuario.id})">Editar</button>
                                <button class="btn btn-sm btn-danger" onclick="Usuarios.deleteUsuario(${usuario.id})">Eliminar</button>
                            </div>
                        </td>
                    </tr>
                `).join('');
            },

            /**
             * Crear nuevo usuario
             */
            createUsuario: function() {
                const form = document.getElementById('formUsuario');
                const formData = new FormData(form);
                formData.append('action', 'createUsuario');

                Utils.showLoading(form);

                fetch('controllers/usuarios.api.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(result => {
                    Utils.hideLoading(form);
                    if (result.success) {
                        Utils.showAlert('Usuario creado correctamente', 'success');
                        form.reset();
                        Modal.close('modalUsuario');
                        this.loadUsuarios();
                    } else {
                        Utils.showAlert(result.error, 'danger');
                    }
                });
            },

            /**
             * Editar usuario
             */
            editUsuario: function(id) {
                const formData = new FormData();
                formData.append('action', 'getUsuario');
                formData.append('id', id);

                fetch('controllers/usuarios.api.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        const usuario = result.data;
                        const form = document.getElementById('formUsuario');
                        form.dataset.usuarioId = id;
                        document.getElementById('inputNombre').value = usuario.nombre;
                        document.getElementById('inputEmail').value = usuario.email;
                        document.getElementById('inputPerfil').value = usuario.perfil;
                        document.getElementById('inputEstado').value = usuario.estado;
                        document.getElementById('passwordGroup').style.display = 'none';
                        document.getElementById('inputPassword').removeAttribute('required');
                        document.querySelector('#modalUsuario .modal-title').textContent = 'Editar Usuario';
                        form.dataset.action = 'update';
                        Modal.open('modalUsuario');
                    }
                });
            },

            /**
             * Actualizar usuario
             */
            updateUsuario: function() {
                const form = document.getElementById('formUsuario');
                const usuarioId = form.dataset.usuarioId;
                const formData = new FormData(form);
                formData.append('action', 'updateUsuario');
                formData.append('id', usuarioId);

                Utils.showLoading(form);

                fetch('controllers/usuarios.api.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(result => {
                    Utils.hideLoading(form);
                    if (result.success) {
                        Utils.showAlert('Usuario actualizado correctamente', 'success');
                        form.reset();
                        delete form.dataset.usuarioId;
                        delete form.dataset.action;
                        Modal.close('modalUsuario');
                        this.loadUsuarios();
                    } else {
                        Utils.showAlert(result.error, 'danger');
                    }
                });
            },

            /**
             * Eliminar usuario
             */
            deleteUsuario: function(id) {
                if (!confirm('¿Está seguro que desea eliminar este usuario?')) {
                    return;
                }

                const formData = new FormData();
                formData.append('action', 'deleteUsuario');
                formData.append('id', id);

                fetch('controllers/usuarios.api.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        Utils.showAlert('Usuario eliminado correctamente', 'success');
                        this.loadUsuarios();
                    } else {
                        Utils.showAlert(result.error, 'danger');
                    }
                });
            }
        };

        // Inicializar
        document.addEventListener('DOMContentLoaded', function() {
            Usuarios.loadUsuarios();

            document.getElementById('btnNewUsuario').addEventListener('click', function() {
                document.getElementById('formUsuario').reset();
                delete document.getElementById('formUsuario').dataset.usuarioId;
                delete document.getElementById('formUsuario').dataset.action;
                document.getElementById('passwordGroup').style.display = 'block';
                document.getElementById('inputPassword').setAttribute('required', 'required');
                document.querySelector('#modalUsuario .modal-title').textContent = 'Nuevo Usuario';
                Modal.open('modalUsuario');
            });

            document.getElementById('formUsuario').addEventListener('submit', function(e) {
                e.preventDefault();
                const action = this.dataset.action || 'create';
                if (action === 'create') {
                    Usuarios.createUsuario();
                } else {
                    Usuarios.updateUsuario();
                }
            });
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
