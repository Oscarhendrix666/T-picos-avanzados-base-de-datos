DROP TABLE Vuelo CASCADE CONSTRAINTS;
DROP TABLE Asiento CASCADE CONSTRAINTS;
DROP TABLE Pasaje CASCADE CONSTRAINTS;
DROP TABLE Pasajero CASCADE CONSTRAINTS;
DROP TABLE Registro_CheckIn CASCADE CONSTRAINTS;
DROP TABLE Equipaje CASCADE CONSTRAINTS;

-- TABLAS

-- Tabla de Vuelos (poblada por sistemas)
CREATE TABLE Vuelo (
    id_vuelo INT PRIMARY KEY,
    origen VARCHAR(50) NOT NULL,
    destino VARCHAR(50) NOT NULL,
    hora_salida DATE NOT NULL,
    hora_llegada DATE NOT NULL,
    estado VARCHAR(20) DEFAULT 'Programado'
);


-- Tabla de Asientos (poblada por sistemas)
CREATE TABLE Asiento (
    id_asiento INT PRIMARY KEY,
    id_vuelo INT,
    numero_asiento VARCHAR(5) NOT NULL,
    clase VARCHAR(20) NOT NULL,
    estado VARCHAR(20) DEFAULT 'Disponible',
    FOREIGN KEY (id_vuelo) REFERENCES Vuelo(id_vuelo) ON DELETE CASCADE
);

-- Tabla de Pasajeros (actualizada por el cliente)
CREATE TABLE Pasajero (
    rut_pasajero VARCHAR(10) PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    apellido VARCHAR(50) NOT NULL,
    numero_pasaporte VARCHAR(20) UNIQUE,
    correo VARCHAR(50)
);

-- Tabla de Pasajes (poblada por sistemas)
CREATE TABLE Pasaje (
    id_pasaje INT PRIMARY KEY,
    id_vuelo INT,
    id_asiento INT,
    rut_pasajero VARCHAR(10),
    FOREIGN KEY (id_vuelo) REFERENCES Vuelo(id_vuelo) ON DELETE CASCADE,
    FOREIGN KEY (id_asiento) REFERENCES Asiento(id_asiento) ON DELETE CASCADE,
    FOREIGN KEY (rut_pasajero) REFERENCES Pasajero(rut_pasajero) ON DELETE CASCADE
);

-- Tabla de Check-In (actualizada por el cliente)
CREATE TABLE Registro_CheckIn (
    id_checkin INT PRIMARY KEY,
    rut_pasajero VARCHAR(10),
    id_vuelo INT,
    hora_checkin DATE DEFAULT CURRENT_TIMESTAMP,
    estado VARCHAR(20) DEFAULT 'Pendiente',
    FOREIGN KEY (rut_pasajero) REFERENCES Pasajero(rut_pasajero) ON DELETE CASCADE,
    FOREIGN KEY (id_vuelo) REFERENCES Vuelo(id_vuelo) ON DELETE CASCADE
);

-- Tabla de Equipaje
CREATE TABLE Equipaje (
    id_equipaje INT PRIMARY KEY,
    rut_pasajero VARCHAR(10),
    id_vuelo INT,
    peso DECIMAL(5,2) NOT NULL,
    tipo VARCHAR(20) NOT NULL,
    FOREIGN KEY (rut_pasajero) REFERENCES Pasajero(rut_pasajero) ON DELETE CASCADE,
    FOREIGN KEY (id_vuelo) REFERENCES Vuelo(id_vuelo) ON DELETE CASCADE
);


--TRIGGERS
CREATE TRIGGER ValidarPasaporteUnico
BEFORE INSERT ON Pasajero
FOR EACH ROW
DECLARE
    cantidad_pasaporte INT DEFAULT 0;
    error_pasaporte_duplicado EXCEPTION;
BEGIN
    -- Verifica si el pasaporte ya está registrado
    SELECT COUNT(*)
    INTO cantidad_pasaporte
    FROM Pasajero
    WHERE numero_pasaporte = NEW.numero_pasaporte;

    IF cantidad_pasaporte > 0 THEN
        RAISE error_pasaporte_duplicado;
    END IF;
EXCEPTION
    WHEN error_pasaporte_duplicado THEN
        RAISE_APPLICATION_ERROR(-20005, 'El pasaporte ya está registrado');
END;


-- Trigger para verificar peso de equipaje permitido
CREATE TRIGGER VerificarPesoEquipaje
BEFORE INSERT ON Equipaje
FOR EACH ROW
DECLARE
    error_peso EXCEPTION;
BEGIN
    IF NEW.peso > 23 THEN
        RAISE error_peso;
    END IF;
EXCEPTION
    WHEN error_peso THEN
        RAISE_APPLICATION_ERROR(-20003, 'El peso del equipaje excede el límite permitido de 23 kg');
END;


CREATE TRIGGER RegistrarHistorialVuelo
AFTER INSERT ON Registro_CheckIn
FOR EACH ROW
BEGIN
    INSERT INTO Historial_Vuelos (rut_pasajero, id_vuelo)
    VALUES (NEW.rut_pasajero, NEW.id_vuelo);
END;


-- PROCEDIMIENTOS

-- Procedimiento para Insertar un Pasajero
CREATE PROCEDURE AgregarPasajero (
    IN rut_pasajero VARCHAR(10),
    IN nombre VARCHAR(50),
    IN apellido VARCHAR(50),
    IN numero_pasaporte VARCHAR(20),
    IN correo VARCHAR(50)
)
IS
    error_formato EXCEPTION;
    error_duplicado EXCEPTION;
BEGIN
    IF NOT REGEXP_LIKE(rut_pasajero, '^[0-9]{7,8}-[0-9kK]$') THEN
        RAISE error_formato;
    ELSIF EXISTS (SELECT 1 FROM Pasajero WHERE rut_pasajero = rut_pasajero) THEN
        RAISE error_duplicado;
    ELSE
        INSERT INTO Pasajero (rut_pasajero, nombre, apellido, numero_pasaporte, correo)
        VALUES (rut_pasajero, nombre, apellido, numero_pasaporte, correo);
    END IF;

    COMMIT;
EXCEPTION
    WHEN error_formato THEN
        RAISE_APPLICATION_ERROR(-20001, 'El RUT debe estar en el formato 12345678-k o 12345678-9');
    WHEN error_duplicado THEN
        RAISE_APPLICATION_ERROR(-20002, 'El pasaporte ya está registrado');
    WHEN OTHERS THEN
        ROLLBACK;
        RAISE_APPLICATION_ERROR(-20000, 'Error inesperado al insertar el pasajero');
END;


-- Procedimiento para Eliminar un Pasajero
CREATE PROCEDURE EliminarPasajero (
    IN rut_pasajero INT
)
IS
    error_no_existente EXCEPTION;
BEGIN
    IF EXISTS (SELECT 1 FROM Pasajero WHERE rut_pasajero = rut_pasajero) THEN
        DELETE FROM Pasajero WHERE rut_pasajero = rut_pasajero;
    ELSE
        RAISE error_no_existente;
    END IF;

    COMMIT;
EXCEPTION
    WHEN error_no_existente THEN
        RAISE_APPLICATION_ERROR(-20004, 'El pasajero no existe');
    WHEN OTHERS THEN
        ROLLBACK;
        RAISE_APPLICATION_ERROR(-20000, 'Error inesperado al eliminar el pasajero');
END;


-- Procedimiento para actualizar el estado del check-in
CREATE PROCEDURE ActualizarCheckIn (
    IN id_checkin INT,
    IN nuevo_estado VARCHAR(20)
)
IS
    error_checkin_no_existe EXCEPTION;
BEGIN
    START TRANSACTION;
    
    IF EXISTS (SELECT 1 FROM Registro_CheckIn WHERE id_checkin = id_checkin) THEN
        UPDATE Registro_CheckIn
        SET estado = nuevo_estado
        WHERE id_checkin = id_checkin;
    ELSE
        RAISE error_checkin_no_existe;
    END IF;

    COMMIT;
EXCEPTION
    WHEN error_checkin_no_existe THEN
        ROLLBACK;
        RAISE_APPLICATION_ERROR(-20006, 'El check-in no existe');
    WHEN OTHERS THEN
        ROLLBACK;
        RAISE_APPLICATION_ERROR(-20000, 'Error inesperado al actualizar el check-in');
END;


-- Procedimiento para Registrar Equipaje
CREATE PROCEDURE RegistrarEquipaje (
    IN rut_pasajero INT,
    IN id_vuelo INT,
    IN peso DECIMAL(5,2),
    IN tipo VARCHAR(20)
)
IS
    error_peso EXCEPTION;
BEGIN
    -- Verifica el peso del equipaje antes de insertar
    IF peso > 23 THEN
        RAISE error_peso;
    ELSE
        INSERT INTO Equipaje (rut_pasajero, id_vuelo, peso, tipo)
        VALUES (rut_pasajero, id_vuelo, peso, tipo);
    END IF;

    COMMIT;
EXCEPTION
    WHEN error_peso THEN
        RAISE_APPLICATION_ERROR(-20003, 'El peso del equipaje excede el límite permitido de 23 kg');
    WHEN OTHERS THEN
        ROLLBACK;
        RAISE_APPLICATION_ERROR(-20000, 'Error inesperado al registrar el equipaje');
END;


-- Procedimiento para consultar el historial de vuelos de un pasajero
CREATE PROCEDURE ConsultarHistorialVuelos (
    IN rut_pasajero INT
)
BEGIN
    SELECT V.id_vuelo, V.origen, V.destino, HV.fecha
    FROM Historial_Vuelos HV
    JOIN Vuelo V ON HV.id_vuelo = V.id_vuelo
    WHERE HV.rut_pasajero = rut_pasajero
    ORDER BY HV.fecha DESC;
END;


-- Procedimiento para eliminar un pasaje
CREATE PROCEDURE EliminarPasaje (
    IN id_pasaje INT
)
IS
    error_pasaje_no_existe EXCEPTION;
BEGIN
    -- Inicia una transacción
    START TRANSACTION;

    -- Verifica si el pasaje existe
    IF EXISTS (SELECT 1 FROM Pasaje WHERE id_pasaje = id_pasaje) THEN
        DELETE FROM Pasaje WHERE id_pasaje = id_pasaje;
    ELSE
        RAISE error_pasaje_no_existe;
    END IF;

    -- Confirma la transacción si no hay errores
    COMMIT;
EXCEPTION
    WHEN error_pasaje_no_existe THEN
        ROLLBACK;
        RAISE_APPLICATION_ERROR(-20007, 'El pasaje no existe');
    WHEN OTHERS THEN
        ROLLBACK;
        RAISE_APPLICATION_ERROR(-20000, 'Error inesperado al eliminar el pasaje');
END;


-- Procedimiento para eliminar un equipaje
CREATE PROCEDURE EliminarEquipaje (
    IN id_equipaje INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SELECT 'Error al eliminar el equipaje';
    END;

    START TRANSACTION;

    IF EXISTS (SELECT 1 FROM Equipaje WHERE id_equipaje = id_equipaje) THEN
        DELETE FROM Equipaje WHERE id_equipaje = id_equipaje;
    ELSE
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El equipaje no existe';
    END IF;

    COMMIT;
END;


DROP TRIGGER ValidarPasaporteUnico;
DROP TRIGGER VerificarPesoEquipaje;
DROP TRIGGER RegistrarHistorialVuelo;
DROP PROCEDURE AgregarPasajero;
DROP PROCEDURE EliminarPasajero;
DROP PROCEDURE ConsultarHistorialVuelos;
DROP PROCEDURE ActualizarCheckIn;
DROP PROCEDURE RegistrarEquipaje;
DROP PROCEDURE EliminarPasaje;
DROP PROCEDURE EliminarEquipaje;
