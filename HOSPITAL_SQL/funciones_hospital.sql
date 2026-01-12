USE HOSPITAL;
GO

-- 1. Validar si una cita cumple con las 48 horas de anticipación
IF OBJECT_ID('FN_ValidarFechaCita', 'FN') IS NOT NULL DROP FUNCTION FN_ValidarFechaCita;
GO
CREATE FUNCTION FN_ValidarFechaCita (@FechaCita DATETIME)
RETURNS INT AS
BEGIN
    DECLARE @FechaLimite DATETIME = DATEADD(HOUR, 48, GETDATE());
    IF @FechaCita < @FechaLimite RETURN 0;
    RETURN 1;
END
GO

-- 2. Validar nacimiento (no fechas futuras)
IF OBJECT_ID('FN_ValidarFechaNacimiento', 'FN') IS NOT NULL DROP FUNCTION FN_ValidarFechaNacimiento;
GO
CREATE FUNCTION FN_ValidarFechaNacimiento (@FechaNacimiento DATE)
RETURNS INT AS
BEGIN
    IF @FechaNacimiento > CONVERT(DATE, GETDATE()) RETURN 0;
    RETURN 1;
END
GO

-- 3. Calcular tiempo restante para pagar (8 horas)
IF OBJECT_ID('FN_TiempoRestantePago', 'FN') IS NOT NULL DROP FUNCTION FN_TiempoRestantePago;
GO
CREATE FUNCTION dbo.FN_TiempoRestantePago (@folio_cita INT, @FechaActual DATETIME)
RETURNS DECIMAL(10, 2) AS
BEGIN
    DECLARE @FechaSolicitud DATETIME;
    SELECT @FechaSolicitud = CAST(fecha_solicitud AS DATETIME) FROM CITA WHERE folio_cita = @folio_cita;
    IF @FechaSolicitud IS NULL RETURN 0.00;
    
    DECLARE @FechaLimite DATETIME = DATEADD(HOUR, 8, @FechaSolicitud);
    DECLARE @SegundosRestantes BIGINT = DATEDIFF(SECOND, @FechaActual, @FechaLimite);
    
    IF @SegundosRestantes < 0 RETURN 0.00;
    RETURN CAST(@SegundosRestantes AS DECIMAL(10, 2)) / 3600.00;
END
GO

-- 4. Calcular IMC
IF OBJECT_ID('FN_CalcularIMC', 'FN') IS NOT NULL DROP FUNCTION FN_CalcularIMC;
GO
CREATE FUNCTION FN_CalcularIMC (@Peso DECIMAL(5,2), @Estatura DECIMAL(5,2))
RETURNS DECIMAL(5,2) AS
BEGIN
    IF @Estatura > 0 RETURN @Peso / (@Estatura * @Estatura);
    RETURN 0;
END
GO

-- 5. Validar si un empleado se puede dar de baja (sin pendientes activos)
IF OBJECT_ID('FN_ValidarBajaEmpleado', 'FN') IS NOT NULL DROP FUNCTION FN_ValidarBajaEmpleado;
GO
Select*from EMPLEADO
CREATE FUNCTION FN_ValidarBajaEmpleado (@id_empleado INT)
RETURNS INT AS
BEGIN
    DECLARE @EsPosible INT = 1;
    DECLARE @id_doctor INT;
    SELECT @id_doctor = id_doctor FROM DOCTOR WHERE id_empleado = @id_empleado;

    IF @id_doctor IS NOT NULL
    BEGIN
        -- Si tiene citas vivas (Pendiente Pago, Por Atender o Solicitud Cancelación)
        IF EXISTS (SELECT 1 FROM CITA WHERE id_doctor = @id_doctor AND id_estado IN (1, 2, 5))
            SET @EsPosible = 0;
    END
    RETURN @EsPosible;
END
GO
