USE HOSPITAL;
GO

-- 1. Aprobar Solicitud Empleado (Operativo)
-- Cuando el estado cambia a 'Aceptado' (2), crea el usuario y el empleado.
/*
Automatiza el proceso de contratación. Convierte a un candidato en un usuario del sistema.
AFTER UPDATE en la tabla SOLICITUD_EMPLEADO.
Se dispara cuando el administrador cambia el estado de una solicitud a 'Aceptado' (2).
Verifica que el correo no exista ya en el sistema.
Crea automáticamente el registro de acceso en la tabla USUARIO.
Crea automáticamente el perfil en la tabla EMPLEADO con los datos de la solicitud.
Uso en el Sistema: Módulo 'Solicitudes de Empleo'. Al dar clic en "Aprobar", 
el sistema realiza toda la carga de datos sin intervención manual.
*/
IF OBJECT_ID('TRG_Aprobar_Empleado', 'TR') IS NOT NULL DROP TRIGGER TRG_Aprobar_Empleado;
GO
CREATE TRIGGER TRG_Aprobar_Empleado ON SOLICITUD_EMPLEADO AFTER UPDATE AS
BEGIN
    SET NOCOUNT ON;
    IF EXISTS (SELECT 1 FROM inserted WHERE id_estado = 2)
    BEGIN
        INSERT INTO USUARIO (correo, contrasenia, tipo)
        SELECT i.correo, i.contrasenia, 'Empleado' FROM inserted i WHERE i.id_estado = 2 AND NOT EXISTS (SELECT 1 FROM USUARIO u WHERE u.correo = i.correo);
        INSERT INTO EMPLEADO (genero, curp, primer_nom_emp, segundo_nom_emp, a_pat_emp, a_mat_emp, telefono, correo, activo)
        SELECT i.genero, i.curp, i.primer_nombre, i.segundo_nombre, i.a_paterno, i.a_materno, i.telefono, i.correo, 1
        FROM inserted i WHERE i.id_estado = 2 AND NOT EXISTS (SELECT 1 FROM EMPLEADO e WHERE e.correo = i.correo);
    END
END;
GO

-- 2. Auditoría: Baja Lógica de Empleado
-- Se dispara cuando el campo 'activo' cambia de 1 a 0.
/*
Mecanismo de seguridad crítica (Integridad Referencial de Negocio).
Evita que se desactive a un médico si este todavía tiene pacientes esperando ser atendidos.
AFTER UPDATE en la tabla EMPLEADO.
Detecta si la columna 'activo' cambió de 1 (Activo) a 0 (Inactivo).
Si el empleado es un Doctor, busca en la tabla CITA si tiene citas futuras y activas.
Si encuentra pendientes, ejecuta ROLLBACK y lanza un error explicativo.
Uso en el Sistema: Módulo 'Gestión de Personal'. Protege contra errores humanos de RRHH 
al intentar dar de baja personal médico activo.
*/
DROP TRIGGER IF EXISTS trg_ValidarBajaEmpleado;
GO

CREATE TRIGGER trg_ValidarBajaEmpleado
ON EMPLEADO
AFTER UPDATE
AS
BEGIN
    SET NOCOUNT ON;
    IF UPDATE(activo)
    BEGIN
        IF EXISTS (
            SELECT 1
            FROM inserted i
            INNER JOIN deleted d ON i.id_empleado = d.id_empleado
            INNER JOIN DOCTOR doc ON i.id_empleado = doc.id_empleado -- Join para ver si es doctor
            INNER JOIN CITA c ON doc.id_doctor = c.id_doctor         -- Join para ver sus citas
            WHERE i.activo = 0        -- Nuevo estado: Inactivo
              AND d.activo = 1        -- Viejo estado: Activo
              AND c.fecha_cita >= CAST(GETDATE() AS DATE) 
              AND c.id_estado NOT IN (3, 4, 5, 6, 7, 9, 10) 
        )
        BEGIN
            ROLLBACK TRANSACTION;
            RAISERROR ('ACCION DENEGADA: El doctor tiene citas programadas pendientes. Debes cancelarlas o reasignarlas antes de darlo de baja.', 16, 1);
            RETURN;
        END
    END
END;
GO

-- 3. Auditoría: Cambio de Estado Cita
-- Se dispara cuando una cita cambia de estado (ej. de Pendiente a Cancelada).
/*
Rastreo del ciclo de vida de las citas médicas (Trazabilidad).
Registra cada movimiento importante que sufre una cita desde que se crea hasta que termina.
AFTER UPDATE en la tabla CITA.
Compara el estado anterior (deleted) con el nuevo estado (inserted).
Si son diferentes, guarda un registro en BITACORA_HISTORIAL indicando la transición (ej. "1 -> 3").
Uso en el Sistema: Transversal. Auditoría de cambios realizados por Cajeros (Pago), 
Doctores (Atención) o Administradores (Cancelación).
*/
IF OBJECT_ID('TRG_Auditoria_CambioEstadoCita', 'TR') IS NOT NULL DROP TRIGGER TRG_Auditoria_CambioEstadoCita;
GO
CREATE TRIGGER TRG_Auditoria_CambioEstadoCita ON CITA AFTER UPDATE AS
BEGIN
    DECLARE @id INT, @old INT, @new INT; 
    SELECT @id = folio_cita, @old = id_estado FROM deleted; 
    SELECT @new = id_estado FROM inserted;
    
    IF @old <> @new 
        INSERT INTO BITACORA_HISTORIAL VALUES (GETDATE(), 'SISTEMA', 'CAMBIO ESTATUS', 'Cita #' + CAST(@id AS VARCHAR) + ': ' + CAST(@old AS VARCHAR) + ' -> ' + CAST(@new AS VARCHAR), @id);
END;
GO






