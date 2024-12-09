<?php
function conectarDB() {
    return oci_connect('OSCARHORTA99', 'RETURN#ROOT', 'localhost/XE');
}

function listarAsientos() {
    $conn = conectarDB();
    $query = "SELECT id_asiento, id_vuelo, numero_asiento, estado FROM asientos";
    $stmt = oci_parse($conn, $query);
    oci_execute($stmt);

    $asientos = [];
    while ($row = oci_fetch_assoc($stmt)) {
        $asientos[] = $row;
    }

    oci_free_statement($stmt);
    oci_close($conn);

    return $asientos;
}

function insertarAsiento($id_vuelo, $numero_asiento, $estado = 'Libre') {
    $conn = conectarDB();
    try {
        $query = "INSERT INTO asientos (id_asiento, id_vuelo, numero_asiento, estado) 
                  VALUES (asientos_seq.NEXTVAL, :id_vuelo, :numero_asiento, :estado)";
        $stmt = oci_parse($conn, $query);
        oci_bind_by_name($stmt, ":id_vuelo", $id_vuelo);
        oci_bind_by_name($stmt, ":numero_asiento", $numero_asiento);
        oci_bind_by_name($stmt, ":estado", $estado);

        if (!oci_execute($stmt)) {
            $error = oci_error($stmt);
            throw new Exception("Error al insertar: " . $error['message']);
        }

        oci_commit($conn);
        echo "Asiento insertado exitosamente.";
    } catch (Exception $e) {
        oci_rollback($conn);
        echo "Error: " . $e->getMessage();
    } finally {
        oci_free_statement($stmt);
        oci_close($conn);
    }
}

function actualizarAsiento($id_asiento, $numero_asiento, $estado) {
    $conn = conectarDB();
    try {
        $query = "UPDATE asientos SET numero_asiento = :numero_asiento, estado = :estado 
                  WHERE id_asiento = :id_asiento";
        $stmt = oci_parse($conn, $query);
        oci_bind_by_name($stmt, ":id_asiento", $id_asiento);
        oci_bind_by_name($stmt, ":numero_asiento", $numero_asiento);
        oci_bind_by_name($stmt, ":estado", $estado);

        if (!oci_execute($stmt)) {
            $error = oci_error($stmt);
            throw new Exception("Error al actualizar: " . $error['message']);
        }

        oci_commit($conn);
        echo "Asiento actualizado exitosamente.";
    } catch (Exception $e) {
        oci_rollback($conn);
        echo "Error: " . $e->getMessage();
    } finally {
        oci_free_statement($stmt);
        oci_close($conn);
    }
}

function eliminarAsiento($id_asiento) {
    $conn = conectarDB();
    try {
        $query = "DELETE FROM asientos WHERE id_asiento = :id_asiento";
        $stmt = oci_parse($conn, $query);
        oci_bind_by_name($stmt, ":id_asiento", $id_asiento);

        if (!oci_execute($stmt)) {
            $error = oci_error($stmt);
            throw new Exception("Error al eliminar: " . $error['message']);
        }

        oci_commit($conn);
        echo "Asiento eliminado exitosamente.";
    } catch (Exception $e) {
        oci_rollback($conn);
        echo "Error: " . $e->getMessage();
    } finally {
        oci_free_statement($stmt);
        oci_close($conn);
    }
}
?>
