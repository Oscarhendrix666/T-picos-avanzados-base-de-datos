<?php
function conectarDB() {
    $conn = oci_connect('OSCARHORTA99', 'RETURN#ROOT', 'localhost/XE');
    if (!$conn) {
        $e = oci_error();
        die("Error en la conexión: " . $e['message']);
    }
    return $conn;
}


function insertarPasajero($nombre, $apellido, $numero_pasaporte, $correo) {
    $conn = conectarDB();
    try {
        $query = "INSERT INTO pasajeros (id_pasajero, nombre, apellido, numero_pasaporte, correo) 
                  VALUES (pasajeros_seq.NEXTVAL, :nombre, :apellido, :numero_pasaporte, :correo)";
        $stmt = oci_parse($conn, $query);
        oci_bind_by_name($stmt, ":nombre", $nombre);
        oci_bind_by_name($stmt, ":apellido", $apellido);
        oci_bind_by_name($stmt, ":numero_pasaporte", $numero_pasaporte);
        oci_bind_by_name($stmt, ":correo", $correo);

        if (!oci_execute($stmt)) {
            $error = oci_error($stmt);
            throw new Exception("Error al insertar: " . $error['message']);
        }

        oci_commit($conn);
        echo "Pasajero registrado exitosamente.";
    } catch (Exception $e) {
        oci_rollback($conn);
        echo "Error: " . $e->getMessage();
    } finally {
        oci_free_statement($stmt);
        oci_close($conn);
    }
}

// Procesar formulario de inserción
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'agregar') {
    $nombre = isset($_POST['nombre']) ? $_POST['nombre'] : null;
    $apellido = isset($_POST['apellido']) ? $_POST['apellido'] : null;
    $numero_pasaporte = isset($_POST['numero_pasaporte']) ? $_POST['numero_pasaporte'] : null;
    $correo = isset($_POST['correo']) ? $_POST['correo'] : null;

    if ($nombre && $apellido && $numero_pasaporte && $correo) {
        insertarPasajero($nombre, $apellido, $numero_pasaporte, $correo);
    } else {
        echo "Por favor, completa todos los campos.";
    }
}


function actualizarPasajero($id_pasajero, $nombre, $apellido, $numero_pasaporte, $correo) {
    $conn = conectarDB();
    try {
        $query = "UPDATE pasajeros SET nombre = :nombre, apellido = :apellido, 
                  numero_pasaporte = :numero_pasaporte, correo = :correo 
                  WHERE id_pasajero = :id_pasajero";
        $stmt = oci_parse($conn, $query);
        oci_bind_by_name($stmt, ":id_pasajero", $id_pasajero);
        oci_bind_by_name($stmt, ":nombre", $nombre);
        oci_bind_by_name($stmt, ":apellido", $apellido);
        oci_bind_by_name($stmt, ":numero_pasaporte", $numero_pasaporte);
        oci_bind_by_name($stmt, ":correo", $correo);

        if (!oci_execute($stmt)) {
            $error = oci_error($stmt);
            throw new Exception("Error al actualizar: " . $error['message']);
        }

        oci_commit($conn);
        echo "Pasajero actualizado exitosamente.";
    } catch (Exception $e) {
        oci_rollback($conn);
        echo "Error: " . $e->getMessage();
    } finally {
        oci_free_statement($stmt);
        oci_close($conn);
    }
}

// Procesar formulario de moficacion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'modificar') {
    $id_pasajero = isset($_POST['id_pasajero']) ? $_POST['id_pasajero'] : null;
    $nombre = isset($_POST['nombre']) ? $_POST['nombre'] : null;
    $apellido = isset($_POST['apellido']) ? $_POST['apellido'] : null;
    $numero_pasaporte = isset($_POST['numero_pasaporte']) ? $_POST['numero_pasaporte'] : null;
    $correo = isset($_POST['correo']) ? $_POST['correo'] : null;

    if ($id_pasajero && ($nombre || $apellido || $numero_pasaporte || $correo)) {
        actualizarPasajero($id_pasajero, $nombre, $apellido, $numero_pasaporte, $correo);
    } else {
        echo "Por favor, proporciona el ID del pasajero y al menos un campo para actualizar.";
    }
}

function eliminarPasajero($id_pasajero) {
    $conn = conectarDB();
    try {
        $query = "DELETE FROM pasajeros WHERE id_pasajero = :id_pasajero";
        $stmt = oci_parse($conn, $query);
        oci_bind_by_name($stmt, ":id_pasajero", $id_pasajero);

        if (!oci_execute($stmt)) {
            $error = oci_error($stmt);
            throw new Exception("Error al eliminar: " . $error['message']);
        }

        oci_commit($conn);
        echo "Pasajero eliminado exitosamente.";
    } catch (Exception $e) {
        oci_rollback($conn);
        echo "Error: " . $e->getMessage();
    } finally {
        oci_free_statement($stmt);
        oci_close($conn);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'eliminar') {
    $id_pasajero = isset($_POST['id_pasajero']) ? $_POST['id_pasajero'] : null;

    if ($id_pasajero) {
        eliminarPasajero($id_pasajero);
    } else {
        echo "Por favor, proporciona el ID del pasajero a eliminar.";
    }
}


function mostrarPasajeros() {
    $conn = conectarDB();
    try {
        $query = "SELECT ID_PASAJERO, NOMBRE, APELLIDO, NUMERO_PASAPORTE, CORREO FROM PASAJEROS";
        $stmt = oci_parse($conn, $query);

        if (!oci_execute($stmt)) {
            $error = oci_error($stmt);
            throw new Exception("Error al mostrar pasajeros: " . $error['message']);
        }

        echo "<table>";
        echo "<thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Número de Pasaporte</th>
                    <th>Correo</th>
                </tr>
              </thead>";
        echo "<tbody>";

        while ($row = oci_fetch_assoc($stmt)) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['ID_PASAJERO']) . "</td>";
            echo "<td>" . htmlspecialchars($row['NOMBRE']) . "</td>";
            echo "<td>" . htmlspecialchars($row['APELLIDO']) . "</td>";
            echo "<td>" . htmlspecialchars($row['NUMERO_PASAPORTE']) . "</td>";
            echo "<td>" . htmlspecialchars($row['CORREO']) . "</td>";
            echo "</tr>";
        }

        echo "</tbody>";
        echo "</table>";
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    } finally {
        oci_free_statement($stmt);
        oci_close($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manejo de Pasajeros</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        header {
            background-color: #007BFF;
            color: white;
            padding: 20px;
            text-align: center;
        }
        nav {
            text-align: center;
            margin: 20px 0;
        }
        nav button {
            background-color: #007BFF;
            color: white;
            padding: 10px 20px;
            margin: 5px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        nav button:hover {
            background-color: #0056b3;
        }
        .section {
            display: none;
            margin: 20px auto;
            width: 80%;
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #007BFF;
            color: white;
        }
        input, select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        input[type="submit"] {
            background-color: #007BFF;
            color: white;
            border: none;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #0056b3;
        }

        .volver-boton {
            position: fixed; /* Fija el botón en un lugar de la pantalla */
            top: 20px; /* Distancia desde la parte superior */
            right: 20px; /* Distancia desde el lado derecho */
            padding: 10px 20px; /* Tamaño del botón */
            background-color: #007BFF; /* Color del fondo */
            color: white; /* Color del texto */
            border: none; /* Sin bordes */
            border-radius: 5px; /* Bordes redondeados */
            cursor: pointer; /* Cambia el cursor a un puntero */
            font-size: 16px; /* Tamaño del texto */
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Sombra */
        }

        .volver-boton:hover {
            background-color: #0056b3; /* Color más oscuro al pasar el mouse */
        }

    </style>
    <script>
        function showSection(id) {
            const sections = document.querySelectorAll('.section');
            sections.forEach(section => section.style.display = 'none');
            document.getElementById(id).style.display = 'block';
        }
    </script>
</head>
<body>
    <header>
        <h1>Manejo de Pasajeros</h1>
        <section id="volver-index">
            <button onclick="location.href='index.php'" class="volver-boton">Volver al Inicio</button>
        </section>
    </header>
    <nav>
        <button onclick="showSection('agregar')">Agregar Pasajero</button>
        <button onclick="showSection('Modificar')">Modificar Pasajero</button>
        <button onclick="showSection('Eliminar')">Eliminar Pasajero</button>
        <button onclick="showSection('Mostrar')">Mostrar Pasajeros</button>
        
    </nav>


    <!-- Agregar Pasajero -->
    <section id="agregar" class="section">
        <h2>Registrar Nuevo Pasajero</h2>
        <form method="POST" action="">
            <input type="hidden" name="accion" value="agregar">
            <input type="text" name="nombre" placeholder="Nombre" required>
            <input type="text" name="apellido" placeholder="Apellido" required>
            <input type="text" name="numero_pasaporte" placeholder="Número de Pasaporte" required>
            <input type="email" name="correo" placeholder="Correo Electrónico" required>
            <input type="submit" value="Agregar">
        </form>
    </section>

    <section id="Modificar" class="section">
        <h2>Modificar Pasajero</h2>
        <form method="POST" action="">
            <input type="hidden" name="accion" value="modificar">

            <!-- Campo para preguntar el ID del pasajero -->
            <div style="margin-bottom: 20px;">
                <label for="id_pasajero" style="font-weight: bold;">¿Cuál es el ID del Pasajero que deseas modificar?</label>
                <input type="text" id="id_pasajero" name="id_pasajero" placeholder="ID del Pasajero" required>
            </div>

            <!-- Campos para modificar los datos -->
            <h3>Introduce los nuevos datos:</h3>
            <input type="text" name="nombre" placeholder="Nuevo Nombre">
            <input type="text" name="apellido" placeholder="Nuevo Apellido">
            <input type="text" name="numero_pasaporte" placeholder="Nuevo Número de Pasaporte">
            <input type="email" name="correo" placeholder="Nuevo Correo Electrónico">
            <input type="submit" value="Modificar">
        </form>
    </section>
    <!-- Eliminar Pasajero -->
    <section id="Eliminar" class="section">
        <h2>Eliminar Pasajero</h2>
        <form method="POST" action="">
            <input type="hidden" name="accion" value="eliminar">
            <input type="text" name="id_pasajero" placeholder="ID del Pasajero a Eliminar" required>
            <input type="submit" value="Eliminar">
        </form>
    </section>

    <section id="Mostrar" class="section">
    <h2>Lista de Pasajeros</h2>
    <?php mostrarPasajeros(); ?>
    </section>
</body>
</html>