USE HOSPITAL;
GO

/* 1. VISTA DE DOCTORES*/
IF OBJECT_ID('V_InfoDoctores', 'V') IS NOT NULL
    DROP VIEW V_InfoDoctores;
GO

CREATE VIEW V_InfoDoctores AS
SELECT
    D.id_doctor,
    E.id_empleado,
    D.no_consultorio,
    E.primer_nom_emp + ' ' + E.a_pat_emp + ISNULL(' ' + E.a_mat_emp, '') AS nombre_doctor,
    ES.nombre AS especialidad,
    ES.tiempo_min_cita
FROM EMPLEADO AS E
INNER JOIN DOCTOR AS D ON D.id_empleado = E.id_empleado
INNER JOIN ESPECIALIDAD AS ES ON ES.id_especialidad = D.id_especialidad;
GO

SELECT * FROM V_InfoDoctores;

/* 2. VISTA DE PACIENTES */
IF OBJECT_ID('V_InfoPacientes', 'V') IS NOT NULL
    DROP VIEW V_InfoPacientes;
GO

CREATE VIEW V_InfoPacientes AS
SELECT 
    P.id_paciente,
    U.correo,
    P.primer_nombre + ' ' + P.a_paterno + ISNULL(' ' + P.a_materno, '') AS nombre_paciente,
    P.nacimiento,
    P.telefono,
    P.tipo_sangre,
    P.alergias,
    P.donante,
    P.sexo,
    P.fracturas
FROM PACIENTE AS P
INNER JOIN USUARIO AS U ON U.correo = P.correo;
GO

SELECT * FROM V_InfoPacientes;

/* 3. VISTA DE CITAS DETALLADAS */
IF OBJECT_ID('V_CitasDetalladas', 'V') IS NOT NULL
    DROP VIEW V_CitasDetalladas;
GO

CREATE VIEW V_CitasDetalladas AS 
SELECT 
    C.folio_cita,
    C.fecha_cita,
    C.fecha_fin_cita,
    EC.estado AS estado_actual,
    C.no_consultorio,
    C.id_paciente, 
    P.nombre_paciente,
    D.id_doctor,
    D.nombre_doctor,
    D.especialidad
FROM CITA AS C
INNER JOIN ESTADO_CITA AS EC ON C.id_estado = EC.id_estado
INNER JOIN V_InfoDoctores AS D ON C.id_doctor = D.id_doctor
INNER JOIN V_InfoPacientes AS P ON C.id_paciente = P.id_paciente;
GO

SELECT * FROM V_CitasDetalladas;

/* 4. VISTA DE HISTORIAL MEDICO */
IF OBJECT_ID('V_HistorialMedico', 'V') IS NOT NULL
    DROP VIEW V_HistorialMedico;
GO

CREATE VIEW V_HistorialMedico AS 
SELECT 
    HM.id_historial_medico,
    P.id_paciente, 
    P.nombre_paciente, 
    P.fracturas,
    P.alergias,
    P.tipo_sangre,
    HM.padecimiento,
    HM.presion_sistolica,
    HM.presion_diastolica,
    HM.peso,
    HM.estatura,
    HM.fecha,
    HM.oxigenacion,
    HM.detalles
FROM HISTORIAL_MEDICO AS HM
JOIN V_InfoPacientes AS P ON HM.id_paciente = P.id_paciente;
GO



SELECT * FROM V_HistorialMedico;


/* 5. VISTA DE DATOS DE LA RECETA */
IF OBJECT_ID('V_DatosReceta', 'V') IS NOT NULL
    DROP VIEW V_DatosReceta;
GO

CREATE VIEW V_DatosReceta AS
SELECT 
    c.folio_cita,
    c.fecha_cita,
    p.id_paciente,
    p.primer_nombre AS p_nombre,
    p.a_paterno AS p_paterno,
    p.a_materno AS p_materno,
    p.nacimiento,
    p.sexo,
    doc_emp.primer_nom_emp AS d_nombre,
    doc_emp.a_pat_emp AS d_paterno,
    d.cedula_profesional,
    d.no_consultorio,
    e.nombre AS especialidad,
    -- Datos Clínicos
    r.diagnostico,
    hm.detalles AS tratamiento
FROM CITA c
    INNER JOIN RECETA r ON c.folio_cita = r.folio_cita
    INNER JOIN PACIENTE p ON c.id_paciente = p.id_paciente
    INNER JOIN DOCTOR d ON c.id_doctor = d.id_doctor
    INNER JOIN EMPLEADO doc_emp ON d.id_empleado = doc_emp.id_empleado
    INNER JOIN ESPECIALIDAD e ON d.id_especialidad = e.id_especialidad
    LEFT JOIN HISTORIAL_MEDICO hm ON hm.id_paciente = p.id_paciente 
         AND CAST(hm.fecha AS DATE) = CAST(c.fecha_cita AS DATE);
GO

SELECT * FROM V_DatosReceta;


/* 6. VISTA DE LA BITACORA GENERAL DE LAS ACCIONES REALIZADAS*/
IF OBJECT_ID('V_Bitacora_General', 'V') IS NOT NULL
    DROP VIEW V_Bitacora_General;
GO

CREATE VIEW V_Bitacora_General AS
SELECT 
    id_historial AS ID,
    FORMAT(fecha, 'dd/MM/yyyy HH:mm') AS Fecha_Hora,
    correo AS Usuario,
    cambio_realizado AS Accion,
    detalles AS Detalles,
    id_afectados AS ID_Referencia
FROM BITACORA_HISTORIAL;
GO

SELECT * FROM V_Bitacora_General;