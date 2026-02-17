<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Salud Total | Iniciar Sesión</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.3);
        }
        .card-header {
            background: #F47920;
            color: white;
            text-align: center;
            padding: 2rem;
        }
        .btn-login {
            background: #F47920;
            border: none;
            font-weight: bold;
        }
        .btn-login:hover {
            background: #d96500;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <img src="img/logo-upqroo.png" alt="UPQROO" class="img-fluid mb-3" style="max-width: 230px;">
                    <h4>Dispensario Médico</h4>
                    <h3 class="mb-0"><strong>Salud Total</strong></h3>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label class="form-label">Correo</label>
                            <input type="email" name="email" class="form-control form-control-lg"
                                   placeholder="admin@farmacia.com" required autofocus>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Contraseña</label>
                            <input type="password" name="clave" class="form-control form-control-lg" required>
                        </div>
                        <button type="submit" class="btn btn-login text-white w-100">
                            Iniciar Sesión
                        </button>
                    </form>

                    <?php if ($mensaje): ?>
                        <div class="mt-4 alert alert-<?= $tipo ?> alert-dismissible fade show">
                            <?= htmlspecialchars($mensaje) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="card-footer text-center small text-muted py-3">
                    Universidad Politécnica de Quintana Roo © 2025
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
