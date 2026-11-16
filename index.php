<?php
require_once 'db_config.php';
session_start();

$msg = "";
$tipo_msg = "";

// Cerrar sesión.
if (isset($_GET['logout'])) {
    $_SESSION = [];
    session_destroy();
    header("Location: index.php");
    exit;
}

// Comprobar los datos de acceso.
if (isset($_POST['login'])) {
    $stmt = $pdo->prepare(
        "SELECT * FROM usuarios WHERE usuario = ? AND password = ?"
    );
    $stmt->execute([
        $_POST['user'] ?? '',
        $_POST['pass'] ?? ''
    ]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        session_regenerate_id(true);
        $_SESSION['usuario'] = $user['usuario'];
        header("Location: index.php");
        exit;
    } else {
        $msg = "Usuario o contraseña incorrectos. Inténtalo nuevamente.";
        $tipo_msg = "error";
    }
}

// Guardar una cita únicamente cuando hay una sesión iniciada.
if (isset($_POST['agendar']) && isset($_SESSION['usuario'])) {
    $paciente = trim($_POST['paciente'] ?? '');
    $especialidad = $_POST['especialidad'] ?? '';
    $fecha = $_POST['fecha'] ?? '';
    $hora = $_POST['hora'] ?? '';

    $especialidades = [
        'Medicina General',
        'Pediatría',
        'Odontología'
    ];

    if (
        $paciente === '' ||
        $fecha === '' ||
        $hora === '' ||
        !in_array($especialidad, $especialidades, true)
    ) {
        $msg = "Completa todos los campos y selecciona una especialidad válida.";
        $tipo_msg = "error";
    } else {
        $stmt = $pdo->prepare(
            "INSERT INTO citas
            (paciente_nombre, especialidad, fecha, hora)
            VALUES (?, ?, ?, ?)"
        );
        $stmt->execute([$paciente, $especialidad, $fecha, $hora]);

        // Volver a la página evita repetir el registro al actualizar.
        header("Location: index.php?guardado=1");
        exit;
    }
}

if (
    isset($_GET['guardado']) &&
    isset($_SESSION['usuario']) &&
    $msg === ''
) {
    $msg = "La cita se registró correctamente.";
    $tipo_msg = "exito";
}

// Consultar las citas para mostrarlas en el reporte.
$citas = [];

if (isset($_SESSION['usuario'])) {
    $stmt = $pdo->query("SELECT * FROM citas ORDER BY id DESC");
    $citas = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Mostrar los textos de forma segura dentro del HTML.
function escapar($texto) {
    return htmlspecialchars(
        (string) $texto,
        ENT_QUOTES,
        'UTF-8'
    );
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Sistema de Citas Médicas</title>

    <link rel="stylesheet" href="css/style.css">

    <!-- Mejoras visuales incluidas en este mismo archivo. -->
    <style>
        label {
            display: block;
            margin-top: 12px;
            font-weight: 600;
            color: #243746;
        }

        .ayuda {
            color: #526170;
            line-height: 1.5;
        }

        .fecha-hora {
            display: flex;
            gap: 12px;
        }

        .fecha-hora > div {
            flex: 1;
            min-width: 0;
        }

        .mensaje {
            padding: 14px;
            margin: 16px 0;
            border-radius: 8px;
            line-height: 1.5;
        }

        .exito {
            background: #e8f5ec;
            color: #185c30;
            border: 1px solid #a5d6b2;
        }

        .error {
            background: #fff0f0;
            color: #9b2020;
            border: 1px solid #efb5b5;
        }

        .tabla-contenedor {
            overflow-x: auto;
        }

        .tabla-contenedor table {
            min-width: 560px;
        }

        .sin-citas {
            text-align: center;
            padding: 24px;
            color: #526170;
        }

        input, select, button {
            font: inherit;
        }

        input:focus-visible,
        select:focus-visible,
        button:focus-visible,
        a:focus-visible {
            outline: 3px solid #0056b3;
            outline-offset: 3px;
        }

        @media (max-width: 600px) {
            body {
                padding: 10px;
            }

            .container {
                padding: 16px;
            }

            .login-form,
            .appointment-form {
                padding: 15px;
            }

            .fecha-hora {
                flex-direction: column;
                gap: 0;
            }

            .header {
                flex-wrap: wrap;
                gap: 12px;
            }

            h2 {
                font-size: 1.35rem;
            }
        }
    </style>
</head>

<body>
<main class="container">

    <?php if (!isset($_SESSION['usuario'])): ?>

        <section class="login-form">
            <h2>Acceso al sistema médico</h2>
            <p class="ayuda">
                Ingresa tus credenciales para gestionar las citas.
            </p>

            <?php if ($msg !== ''): ?>
                <p class="mensaje <?= escapar($tipo_msg) ?>" role="alert">
                    <?= escapar($msg) ?>
                </p>
            <?php endif; ?>

            <form method="POST">
                <label for="user">Usuario</label>
                <input
                    type="text"
                    id="user"
                    name="user"
                    placeholder="Escribe tu usuario"
                    autocomplete="username"
                    required
                >

                <label for="pass">Contraseña</label>
                <input
                    type="password"
                    id="pass"
                    name="pass"
                    placeholder="Escribe tu contraseña"
                    autocomplete="current-password"
                    required
                >

                <button type="submit" name="login">
                    Ingresar
                </button>
            </form>
        </section>

    <?php else: ?>

        <header class="header">
            <span>
                Bienvenido:
                <strong><?= escapar($_SESSION['usuario']) ?></strong>
            </span>

            <a href="?logout=1" class="logout">
                Cerrar sesión
            </a>
        </header>

        <section class="appointment-form">
            <h2>Agendar cita médica</h2>

            <p class="ayuda">
                Completa todos los campos para registrar una cita.
            </p>

            <?php if ($msg !== ''): ?>
                <p
                    class="mensaje <?= escapar($tipo_msg) ?>"
                    role="<?= $tipo_msg === 'error' ? 'alert' : 'status' ?>"
                >
                    <?= escapar($msg) ?>
                </p>
            <?php endif; ?>

            <form method="POST">
                <label for="paciente">
                    Nombre completo del paciente
                </label>
                <input
                    type="text"
                    id="paciente"
                    name="paciente"
                    placeholder="Ejemplo: Ana López"
                    value="<?= escapar($paciente ?? '') ?>"
                    required
                >

                <label for="especialidad">Especialidad médica</label>
                <select id="especialidad" name="especialidad" required>
                    <?php foreach (
                        ['Medicina General', 'Pediatría', 'Odontología']
                        as $opcion
                    ): ?>
                        <option
                            value="<?= escapar($opcion) ?>"
                            <?= ($especialidad ?? '') === $opcion
                                ? 'selected' : '' ?>
                        >
                            <?= escapar($opcion) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <div class="fecha-hora">
                    <div>
                        <label for="fecha">Fecha de la cita</label>
                        <input
                            type="date"
                            id="fecha"
                            name="fecha"
                            value="<?= escapar($fecha ?? '') ?>"
                            required
                        >
                    </div>

                    <div>
                        <label for="hora">Hora de la cita</label>
                        <input
                            type="time"
                            id="hora"
                            name="hora"
                            value="<?= escapar($hora ?? '') ?>"
                            required
                        >
                    </div>
                </div>

                <button type="submit" name="agendar">
                    Registrar cita
                </button>
            </form>
        </section>

        <section aria-labelledby="titulo-reporte">
            <h2 id="titulo-reporte">Citas registradas</h2>
            <p class="ayuda">
                Total de citas: <?= count($citas) ?>.
                Los registros se almacenan en la base de datos local.
            </p>

            <div class="tabla-contenedor">
                <table>
                    <thead>
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">Paciente</th>
                            <th scope="col">Especialidad</th>
                            <th scope="col">Fecha</th>
                            <th scope="col">Hora</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($citas)): ?>
                            <tr>
                                <td colspan="5" class="sin-citas">
                                    Todavía no hay citas registradas.
                                    Utiliza el formulario para agregar la primera.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($citas as $cita): ?>
                                <tr>
                                    <td><?= escapar($cita['id']) ?></td>
                                    <td><?= escapar($cita['paciente_nombre']) ?></td>
                                    <td><?= escapar($cita['especialidad']) ?></td>
                                    <td><?= escapar($cita['fecha']) ?></td>
                                    <td><?= escapar($cita['hora']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

    <?php endif; ?>

</main>
</body>
</html>