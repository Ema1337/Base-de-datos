USE HOSPITAL
GO 

INSERT INTO USUARIO (correo, contrasenia, tipo)
VALUES
-- PACIENTES (10)
('paciente1@correo.com', 'pass101', 'Paciente'),
('paciente2@correo.com', 'pass102', 'Paciente'),
('paciente3@correo.com', 'pass103', 'Paciente'),
('paciente4@correo.com', 'pass104', 'Paciente'),
('paciente5@correo.com', 'pass105', 'Paciente'),
('paciente6@correo.com', 'pass106', 'Paciente'),
('paciente7@correo.com', 'pass107', 'Paciente'),
('paciente8@correo.com', 'pass108', 'Paciente'),
('paciente9@correo.com', 'pass109', 'Paciente'),
('paciente10@correo.com', 'pass110', 'Paciente'),

-- DOCTORES (40)
('dr.sanchez@correo.com', 'pass201', 'Doctor'),
('dr.martin@correo.com', 'pass202', 'Doctor'),
('dr.avila@correo.com', 'pass203', 'Doctor'),
('dr.gomez@correo.com', 'pass204', 'Doctor'),
('dr.lopez@correo.com', 'pass205', 'Doctor'),
('dr.herrera@correo.com', 'pass206', 'Doctor'),
('dr.diaz@correo.com', 'pass207', 'Doctor'),
('dr.torres@correo.com', 'pass208', 'Doctor'),
('dr.mendoza@correo.com', 'pass209', 'Doctor'),
('dr.silva@correo.com', 'pass210', 'Doctor'),
('dr.ruiz@correo.com', 'pass211', 'Doctor'),
('dr.castillo@correo.com', 'pass212', 'Doctor'),
('dr.morales@correo.com', 'pass213', 'Doctor'),
('dr.ramirez@correo.com', 'pass214', 'Doctor'),
('dr.reyes@correo.com', 'pass215', 'Doctor'),
('dr.ortiz@correo.com', 'pass216', 'Doctor'),
('dr.navarro@correo.com', 'pass217', 'Doctor'),
('dr.cortes@correo.com', 'pass218', 'Doctor'),
('dr.soto@correo.com', 'pass219', 'Doctor'),
('dr.arias@correo.com', 'pass220', 'Doctor'),
('dr.mejia@correo.com', 'pass221', 'Doctor'),
('dr.vargas@correo.com', 'pass222', 'Doctor'),
('dr.perez@correo.com', 'pass223', 'Doctor'),
('dr.rosas@correo.com', 'pass224', 'Doctor'),
('dr.rivera@correo.com', 'pass225', 'Doctor'),
('dr.campos@correo.com', 'pass226', 'Doctor'),
('dr.carrillo@correo.com', 'pass227', 'Doctor'),
('dr.luna@correo.com', 'pass228', 'Doctor'),
('dr.sandoval@correo.com', 'pass229', 'Doctor'),
('dr.sierra@correo.com', 'pass230', 'Doctor'),
('dr.garcia@correo.com', 'pass231', 'Doctor'),
('dr.valdez@correo.com', 'pass232', 'Doctor'),
('dr.bravo@correo.com', 'pass233', 'Doctor'),
('dr.leon@correo.com', 'pass234', 'Doctor'),
('dr.maldonado@correo.com', 'pass235', 'Doctor'),
('dr.romero@correo.com', 'pass236', 'Doctor'),
('dr.caballero@correo.com', 'pass237', 'Doctor'),
('dr.espinoza@correo.com', 'pass238', 'Doctor'),
('dr.zamora@correo.com', 'pass239', 'Doctor'),
('dr.franco@correo.com', 'pass240', 'Doctor'),

-- FARMACÉUTICOS (10)
('farma.lopez@correo.com', 'pass301', 'Farmaceutico'),
('farma.diaz@correo.com', 'pass302', 'Farmaceutico'),
('farma.moreno@correo.com', 'pass303', 'Farmaceutico'),
('farma.martinez@correo.com', 'pass304', 'Farmaceutico'),
('farma.perez@correo.com', 'pass305', 'Farmaceutico'),
('farma.garcia@correo.com', 'pass306', 'Farmaceutico'),
('farma.santos@correo.com', 'pass307', 'Farmaceutico'),
('farma.cruz@correo.com', 'pass308', 'Farmaceutico'),
('farma.reyes@correo.com', 'pass309', 'Farmaceutico'),
('farma.sosa@correo.com', 'pass310', 'Farmaceutico'),

--Recepcionistas(10)
('recep.gomez@correo.com', 'pass100', 'Recepcionista'),
('recep.martinez@correo.com', 'recp101', 'Recepcionista'),
('recep.lopez@correo.com', 'recp102', 'Recepcionista'),
('recep.hernandez@correo.com', 'recp103', 'Recepcionista'),
('recep.ramirez@correo.com', 'recp104', 'Recepcionista'),
('recep.cruz@correo.com', 'recp105', 'Recepcionista'),
('recep.torres@correo.com', 'recp106', 'Recepcionista'),
('recep.garcia@correo.com', 'recp107', 'Recepcionista'),
('recep.mendoza@correo.com', 'recp108', 'Recepcionista'),
('recep.salazar@correo.com', 'recp109', 'Recepcionista');
GO


INSERT INTO ESPECIALIDAD (nombre, tiempo_min_cita)
VALUES
('Inhaloterapia', 20),
('Bariatría', 30),
('Neurología', 30),
('Ortopedia y Traumatología', 30),
('Neonatología', 25),
('Nutrición', 20),
('Infectología', 20),
('Endoscopia', 45),
('Fisiología Digestiva', 45),
('Dermatología', 20); 
GO

INSERT INTO CONSULTORIO (activo, tipo)
VALUES
(1, 'Neurología'),
(1, 'Nutrición'),
(1, 'Dermatología'),
(1, 'Ortopedia'),
(1, 'Infectología'),
(1, 'Bariatría'),
(0, 'Neonatología'),
(1, 'Endoscopia'),
(1, 'Fisiología Digestiva'),
(1, 'General'),
(1, 'Inhaloterapia');
GO

INSERT INTO SERVICIO (nombre, detalles, precio)
VALUES
('Consulta General', 'Revisión estándar', 450.00),
('Consulta Especialista', 'Revisión por especialista', 800.00),
('Electrocardiograma', 'Estudio de ritmo cardíaco', 1200.00),
('Radiografía (1 zona)', 'Placa de Rayos-X', 750.00),
('Radiografía (2 zonas)', 'Placa de Rayos-X 2 vistas', 1100.00),
('Examen de Sangre Básico', 'Química sanguínea 6 elementos', 500.00),
('Biometría Hemática', 'Conteo sanguíneo completo', 350.00),
('Terapia Respiratoria', 'Sesión de Inhaloterapia', 600.00),
('Endoscopia Superior', 'Estudio gástrico', 4500.00),
('Plan Nutricional', 'Elaboración de dieta', 900.00);
GO

INSERT INTO MEDICAMENTO (nombre, detalles, precio, stock, presentacion)
VALUES
('Paracetamol', '500 mg, caja 20 tabletas', 50.00, 100, 'Tabletas'),
('Amoxicilina', '250 mg, suspensión 100ml', 180.00, 50, 'Suspensión'),
('Loratadina', '10 mg, caja 10 tabletas', 120.00, 75, 'Tabletas'),
('Ibuprofeno', '400 mg, caja 20 tabletas', 70.00, 150, 'Tabletas'),
('Omeprazol', '20 mg, caja 14 cápsulas', 130.00, 80, 'Cápsulas'),
('Metformina', '850 mg, caja 30 tabletas', 90.00, 60, 'Tabletas'),
('Losartán', '50 mg, caja 30 tabletas', 200.00, 40, 'Tabletas'),
('Salbutamol', '100 mcg, Inhalador', 250.00, 30, 'Inhalador'),
('Ciprofloxacino', '500 mg, caja 12 tabletas', 220.00, 25, 'Tabletas'),
('Ketorolaco', '10 mg, caja 10 tabletas', 110.00, 55, 'Tabletas');
GO

INSERT INTO PACIENTE 
(nacimiento, primer_nombre, a_paterno, a_materno, tipo_sangre, donante, sexo, telefono, alergias, correo)
VALUES
('1985-05-15', 'Juan', 'Perez', 'Gomez', 'O+', 1, 'Masculino', '5512345678', 'Penicilina', 'paciente1@correo.com'),
('1992-11-30', 'Ana', 'Garcia', 'Lopez', 'A-', 0, 'Femenino', '5598765432', NULL, 'paciente2@correo.com'),
('2001-01-20', 'Luis', 'Martinez', 'Rios', 'B+', 0, 'Masculino', '5511223344', 'Sulfas', 'paciente3@correo.com'),
('1978-07-10', 'Maria', 'Rodriguez', 'Soto', 'AB+', 1, 'Femenino', '5555667788', NULL, 'paciente4@correo.com'),
('1999-03-05', 'Carlos', 'Hernandez', 'Diaz', 'O-', 0, 'Masculino', '5544332211', 'Lactosa', 'paciente5@correo.com'),
('1988-09-25', 'Laura', 'Torres', 'Ramirez', 'A+', 1, 'Femenino', '5577889900', 'Polvo', 'paciente6@correo.com'),
('1975-02-14', 'Jorge', 'Sanchez', 'Pineda', 'B-', 0, 'Masculino', '5522110099', NULL, 'paciente7@correo.com'),
('1996-12-01', 'Patricia', 'Navarro', 'Cruz', 'AB-', 1, 'Femenino', '5566778899', 'Penicilina', 'paciente8@correo.com'),
('2003-06-22', 'Miguel', 'Ortiz', 'Flores', 'O+', 0, 'Masculino', '5599001122', 'Mariscos', 'paciente9@correo.com'),
('1981-04-18', 'Sofia', 'Dominguez', 'Vega', 'A-', 1, 'Femenino', '5533445566', NULL, 'paciente10@correo.com');
GO

-- =============================
-- CORRECCIÓN: Los correos aquí ahora coinciden con la tabla USUARIO
-- =============================
INSERT INTO EMPLEADO (genero, curp, primer_nom_emp, segundo_nom_emp, a_pat_emp, a_mat_emp, telefono, correo)
VALUES
-- =============================
-- 40 DOCTORES
-- =============================
('Masculino', 'SAGC800101HDFRRL01', 'Carlos', 'Eduardo', 'Sanchez', 'Guzman', '5511110001', 'dr.sanchez@correo.com'),
('Femenino', 'MAFR820202MDFRRS02', 'Fernanda', NULL, 'Martin', 'Rios', '5511110002', 'dr.martin@correo.com'),
('Masculino', 'AVGM850303HNLGLZ03', 'Miguel', NULL, 'Avila', 'Gonzalez', '5511110003', 'dr.avila@correo.com'),
('Femenino', 'RALB860404MDFMRA04', 'Laura', 'Beatriz', 'Gomez', 'Mora', '5511110004', 'dr.gomez@correo.com'), -- Ajustado
('Masculino', 'HEJD870505HDFDZN05', 'Jorge', NULL, 'Lopez', 'Diaz', '5511110005', 'dr.lopez@correo.com'), -- Ajustado
('Masculino', 'LOAF880606HPLLZN06', 'Andres', 'Felipe', 'Herrera', NULL, '5511110006', 'dr.herrera@correo.com'), -- Ajustado
('Femenino', 'PELC890707MDFCMS07', 'Lucia', NULL, 'Diaz', 'Campos', '5511110007', 'dr.diaz@correo.com'), -- Ajustado
('Masculino', 'GADA900808HJCRSL08', 'David', 'Alejandro', 'Torres', 'Rosales', '5511110008', 'dr.torres@correo.com'), -- Ajustado
('Femenino', 'SOVM910909MDFVGA09', 'Mariana', NULL, 'Mendoza', 'Vega', '5511110009', 'dr.mendoza@correo.com'), -- Ajustado
('Masculino', 'RUIH920101HDFCRZ10', 'Hector', NULL, 'Silva', 'Cruz', '5511110010', 'dr.silva@correo.com'), -- Ajustado
('Femenino', 'JIPD930202MDFLFR11', 'Patricia', NULL, 'Ruiz', 'Flores', '5511110011', 'dr.ruiz@correo.com'), -- Ajustado
('Masculino', 'MERJ940303HDFMDZ12', 'Rafael', 'Antonio', 'Castillo', NULL, '5511110012', 'dr.castillo@correo.com'), -- Ajustado
('Masculino', 'TOSJ950404HDFLNA13', 'Sergio', NULL, 'Morales', 'Luna', '5511110013', 'dr.morales@correo.com'), -- Ajustado
('Femenino', 'VAII960505MDFRYE14', 'Adriana', 'Isabel', 'Ramirez', 'Reyes', '5511110014', 'dr.ramirez@correo.com'), -- Ajustado
('Masculino', 'CASR970606HDFSRZ15', 'Ricardo', NULL, 'Reyes', 'Suarez', '5511110015', 'dr.reyes@correo.com'), -- Ajustado
('Femenino', 'ORMM980707MDFNVA16', 'Marta', NULL, 'Ortiz', 'Nava', '5511110016', 'dr.ortiz@correo.com'), -- Ajustado
('Masculino', 'DOLU990808HDFDMG17', 'Luis', 'Enrique', 'Navarro', NULL, '5511110017', 'dr.navarro@correo.com'), -- Ajustado
('Masculino', 'COFR000909HDFVLD18', 'Francisco', NULL, 'Cortes', 'Valdez', '5511110018', 'dr.cortes@correo.com'),
('Femenino', 'CABD010101MDFAVL19', 'Daniela', 'Berenice', 'Soto', 'Avila', '5511110019', 'dr.soto@correo.com'), -- Ajustado
('Masculino', 'GOTP020202HDFTRJ20', 'Pablo', NULL, 'Arias', 'Trejo', '5511110020', 'dr.arias@correo.com'), -- Ajustado
('Masculino', 'NAVV030303HDFMNZ21', 'Victor', NULL, 'Mejia', 'Mendez', '5511110021', 'dr.mejia@correo.com'), -- Ajustado
('Femenino', 'GUCV040404MDFSSL22', 'Carmen', NULL, 'Vargas', 'Solano', '5511110022', 'dr.vargas@correo.com'), -- Ajustado
('Masculino', 'JUER050505HDFRFL23', 'Emilio', 'Rafael', 'Perez', NULL, '5511110023', 'dr.perez@correo.com'), -- Ajustado
('Masculino', 'SABJ060606HDFBNZ24', 'Julio', NULL, 'Rosas', 'Benitez', '5511110024', 'dr.rosas@correo.com'), -- Ajustado
('Femenino', 'HILM070707MDFPRZ25', 'Liliana', NULL, 'Rivera', 'Perez', '5511110025', 'dr.rivera@correo.com'), -- Ajustado
('Masculino', 'CAAM080808HDFHRN26', 'Alberto', 'Manuel', 'Campos', 'Hernandez', '5511110026', 'dr.campos@correo.com'), -- Ajustado
('Femenino', 'MESB090909MDFRBL27', 'Sonia', NULL, 'Carrillo', 'Robles', '5511110027', 'dr.carrillo@correo.com'), -- Ajustado
('Masculino', 'ROEE100101HDFJMN28', 'Eduardo', NULL, 'Luna', NULL, '5511110028', 'dr.luna@correo.com'), -- Ajustado
('Femenino', 'CARM110202MDFJMN29', 'Rosa', 'Maria', 'Sandoval', 'Jimenez', '5511110029', 'dr.sandoval@correo.com'), -- Ajustado
('Masculino', 'PAOA120303HDFZRT30', 'Alfonso', NULL, 'Sierra', 'Zarate', '5511110030', 'dr.sierra@correo.com'), -- Ajustado
('Femenino', 'RANM130404MDFORR31', 'Norma', NULL, 'Garcia', 'Ortega', '5511110031', 'dr.garcia@correo.com'), -- Ajustado
('Masculino', 'CAIM140505HDFCMP32', 'Manuel', 'Ignacio', 'Valdez', NULL, '5511110032', 'dr.valdez@correo.com'), -- Ajustado
('Femenino', 'AGUB150606MDFPNC33', 'Beatriz', NULL, 'Bravo', 'Ponce', '5511110033', 'dr.bravo@correo.com'), -- Ajustado
('Masculino', 'MOEO160707HDFESR34', 'Oscar', NULL, 'Leon', 'Estrada', '5511110034', 'dr.leon@correo.com'), -- Ajustado
('Femenino', 'FUII170808MDFOCH35', 'Isabel', NULL, 'Maldonado', 'Ochoa', '5511110035', 'dr.maldonado@correo.com'), -- Ajustado
('Masculino', 'VAVR180909HDFVLD36', 'Rodrigo', 'Josue', 'Romero', NULL, '5511110036', 'dr.romero@correo.com'), -- Ajustado
('Femenino', 'LOZG190101MDFVRG37', 'Gabriela', NULL, 'Caballero', 'Vargas', '5511110037', 'dr.caballero@correo.com'), -- Ajustado
('Masculino', 'ESHH200202HDFMRQ38', 'Hugo', NULL, 'Espinoza', 'Marquez', '5511110038', 'dr.espinoza@correo.com'), -- Ajustado
('Femenino', 'MIVV210303MDFTLD39', 'Veronica', 'Luz', 'Zamora', 'Toledo', '5511110039', 'dr.zamora@correo.com'), -- Ajustado
('Masculino', 'PEII220404HDFGRC40', 'Ignacio', NULL, 'Franco', 'Garcia', '5511110040', 'dr.franco@correo.com'), -- Ajustado

-- =============================
-- 10 FARMACÉUTICOS
-- =============================
('Femenino', 'LOLM900101MDFLZN01', 'Maria', 'Luisa', 'Lopez', NULL, '5522221001', 'farma.lopez@correo.com'),
('Masculino', 'DIAF910202HDFALV02', 'Fernando', NULL, 'Diaz', 'Alvarez', '5522221002', 'farma.diaz@correo.com'),
('Femenino', 'VARE920303MDFRYS03', 'Elena', 'Patricia', 'Moreno', 'Reyes', '5522221003', 'farma.moreno@correo.com'), -- Ajustado
('Masculino', 'SIRR930404HDFSLV04', 'Ramon', NULL, 'Martinez', NULL, '5522221004', 'farma.martinez@correo.com'), -- Ajustado
('Femenino', 'ORMC940505MDFMRA05', 'Claudia', NULL, 'Perez', 'Mora', '5522221005', 'farma.perez@correo.com'), -- Ajustado
('Masculino', 'CAPA950606HDRRLS06', 'Pedro', 'Antonio', 'Garcia', 'Rios', '5522221006', 'farma.garcia@correo.com'), -- Ajustado
('Masculino', 'GUAA960707HDFRRZ07', 'Alonso', NULL, 'Santos', 'Ruiz', '5522221007', 'farma.santos@correo.com'), -- Ajustado
('Femenino', 'JISN970808MDFRMZ08', 'Sandra', NULL, 'Cruz', NULL, '5522221008', 'farma.cruz@correo.com'), -- Ajustado
('Masculino', 'MEMH980909HDFLFR09', 'Hector', 'Luis', 'Reyes', 'Flores', '5522221009', 'farma.reyes@correo.com'), -- Ajustado
('Femenino', 'CAGB990101MDFGMZ10', 'Beatriz', NULL, 'Sosa', 'Gomez', '5522221010', 'farma.sosa@correo.com'), -- Ajustado

-- =============================
-- 10 RECEPCIONISTAS
-- =============================
('Femenino', 'GOPP900101MDFRRZ01', 'Paola', NULL, 'Gomez', 'Ruiz', '5533331001', 'recep.gomez@correo.com'),
('Masculino', 'LOAJ910202HDFCRZ02', 'Javier', 'Alejandro', 'Martinez', 'Cruz', '5533331002', 'recep.martinez@correo.com'), -- Ajustado
('Femenino', 'MAVS920303MDFTRZ03', 'Valeria', 'Sofia', 'Lopez', NULL, '5533331003', 'recep.lopez@correo.com'), -- Ajustado
('Femenino', 'HECC930404MDFSTO04', 'Clara', NULL, 'Hernandez', 'Soto', '5533331004', 'recep.hernandez@correo.com'),
('Masculino', 'TERR940505HDFRNZ05', 'Raul', 'Eduardo', 'Ramirez', NULL, '5533331005', 'recep.ramirez@correo.com'), -- Ajustado
('Femenino', 'CADI950606MDFPRZ06', 'Diana', NULL, 'Cruz', 'Perez', '5533331006', 'recep.cruz@correo.com'), -- Ajustado
('Masculino', 'GOMR960707HDFAVL07', 'Mario', 'Ricardo', 'Torres', 'Avila', '5533331007', 'recep.torres@correo.com'), -- Ajustado
('Femenino', 'ROAA970808MDFRRS08', 'Andrea', NULL, 'Garcia', 'Rios', '5533331008', 'recep.garcia@correo.com'), -- Ajustado
('Masculino', 'CAEE980909HDFCTR09', 'Esteban', NULL, 'Mendoza', NULL, '5533331009', 'recep.mendoza@correo.com'), -- Ajustado
('Femenino', 'GAFN990101MDFMNZ10', 'Natalia', 'Fernanda', 'Salazar', 'Mendez', '5533331010', 'recep.salazar@correo.com'); -- Ajustado
GO

-- ===============================================
-- HORARIOS DEL HOSPITAL GENERAL
-- Cumple con 48 horas semanales por empleado
-- Doctores (1–40): Lunes a Sábado, descanso Domingo
-- Farmacéuticos (41–50): Lunes a Domingo, descanso Miércoles
-- Recepcionistas (51–60): Domingo a Viernes, descanso Sábado
-- ===============================================
INSERT INTO HORARIO (dia, hora_ini, hora_fin, horas, id_empleado)
VALUES
-- DOCTORES (IDs 1–20): Turno MATUTINO (07:00–15:00)
('Lunes','07:00:00','15:00:00',8,1),('Martes','07:00:00','15:00:00',8,1),('Miércoles','07:00:00','15:00:00',8,1),('Jueves','07:00:00','15:00:00',8,1),('Viernes','07:00:00','15:00:00',8,1),('Sábado','07:00:00','15:00:00',8,1),
('Lunes','07:00:00','15:00:00',8,2),('Martes','07:00:00','15:00:00',8,2),('Miércoles','07:00:00','15:00:00',8,2),('Jueves','07:00:00','15:00:00',8,2),('Viernes','07:00:00','15:00:00',8,2),('Sábado','07:00:00','15:00:00',8,2),
('Lunes','07:00:00','15:00:00',8,3),('Martes','07:00:00','15:00:00',8,3),('Miércoles','07:00:00','15:00:00',8,3),('Jueves','07:00:00','15:00:00',8,3),('Viernes','07:00:00','15:00:00',8,3),('Sábado','07:00:00','15:00:00',8,3),
('Lunes','07:00:00','15:00:00',8,4),('Martes','07:00:00','15:00:00',8,4),('Miércoles','07:00:00','15:00:00',8,4),('Jueves','07:00:00','15:00:00',8,4),('Viernes','07:00:00','15:00:00',8,4),('Sábado','07:00:00','15:00:00',8,4),
('Lunes','07:00:00','15:00:00',8,5),('Martes','07:00:00','15:00:00',8,5),('Miércoles','07:00:00','15:00:00',8,5),('Jueves','07:00:00','15:00:00',8,5),('Viernes','07:00:00','15:00:00',8,5),('Sábado','07:00:00','15:00:00',8,5),
('Lunes','07:00:00','15:00:00',8,6),('Martes','07:00:00','15:00:00',8,6),('Miércoles','07:00:00','15:00:00',8,6),('Jueves','07:00:00','15:00:00',8,6),('Viernes','07:00:00','15:00:00',8,6),('Sábado','07:00:00','15:00:00',8,6),
('Lunes','07:00:00','15:00:00',8,7),('Martes','07:00:00','15:00:00',8,7),('Miércoles','07:00:00','15:00:00',8,7),('Jueves','07:00:00','15:00:00',8,7),('Viernes','07:00:00','15:00:00',8,7),('Sábado','07:00:00','15:00:00',8,7),
('Lunes','07:00:00','15:00:00',8,8),('Martes','07:00:00','15:00:00',8,8),('Miércoles','07:00:00','15:00:00',8,8),('Jueves','07:00:00','15:00:00',8,8),('Viernes','07:00:00','15:00:00',8,8),('Sábado','07:00:00','15:00:00',8,8),
('Lunes','07:00:00','15:00:00',8,9),('Martes','07:00:00','15:00:00',8,9),('Miércoles','07:00:00','15:00:00',8,9),('Jueves','07:00:00','15:00:00',8,9),('Viernes','07:00:00','15:00:00',8,9),('Sábado','07:00:00','15:00:00',8,9),
('Lunes','07:00:00','15:00:00',8,10),('Martes','07:00:00','15:00:00',8,10),('Miércoles','07:00:00','15:00:00',8,10),('Jueves','07:00:00','15:00:00',8,10),('Viernes','07:00:00','15:00:00',8,10),('Sábado','07:00:00','15:00:00',8,10),
('Lunes','07:00:00','15:00:00',8,11),('Martes','07:00:00','15:00:00',8,11),('Miércoles','07:00:00','15:00:00',8,11),('Jueves','07:00:00','15:00:00',8,11),('Viernes','07:00:00','15:00:00',8,11),('Sábado','07:00:00','15:00:00',8,11),
('Lunes','07:00:00','15:00:00',8,12),('Martes','07:00:00','15:00:00',8,12),('Miércoles','07:00:00','15:00:00',8,12),('Jueves','07:00:00','15:00:00',8,12),('Viernes','07:00:00','15:00:00',8,12),('Sábado','07:00:00','15:00:00',8,12),
('Lunes','07:00:00','15:00:00',8,13),('Martes','07:00:00','15:00:00',8,13),('Miércoles','07:00:00','15:00:00',8,13),('Jueves','07:00:00','15:00:00',8,13),('Viernes','07:00:00','15:00:00',8,13),('Sábado','07:00:00','15:00:00',8,13),
('Lunes','07:00:00','15:00:00',8,14),('Martes','07:00:00','15:00:00',8,14),('Miércoles','07:00:00','15:00:00',8,14),('Jueves','07:00:00','15:00:00',8,14),('Viernes','07:00:00','15:00:00',8,14),('Sábado','07:00:00','15:00:00',8,14),
('Lunes','07:00:00','15:00:00',8,15),('Martes','07:00:00','15:00:00',8,15),('Miércoles','07:00:00','15:00:00',8,15),('Jueves','07:00:00','15:00:00',8,15),('Viernes','07:00:00','15:00:00',8,15),('Sábado','07:00:00','15:00:00',8,15),
('Lunes','07:00:00','15:00:00',8,16),('Martes','07:00:00','15:00:00',8,16),('Miércoles','07:00:00','15:00:00',8,16),('Jueves','07:00:00','15:00:00',8,16),('Viernes','07:00:00','15:00:00',8,16),('Sábado','07:00:00','15:00:00',8,16),
('Lunes','07:00:00','15:00:00',8,17),('Martes','07:00:00','15:00:00',8,17),('Miércoles','07:00:00','15:00:00',8,17),('Jueves','07:00:00','15:00:00',8,17),('Viernes','07:00:00','15:00:00',8,17),('Sábado','07:00:00','15:00:00',8,17),
('Lunes','07:00:00','15:00:00',8,18),('Martes','07:00:00','15:00:00',8,18),('Miércoles','07:00:00','15:00:00',8,18),('Jueves','07:00:00','15:00:00',8,18),('Viernes','07:00:00','15:00:00',8,18),('Sábado','07:00:00','15:00:00',8,18),
('Lunes','07:00:00','15:00:00',8,19),('Martes','07:00:00','15:00:00',8,19),('Miércoles','07:00:00','15:00:00',8,19),('Jueves','07:00:00','15:00:00',8,19),('Viernes','07:00:00','15:00:00',8,19),('Sábado','07:00:00','15:00:00',8,19),
('Lunes','07:00:00','15:00:00',8,20),('Martes','07:00:00','15:00:00',8,20),('Miércoles','07:00:00','15:00:00',8,20),('Jueves','07:00:00','15:00:00',8,20),('Viernes','07:00:00','15:00:00',8,20),('Sábado','07:00:00','15:00:00',8,20),
-- DOCTORES (IDs 21–40): Turno VESPERTINO (13:00–21:00)
('Lunes','13:00:00','21:00:00',8,21),('Martes','13:00:00','21:00:00',8,21),('Miércoles','13:00:00','21:00:00',8,21),('Jueves','13:00:00','21:00:00',8,21),('Viernes','13:00:00','21:00:00',8,21),('Sábado','13:00:00','21:00:00',8,21),
('Lunes','13:00:00','21:00:00',8,22),('Martes','13:00:00','21:00:00',8,22),('Miércoles','13:00:00','21:00:00',8,22),('Jueves','13:00:00','21:00:00',8,22),('Viernes','13:00:00','21:00:00',8,22),('Sábado','13:00:00','21:00:00',8,22),
('Lunes','13:00:00','21:00:00',8,23),('Martes','13:00:00','21:00:00',8,23),('Miércoles','13:00:00','21:00:00',8,23),('Jueves','13:00:00','21:00:00',8,23),('Viernes','13:00:00','21:00:00',8,23),('Sábado','13:00:00','21:00:00',8,23),
('Lunes','13:00:00','21:00:00',8,24),('Martes','13:00:00','21:00:00',8,24),('Miércoles','13:00:00','21:00:00',8,24),('Jueves','13:00:00','21:00:00',8,24),('Viernes','13:00:00','21:00:00',8,24),('Sábado','13:00:00','21:00:00',8,24),
('Lunes','13:00:00','21:00:00',8,25),('Martes','13:00:00','21:00:00',8,25),('Miércoles','13:00:00','21:00:00',8,25),('Jueves','13:00:00','21:00:00',8,25),('Viernes','13:00:00','21:00:00',8,25),('Sábado','13:00:00','21:00:00',8,25),
('Lunes','13:00:00','21:00:00',8,26),('Martes','13:00:00','21:00:00',8,26),('Miércoles','13:00:00','21:00:00',8,26),('Jueves','13:00:00','21:00:00',8,26),('Viernes','13:00:00','21:00:00',8,26),('Sábado','13:00:00','21:00:00',8,26),
('Lunes','13:00:00','21:00:00',8,27),('Martes','13:00:00','21:00:00',8,27),('Miércoles','13:00:00','21:00:00',8,27),('Jueves','13:00:00','21:00:00',8,27),('Viernes','13:00:00','21:00:00',8,27),('Sábado','13:00:00','21:00:00',8,27),
('Lunes','13:00:00','21:00:00',8,28),('Martes','13:00:00','21:00:00',8,28),('Miércoles','13:00:00','21:00:00',8,28),('Jueves','13:00:00','21:00:00',8,28),('Viernes','13:00:00','21:00:00',8,28),('Sábado','13:00:00','21:00:00',8,28),
('Lunes','13:00:00','21:00:00',8,29),('Martes','13:00:00','21:00:00',8,29),('Miércoles','13:00:00','21:00:00',8,29),('Jueves','13:00:00','21:00:00',8,29),('Viernes','13:00:00','21:00:00',8,29),('Sábado','13:00:00','21:00:00',8,29),
('Lunes','13:00:00','21:00:00',8,30),('Martes','13:00:00','21:00:00',8,30),('Miércoles','13:00:00','21:00:00',8,30),('Jueves','13:00:00','21:00:00',8,30),('Viernes','13:00:00','21:00:00',8,30),('Sábado','13:00:00','21:00:00',8,30),
('Lunes','13:00:00','21:00:00',8,31),('Martes','13:00:00','21:00:00',8,31),('Miércoles','13:00:00','21:00:00',8,31),('Jueves','13:00:00','21:00:00',8,31),('Viernes','13:00:00','21:00:00',8,31),('Sábado','13:00:00','21:00:00',8,31),
('Lunes','13:00:00','21:00:00',8,32),('Martes','13:00:00','21:00:00',8,32),('Miércoles','13:00:00','21:00:00',8,32),('Jueves','13:00:00','21:00:00',8,32),('Viernes','13:00:00','21:00:00',8,32),('Sábado','13:00:00','21:00:00',8,32),
('Lunes','13:00:00','21:00:00',8,33),('Martes','13:00:00','21:00:00',8,33),('Miércoles','13:00:00','21:00:00',8,33),('Jueves','13:00:00','21:00:00',8,33),('Viernes','13:00:00','21:00:00',8,33),('Sábado','13:00:00','21:00:00',8,33),
('Lunes','13:00:00','21:00:00',8,34),('Martes','13:00:00','21:00:00',8,34),('Miércoles','13:00:00','21:00:00',8,34),('Jueves','13:00:00','21:00:00',8,34),('Viernes','13:00:00','21:00:00',8,34),('Sábado','13:00:00','21:00:00',8,34),
('Lunes','13:00:00','21:00:00',8,35),('Martes','13:00:00','21:00:00',8,35),('Miércoles','13:00:00','21:00:00',8,35),('Jueves','13:00:00','21:00:00',8,35),('Viernes','13:00:00','21:00:00',8,35),('Sábado','13:00:00','21:00:00',8,35),
('Lunes','13:00:00','21:00:00',8,36),('Martes','13:00:00','21:00:00',8,36),('Miércoles','13:00:00','21:00:00',8,36),('Jueves','13:00:00','21:00:00',8,36),('Viernes','13:00:00','21:00:00',8,36),('Sábado','13:00:00','21:00:00',8,36),
('Lunes','13:00:00','21:00:00',8,37),('Martes','13:00:00','21:00:00',8,37),('Miércoles','13:00:00','21:00:00',8,37),('Jueves','13:00:00','21:00:00',8,37),('Viernes','13:00:00','21:00:00',8,37),('Sábado','13:00:00','21:00:00',8,37),
('Lunes','13:00:00','21:00:00',8,38),('Martes','13:00:00','21:00:00',8,38),('Miércoles','13:00:00','21:00:00',8,38),('Jueves','13:00:00','21:00:00',8,38),('Viernes','13:00:00','21:00:00',8,38),('Sábado','13:00:00','21:00:00',8,38),
('Lunes','13:00:00','21:00:00',8,39),('Martes','13:00:00','21:00:00',8,39),('Miércoles','13:00:00','21:00:00',8,39),('Jueves','13:00:00','21:00:00',8,39),('Viernes','13:00:00','21:00:00',8,39),('Sábado','13:00:00','21:00:00',8,39),
('Lunes','13:00:00','21:00:00',8,40),('Martes','13:00:00','21:00:00',8,40),('Miércoles','13:00:00','21:00:00',8,40),('Jueves','13:00:00','21:00:00',8,40),('Viernes','13:00:00','21:00:00',8,40),('Sábado','13:00:00','21:00:00',8,40),
-- FARMACÉUTICOS (IDs 41–45): Turno MATUTINO (08:00–16:00)
('Lunes','08:00:00','16:00:00',8,41),('Martes','08:00:00','16:00:00',8,41),('Jueves','08:00:00','16:00:00',8,41),('Viernes','08:00:00','16:00:00',8,41),('Sábado','08:00:00','16:00:00',8,41),('Domingo','08:00:00','16:00:00',8,41),
('Lunes','08:00:00','16:00:00',8,42),('Martes','08:00:00','16:00:00',8,42),('Jueves','08:00:00','16:00:00',8,42),('Viernes','08:00:00','16:00:00',8,42),('Sábado','08:00:00','16:00:00',8,42),('Domingo','08:00:00','16:00:00',8,42),
('Lunes','08:00:00','16:00:00',8,43),('Martes','08:00:00','16:00:00',8,43),('Jueves','08:00:00','16:00:00',8,43),('Viernes','08:00:00','16:00:00',8,43),('Sábado','08:00:00','16:00:00',8,43),('Domingo','08:00:00','16:00:00',8,43),
('Lunes','08:00:00','16:00:00',8,44),('Martes','08:00:00','16:00:00',8,44),('Jueves','08:00:00','16:00:00',8,44),('Viernes','08:00:00','16:00:00',8,44),('Sábado','08:00:00','16:00:00',8,44),('Domingo','08:00:00','16:00:00',8,44),
('Lunes','08:00:00','16:00:00',8,45),('Martes','08:00:00','16:00:00',8,45),('Jueves','08:00:00','16:00:00',8,45),('Viernes','08:00:00','16:00:00',8,45),('Sábado','08:00:00','16:00:00',8,45),('Domingo','08:00:00','16:00:00',8,45),
-- FARMACÉUTICOS (IDs 46–50): Turno VESPERTINO (13:00–21:00)
('Lunes','13:00:00','21:00:00',8,46),('Martes','13:00:00','21:00:00',8,46),('Jueves','13:00:00','21:00:00',8,46),('Viernes','13:00:00','21:00:00',8,46),('Sábado','13:00:00','21:00:00',8,46),('Domingo','13:00:00','21:00:00',8,46),
('Lunes','13:00:00','21:00:00',8,47),('Martes','13:00:00','21:00:00',8,47),('Jueves','13:00:00','21:00:00',8,47),('Viernes','13:00:00','21:00:00',8,47),('Sábado','13:00:00','21:00:00',8,47),('Domingo','13:00:00','21:00:00',8,47),
('Lunes','13:00:00','21:00:00',8,48),('Martes','13:00:00','21:00:00',8,48),('Jueves','13:00:00','21:00:00',8,48),('Viernes','13:00:00','21:00:00',8,48),('Sábado','13:00:00','21:00:00',8,48),('Domingo','13:00:00','21:00:00',8,48),
('Lunes','13:00:00','21:00:00',8,49),('Martes','13:00:00','21:00:00',8,49),('Jueves','13:00:00','21:00:00',8,49),('Viernes','13:00:00','21:00:00',8,49),('Sábado','13:00:00','21:00:00',8,49),('Domingo','13:00:00','21:00:00',8,49),
('Lunes','13:00:00','21:00:00',8,50),('Martes','13:00:00','21:00:00',8,50),('Jueves','13:00:00','21:00:00',8,50),('Viernes','13:00:00','21:00:00',8,50),('Sábado','13:00:00','21:00:00',8,50),('Domingo','13:00:00','21:00:00',8,50),
-- RECEPCIONISTAS (IDs 51–55): Turno MATUTINO (07:00–15:00)
('Domingo','07:00:00','15:00:00',8,51),('Lunes','07:00:00','15:00:00',8,51),('Martes','07:00:00','15:00:00',8,51),('Miércoles','07:00:00','15:00:00',8,51),('Jueves','07:00:00','15:00:00',8,51),('Viernes','07:00:00','15:00:00',8,51),
('Domingo','07:00:00','15:00:00',8,52),('Lunes','07:00:00','15:00:00',8,52),('Martes','07:00:00','15:00:00',8,52),('Miércoles','07:00:00','15:00:00',8,52),('Jueves','07:00:00','15:00:00',8,52),('Viernes','07:00:00','15:00:00',8,52),
('Domingo','07:00:00','15:00:00',8,53),('Lunes','07:00:00','15:00:00',8,53),('Martes','07:00:00','15:00:00',8,53),('Miércoles','07:00:00','15:00:00',8,53),('Jueves','07:00:00','15:00:00',8,53),('Viernes','07:00:00','15:00:00',8,53),
('Domingo','07:00:00','15:00:00',8,54),('Lunes','07:00:00','15:00:00',8,54),('Martes','07:00:00','15:00:00',8,54),('Miércoles','07:00:00','15:00:00',8,54),('Jueves','07:00:00','15:00:00',8,54),('Viernes','07:00:00','15:00:00',8,54),
('Domingo','07:00:00','15:00:00',8,55),('Lunes','07:00:00','15:00:00',8,55),('Martes','07:00:00','15:00:00',8,55),('Miércoles','07:00:00','15:00:00',8,55),('Jueves','07:00:00','15:00:00',8,55),('Viernes','07:00:00','15:00:00',8,55),
-- RECEPCIONISTAS (IDs 56–60): Turno VESPERTINO (13:00–21:00)
('Domingo','13:00:00','21:00:00',8,56),('Lunes','13:00:00','21:00:00',8,56),('Martes','13:00:00','21:00:00',8,56),('Miércoles','13:00:00','21:00:00',8,56),('Jueves','13:00:00','21:00:00',8,56),('Viernes','13:00:00','21:00:00',8,56),
('Domingo','13:00:00','21:00:00',8,57),('Lunes','13:00:00','21:00:00',8,57),('Martes','13:00:00','21:00:00',8,57),('Miércoles','13:00:00','21:00:00',8,57),('Jueves','13:00:00','21:00:00',8,57),('Viernes','13:00:00','21:00:00',8,57),
-- ¡¡¡ ERROR CORREGIDO AQUÍ !!!
('Domingo','13:00:00','21:00:00',8,58),('Lunes','13:00:00','21:00:00',8,58),('Martes','13:00:00','21:00:00',8,58),('Miércoles','13:00:00','21:00:00',8,58),('Jueves','13:00:00','21:00:00',8,58),('Viernes','13:00:00','21:00:00',8,58),
('Domingo','13:00:00','21:00:00',8,59),('Lunes','13:00:00','21:00:00',8,59),('Martes','13:00:00','21:00:00',8,59),('Miércoles','13:00:00','21:00:00',8,59),('Jueves','13:00:00','21:00:00',8,59),('Viernes','13:00:00','21:00:00',8,59),
('Domingo','13:00:00','21:00:00',8,60),('Lunes','13:00:00','21:00:00',8,60),('Martes','13:00:00','21:00:00',8,60),('Miércoles','13:00:00','21:00:00',8,60),('Jueves','13:00:00','21:00:00',8,60),('Viernes','13:00:00','21:00:00',8,60);
GO

INSERT INTO DOCTOR (cedula_profesional, id_empleado, no_consultorio, id_especialidad)
VALUES
('DCP1000001', 1, 1, 1),
('DCP1000002', 2, 2, 2),
('DCP1000003', 3, 3, 3),
('DCP1000004', 4, 4, 4),
('DCP1000005', 5, 5, 5),
('DCP1000006', 6, 6, 6),
('DCP1000007', 7, 7, 7),
('DCP1000008', 8, 8, 8),
('DCP1000009', 9, 9, 9),
('DCP1000010', 10, 10, 10),

('DCP1000011', 11, 11, 1),
('DCP1000012', 12, 1, 2),
('DCP1000013', 13, 2, 3),
('DCP1000014', 14, 3, 4),
('DCP1000015', 15, 4, 5),
('DCP1000016', 16, 5, 6),
('DCP1000017', 17, 6, 7),
('DCP1000018', 18, 7, 8),
('DCP1000019', 19, 8, 9),
('DCP1000020', 20, 9, 10),

('DCP1000021', 21, 10, 1),
('DCP1000022', 22, 11, 2),
('DCP1000023', 23, 1, 3),
('DCP1000024', 24, 2, 4),
('DCP1000025', 25, 3, 5),
('DCP1000026', 26, 4, 6),
('DCP1000027', 27, 5, 7),
('DCP1000028', 28, 6, 8),
('DCP1000029', 29, 7, 9),
('DCP1000030', 30, 8, 10),

('DCP1000031', 31, 9, 1),
('DCP1000032', 32, 10, 2),
('DCP1000033', 33, 11, 3),
('DCP1000034', 34, 1, 4),
('DCP1000035', 35, 2, 5),
('DCP1000036', 36, 3, 6),
('DCP1000037', 37, 4, 7),
('DCP1000038', 38, 5, 8),
('DCP1000039', 39, 6, 9),
('DCP1000040', 40, 7, 10);
GO

INSERT INTO FARMACEUTICO (id_empleado)
VALUES
(41), -- Maria Luisa Lopez
(42), -- Fernando Diaz Alvarez
(43), -- Elena Patricia Vargas Reyes
(44), -- Ramon Silva
(45), -- Claudia Ortega Mora
(46), -- Pedro Antonio Camacho Rios
(47), -- Alonso Gutierrez Ruiz
(48), -- Sandra Jimenez
(49), -- Hector Luis Mendoza Flores
(50); -- Beatriz Carrillo Gomez
GO

INSERT INTO HISTORIAL_MEDICO 
(padecimiento, presion_sistolica, presion_diastolica, peso, estatura, fecha, oxigenacion, detalles, id_paciente)
VALUES
-- Paciente 1 - Juan Perez
('Hipertensión leve', 138, 88, 84.0, 1.77, '2025-01-15', 98, 'Paciente con estrés laboral, controlado con dieta baja en sodio.', 1),
('Seguimiento hipertensión', 130, 85, 83.0, 1.77, '2025-07-15', 98, 'Buena evolución, mantiene tratamiento con Losartán.', 1),

-- Paciente 2 - Ana Garcia
('Revisión general', 118, 78, 66.5, 1.64, '2025-02-10', 99, 'Sin síntomas, análisis dentro de parámetros normales.', 2),
('Control nutricional', 115, 75, 64.0, 1.64, '2025-07-10', 99, 'Bajó 2.5 kg tras cambio de alimentación.', 2),

-- Paciente 3 - Luis Martinez
('Diabetes tipo 2', 132, 86, 94.0, 1.74, '2025-03-05', 97, 'Inicia control con Metformina 850 mg/día.', 3),
('Seguimiento diabetes', 125, 80, 92.0, 1.74, '2025-07-05', 97, 'Glucosa estabilizada, sin hipoglucemias reportadas.', 3),

-- Paciente 4 - Maria Rodriguez
('Alergia estacional', 110, 70, 74.0, 1.68, '2025-04-12', 99, 'Rinitis leve, tratamiento con antihistamínicos.', 4),
('Control alergia', 112, 72, 74.5, 1.68, '2025-07-12', 99, 'Síntomas disminuidos, sin nuevas reacciones.', 4),

-- Paciente 5 - Carlos Hernandez
('Gastritis crónica', 126, 82, 81.0, 1.73, '2025-02-18', 98, 'Control con Omeprazol, dieta ligera.', 5),
('Revisión gastritis', 120, 80, 80.0, 1.73, '2025-08-18', 98, 'Sin molestias recientes, mantiene dieta adecuada.', 5),

-- Paciente 6 - Laura Torres
('Rinitis alérgica', 112, 74, 70.0, 1.65, '2025-03-20', 99, 'Alergia leve al polvo, tratamiento con Loratadina.', 6),
('Chequeo anual', 118, 76, 69.5, 1.65, '2025-09-20', 99, 'Sin cambios relevantes, condición estable.', 6),

-- Paciente 7 - Jorge Sanchez
('Dolor lumbar leve', 125, 80, 86.0, 1.80, '2025-04-25', 98, 'Se recomienda fisioterapia semanal.', 7),
('Revisión ortopédica', 122, 79, 85.5, 1.80, '2025-10-25', 98, 'Mejoría evidente, sin limitación de movimiento.', 7),

-- Paciente 8 - Patricia Navarro
('Alergia a penicilina', 115, 73, 68.0, 1.68, '2025-05-15', 99, 'Reacción cutánea leve por exposición accidental.', 8),
('Revisión post-alergia', 117, 75, 67.8, 1.68, '2025-09-15', 99, 'Sin nuevos episodios alérgicos.', 8),

-- Paciente 9 - Miguel Ortiz
('Alergia alimentaria', 118, 76, 74.0, 1.75, '2025-06-02', 98, 'Reacción leve a mariscos, se prescribe Epinefrina.', 9),
('Control alergia', 116, 74, 73.5, 1.75, '2025-10-02', 98, 'Sin incidentes desde última revisión.', 9),

-- Paciente 10 - Sofia Dominguez
('Chequeo general', 119, 78, 65.0, 1.63, '2025-01-22', 99, 'Paciente asintomática, análisis normales.', 10),
('Revisión anual', 118, 77, 65.2, 1.63, '2025-07-22', 99, 'Sin alteraciones, mantiene buena salud.', 10);
GO

INSERT INTO ESTADO_SOLICITUD 
(detalle)
VALUES
('Pendiente'), 	
('Aceptado'), 	
('Rechazado');
GO


