USE HOSPITAL;
GO

-- 1. Agendar Cita (Validaciones complejas y transacción)
/*Se encarga de crear una nueva cita médica asegurando que se cumplan todas las reglas de negocio, 
  disponibilidad de horario y consistencia de datos.
@id_paciente: El ID del paciente que solicita la atención.
@id_doctor: El ID del médico seleccionado.
@fecha_hora: La fecha y hora exacta deseada para la cita.
Transacción: Garantiza que si algo falla, no se guarde basura en la base de datos.
Cálculo de Duración: Busca la especialidad del doctor para saber cuánto dura la cita y calcular la hora de fin.
Validaciones de Negocio:
    Que la fecha sea futura.
    Que sea con 48 horas de anticipación (usando la función FN_ValidarFechaCita).
    Que el paciente no tenga ya una cita pendiente con ese mismo doctor.
Validación de Horario Laboral: Verifica en la tabla HORARIO si el doctor trabaja ese día de la semana
y si la hora solicitada está dentro de su turno (convierte los días de inglés a español).
Validación de Traslape: Revisa que el doctor no tenga otra cita ocupada en ese mismo lapso de tiempo.
Inserción: Si todo está bien, guarda el registro en la tabla CITA*/
IF OBJECT_ID('SP_AgendarCita', 'P') IS NOT NULL DROP PROCEDURE SP_AgendarCita;
GO
CREATE PROCEDURE SP_AgendarCita
    @id_paciente INT, @id_doctor INT, @fecha_hora DATETIME
AS
BEGIN
    SET NOCOUNT ON;
    DECLARE @fecha_fin DATETIME;
    DECLARE @id_empleado_doctor INT, @no_consultorio INT, @tiempo_min_cita INT;
    DECLARE @dia_semana_en VARCHAR(20), @dia_semana_es VARCHAR(20);
    DECLARE @hora_inicio TIME, @hora_fin TIME;
    
    BEGIN TRY
        BEGIN TRANSACTION;
        SELECT @id_empleado_doctor = d.id_empleado, @no_consultorio = d.no_consultorio, @tiempo_min_cita = e.tiempo_min_cita
        FROM DOCTOR d JOIN ESPECIALIDAD e ON d.id_especialidad = e.id_especialidad WHERE d.id_doctor = @id_doctor;
        IF @id_empleado_doctor IS NULL THROW 50001, 'El doctor seleccionado no existe.', 1;
        SET @fecha_fin = DATEADD(MINUTE, @tiempo_min_cita, @fecha_hora);
        IF @fecha_hora <= GETDATE() THROW 50002, 'La fecha debe ser futura.', 1;
        IF dbo.FN_ValidarFechaCita(@fecha_hora) = 0 THROW 50003, 'Requiere 48 horas de anticipación.', 1;
        IF EXISTS (SELECT 1 FROM CITA WHERE id_paciente = @id_paciente AND id_doctor = @id_doctor AND id_estado IN (1, 2))
            THROW 50004, 'Ya tienes una cita pendiente con este doctor.', 1;
        SET @dia_semana_en = DATENAME(WEEKDAY, @fecha_hora);
        SET @dia_semana_es = CASE @dia_semana_en WHEN 'Monday' THEN 'Lunes' WHEN 'Tuesday' THEN 'Martes' WHEN 'Wednesday' THEN 'Miércoles' WHEN 'Thursday' THEN 'Jueves' WHEN 'Friday' THEN 'Viernes' WHEN 'Saturday' THEN 'Sábado' ELSE 'Domingo' END;
        SELECT @hora_inicio = hora_ini, @hora_fin = hora_fin FROM HORARIO WHERE id_empleado = @id_empleado_doctor AND dia = @dia_semana_es;
        IF @hora_inicio IS NULL OR CAST(@fecha_hora AS TIME) < @hora_inicio OR CAST(@fecha_fin AS TIME) > @hora_fin
            THROW 50006, 'Fuera de horario laboral.', 1;
        IF EXISTS (SELECT 1 FROM CITA WHERE id_doctor = @id_doctor AND id_estado NOT IN (3, 4) AND ((@fecha_hora < fecha_fin_cita AND @fecha_fin > fecha_cita)))
            THROW 50007, 'Horario ocupado por otro paciente.', 1;
        INSERT INTO CITA (fecha_cita, fecha_solicitud, id_estado, fecha_fin_cita, id_paciente, no_consultorio, id_doctor)
        VALUES (@fecha_hora, GETDATE(), 1, @fecha_fin, @id_paciente, @no_consultorio, @id_doctor);
        COMMIT TRANSACTION;
    END TRY
    BEGIN CATCH
        IF @@TRANCOUNT > 0 ROLLBACK TRANSACTION;
        THROW;
    END CATCH
END;
GO

EXEC SP_AgendarCita 1, 5, '2026-12-25 10:00:00';

-- 2. Dar de Baja Empleado (Baja Lógica Segura)
/*
Realiza una Baja Lógica de un empleado. En lugar de borrar el registro, simplemente cambia su estado a "Inactivo".
@id_empleado: El ID del empleado a desactivar.
Verificación de Rol: Revisa si el empleado es un Doctor en la tabla DOCTOR.
Bloqueo de Seguridad: Si es doctor, verifica si tiene citas "viv" (Pendientes, Confirmadas o Reprogramadas). 
                      Si tiene citas futuras, lanza un error y detiene la baja para evitar dejar pacientes abandonados.
Actualización: Si no hay bloqueos, ejecuta un UPDATE EMPLEADO SET activo = 0.
Uso en el Sistema: Se utiliza en el módulo "Gestión de Personal"
*/
IF OBJECT_ID('SP_DarBajaEmpleado', 'P') IS NOT NULL DROP PROCEDURE SP_DarBajaEmpleado;
GO
CREATE PROCEDURE SP_DarBajaEmpleado @id_empleado INT
AS
BEGIN
    SET NOCOUNT ON;
    BEGIN TRY
        BEGIN TRANSACTION;
        DECLARE @id_doctor INT;
        SELECT @id_doctor = id_doctor FROM DOCTOR WHERE id_empleado = @id_empleado;

        IF @id_doctor IS NOT NULL
        BEGIN
            IF EXISTS (SELECT 1 FROM CITA WHERE id_doctor = @id_doctor AND id_estado IN (1, 2, 5))
                THROW 50001, 'DENEGADO: El médico tiene citas pendientes. Debe cerrarlas antes.', 1;
        END
        UPDATE EMPLEADO SET activo = 0 WHERE id_empleado = @id_empleado;
        COMMIT TRANSACTION;
    END TRY
    BEGIN CATCH
        IF @@TRANCOUNT > 0 ROLLBACK TRANSACTION;
        THROW;
    END CATCH
END;
GO

EXEC SP_DarBajaEmpleado 45;

-- 3. Obtener Historial Médico
/*
Obtiene el expediente clínico completo de un paciente específico.
@id_paciente: El ID único del paciente del cual queremos ver la historia.
Selección de Datos: Trae datos de HISTORIAL_MEDICO.
Cruce de Información (JOINs): Como la tabla de historial solo guarda datos clínicos, 
este SP hace un cruce con la tabla CITA coincidiendo la fecha y el paciente.
Recuperación del Doctor: Gracias al cruce anterior, deduce qué doctor atendió al paciente ese día y muestra su nombre y especialidad.
Ordenamiento: Muestra los registros del más reciente al más antiguo.
Uso en el Sistema: Se utiliza probablemente en el Perfil del Paciente o cuando un doctor ya está atendiendo
a alguien y quiere ver sus antecedentes específicos. Es una consulta "directa" por ID.
*/
IF OBJECT_ID('SP_ObtenerHistorialPaciente', 'P') IS NOT NULL
    DROP PROCEDURE SP_ObtenerHistorialPaciente;
GO

CREATE PROCEDURE SP_ObtenerHistorialPaciente
    @id_paciente INT
AS
BEGIN
    SET NOCOUNT ON;
    SELECT 
        h.id_historial_medico AS id_bitacora, 
        h.fecha AS Fecha_movimiento,          
        COALESCE(doc_emp.primer_nom_emp + ' ' + doc_emp.a_pat_emp, 'Sin Dato') AS Usuario_Medico,
        COALESCE(esp.nombre, 'General') AS Especialidad,
        p.primer_nombre + ' ' + p.a_paterno AS Nombre_Paciente,
        h.padecimiento AS Diagnostico,
        COALESCE(CAST(c.no_consultorio AS VARCHAR), 'N/A') AS Consultorio,
        h.detalles AS Tratamiento_Observaciones
    FROM HISTORIAL_MEDICO h
    JOIN PACIENTE p ON h.id_paciente = p.id_paciente
    LEFT JOIN CITA c ON h.id_paciente = c.id_paciente AND CAST(c.fecha_cita AS DATE) = h.fecha
    LEFT JOIN DOCTOR d ON c.id_doctor = d.id_doctor
    LEFT JOIN EMPLEADO doc_emp ON d.id_empleado = doc_emp.id_empleado
    LEFT JOIN ESPECIALIDAD esp ON d.id_especialidad = esp.id_especialidad
    WHERE h.id_paciente = @id_paciente
    ORDER BY h.fecha DESC;
END;
GO

EXEC SP_ObtenerHistorialPaciente 11;

-- 4. Obtener Historial Del Doctor
/*
Este es el motor del Buscador General de Historiales para la recepcionista. 
A diferencia del anterior (que busca por ID exacto), este permite buscar por coincidencias de texto (Nombre, Apellido o ID).
@busqueda: Una cadena de texto (ej. "Juan", "Perez", "55").
Comodines (%): Agrega porcentajes al texto para usar la búsqueda flexible LIKE.
Inferencia de Datos: Al igual que el anterior, une HISTORIAL_MEDICO con PACIENTE y CITA para averiguar qué doctor atendió esa consulta.
Manejo de Nulos (ISNULL): Si el historial se creó sin una cita previa, 
                          rellena el nombre del doctor con "No asignado" para que la tabla no se rompa visualmente.
Filtro Flexible (OR): Busca si el texto coincide con el ID O el Nombre O el Apellido Paterno O el Materno.
Uso en el Sistema: Se utiliza en la pantalla ver_historial.php (Panel de Recepción). 
                   Es el que permite que la barra de búsqueda encuentre resultados dinámicamente.
*/
CREATE OR ALTER PROCEDURE sp_BuscarHistorialConDoctor
    @busqueda VARCHAR(100)
AS
BEGIN
    SET NOCOUNT ON;
    DECLARE @termino VARCHAR(102) = '%' + @busqueda + '%';

    SELECT 
        HM.id_historial_medico,
        HM.fecha,
        HM.padecimiento,
        HM.detalles,
        HM.presion_sistolica,
        HM.presion_diastolica,
        HM.peso,
        HM.estatura,
        HM.oxigenacion,
        P.id_paciente,
        P.primer_nombre,
        P.a_paterno,
        P.a_materno,
        P.tipo_sangre,
        ISNULL(Emp.primer_nom_emp + ' ' + Emp.a_pat_emp, 'No asignado') AS nombre_doctor,
        ISNULL(Emp.telefono, 'S/N') AS contacto_doctor

    FROM HISTORIAL_MEDICO HM
    INNER JOIN PACIENTE P ON HM.id_paciente = P.id_paciente
    LEFT JOIN CITA C ON C.id_paciente = HM.id_paciente 
                     AND CAST(C.fecha_cita AS DATE) = HM.fecha 
    LEFT JOIN DOCTOR D ON C.id_doctor = D.id_doctor
    LEFT JOIN EMPLEADO Emp ON D.id_empleado = Emp.id_empleado
    WHERE 
        (CAST(P.id_paciente AS VARCHAR) LIKE @termino --aqui verificamos las coincidencias con la cadena de texto
        OR P.primer_nombre LIKE @termino 
        OR P.a_paterno LIKE @termino
        OR P.a_materno LIKE @termino)
    ORDER BY HM.fecha DESC;
END;
GO

EXEC sp_BuscarHistorialConDoctor 'Juan';