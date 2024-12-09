<?php
function conectarDB() {
    return oci_connect('OSCARHORTA99', 'RETURN#ROOT', 'localhost/XE');
}

function listarReservas() {
    $conn = conectarDB();
    $query = "SELECT id_reserva, id_pasajero, id_vuelo, numero_asiento, estado, fecha_reserva, codigo_compra 
              FROM reservas";
    $stmt = oci_parse($conn, $query);
    oci_execute($stmt);

    $reservas = [];
    while ($row = oci_fetch_assoc($stmt)) {
        $reservas[] = $row;
    }

    oci_free_statement($stmt);
    oci_close($conn);

    return $reservas;
}

function insertarReserva($id_pasajero, $id_vuelo, $numero_asiento, $estado = 'Pendiente', $codigo_compra = null) {
    $conn = conectarDB();
    try {
        $query = "INSERT INTO reservas (id_reserva, id_pasajero, id_vuelo, numero_asiento, estado, fecha_reserva, codigo_compra) 
                  VALUES (reserva_seq.NEXTVAL, :id_pasajero, :id_vuelo, :numero_asiento, :estado, SYSDATE, :codigo_compra)";
        $stmt = oci_parse($conn, $query);
        oci_bind_by_name($stmt, ":id_pasajero", $id_pasajero);
        oci_bind_by_name($stmt, ":id_vuelo", $id_vuelo);
        oci_bind_by_name($stmt, ":numero_asiento", $numero_asiento);
        oci_bind_by_name($stmt, ":estado", $estado);
        oci_bind_by_name($stmt, ":codigo_compra", $codigo_compra);

        if (!oci_execute($stmt)) {
            $error = oci_error($stmt);
            throw new Exception("Error al insertar: " . $error['message']);
        }

        oci_commit($conn);
        echo "Reserva registrada exitosamente.";
    } catch (Exception $e) {
        oci_rollback($conn);
        echo "Error: " . $e->getMessage();
    } finally {
        oci_free_statement($stmt);
        oci_close($conn);
    }
}

function actualizarReserva($id_reserva, $numero_asiento, $estado, $codigo_compra = null) {
    $conn = conectarDB();
    try {
        $query = "UPDATE reservas SET numero_asiento = :numero_asiento, estado = :estado, codigo_compra = :codigo_compra 
                  WHERE id_reserva = :id_reserva";
        $stmt = oci_parse($conn, $query);
        oci_bind_by_name($stmt, ":id_reserva", $id_reserva);
        oci_bind_by_name($stmt, ":numero_asiento", $numero_asiento);
        oci_bind_by_name($stmt, ":estado", $estado);
        oci_bind_by_name($stmt, ":codigo_compra", $codigo_compra);

        if (!oci_execute($stmt)) {
            $error = oci_error($stmt);
            throw new Exception("Error al actualizar: " . $error['message']);
        }

        oci_commit($conn);
        echo "Reserva actualizada exitosamente.";
    } catch (Exception $e) {
        oci_rollback($conn);
        echo "Error: " . $e->getMessage();
    } finally {
        oci_free_statement($stmt);
        oci_close($conn);
    }
}

function eliminarReserva($id_reserva) {
    $conn = conectarDB();
    try {
        $query = "DELETE FROM reservas WHERE id_reserva = :id_reserva";
        $stmt = oci_parse($conn, $query);
        oci_bind_by_name($stmt, ":id_reserva", $id_reserva);

        if (!oci_execute($stmt)) {
            $error = oci_error($stmt);
            throw new Exception("Error al eliminar: " . $error['message']);
        }

        oci_commit($conn);
        echo "Reserva eliminada exitosamente.";
    } catch (Exception $e) {
        oci_rollback($conn);
        echo "Error: " . $e->getMessage();
    } finally {
        oci_free_statement($stmt);
        oci_close($conn);
    }
}
?>