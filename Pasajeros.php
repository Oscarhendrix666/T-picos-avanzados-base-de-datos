<?php
// Función para conectar a la base de datos
function conectarDB() {
    $conn = oci_connect('OSCARHORTA99', 'RETURN#ROOT', 'localhost/XE');
    if (!$conn) {
        $e = oci_error();
        die("Error en la conexión: " . $e['message']);
    }
    return $conn;
}

// Función para mostrar todos los pasajeros
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

// Función para insertar un pasajero
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
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manejo de Pasajeros</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
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
    </header>
    <nav>
        <button onclick="showSection('mostrar')">Mostrar Pasajeros</button>
        <button onclick="showSection('agregar')">Agregar Pasajero</button>
    </nav>

    <!-- Sección para mostrar pasajeros -->
    <section id="mostrar" class="section">
        <h2>Lista de Pasajeros</h2>
        <?php mostrarPasajeros(); ?>
    </section>

    <!-- Sección para agregar pasajeros -->
    <section id="agregar" class="section">
        <h2>Registrar Nuevo Pasajero</h2>
        <form method="POST" action="">
            <input type="hidden" name="accion" value="agregar">
            <input type="text" name="nombre" placeholder="Nombre" required>
            <input type="text" name="apellido" placeholder="Apellido" required>
            <input type="text" name="numero_pasaporte" placeholder="Número de Pasaporte" required>
            <input type="email" name="correo" placeholder="Correo Electrónico" required>
            <input type="submit" value="Registrar">
        </form>
    </section>
</body>
</html>



CREATE SEQUENCE pasajeros_seq
START WITH 1 -- El valor inicial que tomará la secuencia
INCREMENT BY 1 -- Incremento entre cada número generado
NOCACHE -- No almacenar valores en caché (opcional)
NOCYCLE; -- No permitir que la secuencia reinicie automáticamente

CREATE OR REPLACE TRIGGER trg_pasajeros_id
BEFORE INSERT ON PASAJEROS
FOR EACH ROW
BEGIN
    IF :NEW.ID_PASAJERO IS NULL THEN
        SELECT pasajeros_seq.NEXTVAL INTO :NEW.ID_PASAJERO FROM DUAL;
    END IF;
END;
/