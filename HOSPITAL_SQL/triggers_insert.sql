USE HOSPITAL;
GO

-- 1. Actualizar Stock Farmacia (Operativo)
-- Al insertar un detalle de venta, resta el stock del medicamento.
/*
Mantiene el inventario de la farmacia actualizado en tiempo real. 
Es un trigger crítico para evitar vender medicamentos que ya no existen físicamente.
AFTER INSERT en la tabla DETALLE_VENTA.
Se dispara cuando se confirma qué medicamentos lleva el cliente.
Toma la cantidad vendida de la tabla virtual inserted.
Realiza una resta directa al campo stock de la tabla MEDICAMENTO correspondiente.
Uso en el Sistema: Módulo de Farmacia. Cuando el farmacéutico escanea productos y finaliza la venta, 
este trigger descuenta las cajas automáticamente.
*/
IF OBJECT_ID('TRG_ActualizarStock', 'TR') IS NOT NULL DROP TRIGGER TRG_ActualizarStock;
GO
CREATE TRIGGER TRG_ActualizarStock ON DETALLE_VENTA AFTER INSERT AS
BEGIN
    SET NOCOUNT ON;
    UPDATE m SET m.stock = m.stock - i.cantidad 
    FROM MEDICAMENTO m INNER JOIN inserted i ON m.id_medicamento = i.id_medicamento;
END;
GO

-- 2. Actualizar Cita al Pagar (Operativo)
-- Al insertar un pago, la cita cambia de estado automáticamente.
/*
Automatiza el flujo de la cita médica, vinculando el cobro en caja con la agenda del doctor.
AFTER INSERT en la tabla PAGO.
Se dispara inmediatamente después de registrar un cobro exitoso.
Identifica el folio de la cita asociado al pago en la tabla virtual inserted.
Actualiza el estado de dicha cita en la tabla CITA al valor 2 (Pagada/Confirmada).
Uso en el Sistema: Módulo de Caja. Permite que la cita pase de "Pendiente" a "Lista para atender" 
sin intervención manual del cajero.
*/
IF OBJECT_ID('TRG_ActualizarEstado_Al_Pagar', 'TR') IS NOT NULL DROP TRIGGER TRG_ActualizarEstado_Al_Pagar;
GO
CREATE TRIGGER TRG_ActualizarEstado_Al_Pagar ON PAGO AFTER INSERT AS
BEGIN
    SET NOCOUNT ON;
    UPDATE CITA SET id_estado = 2 
    FROM CITA c INNER JOIN inserted i ON c.folio_cita = i.folio_cita;
END;
GO

-- 3. Auditoría: Nuevo Diagnóstico
/*
Registra en la bitácora cada vez que un médico finaliza una consulta y guarda un diagnóstico.
AFTER INSERT en la tabla HISTORIAL_MEDICO.
Captura el ID del paciente y los primeros 50 caracteres del padecimiento desde inserted.
Inserta un registro en BITACORA_HISTORIAL con el rol 'SISTEMA' y la acción 'NUEVO DIAGNOSTICO'.
Uso en el Sistema: Módulo de Doctores. Auditoría de actividad clínica y trazabilidad de expedientes.
*/
IF OBJECT_ID('TRG_Nuevo_Diagnostico', 'TR') IS NOT NULL DROP TRIGGER TRG_Nuevo_Diagnostico;
GO
CREATE TRIGGER TRG_Nuevo_Diagnostico ON HISTORIAL_MEDICO AFTER INSERT AS
BEGIN
    DECLARE @id INT, @dx VARCHAR(50); SELECT @id = id_paciente, @dx = LEFT(padecimiento, 50) FROM inserted;
    INSERT INTO BITACORA_HISTORIAL VALUES (GETDATE(), 'SISTEMA', 'NUEVO DIAGNOSTICO', 'Px: ' + CAST(@id AS VARCHAR) + ' - ' + @dx, @id);
END;
GO

-- 4. Auditoría: Nueva Receta
/*
Documenta la emisión de recetas médicas para control interno.
AFTER INSERT en la tabla RECETA.
Obtiene el folio de la receta y el folio de la cita asociada desde inserted.
Registra en BITACORA_HISTORIAL bajo el rol 'DOCTOR' con la acción 'RECETA GENERADA'.
Uso en el Sistema: Módulo de Doctores. Permite rastrear qué receta pertenece a qué cita 
y confirma que el médico generó el documento.
*/
IF OBJECT_ID('TRG_Bitacora_NuevaReceta', 'TR') IS NOT NULL DROP TRIGGER TRG_Bitacora_NuevaReceta;
GO
CREATE TRIGGER TRG_Bitacora_NuevaReceta ON RECETA AFTER INSERT AS
BEGIN
    DECLARE @id INT, @cita INT; SELECT @id = folio_receta, @cita = folio_cita FROM inserted;
    INSERT INTO BITACORA_HISTORIAL VALUES (GETDATE(), 'DOCTOR', 'RECETA GENERADA', 'Receta #' + CAST(@id AS VARCHAR) + ' Cita #' + CAST(@cita AS VARCHAR), @id);
END;
GO

-- 5. Auditoría: Nuevo Pago (Ingreso)
/*
Auditoría financiera específica para los cobros de Consultas Médicas.
AFTER INSERT en la tabla PAGO.
Captura el folio del pago y el monto total cobrado.
Inserta en BITACORA_HISTORIAL con el rol 'CAJA' y la etiqueta 'COBRO'.
Uso en el Sistema: Módulo de Caja. Deja evidencia inmutable del ingreso de dinero por servicios médicos.
*/
IF OBJECT_ID('TRG_Bitacora_NuevoPago', 'TR') IS NOT NULL DROP TRIGGER TRG_Bitacora_NuevoPago;
GO
CREATE TRIGGER TRG_Bitacora_NuevoPago ON PAGO AFTER INSERT AS
BEGIN
    DECLARE @id INT, @monto DECIMAL(10,2); SELECT @id = folio_pago, @monto = total FROM inserted;
    INSERT INTO BITACORA_HISTORIAL VALUES (GETDATE(), 'CAJA', 'COBRO', 'Ingreso $' + CAST(@monto AS VARCHAR), @id);
END;
GO

-- 6. Auditoría: Nueva Venta Farmacia

/*
Auditoría financiera específica para los ingresos de Farmacia.
Diferencia el flujo de efectivo de medicamentos vs. consultas médicas.
AFTER INSERT en la tabla VENTA.
Obtiene el folio de la venta y el subtotal desde inserted.
Registra en BITACORA_HISTORIAL bajo el rol 'FARMACIA' con la acción 'VENTA'.
Uso en el Sistema: Módulo de Farmacia. Control de ingresos por venta de productos.
*/
IF OBJECT_ID('TRG_Auditoria_VentaFarmacia', 'TR') IS NOT NULL DROP TRIGGER TRG_Auditoria_VentaFarmacia;
GO
CREATE TRIGGER TRG_Auditoria_VentaFarmacia ON VENTA AFTER INSERT AS
BEGIN
    DECLARE @id INT, @monto DECIMAL(10,2); SELECT @id = folio_venta, @monto = subtotal FROM inserted;
    INSERT INTO BITACORA_HISTORIAL VALUES (GETDATE(), 'FARMACIA', 'VENTA', 'Ticket #' + CAST(@id AS VARCHAR) + ' $' + CAST(@monto AS VARCHAR), @id);
END;
GO

-- 7. Auditoría: Alta Empleado Directo
/*
Control de seguridad para el departamento de Recursos Humanos.
Deja evidencia de cuándo se contrató a alguien nuevo y quién fue.
AFTER INSERT en la tabla EMPLEADO.
Captura el ID del nuevo empleado y concatena su nombre completo.
Inserta en BITACORA_HISTORIAL con el rol 'RRHH' y la acción 'NUEVO EMPLEADO'.
Uso en el Sistema: Gestión de Personal. Auditoría de contrataciones y altas en el sistema.
*/
IF OBJECT_ID('TRG_Bitacora_AltaEmpleado', 'TR') IS NOT NULL DROP TRIGGER TRG_Bitacora_AltaEmpleado;
GO
CREATE TRIGGER TRG_Bitacora_AltaEmpleado ON EMPLEADO AFTER INSERT AS
BEGIN
    DECLARE @id INT, @nom VARCHAR(100); SELECT @id = id_empleado, @nom = primer_nom_emp + ' ' + a_pat_emp FROM inserted;
    INSERT INTO BITACORA_HISTORIAL VALUES (GETDATE(), 'RRHH', 'NUEVO EMPLEADO', 'Alta: ' + @nom, @id);
END;
GO