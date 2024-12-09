<?php
function conectarDB() {
    return oci_connect('OSCARHORTA99', 'RETURN#ROOT', 'localhost/XE');
}

function listarUsuarios() {
    $conn = conectarDB();
    $query = "SELECT id_usuario, nombre, correo, rol FROM usuarios";
    $stmt = oci_parse($conn, $query);
    oci_execute($stmt);

    $usuarios = [];
    while ($row = oci_fetch_assoc($stmt)) {
        $usuarios[] = $row;
    }

    oci_free_statement($stmt);
    oci_close($conn);

    return $usuarios;
}

function insertarUsuario($nombre, $correo, $contraseña, $rol) {
    $conn = conectarDB();
    try {
        $query = "INSERT INTO usuarios (id_usuario, nombre, correo, contraseña, rol) 
                  VALUES (usuarios_seq.NEXTVAL, :nombre, :correo, :contraseña, :rol)";
        $stmt = oci_parse($conn, $query);
        oci_bind_by_name($stmt, ":nombre", $nombre);
        oci_bind_by_name($stmt, ":correo", $correo);
        oci_bind_by_name($stmt, ":contraseña", $contraseña);
        oci_bind_by_name($stmt, ":rol", $rol);

        if (!oci_execute($stmt)) {
            $error = oci_error($stmt);
            throw new Exception("Error al insertar: " . $error['message']);
        }

        oci_commit($conn);
        echo "Usuario registrado exitosamente.";
    } catch (Exception $e) {
        oci_rollback($conn);
        echo "Error: " . $e->getMessage();
    } finally {
        oci_free_statement($stmt);
        oci_close($conn);
    }
}

function actualizarUsuario($id_usuario, $nombre, $correo, $rol) {
    $conn = conectarDB();
    try {
        $query = "UPDATE usuarios SET nombre = :nombre, correo = :correo, rol = :rol 
                  WHERE id_usuario = :id_usuario";
        $stmt = oci_parse($conn, $query);
        oci_bind_by_name($stmt, ":id_usuario", $id_usuario);
        oci_bind_by_name($stmt, ":nombre", $nombre);
        oci_bind_by_name($stmt, ":correo", $correo);
        oci_bind_by_name($stmt, ":rol", $rol);

        if (!oci_execute($stmt)) {
            $error = oci_error($stmt);
            throw new Exception("Error al actualizar: " . $error['message']);
        }

        oci_commit($conn);
        echo "Usuario actualizado exitosamente.";
    } catch (Exception $e) {
        oci_rollback($conn);
        echo "Error: " . $e->getMessage();
    } finally {
        oci_free_statement($stmt);
        oci_close($conn);
    }
}

function actualizarContraseñaUsuario($id_usuario, $nueva_contraseña) {
    $conn = conectarDB();
    try {
        $query = "UPDATE usuarios SET contraseña = :contraseña WHERE id_usuario = :id_usuario";
        $stmt = oci_parse($conn, $query);
        oci_bind_by_name($stmt, ":id_usuario", $id_usuario);
        oci_bind_by_name($stmt, ":contraseña", $nueva_contraseña);

        if (!oci_execute($stmt)) {
            $error = oci_error($stmt);
            throw new Exception("Error al actualizar la contraseña: " . $error['message']);
        }

        oci_commit($conn);
        echo "Contraseña actualizada exitosamente.";
    } catch (Exception $e) {
        oci_rollback($conn);
        echo "Error: " . $e->getMessage();
    } finally {
        oci_free_statement($stmt);
        oci_close($conn);
    }
}

function eliminarUsuario($id_usuario) {
    $conn = conectarDB();
    try {
        $query = "DELETE FROM usuarios WHERE id_usuario = :id_usuario";
        $stmt = oci_parse($conn, $query);
        oci_bind_by_name($stmt, ":id_usuario", $id_usuario);

        if (!oci_execute($stmt)) {
            $error = oci_error($stmt);
            throw new Exception("Error al eliminar: " . $error['message']);
        }

        oci_commit($conn);
        echo "Usuario eliminado exitosamente.";
    } catch (Exception $e) {
        oci_rollback($conn);
        echo "Error: " . $e->getMessage();
    } finally {
        oci_free_statement($stmt);
        oci_close($conn);
    }
}
?>