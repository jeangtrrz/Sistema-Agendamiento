<?php
/**
 * Página de Login
 */

require_once 'config/config.php';
require_once 'config/session.php';

// Si ya está autenticado, redirigir al dashboard
if (isAuthenticated()) {
    header('Location: ' . APP_URL . '/dashboard.php');
    exit();
}

$error = $_GET['error'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-dark) 100%);
        }

        .login-container {
            width: 100%;
            max-width: 400px;
            background-color: white;
            border-radius: 8px;
            box-shadow: var(--shadow-lg);
            overflow: hidden;
        }

        .login-header {
            background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-dark) 100%);
            color: white;
            padding: 40px 20px;
            text-align: center;
        }

        .login-header h1 {
            font-size: 24px;
            margin-bottom: 8px;
            color: white;
        }

        .login-header p {
            font-size: 13px;
            opacity: 0.9;
        }

        .login-body {
            padding: 40px;
        }

        .form-group input {
            padding: 12px;
            font-size: 15px;
        }

        .btn-login {
            width: 100%;
            padding: 12px;
            font-size: 15px;
            font-weight: 600;
            margin-top: 20px;
        }

        .login-footer {
            text-align: center;
            padding: 20px;
            background-color: #f8fafc;
            font-size: 13px;
            color: var(--color-text-muted);
        }

        .alert {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1>🌐 Internet Cordillera</h1>
            <p>Sistema de Agendamiento Online</p>
        </div>

        <div class="login-body">
            <?php if ($error === 'timeout'): ?>
                <div class="alert alert-warning">
                    Tu sesión ha expirado. Por favor, inicia sesión nuevamente.
                </div>
            <?php endif; ?>

            <form id="loginForm" method="POST">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required placeholder="nombre@internetcordillera.cl">
                </div>

                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <input type="password" id="password" name="password" required placeholder="••••••••">
                </div>

                <button type="submit" class="btn btn-primary btn-login">Iniciar Sesión</button>
            </form>

            <div id="alertContainer"></div>
        </div>

        <div class="login-footer">
            <p><strong>Sistema de Agendamiento version 1.0</strong><br>
            Desarrollado por Jean Gutiérrez<br></p>
        </div>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData();
            formData.append('action', 'login');
            formData.append('email', document.getElementById('email').value);
            formData.append('password', document.getElementById('password').value);

            fetch('controllers/auth.api.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    window.location.href = 'dashboard.php';
                } else {
                    const alertDiv = document.createElement('div');
                    alertDiv.className = 'alert alert-danger';
                    alertDiv.innerHTML = '<span>✕</span> <span>' + result.error + '</span>';
                    document.getElementById('alertContainer').innerHTML = '';
                    document.getElementById('alertContainer').appendChild(alertDiv);

                    setTimeout(() => {
                        alertDiv.remove();
                    }, 5000);
                }
            })
            .catch(err => {
                console.error('Error:', err);
                const alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-danger';
                alertDiv.innerHTML = '<span>✕</span> <span>Error de conexión</span>';
                document.getElementById('alertContainer').innerHTML = '';
                document.getElementById('alertContainer').appendChild(alertDiv);
            });
        });
    </script>
</body>
</html>
