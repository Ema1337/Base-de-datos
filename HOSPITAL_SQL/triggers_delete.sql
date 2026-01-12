USE HOSPITAL;
GO

-- 1. Auditoría: Reembolso (Borrado de Pago)
-- Se dispara cuando se elimina un registro de la tabla PAGO.
/*Su función es detectar cuando se elimina un registro de pago y asumir que se trata de un Reembolso o cancelación, 
  dejando evidencia permanente en la bitácora para evitar desfalcos o errores contables.
AFTER DELETE (Se ejecuta inmediatamente después de borrar una fila) en la tabla PAGO.
Captura de Datos (deleted): Utiliza la tabla virtual deleted (propia de SQL Server) para recuperar los datos que acaban de desaparecer: 
                            el folio_pago y el total.
Generación de Evidencia: Construye una cadena de texto detallando qué pago se borró y por qué monto.
Registro en Bitácora: Inserta automáticamente un nuevo registro en BITACORA_HISTORIAL con la etiqueta "REEMBOLSO" y el usuario "CAJA".
Uso en el Sistema: Actúa de forma invisible en el módulo de Caja. 
                   Si el cajero o administrador elimina un cobro por error o devolución, 
                   el sistema guarda el rastro sin que el usuario tenga que escribir nada.
*/
IF OBJECT_ID('TRG_Auditoria_Reembolso', 'TR') IS NOT NULL DROP TRIGGER TRG_Auditoria_Reembolso;
GO
CREATE TRIGGER TRG_Auditoria_Reembolso ON PAGO AFTER DELETE AS
BEGIN
    SET NOCOUNT ON;
    DECLARE @id INT, @monto DECIMAL(10,2); 
    SELECT @id = folio_pago, @monto = total FROM deleted;
    
    INSERT INTO BITACORA_HISTORIAL VALUES (GETDATE(), 'CAJA', 'REEMBOLSO', 'Se eliminó pago #' + CAST(@id AS VARCHAR) + ' (-$' + CAST(@monto AS VARCHAR) + ')', @id);
END;
GO


-- 2. Seguridad: Protección de Bitácora (INSTEAD OF DELETE)
-- Impide que alguien borre registros de la bitácora histórica.
/*Convierte a la tabla de bitácora en un registro inmutable. Esto es crucial para auditorías forenses, asegurando que nadie 
pueda borrar sus huellas o alterar el pasado.
INSTEAD OF DELETE, UPDATE (Se ejecuta en lugar de la acción solicitada) en la tabla BITACORA_HISTORIAL.
Intercepción: Cuando SQL Server recibe una orden de DELETE o UPDATE sobre esta tabla, el trigger intercepta la orden antes de que 
              toque los datos.
Bloqueo y Alerta: Lanza un error personalizado nivel 16 (RAISERROR) con el mensaje "ALERTA SEGURIDAD".
Cancelación (ROLLBACK): Cancela inmediatamente la transacción completa, dejando los datos intactos.
Uso en el Sistema: Es un "guardián pasivo". No interactúa con la interfaz web, sino que protege la base de datos contra ataques internos, 
hackers o errores humanos que intenten limpiar el historial de actividades.
*/
IF OBJECT_ID('TRG_Seguridad_Bitacora', 'TR') IS NOT NULL DROP TRIGGER TRG_Seguridad_Bitacora;
GO
CREATE TRIGGER TRG_Seguridad_Bitacora ON BITACORA_HISTORIAL INSTEAD OF DELETE, UPDATE AS
BEGIN
    RAISERROR ('ALERTA SEGURIDAD: No está permitido borrar ni alterar la bitácora de auditoría.', 16, 1);
    ROLLBACK TRANSACTION;
END;
GO