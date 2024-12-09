<?php
function conectarDB() {
    return oci_connect('OSCARHORTA99', 'RETURN#ROOT', 'localhost/XE');
}


function insertarCheckin($id_reserva, $id_puerta) {
    $conn = conectarDB();
    try {
        $query = "INSERT INTO checkin (id_checkin, id_reserva, fecha_checkin, id_puerta) 
                  VALUES (checkin_seq.NEXTVAL, :id_reserva, SYSTIMESTAMP, :id_puerta)";
        $stmt = oci_parse($conn, $query);
        oci_bind_by_name($stmt, ":id_reserva", $id_reserva);
        oci_bind_by_name($stmt, ":id_puerta", $id_puerta);

        if (!oci_execute($stmt)) {
            $error = oci_error($stmt);
            throw new Exception("Error al insertar: " . $error['message']);
        }

        oci_commit($conn);
        echo "Check-in registrado exitosamente.";
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
    $id_reserva = isset($_POST['id_reserva']) ? $_POST['id_reserva'] : null;
    $id_puerta = isset($_POST['id_puerta']) ? $_POST['id_puerta'] : null;

    // Validar campos obligatorios
    if ($id_reserva && $id_puerta) {
        insertarCheckin($id_reserva, $id_puerta);
    } else {
        echo "Por favor, completa todos los campos obligatorios.";
    }
}

function actualizarCheckin($id_checkin, $id_puerta) {
    $conn = conectarDB();
    try {
        $query = "UPDATE checkin SET id_puerta = :id_puerta 
                  WHERE id_checkin = :id_checkin";
        $stmt = oci_parse($conn, $query);
        oci_bind_by_name($stmt, ":id_checkin", $id_checkin);
        oci_bind_by_name($stmt, ":id_puerta", $id_puerta);

        if (!oci_execute($stmt)) {
            $error = oci_error($stmt);
            throw new Exception("Error al actualizar: " . $error['message']);
        }

        oci_commit($conn);
        echo "Check-in actualizado exitosamente.";
    } catch (Exception $e) {
        oci_rollback($conn);
        echo "Error: " . $e->getMessage();
    } finally {
        oci_free_statement($stmt);
        oci_close($conn);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'modificar') {
    $id_reserva = isset($_POST['id_reserva']) ? $_POST['id_reserva'] : null;
    $id_puerta = isset($_POST['id_puerta']) ? $_POST['id_puerta'] : null;

    // Validar campos obligatorios
    if ($id_reserva && $id_puerta) {
        actualizarCheckinCheckin($id_reserva, $id_puerta);
    } else {
        echo "Por favor, completa todos los campos obligatorios.";
    }
}


function eliminarCheckin($id_checkin) {
    $conn = conectarDB();
    try {
        $query = "DELETE FROM checkin WHERE id_checkin = :id_checkin";
        $stmt = oci_parse($conn, $query);
        oci_bind_by_name($stmt, ":id_checkin", $id_checkin);

        if (!oci_execute($stmt)) {
            $error = oci_error($stmt);
            throw new Exception("Error al eliminar: " . $error['message']);
        }

        oci_commit($conn);
        echo "Check-in eliminado exitosamente.";
    } catch (Exception $e) {
        oci_rollback($conn);
        echo "Error: " . $e->getMessage();
    } finally {
        oci_free_statement($stmt);
        oci_close($conn);
    }

}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'eliminar') {
    $id_checkin = isset($_POST['id_checkin']) ? $_POST['id_checkin'] : null;

    if ($id_checkin) {
        eliminarPasajero($id_checkin);
    } else {
        echo "Por favor, proporciona el ID del pasajero a eliminar.";
    }
}


function listarCheckin() {
    $conn = conectarDB();
    try {
        $query = "SELECT id_checkin, id_reserva, fecha_checkin, id_puerta FROM checkin";
        $stmt = oci_parse($conn, $query);
        if (!oci_execute($stmt)) {
            $error = oci_error($stmt);
            throw new Exception("Error al mostrar pasajeros: " . $error['message']);
        }
        echo "<table>";
        echo "<thead>
                <tr>
                    <th>id_checkin</th>
                    <th>id_reserva</th>
                    <th>fecha_checkin</th>
                    <th>id_puerta </th>
                </tr>
              </thead>";
        echo "<tbody>";

        while ($row = oci_fetch_assoc($stmt)) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['ID_CHECKIN']) . "</td>";
            echo "<td>" . htmlspecialchars($row['ID_RESERVA']) . "</td>";
            echo "<td>" . htmlspecialchars($row['FECHA_CHECKIN']) . "</td>";
            echo "<td>" . htmlspecialchars($row['ID_PUERTA']) . "</td>";
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
    <title>Gestión de Equipaje</title>
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
        <h1>Gestión de Check-in</h1>
        <section id="volver-index">
            <button onclick="location.href='index.php'" class="volver-boton">Volver al Inicio</button>
        </section>
    </header>
    <nav>
        <button onclick="showSection('buscar')">Buscar Check-in</button>
        <button onclick="showSection('listar')">Listar Check-ins</button>
        <button onclick="showSection('agregar')">Agregar Check-in</button>
        <button onclick="showSection('modificar')">Modificar Check-in</button>
        <button onclick="showSection('eliminar')">Eliminar Check-in</button>
    </nav>


    <!-- Agregar Check-in -->
    <section id="agregar" class="section">
        <h2>Registrar Nuevo Check-in</h2>
        <form method="POST" action="">
            <input type="hidden" name="accion" value="agregar">
            <input type="number" name="id_reserva" placeholder="ID de la Reserva" required>
            <input type="number" name="id_puerta" placeholder="ID de la Puerta" required>
            <input type="submit" value="Registrar">
        </form>
    </section>

    <!-- Modificar Check-in -->
    <section id="modificar" class="section">
        <h2>Modificar Check-in</h2>
        <form method="POST" action="">
            <input type="hidden" name="accion" value="modificar">
            <input type="number" name="id_checkin_modificar" placeholder="ID del Check-in a Modificar" required>
            <input type="number" name="id_puerta" placeholder="Nuevo ID de la Puerta" required>
            <input type="submit" value="Modificar">
        </form>
    </section>

    <!-- Eliminar Check-in -->
    <section id="eliminar" class="section">
        <h2>Eliminar Check-in</h2>
        <form method="POST" action="">
            <input type="hidden" name="accion" value="eliminar">
            <input type="number" name="id_checkin_eliminar" placeholder="ID del Check-in a Eliminar" required>
            <input type="submit" value="Eliminar">
        </form>
    <section id="Mostrar" class="section">
        <h2>Lista de CheckIn</h2>
        <?php listarCheckin(); ?>
    </section>
</body>
</html>