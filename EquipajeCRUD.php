<?php
function conectarDB() {
    return oci_connect('OSCARHORTA99', 'RETURN#ROOT', 'localhost/XE');
}

function insertarEquipaje($id_reserva, $tipo, $peso) {
    $conn = conectarDB();
    try {
        $query = "INSERT INTO equipaje (id_equipaje, id_reserva, tipo, peso) 
                  VALUES (equipaje_seq.NEXTVAL, :id_reserva, :tipo, :peso)";
        $stmt = oci_parse($conn, $query);
        oci_bind_by_name($stmt, ":id_reserva", $id_reserva);
        oci_bind_by_name($stmt, ":tipo", $tipo);
        oci_bind_by_name($stmt, ":peso", $peso);

        if (!oci_execute($stmt)) {
            $error = oci_error($stmt);
            throw new Exception("Error al insertar: " . $error['message']);
        }

        oci_commit($conn);
        echo "Equipaje registrado exitosamente.";
    } catch (Exception $e) {
        oci_rollback($conn);
        echo "Error: " . $e->getMessage();
    } finally {
        oci_free_statement($stmt);
        oci_close($conn);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'agregar') {
    $id_reserva= isset($_POST['id_reserva']) ? $_POST['id_reservaa'] : null;
    $tipo = isset($_POST['tipo']) ? $_POST['tipo'] : null;
    $peso = isset($_POST['peso']) ? $_POST['peso'] : null;

    if ($id_reserva && $tipo && $peso) {
        insertarPasajero($id_reserva, $tipo, $peso);
    } else {
        echo "Por favor, completa todos los campos.";
    }
}

function actualizarEquipaje($id_equipaje, $tipo, $peso) {
    $conn = conectarDB();
    try {
        $query = "UPDATE equipaje SET tipo = :tipo, peso = :peso 
                  WHERE id_equipaje = :id_equipaje";
        $stmt = oci_parse($conn, $query);
        oci_bind_by_name($stmt, ":id_equipaje", $id_equipaje);
        oci_bind_by_name($stmt, ":tipo", $tipo);
        oci_bind_by_name($stmt, ":peso", $peso);

        if (!oci_execute($stmt)) {
            $error = oci_error($stmt);
            throw new Exception("Error al actualizar: " . $error['message']);
        }

        oci_commit($conn);
        echo "Equipaje actualizado exitosamente.";
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
    $id_equipaje = isset($_POST['id_equipaje']) ? $_POST['id_equipaje'] : null;
    $tipo = isset($_POST['tipo']) ? $_POST['tipo'] : null;
    $peso = isset($_POST['peso']) ? $_POST['peso'] : null;

    if ($id_equipaje && ($tipo || $peso) {
        actualizarPasajero($id_pasajero, $nombre, $apellido, $numero_pasaporte, $correo);
    } else {
        echo "Por favor, proporciona el ID del equipaje y al menos un campo para actualizar.";
    }
}

function eliminarEquipaje($id_equipaje) {
    $conn = conectarDB();
    try {
        $query = "DELETE FROM equipaje WHERE id_equipaje = :id_equipaje";
        $stmt = oci_parse($conn, $query);
        oci_bind_by_name($stmt, ":id_equipaje", $id_equipaje);

        if (!oci_execute($stmt)) {
            $error = oci_error($stmt);
            throw new Exception("Error al eliminar: " . $error['message']);
        }

        oci_commit($conn);
        echo "Equipaje eliminado exitosamente.";
    } catch (Exception $e) {
        oci_rollback($conn);
        echo "Error: " . $e->getMessage();
    } finally {
        oci_free_statement($stmt);
        oci_close($conn);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'eliminar') {
    $id_equipaje = isset($_POST['id_equipaje']) ? $_POST['id_equipaje'] : null;

    if ($id_equipaje) {
        eliminarPasajero($id_equipaje);
    } else {
        echo "Por favor, proporciona el ID del equipaje a eliminar.";
    }
}

function listarEquipaje() {
    $conn = conectarDB();
    try {
        $query = "SELECT id_equipaje, id_reserva, tipo, peso FROM equipaje";
    $stmt = oci_parse($conn, $query);
    oci_execute($stmt);

    $equipaje = [];
    while ($row = oci_fetch_assoc($stmt)) {
        $equipaje[] = $row;
    }

    oci_free_statement($stmt);
    oci_close($conn);

    return $equipaje;
}
function listarEquipaje() {
    $conn = conectarDB();
    try {
        $query = "SELECT id_equipaje, id_reserva, tipo, peso FROM equipaje";
        $stmt = oci_parse($conn, $query);

        if (!oci_execute($stmt)) {
            $error = oci_error($stmt);
            throw new Exception("Error al mostrar pasajeros: " . $error['message']);
        }

        echo "<table>";
        echo "<thead>
                <tr>
                    <th>ID_EQUIPAJE</th>
                    <th>ID_RESERVA</th>
                    <th>TIPO</th>
                    <th>PESO</th>
                </tr>
              </thead>";
        echo "<tbody>";

        while ($row = oci_fetch_assoc($stmt)) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['ID_EQUIPAJE']) . "</td>";
            echo "<td>" . htmlspecialchars($row['ID_RESERVA']) . "</td>";
            echo "<td>" . htmlspecialchars($row['TIPO']) . "</td>";
            echo "<td>" . htmlspecialchars($row['PESO']) . "</td>";
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
        <h1>Gestión de Equipaje</h1>
        <section id="volver-index">
            <button onclick="location.href='index.php'" class="volver-boton">Volver al Inicio</button>
        </section>
    </header>
    <nav>
        <button onclick="showSection('listar')">Listar Equipaje</button>
        <button onclick="showSection('agregar')">Agregar Equipaje</button>
        <button onclick="showSection('modificar')">Modificar Equipaje</button>
        <button onclick="showSection('eliminar')">Eliminar Equipaje</button>
    </nav>

    <!-- Insertar Equipaje -->
    <section id="agregar" class="section">
        <h2>Agregar Nuevo Equipaje</h2>
        <form method="POST" action="">
            <input type="hidden" name="accion" value="agregar">
            <input type="number" name="id_reserva" placeholder="ID de la Reserva" required>
            <input type="text" name="tipo" placeholder="Tipo de Equipaje (Ej: Maleta, Mochila, etc.)" required>
            <input type="number" name="peso" placeholder="Peso (kg)" required>
            <input type="submit" value="Agregar">
        </form>
    </section>

    <!-- Modificar Equipaje -->
    <section id="modificar" class="section">
        <h2>Modificar Equipaje</h2>
        <form method="POST" action="">
            <input type="hidden" name="accion" value="modificar">
            <input type="number" name="id_equipaje_modificar" placeholder="ID del Equipaje a Modificar" required>
            <input type="text" name="tipo" placeholder="Nuevo Tipo de Equipaje" required>
            <input type="number" name="peso" placeholder="Nuevo Peso (kg)" required>
            <input type="submit" value="Modificar">
        </form>
    </section>

    <!-- Eliminar Equipaje -->
    <section id="eliminar" class="section">
        <h2>Eliminar Equipaje</h2>
        <form method="POST" action="">
            <input type="hidden" name="accion" value="eliminar">
            <input type="number" name="id_equipaje_eliminar" placeholder="ID del Equipaje a Eliminar" required>
            <input type="submit" value="Eliminar">
        </form>
    </section>
            
    <section id="Mostrar" class="section">
    <h2>Lista de Equipajes</h2>
    <?php listarEquipaje(); ?>
    </section>
            
</body>
</html>
