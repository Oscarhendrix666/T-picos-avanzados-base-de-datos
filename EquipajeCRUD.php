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

function listarEquipaje() {
    $conn = conectarDB();
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
    </header>
    <nav>
        <button onclick="showSection('buscar')">Buscar Equipaje</button>
        <button onclick="showSection('listar')">Listar Equipaje</button>
        <button onclick="showSection('agregar')">Agregar Equipaje</button>
        <button onclick="showSection('modificar')">Modificar Equipaje</button>
        <button onclick="showSection('eliminar')">Eliminar Equipaje</button>
    </nav>

    <!-- Buscar Equipaje -->
    <section id="buscar" class="section">
        <h2>Buscar Equipaje</h2>
        <form method="POST" action="">
            <input type="number" name="id_equipaje_buscar" placeholder="ID del Equipaje" required>
            <input type="submit" value="Buscar">
        </form>
        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_equipaje_buscar'])) {
            $id_equipaje = $_POST['id_equipaje_buscar'];
            $equipaje = buscarEquipaje($id_equipaje);
            if ($equipaje) {
                echo "<table>
                        <tr>
                            <th>ID</th>
                            <th>ID Reserva</th>
                            <th>Tipo</th>
                            <th>Peso</th>
                        </tr>
                        <tr>
                            <td>{$equipaje['id_equipaje']}</td>
                            <td>{$equipaje['id_reserva']}</td>
                            <td>{$equipaje['tipo']}</td>
                            <td>{$equipaje['peso']}</td>
                        </tr>
                      </table>";
            } else {
                echo "<p>No se encontró el equipaje con ID {$id_equipaje}.</p>";
            }
        }
        ?>
    </section>

    <!-- Listar Equipaje -->
    <section id="listar" class="section">
        <h2>Lista de Equipaje</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>ID Reserva</th>
                <th>Tipo</th>
                <th>Peso</th>
            </tr>
            <?php
            $equipaje = listarEquipaje();
            foreach ($equipaje as $item): ?>
            <tr>
                <td><?php echo $item['id_equipaje']; ?></td>
                <td><?php echo $item['id_reserva']; ?></td>
                <td><?php echo $item['tipo']; ?></td>
                <td><?php echo $item['peso']; ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </section>

    <!-- Insertar Equipaje -->
    <section id="agregar" class="section">
        <h2>Agregar Nuevo Equipaje</h2>
        <form method="POST" action="">
            <input type="number" name="id_reserva" placeholder="ID de la Reserva" required>
            <input type="text" name="tipo" placeholder="Tipo de Equipaje (Ej: Maleta, Mochila, etc.)" required>
            <input type="number" name="peso" placeholder="Peso (kg)" required>
            <input type="submit" value="Agregar">
        </form>
        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_reserva'], $_POST['tipo'], $_POST['peso'])) {
            $id_reserva = $_POST['id_reserva'];
            $tipo = $_POST['tipo'];
            $peso = $_POST['peso'];
            
            insertarEquipaje($id_reserva, $tipo, $peso);
        }
        ?>
    </section>

    <!-- Modificar Equipaje -->
    <section id="modificar" class="section">
        <h2>Modificar Equipaje</h2>
        <form method="POST" action="">
            <input type="number" name="id_equipaje_modificar" placeholder="ID del Equipaje a Modificar" required>
            <input type="text" name="tipo" placeholder="Nuevo Tipo de Equipaje" required>
            <input type="number" name="peso" placeholder="Nuevo Peso (kg)" required>
            <input type="submit" value="Modificar">
        </form>
        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_equipaje_modificar'])) {
            $id_equipaje = $_POST['id_equipaje_modificar'];
            $tipo = $_POST['tipo'];
            $peso = $_POST['peso'];
            
            actualizarEquipaje($id_equipaje, $tipo, $peso);
        }
        ?>
    </section>

    <!-- Eliminar Equipaje -->
    <section id="eliminar" class="section">
        <h2>Eliminar Equipaje</h2>
        <form method="POST" action="">
            <input type="number" name="id_equipaje_eliminar" placeholder="ID del Equipaje a Eliminar" required>
            <input type="submit" value="Eliminar">
        </form>
        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_equipaje_eliminar'])) {
            $id_equipaje = $_POST['id_equipaje_eliminar'];
            
            eliminarEquipaje($id_equipaje);
        }
        ?>
    </section>
</body>
</html>