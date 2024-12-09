<?php
function conectarDB() {
    return oci_connect('OSCARHORTA99', 'RETURN#ROOT', 'localhost/XE');
}


function insertarAvion($modelo, $capacidad) {
    $conn = conectarDB();
    try {
        $query = "INSERT INTO aviones (id_avion, modelo, capacidad) 
                  VALUES (avion_seq.NEXTVAL, :modelo, :capacidad)";
        $stmt = oci_parse($conn, $query);
        oci_bind_by_name($stmt, ":modelo", $modelo);
        oci_bind_by_name($stmt, ":capacidad", $capacidad);

        if (!oci_execute($stmt)) {
            $error = oci_error($stmt);
            throw new Exception("Error al insertar: " . $error['message']);
        }

        oci_commit($conn);
        echo "Avión registrado exitosamente.";
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
    $modelo = isset($_POST['modelo']) ? $_POST['modelo'] : null;
    $capacidad = isset($_POST['capacidad']) ? $_POST['capacidad'] : null;

    if ($modelo && $capacidad) {
        insertarAvion($modelo, $capacidad);
    } else {
        echo "Por favor, completa todos los campos.";
    }
}


function actualizarAvion($id_avion, $modelo, $capacidad) {
    $conn = conectarDB();
    try {
        $query = "UPDATE aviones SET modelo = :modelo, capacidad = :capacidad 
                  WHERE id_avion = :id_avion";
        $stmt = oci_parse($conn, $query);
        oci_bind_by_name($stmt, ":id_avion", $id_avion);
        oci_bind_by_name($stmt, ":modelo", $modelo);
        oci_bind_by_name($stmt, ":capacidad", $capacidad);

        if (!oci_execute($stmt)) {
            $error = oci_error($stmt);
            throw new Exception("Error al actualizar: " . $error['message']);
        }

        oci_commit($conn);
        echo "Avión actualizado exitosamente.";
    } catch (Exception $e) {
        oci_rollback($conn);
        echo "Error: " . $e->getMessage();
    } finally {
        oci_free_statement($stmt);
        oci_close($conn);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'modificar') {
    $id_avion = isset($_POST['id_avion']) ? $_POST['id_avion'] : null;
    $modelo = isset($_POST['modelo']) ? $_POST['modelo'] : null;
    $capacidad = isset($_POST['capacidad']) ? $_POST['capacidad'] : null;

    if ($id_avion && ($modelo && $capacidad)) {
        actualizarAvion($id_avion, $modelo, $capacidad);
    } else {
        echo "Por favor, completa todos los campos.";
    }
}


function eliminarAvion($id_avion) {
    $conn = conectarDB();
    try {
        $query = "DELETE FROM aviones WHERE id_avion = :id_avion";
        $stmt = oci_parse($conn, $query);
        oci_bind_by_name($stmt, ":id_avion", $id_avion);

        if (!oci_execute($stmt)) {
            $error = oci_error($stmt);
            throw new Exception("Error al eliminar: " . $error['message']);
        }

        oci_commit($conn);
        echo "Avión eliminado exitosamente.";
    } catch (Exception $e) {
        oci_rollback($conn);
        echo "Error: " . $e->getMessage();
    } finally {
        oci_free_statement($stmt);
        oci_close($conn);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'eliminar') {
    $id_avion = isset($_POST['id_avion']) ? $_POST['id_avion'] : null;

    if ($id_avion) {
        eliminarAvion($id_avion);
    } else {
        echo "Por favor, proporciona el ID del pasajero a eliminar.";
    }
}

function listarAviones() {
    $conn = conectarDB();
    try {
        $query = "SELECT id_avion, modelo, capacidad FROM aviones";
        $stmt = oci_parse($conn, $query);
        if (!oci_execute($stmt)){
            $error = oci_error($stmt);
            throw new Exception("Error al mostrar pasajeros: " . $error['message']);
        };

        echo "<table>";
        echo "<thead>
                <tr>
                    <th>ID</th>
                    <th>Modelo</th>
                    <th>Capacidad</th>
                </tr>
            </thead>";
        echo "<tbody>";

        while ($row = oci_fetch_assoc($stmt)) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['ID_AVION']) . "</td>";
            echo "<td>" . htmlspecialchars($row['MODELO']) . "</td>";
            echo "<td>" . htmlspecialchars($row['CAPACIDAD']) . "</td>";
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
    <title>Gestión de Aviones</title>
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
        <h1>Gestión de Aviones</h1>
        <section id="volver-index">
            <button onclick="location.href='index.php'" class="volver-boton">Volver al Inicio</button>
        </section>
    </header>
    <nav>
        <button onclick="showSection('agregar')">Agregar Avion</button>
        <button onclick="showSection('Modificar')">Modificar Avion</button>
        <button onclick="showSection('Eliminar')">Eliminar Aviono</button>
        <button onclick="showSection('Mostrar')">Mostrar Aviones</button>
        
    </nav>


    <!-- Agregar Avion -->
    <section id="agregar" class="section">
        <h2>Registrar Nuevo Avion</h2>
        <form method="POST" action="">
            <input type="hidden" name="accion" value="agregar">
            <input type="text" name="modelo" placeholder="Modelo" required>
            <input type="text" name="capacidad" placeholder="Capacidad" required>
            <input type="submit" value="Agregar">
        </form>
    </section>

    <section id="Modificar" class="section">
        <h2>Modificar Avion</h2>
        <form method="POST" action="">
            <input type="hidden" name="accion" value="modificar">

            <!-- Campo para preguntar el ID del Avion -->
            <div style="margin-bottom: 20px;">
                <label for="id_avion" style="font-weight: bold;">¿Cuál es el ID del Avion que deseas modificar?</label>
                <input type="text" id="id_avion" name="id_avion" placeholder="ID del Avion" required>
            </div>

            <!-- Campos para modificar los datos -->
            <h3>Introduce los nuevos datos:</h3>
            <input type="text" name="modelo" placeholder="Nuevo Modelo">
            <input type="text" name="capacidad" placeholder="Nueva Capacidad">
            <input type="submit" value="Modificar">
        </form>
    </section>
    <!-- Eliminar Pasajero -->
    <section id="Eliminar" class="section">
        <h2>Eliminar Avion</h2>
        <form method="POST" action="">
            <input type="hidden" name="accion" value="eliminar">
            <input type="text" name="id_avion" placeholder="ID del Avion a Eliminar" required>
            <input type="submit" value="Eliminar">
        </form>
    </section>

    <section id="Mostrar" class="section">
    <h2>Listado de Aviones</h2>
    <?php listarAviones(); ?>
    </section>
</body>
</html>