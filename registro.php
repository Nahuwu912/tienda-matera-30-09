<?php
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Cuenta - Tienda Matera</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Google Fonts (Poppins) -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #121214;
            color: #e4e4e7;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 20px 0;
        }

        .register-card {
            background-color: #18181b;
            border: 1px solid #27272a;
            border-radius: 8px;
            width: 100%;
            max-width: 500px;
        }

        .form-control-dark {
            background-color: #121214;
            border: 1px solid #27272a;
            color: #e4e4e7;
            padding: 12px 15px;
            font-size: 14px;
        }

        .form-control-dark:focus {
            background-color: #121214;
            border-color: #b86b35;
            color: #ffffff;
            box-shadow: 0 0 0 0.25rem rgba(184, 107, 53, 0.25);
        }

        .form-control-dark::placeholder {
            color: #71717a;
        }

        .btn-cobrizo {
            background-color: #b86b35;
            color: #ffffff;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: 1px;
            text-transform: uppercase;
            border-radius: 4px;
            padding: 12px 20px;
            border: none;
            transition: all 0.2s;
        }

        .btn-cobrizo:hover {
            background-color: #9d5a2b;
            color: #ffffff;
        }

        .link-cobrizo {
            color: #b86b35;
            text-decoration: none;
            font-size: 14px;
            transition: color 0.2s;
        }

        .link-cobrizo:hover {
            color: #d98243;
            text-decoration: underline;
        }

        .text-muted-custom {
            color: #a1a1aa !important;
        }
    </style>
</head>

<body>

    <div class="container d-flex justify-content-center">
        <div class="register-card p-4 p-md-5 shadow-lg">

            <!-- Encabezado -->
            <div class="text-center mb-4">
                <div class="fs-1 mb-2">🧉</div>

                <h3 class="fw-bold text-white m-0">
                    Crear Cuenta
                </h3>

                <p class="text-muted-custom small mt-1">
                    Registrate para comenzar a comprar
                </p>
            </div>

            <!-- Formulario -->
            <form action="guardar_usuario.php" method="POST">

                <!-- Nombre -->
                <div class="mb-3">
                    <label class="form-label small text-muted-custom">
                        Nombre completo
                    </label>

                    <input
                        type="text"
                        name="nombre"
                        class="form-control form-control-dark"
                        placeholder="Juan Pérez"
                        required>
                </div>

                <!-- Email -->
                <div class="mb-3">
                    <label class="form-label small text-muted-custom">
                        Correo electrónico
                    </label>

                    <input
                        type="email"
                        name="email"
                        class="form-control form-control-dark"
                        placeholder="tu@email.com"
                        required>
                </div>

                <!-- Dirección -->
                <div class="mb-3">
                    <label class="form-label small text-muted-custom">
                        Dirección
                    </label>

                    <input
                        type="text"
                        name="direccion"
                        class="form-control form-control-dark"
                        placeholder="Av. Siempre Viva 123"
                        required>
                </div>

                <!-- Código postal y localidad -->
                <div class="row">

                    <div class="col-md-5 mb-3">
                        <label class="form-label small text-muted-custom">
                            Código postal
                        </label>

                        <input
                            type="text"
                            name="codigo_postal"
                            class="form-control form-control-dark"
                            placeholder="1234"
                            required>
                    </div>

                    <div class="col-md-7 mb-3">
                        <label class="form-label small text-muted-custom">
                            Localidad
                        </label>

                        <input
                            type="text"
                            name="localidad"
                            class="form-control form-control-dark"
                            placeholder="Buenos Aires"
                            required>
                    </div>

                </div>

                <!-- Contraseña -->
                <div class="mb-4">
                    <label class="form-label small text-muted-custom">
                        Contraseña
                    </label>

                    <input
                        type="password"
                        name="password"
                        class="form-control form-control-dark"
                        placeholder="••••••••"
                        required>
                </div>

                <!-- Botón -->
                <button
                    type="submit"
                    class="btn btn-cobrizo w-100 mb-3">

                    Registrarse

                </button>

            </form>

            <!-- Acciones secundarias -->
            <div
                class="text-center pt-3 border-top"
                style="border-color: #27272a !important;">

                <span class="text-muted-custom small">
                    ¿Ya tenés una cuenta?
                </span>

                <a
                    href="login.php"
                    class="link-cobrizo ms-1 fw-semibold">

                    Iniciar sesión

                </a>

            </div>

            <div class="text-center mt-3">

                <a
                    href="index.php"
                    class="text-muted-custom small text-decoration-none">

                    ⬅ Volver a la tienda

                </a>

            </div>

        </div>
    </div>

</body>
</html>
```
