<?php
function conectarDB() {
    $conn = oci_connect('OSCARHORTA99', 'RETURN#ROOT', 'localhost/XE');
    if (!$conn) {
        $e = oci_error();
        die("Error en la conexión: " . $e['message']);
    }
    return $conn;
}


function insertarVuelo($numero_vuelo, $ciudad_origen, $ciudad_destino, $hora_salida, $hora_llegada, $estado, $id_avion) {
    $conn = conectarDB();
    try {
        $query = "INSERT INTO vuelos (id_vuelo, numero_vuelo, ciudad_origen, ciudad_destino, 
                                      hora_salida, hora_llegada, estado, id_avion) 
                  VALUES (vuelo_seq.NEXTVAL, :numero_vuelo, :ciudad_origen, :ciudad_destino, 
                          TO_TIMESTAMP(:hora_salida, 'YYYY-MM-DD HH24:MI:SS'), 
                          TO_TIMESTAMP(:hora_llegada, 'YYYY-MM-DD HH24:MI:SS'), 
                          :estado, :id_avion)";
        $stmt = oci_parse($conn, $query);
        oci_bind_by_name($stmt, ":numero_vuelo", $numero_vuelo);
        oci_bind_by_name($stmt, ":ciudad_origen", $ciudad_origen);
        oci_bind_by_name($stmt, ":ciudad_destino", $ciudad_destino);
        oci_bind_by_name($stmt, ":hora_salida", $hora_salida);
        oci_bind_by_name($stmt, ":hora_llegada", $hora_llegada);
        oci_bind_by_name($stmt, ":estado", $estado);
        oci_bind_by_name($stmt, ":id_avion", $id_avion);

        if (!oci_execute($stmt)) {
            $error = oci_error($stmt);
            throw new Exception("Error al insertar: " . $error['message']);
        }

        oci_commit($conn);
        echo "Vuelo registrado exitosamente.";
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
    $numero_vuelo = isset($_POST['numero_vuelo']) ? $_POST['numero_vuelo'] : null;
    $ciudad_origen = isset($_POST['ciudad_origen']) ? $_POST['ciudad_origen'] : null;
    $ciudad_destino = isset($_POST['ciudad_destino']) ? $_POST['ciudad_destino'] : null;
    $hora_salida = isset($_POST['hora_salida']) ? $_POST['hora_salida'] : null;
    $hora_llegada = isset($_POST['hora_llegada']) ? $_POST['hora_llegada'] : null;
    $estado = isset($_POST['estado']) ? $_POST['estado'] : null;
    $id_avion = isset($_POST['id_avion']) ? $_POST['id_avion'] : null;

    if ($numero_vuelo && $ciudad_origen && $ciudad_destino && $hora_salida && $hora_llegada && $estado && $id_avion) {
        insertarVuelo($numero_vuelo, $ciudad_origen, $ciudad_destino, $hora_salida, $hora_llegada, $estado, $id_avion);
    } else {
        echo "Por favor, completa todos los campos.";
    }
}

function actualizarVuelo($id_vuelo, $numero_vuelo, $ciudad_origen, $ciudad_destino, $hora_salida, $hora_llegada, $estado) {
    $conn = conectarDB();
    try {
        $query = "UPDATE vuelos SET numero_vuelo = :numero_vuelo,
                                    ciudad_origen = :ciudad_origen,
                                    ciudad_destino = :ciudad_destino, 
                                    hora_salida = TO_TIMESTAMP(:hora_salida, 'YYYY-MM-DD HH24:MI:SS'), 
                                    hora_llegada = TO_TIMESTAMP(:hora_llegada, 'YYYY-MM-DD HH24:MI:SS'), 
                                    estado = :estado 
                  WHERE id_vuelo = :id_vuelo";
        $stmt = oci_parse($conn, $query);
        oci_bind_by_name($stmt, ":id_vuelo", $id_vuelo);
        oci_bind_by_name($stmt, ":numero_vuelo", $numero_vuelo);
        oci_bind_by_name($stmt, ":ciudad_origen", $ciudad_origen);
        oci_bind_by_name($stmt, ":ciudad_destino", $ciudad_destino);
        oci_bind_by_name($stmt, ":hora_salida", $hora_salida);
        oci_bind_by_name($stmt, ":hora_llegada", $hora_llegada);
        oci_bind_by_name($stmt, ":estado", $estado);

        if (!oci_execute($stmt)) {
            $error = oci_error($stmt);
            throw new Exception("Error al actualizar: " . $error['message']);
        }

        oci_commit($conn);
        echo "Vuelo actualizado exitosamente.";
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
    $id_vuelo = isset($_POST['id_vuelo']) ? $_POST['id_vuelo'] : null;
    $numero_vuelo = isset($_POST['numero_vuelo']) ? $_POST['numero_vuelo'] : null;
    $ciudad_origen = isset($_POST['ciudad_origen']) ? $_POST['ciudad_origen'] : null;
    $ciudad_destino = isset($_POST['ciudad_destino']) ? $_POST['ciudad_destino'] : null;
    $hora_salida = isset($_POST['hora_salida']) ? $_POST['hora_salida'] : null;
    $hora_llegada = isset($_POST['hora_llegada']) ? $_POST['hora_llegada'] : null;
    $estado = isset($_POST['estado']) ? $_POST['estado'] : null;
    $id_avion = isset($_POST['id_avion']) ? $_POST['id_avion'] : null;

    if ($id_vuelo && ($numero_vuelo || $ciudad_origen || $ciudad_destino || $hora_salida || $hora_llegada || $estado || $id_avion)) {
        actualizarVuelo($id_vuelo, $numero_vuelo, $ciudad_origen, $ciudad_destino, $hora_salida, $hora_llegada, $estado);
    } else {
        echo "Por favor, proporciona el ID del Vuelo y al menos un campo para actualizar.";
    }
}


function eliminarVuelo($id_vuelo) {
    $conn = conectarDB();
    try {
        $query = "DELETE FROM vuelos WHERE id_vuelo = :id_vuelo";
        $stmt = oci_parse($conn, $query);
        oci_bind_by_name($stmt, ":id_vuelo", $id_vuelo);

        if (!oci_execute($stmt)) {
            $error = oci_error($stmt);
            throw new Exception("Error al eliminar: " . $error['message']);
        }

        oci_commit($conn);
        echo "Vuelo eliminado exitosamente.";
    } catch (Exception $e) {
        oci_rollback($conn);
        echo "Error: " . $e->getMessage();
    } finally {
        oci_free_statement($stmt);
        oci_close($conn);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'eliminar') {
    $id_vuelo = isset($_POST['id_vuelo']) ? $_POST['id_vuelo'] : null;

    if ($id_pasajero) {
        eliminarVuelo($id_vuelo);
    } else {
        echo "Por favor, proporciona el ID del pasajero a eliminar.";
    }
}

function listarVuelos() {
    $conn = conectarDB();
    try {
        $query = "SELECT id_vuelo, numero_vuelo, ciudad_origen, ciudad_destino, 
                         hora_salida, hora_llegada, estado, id_avion 
                  FROM vuelos";
        $stmt = oci_parse($conn, $query);

        if (!oci_execute($stmt)) {
            $error = oci_error($stmt);
            throw new Exception("Error al listar vuelos: " . $error['message']);
        }

        echo "<table>";
        echo "<thead>
                <tr>
                    <th>ID Vuelo</th>
                    <th>Número Vuelo</th>
                    <th>Ciudad Origen</th>
                    <th>Ciudad Destino</th>
                    <th>Hora Salida</th>
                    <th>Hora Llegada</th>
                    <th>Estado</th>
                    <th>ID Avión</th>
                </tr>
              </thead>";
        echo "<tbody>";

        while ($row = oci_fetch_assoc($stmt)) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['ID_VUELO']) . "</td>";
            echo "<td>" . htmlspecialchars($row['NUMERO_VUELO']) . "</td>";
            echo "<td>" . htmlspecialchars($row['CIUDAD_ORIGEN']) . "</td>";
            echo "<td>" . htmlspecialchars($row['CIUDAD_DESTINO']) . "</td>";
            echo "<td>" . htmlspecialchars($row['HORA_SALIDA']) . "</td>";
            echo "<td>" . htmlspecialchars($row['HORA_LLEGADA']) . "</td>";
            echo "<td>" . htmlspecialchars($row['ESTADO']) . "</td>";
            echo "<td>" . htmlspecialchars($row['ID_AVION']) . "</td>";
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
    <title>Gestión de Vuelos</title>
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
        <h1>Gestión de Vuelos</h1>
        <section id="volver-index">
            <button onclick="location.href='index.php'" class="volver-boton">Volver al Inicio</button>
        </section>
    </header>
    <nav>
        <button onclick="showSection('listar')">Listar Vuelos</button>
        <button onclick="showSection('agregar')">Agregar Vuelo</button>
        <button onclick="showSection('modificar')">Modificar Vuelo</button>
        <button onclick="showSection('eliminar')">Eliminar Vuelo</button>
    </nav>

    <!-- Agregar Vuelo -->
    <section id="agregar" class="section">
        <h2>Registrar Nuevo Vuelo</h2>
        <form method="POST" action="">
            <input type="hidden" name="accion" value="agregar">
            <input type="text" name="numero_vuelo" placeholder="Número de Vuelo" required>
            <input type="text" name="ciudad_origen" placeholder="Ciudad de Origen" required>
            <input type="text" name="ciudad_destino" placeholder="Ciudad de Destino" required>
            <input type="datetime-local" name="hora_salida" placeholder="Hora de Salida" required>
            <input type="datetime-local" name="hora_llegada" placeholder="Hora de Llegada" required>
            <input type="text" name="estado" placeholder="Estado (Ej: Programado)" required>
            <input type="number" name="id_avion" placeholder="ID del Avión" required>
            <input type="submit" value="Registrar">
        </form>
    </section>

    <!-- Modificar Vuelo -->
    <section id="modificar" class="section">
        <h2>Modificar Vuelo</h2>
        <form method="POST" action="">
            <input type="hidden" name="accion" value="modificar">
            <!-- Campo para preguntar el ID del pasajero -->
            <div style="margin-bottom: 20px;">
                <label for="id_vuelo" style="font-weight: bold;">¿Cuál es el ID del Vuelo que deseas modificar?</label>
                <input type="text" id="id_vuelo" name="id_vuelo" placeholder="ID del Vuelo" required>
            </div>

            <input type="text" name="ciudad_destino" placeholder="Ciudad de Destino">
            <input type="datetime-local" name="hora_salida" placeholder="Nueva Hora de Salida">
            <input type="datetime-local" name="hora_llegada" placeholder="Nueva Hora de Llegada">
            <input type="text" name="estado" placeholder="Nuevo Estado">
            <input type="submit" value="Modificar">
        </form>
    </section>

    <!-- Eliminar Vuelo -->
    <section id="eliminar" class="section">
        <h2>Eliminar Vuelo</h2>
        <form method="POST" action="">
            <input type="hidden" name="accion" value="eliminar">
            <input type="number" name="id_vuelo" placeholder="ID del Vuelo a Eliminar" required>
            <input type="submit" value="Eliminar">
        </form>
    </section>

    <section id="Mostrar" class="section">
    <h2>Lista de Vuelos</h2>
    <?php listarVuelos(); ?>
    </section>
</body>
</html>
