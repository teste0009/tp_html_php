-- phpMyAdmin SQL Dump
-- version 4.9.5deb2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Apr 05, 2022 at 03:37 PM
-- Server version: 8.0.28-0ubuntu0.20.04.3
-- PHP Version: 7.4.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `rrhh`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `empleados_por_fecha` (IN `str_fecha` VARCHAR(10))  procedimiento: BEGIN
  SET sql_mode = 'NO_ZERO_DATE';
  SET @fecha = STR_TO_DATE(str_fecha, '%d/%m/%Y');

  IF ISNULL(@fecha) THEN
    SELECT 'ERROR: Fecha Incorrecta.(d/m/aaaa)' AS ERROR;
    LEAVE procedimiento;
  END IF;

  SELECT de.area, de.descripcion departamento, pu.nombre AS puesto,
    st.descripcion AS estado, pe.desde, pe.hasta, em.nro_legajo,
    CONCAT(em.apellidos, ', ', em.nombres) AS nombre, em.dni

    FROM empleados em

    LEFT JOIN puestos_empleados pe ON (pe.nro_legajo = em.nro_legajo)
    LEFT JOIN departamentos de ON (de.codigo = pe.cod_departamento)
    LEFT JOIN puestos pu ON (pu.id = pe.id_puesto)
    LEFT JOIN estados st ON (st.codigo = pe.cod_estado)

    WHERE ((pe.cod_estado = 100) OR (pe.cod_estado >= 500))
      AND ((@fecha BETWEEN pe.desde AND pe.hasta) OR (@fecha >= pe.desde AND ISNULL(pe.hasta)));

  SELECT FOUND_ROWS() AS total_empleados;
END -- procedimiento$$

--
-- Functions
--
CREATE DEFINER=`root`@`localhost` FUNCTION `ind_rot_personal` (`con` INT, `des` INT, `ini` INT, `fin` INT) RETURNS VARCHAR(8) CHARSET latin1 COLLATE latin1_spanish_ci NO SQL
BEGIN
  RETURN CONCAT(
           FORMAT(
             ((num(con) + num(des)) / 2) / ((num(ini) + num(fin)) / 2) * 100,
           2),
         '%');
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `num` (`int_num` INT) RETURNS INT NO SQL
BEGIN
  RETURN IFNULL(int_num, 0);
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `ciudades`
--

CREATE TABLE `ciudades` (
  `id` int NOT NULL,
  `nombre` varchar(255) COLLATE latin1_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

--
-- Dumping data for table `ciudades`
--

INSERT INTO `ciudades` (`id`, `nombre`) VALUES
(1, 'C.A.B.A.'),
(2, 'Quilmes'),
(3, 'Merlo'),
(4, 'José C. Paz'),
(5, 'Banfield'),
(6, 'González Catán'),
(7, 'Lanús');

-- --------------------------------------------------------

--
-- Table structure for table `consultoras`
--

CREATE TABLE `consultoras` (
  `cuit` bigint NOT NULL,
  `razon_social` varchar(255) COLLATE latin1_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

--
-- Dumping data for table `consultoras`
--

INSERT INTO `consultoras` (`cuit`, `razon_social`) VALUES
(33717057959, 'OPEN SKIES S.A.'),
(33717061379, 'DEL SOL SRL'),
(33717062049, 'BENTEVEO SAS'),
(33717063479, 'CENI SAS'),
(33717064629, 'LUNA SRL');

-- --------------------------------------------------------

--
-- Table structure for table `contratados`
--

CREATE TABLE `contratados` (
  `id` int NOT NULL,
  `nro_legajo` int NOT NULL,
  `cuit_consultora` bigint NOT NULL,
  `valor_hora` decimal(11,2) NOT NULL,
  `valor_hora_extra` decimal(11,2) NOT NULL,
  `desde` date NOT NULL,
  `hasta` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

--
-- Dumping data for table `contratados`
--

INSERT INTO `contratados` (`id`, `nro_legajo`, `cuit_consultora`, `valor_hora`, `valor_hora_extra`, `desde`, `hasta`) VALUES
(1, 1220, 33717064629, '400.00', '600.00', '2012-03-10', '2017-05-21'),
(2, 1130, 33717062049, '500.00', '750.00', '2016-10-20', NULL),
(3, 1190, 33717061379, '450.00', '675.00', '2013-05-07', NULL),
(4, 1230, 33717057959, '300.00', '450.00', '2012-07-09', '2016-12-24');

-- --------------------------------------------------------

--
-- Table structure for table `departamentos`
--

CREATE TABLE `departamentos` (
  `codigo` int NOT NULL,
  `id_ciudad` int NOT NULL,
  `area` enum('Producción','Finanzas','Marketing','RRHH') COLLATE latin1_spanish_ci NOT NULL,
  `descripcion` varchar(255) COLLATE latin1_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

--
-- Dumping data for table `departamentos`
--

INSERT INTO `departamentos` (`codigo`, `id_ciudad`, `area`, `descripcion`) VALUES
(110, 4, 'Producción', 'Fabricación'),
(120, 4, 'Producción', 'Control de Calidad'),
(210, 1, 'Finanzas', 'Contabilidad'),
(220, 1, 'Finanzas', 'Tesorería'),
(310, 4, 'Marketing', 'Compras'),
(320, 4, 'Marketing', 'Mercados'),
(410, 1, 'RRHH', 'Administración de Personal'),
(420, 1, 'RRHH', 'Nómina');

-- --------------------------------------------------------

--
-- Table structure for table `desempenios`
--

CREATE TABLE `desempenios` (
  `codigo` int NOT NULL,
  `descripcion` varchar(255) COLLATE latin1_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

--
-- Dumping data for table `desempenios`
--

INSERT INTO `desempenios` (`codigo`, `descripcion`) VALUES
(1, 'Malo'),
(2, 'Regular'),
(3, 'Bueno'),
(4, 'Muy Bueno'),
(5, 'Excelente');

-- --------------------------------------------------------

--
-- Table structure for table `efectivos`
--

CREATE TABLE `efectivos` (
  `id` int NOT NULL,
  `nro_legajo` int NOT NULL,
  `salario` decimal(11,2) NOT NULL,
  `desde` date NOT NULL,
  `hasta` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

--
-- Dumping data for table `efectivos`
--

INSERT INTO `efectivos` (`id`, `nro_legajo`, `salario`, `desde`, `hasta`) VALUES
(1, 1180, '60000.00', '2009-09-02', '2015-10-23'),
(2, 1030, '65000.00', '2012-04-05', NULL),
(3, 1160, '70000.00', '2014-05-15', NULL),
(4, 1120, '80000.00', '2016-03-24', NULL),
(5, 1200, '75000.00', '2013-07-02', NULL),
(6, 1170, '50000.00', '2011-12-22', NULL),
(7, 1020, '90000.00', '2011-08-05', NULL),
(8, 1050, '85000.00', '2010-07-02', NULL),
(9, 1080, '120000.00', '2016-12-02', NULL),
(10, 1240, '70000.00', '2015-01-26', NULL),
(11, 1150, '90000.00', '2013-08-29', '2018-05-07'),
(12, 1040, '65000.00', '2014-07-18', NULL),
(13, 1010, '72000.00', '2016-01-03', NULL),
(14, 1210, '83000.00', '2016-12-24', NULL),
(15, 1140, '64000.00', '2014-09-14', NULL),
(16, 1060, '90000.00', '2012-03-24', NULL),
(17, 1100, '110000.00', '2016-11-21', NULL),
(18, 1090, '125000.00', '2010-07-25', NULL),
(19, 1110, '95000.00', '2014-06-08', NULL),
(20, 1070, '84000.00', '2010-05-25', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `empleados`
--

CREATE TABLE `empleados` (
  `nro_legajo` int NOT NULL,
  `id_ciudad` int NOT NULL,
  `nombres` varchar(255) COLLATE latin1_spanish_ci NOT NULL,
  `apellidos` varchar(255) COLLATE latin1_spanish_ci NOT NULL,
  `dni` int NOT NULL,
  `fecha_nacimiento` date NOT NULL,
  `email` varchar(255) COLLATE latin1_spanish_ci NOT NULL,
  `telefono` bigint NOT NULL,
  `domicilio` varchar(255) COLLATE latin1_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

--
-- Dumping data for table `empleados`
--

INSERT INTO `empleados` (`nro_legajo`, `id_ciudad`, `nombres`, `apellidos`, `dni`, `fecha_nacimiento`, `email`, `telefono`, `domicilio`) VALUES
(1010, 1, 'Franco', 'Armani', 33986251, '1992-11-15', 'franco@hotmail.com', 1145679873, 'Av. Libertador 1050 7 A'),
(1020, 3, 'María Florencia', 'CHIRIBELO', 31365874, '1997-10-30', 'maria@gmail.com', 1123449864, 'Av. Callao 856 3 B'),
(1030, 4, 'Milton', 'Casco', 30152687, '1998-07-29', 'milton@live.com.ar', 1191238547, 'Medrano 854 4 C'),
(1040, 2, 'Melina', 'MELIPIL', 35154789, '1999-01-12', 'melina@hotmail.com', 1177256341, 'Larrea 123 1 A'),
(1050, 6, 'Robert', 'Rojas', 32323145, '1990-03-08', 'robert@gmail.com', 1158964127, 'Talcahuano 1562 5 D'),
(1060, 3, 'Brenda', 'MOLINAS', 37456789, '1991-05-03', 'brenda@live.com.ar', 1165275341, 'Av. Santa Fe 4494 8 C'),
(1070, 3, 'Paulo', 'Díaz', 30789456, '1995-07-01', 'paulo@hotmail.com', 1142961235, 'Beruti 2564 5 D'),
(1080, 7, 'Lara', 'ESPONDA', 36123456, '1997-06-12', 'lara@gmail.com', 1191498562, 'Av. Alvear 1853 1 A'),
(1090, 1, 'Fabricio', 'Angileri', 35321654, '1995-09-18', 'fabri@live.com', 1156481222, 'Gurruchaga 4567 3 5'),
(1100, 4, 'Andrea', 'LÓPEZ', 34987654, '1993-11-21', 'andrea@hotmail.com', 1163300021, 'Venezuela 4892 4 B'),
(1110, 2, 'Santiago', 'Simón', 32123456, '1992-02-04', 'santiago@gmail.com', 1147727781, 'Av. Córdoba 2562 3 A'),
(1120, 5, 'Bettiana', 'SONETTI', 37987654, '1991-06-07', 'bettiana@live.com.ar', 1175623115, 'Lavalle 565 8 D'),
(1130, 5, 'Enzo', 'Pérez', 38654123, '1992-04-10', 'enzo@hotmail.com', 1136458192, 'Florida 1524 4 A'),
(1140, 7, 'Stephanie', 'Melgarejo', 30456789, '1995-01-09', 'stephanie@gmail.com', 1141586914, 'Av. San Martín 1564 3 G'),
(1150, 1, 'Nicolás', 'De La Cruz', 39123456, '1997-05-05', 'nicolas@live.com.ar', 1133451269, 'Juncal 4450'),
(1160, 2, 'Daniela', 'MERELES', 32123456, '1998-12-03', 'daniela@hotmail.com', 1123458891, 'Mozart 2300'),
(1170, 3, 'Benjamín', 'Rollheiser', 31789456, '1999-09-12', 'benjamin@gmail.com', 1163211456, 'Borges 2548 1 7'),
(1180, 7, 'Giuliana', 'González Ranzuglia', 30321654, '1994-08-11', 'giuliana@live.com.ar', 1147789530, 'Cangallo 854 4 D'),
(1190, 6, 'Julián', 'Álvarez', 36321789, '1995-07-01', 'julian@hotmail.com', 1165439871, 'Pte. Perón 563'),
(1200, 3, 'Magalí', 'MOLINA', 37458632, '1997-05-03', 'magali@gmail.com', 1135694879, 'Riobamba 865 2 A'),
(1210, 1, 'Jorge', 'Carrascal', 32745896, '1990-01-05', 'jorge@live.com.ar', 1145679213, 'Av. Vélez Sarsfield 895 8 D'),
(1220, 3, 'Laura', 'FELIPE', 33456987, '1993-05-15', 'laura@gmail.com', 1178943216, 'Av. Juan B. Justo 2563'),
(1230, 5, 'Catalina', 'ALFONSO', 31235486, '1996-06-07', 'cata@live.com.ar', 1165412398, 'Nicaragua 1546 PB 2'),
(1240, 7, 'Luciana', 'DUARTE', 35639874, '1992-07-28', 'luciana@hotmail.com', 1145683219, 'Uriarte 523 5 B');

-- --------------------------------------------------------

--
-- Table structure for table `estados`
--

CREATE TABLE `estados` (
  `codigo` int NOT NULL,
  `descripcion` varchar(255) COLLATE latin1_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

--
-- Dumping data for table `estados`
--

INSERT INTO `estados` (`codigo`, `descripcion`) VALUES
(100, 'Activo'),
(110, 'de Licencia'),
(120, 'de Vacaciones'),
(500, 'Inactivo'),
(510, 'Despedido'),
(520, 'Despedido con causa');

-- --------------------------------------------------------

--
-- Table structure for table `evaluaciones`
--

CREATE TABLE `evaluaciones` (
  `id` int NOT NULL,
  `nro_legajo` int NOT NULL,
  `cod_desempenio` int NOT NULL,
  `fecha` date NOT NULL,
  `observaciones` varchar(255) COLLATE latin1_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

--
-- Dumping data for table `evaluaciones`
--

INSERT INTO `evaluaciones` (`id`, `nro_legajo`, `cod_desempenio`, `fecha`, `observaciones`) VALUES
(1, 1200, 4, '2016-10-25', ''),
(2, 1030, 3, '2016-10-25', ''),
(3, 1110, 2, '2016-10-25', ''),
(4, 1010, 4, '2016-10-25', ''),
(5, 1220, 4, '2016-10-25', ''),
(6, 1120, 5, '2016-10-25', ''),
(7, 1060, 4, '2016-10-25', ''),
(8, 1230, 5, '2016-10-25', ''),
(9, 1090, 1, '2016-10-25', ''),
(10, 1040, 5, '2016-10-25', ''),
(11, 1170, 1, '2016-10-25', ''),
(12, 1240, 4, '2016-10-25', ''),
(13, 1050, 2, '2016-10-25', ''),
(14, 1190, 3, '2016-10-25', ''),
(15, 1160, 3, '2016-10-25', ''),
(16, 1020, 2, '2016-10-25', ''),
(17, 1150, 3, '2016-10-25', ''),
(18, 1130, 4, '2016-10-25', ''),
(19, 1070, 2, '2016-10-25', ''),
(20, 1140, 3, '2016-10-25', ''),
(21, 1200, 5, '2017-10-26', ''),
(22, 1030, 5, '2017-10-26', ''),
(23, 1100, 5, '2017-10-26', ''),
(24, 1110, 3, '2017-10-26', ''),
(25, 1210, 3, '2017-10-26', ''),
(26, 1010, 1, '2017-10-26', ''),
(27, 1060, 5, '2017-10-26', ''),
(28, 1080, 3, '2017-10-26', ''),
(29, 1120, 4, '2017-10-26', ''),
(30, 1090, 3, '2017-10-26', ''),
(31, 1040, 2, '2017-10-26', ''),
(32, 1050, 3, '2017-10-26', ''),
(33, 1170, 2, '2017-10-26', ''),
(34, 1020, 4, '2017-10-26', ''),
(35, 1150, 2, '2017-10-26', ''),
(36, 1160, 2, '2017-10-26', ''),
(37, 1190, 4, '2017-10-26', ''),
(38, 1240, 5, '2017-10-26', ''),
(39, 1070, 4, '2017-10-26', ''),
(40, 1130, 3, '2017-10-26', ''),
(41, 1140, 4, '2017-10-26', '');

-- --------------------------------------------------------

--
-- Stand-in structure for view `nomina_empleados_contratados`
-- (See below for the actual view)
--
CREATE TABLE `nomina_empleados_contratados` (
`area` enum('Producción','Finanzas','Marketing','RRHH')
,`departamento` varchar(255)
,`puesto` varchar(255)
,`estado` varchar(255)
,`desde` date
,`hasta` date
,`nro_legajo` int
,`nombre` varchar(512)
,`valor_hora` decimal(11,2)
,`valor_hora_extra` decimal(11,2)
,`dni` int
,`cuit_consultora` bigint
,`consultora` varchar(255)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `nomina_empleados_efectivos`
-- (See below for the actual view)
--
CREATE TABLE `nomina_empleados_efectivos` (
`area` enum('Producción','Finanzas','Marketing','RRHH')
,`departamento` varchar(255)
,`puesto` varchar(255)
,`estado` varchar(255)
,`desde` date
,`hasta` date
,`nro_legajo` int
,`nombre` varchar(512)
,`salario` decimal(11,2)
,`dni` int
);

-- --------------------------------------------------------

--
-- Table structure for table `puestos`
--

CREATE TABLE `puestos` (
  `id` int NOT NULL,
  `nombre` varchar(255) COLLATE latin1_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

--
-- Dumping data for table `puestos`
--

INSERT INTO `puestos` (`id`, `nombre`) VALUES
(1, 'Jefe de Manufactura'),
(2, 'Empaquetador'),
(3, 'Gerente Contable'),
(4, 'Tesorero'),
(5, 'Gerente de Abastecimiento'),
(6, 'Publicista'),
(7, 'Asistente de Capacitación'),
(8, 'Encargado de nóminas');

-- --------------------------------------------------------

--
-- Table structure for table `puestos_empleados`
--

CREATE TABLE `puestos_empleados` (
  `cod_departamento` int NOT NULL,
  `id_puesto` int NOT NULL,
  `nro_legajo` int NOT NULL,
  `cod_estado` int NOT NULL,
  `desde` date NOT NULL,
  `hasta` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

--
-- Dumping data for table `puestos_empleados`
--

INSERT INTO `puestos_empleados` (`cod_departamento`, `id_puesto`, `nro_legajo`, `cod_estado`, `desde`, `hasta`) VALUES
(110, 1, 1200, 100, '2013-07-02', NULL),
(120, 2, 1030, 100, '2012-04-05', NULL),
(120, 2, 1030, 120, '2013-01-01', '2013-01-07'),
(120, 2, 1100, 100, '2016-11-21', NULL),
(120, 2, 1110, 100, '2014-06-08', NULL),
(120, 2, 1210, 100, '2016-12-24', NULL),
(120, 2, 1210, 110, '2018-03-05', '2018-03-20'),
(210, 3, 1010, 100, '2016-01-03', NULL),
(220, 4, 1060, 100, '2012-03-24', NULL),
(220, 4, 1080, 100, '2016-12-02', NULL),
(220, 4, 1120, 100, '2016-03-24', NULL),
(220, 4, 1220, 500, '2012-03-10', '2017-05-21'),
(310, 5, 1090, 100, '2010-07-25', NULL),
(320, 6, 1040, 100, '2014-07-18', NULL),
(320, 6, 1050, 100, '2010-07-02', NULL),
(320, 6, 1170, 100, '2011-12-22', NULL),
(320, 6, 1230, 510, '2012-07-09', '2016-12-24'),
(410, 7, 1020, 100, '2011-08-05', NULL),
(410, 7, 1150, 510, '2013-08-29', '2018-05-07'),
(410, 7, 1160, 100, '2014-05-15', NULL),
(410, 7, 1190, 100, '2013-05-07', NULL),
(410, 7, 1240, 100, '2015-01-26', NULL),
(420, 8, 1070, 100, '2010-05-25', NULL),
(420, 8, 1130, 100, '2016-10-20', NULL),
(420, 8, 1130, 110, '2017-07-01', '2017-07-15'),
(420, 8, 1140, 100, '2014-09-14', NULL),
(420, 8, 1140, 120, '2015-01-01', '2015-01-07'),
(420, 8, 1180, 520, '2009-09-02', '2015-10-23');

-- --------------------------------------------------------

--
-- Table structure for table `usuarios`
--

CREATE TABLE `usuarios` (
  `email` varchar(255) COLLATE latin1_spanish_ci NOT NULL,
  `nombre` varchar(255) COLLATE latin1_spanish_ci NOT NULL,
  `password` varchar(255) COLLATE latin1_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

--
-- Dumping data for table `usuarios`
--

INSERT INTO `usuarios` (`email`, `nombre`, `password`) VALUES
('admin@email.com', 'Admin', '1B287D7CFA9BAD74FE30CBBC5DBA2D05'),
('enzo@gmail.com', 'Enzo', 'CA1A7ECAF5295ADB941CCB45635BD4D6');

-- --------------------------------------------------------

--
-- Structure for view `nomina_empleados_contratados`
--
DROP TABLE IF EXISTS `nomina_empleados_contratados`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `nomina_empleados_contratados`  AS  select `de`.`area` AS `area`,`de`.`descripcion` AS `departamento`,`pu`.`nombre` AS `puesto`,`st`.`descripcion` AS `estado`,`pe`.`desde` AS `desde`,`pe`.`hasta` AS `hasta`,`em`.`nro_legajo` AS `nro_legajo`,concat(`em`.`apellidos`,', ',`em`.`nombres`) AS `nombre`,`ec`.`valor_hora` AS `valor_hora`,`ec`.`valor_hora_extra` AS `valor_hora_extra`,`em`.`dni` AS `dni`,`co`.`cuit` AS `cuit_consultora`,`co`.`razon_social` AS `consultora` from ((((((`empleados` `em` join `contratados` `ec` on((`ec`.`nro_legajo` = `em`.`nro_legajo`))) left join `consultoras` `co` on((`co`.`cuit` = `ec`.`cuit_consultora`))) left join `puestos_empleados` `pe` on(((`pe`.`nro_legajo` = `ec`.`nro_legajo`) and (`pe`.`desde` = `ec`.`desde`)))) left join `departamentos` `de` on((`de`.`codigo` = `pe`.`cod_departamento`))) left join `puestos` `pu` on((`pu`.`id` = `pe`.`id_puesto`))) left join `estados` `st` on((`st`.`codigo` = `pe`.`cod_estado`))) where (`pe`.`cod_estado` < 500) order by `de`.`area`,`de`.`descripcion` ;

-- --------------------------------------------------------

--
-- Structure for view `nomina_empleados_efectivos`
--
DROP TABLE IF EXISTS `nomina_empleados_efectivos`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `nomina_empleados_efectivos`  AS  select `de`.`area` AS `area`,`de`.`descripcion` AS `departamento`,`pu`.`nombre` AS `puesto`,`st`.`descripcion` AS `estado`,`pe`.`desde` AS `desde`,`pe`.`hasta` AS `hasta`,`em`.`nro_legajo` AS `nro_legajo`,concat(`em`.`apellidos`,', ',`em`.`nombres`) AS `nombre`,`ef`.`salario` AS `salario`,`em`.`dni` AS `dni` from (((((`empleados` `em` join `efectivos` `ef` on((`ef`.`nro_legajo` = `em`.`nro_legajo`))) left join `puestos_empleados` `pe` on(((`pe`.`nro_legajo` = `ef`.`nro_legajo`) and (`pe`.`desde` = `ef`.`desde`)))) left join `departamentos` `de` on((`de`.`codigo` = `pe`.`cod_departamento`))) left join `puestos` `pu` on((`pu`.`id` = `pe`.`id_puesto`))) left join `estados` `st` on((`st`.`codigo` = `pe`.`cod_estado`))) where (`pe`.`cod_estado` < 500) order by `de`.`area`,`de`.`descripcion` ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ciudades`
--
ALTER TABLE `ciudades`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `consultoras`
--
ALTER TABLE `consultoras`
  ADD PRIMARY KEY (`cuit`);

--
-- Indexes for table `contratados`
--
ALTER TABLE `contratados`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_cont_nro_legajo` (`nro_legajo`),
  ADD KEY `fk_cuit_consult` (`cuit_consultora`);

--
-- Indexes for table `departamentos`
--
ALTER TABLE `departamentos`
  ADD PRIMARY KEY (`codigo`),
  ADD KEY `fk_dep_id_ciudad` (`id_ciudad`);

--
-- Indexes for table `desempenios`
--
ALTER TABLE `desempenios`
  ADD PRIMARY KEY (`codigo`);

--
-- Indexes for table `efectivos`
--
ALTER TABLE `efectivos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_efe_nro_legajo` (`nro_legajo`);

--
-- Indexes for table `empleados`
--
ALTER TABLE `empleados`
  ADD PRIMARY KEY (`nro_legajo`),
  ADD KEY `fk_emp_id_ciudad` (`id_ciudad`);

--
-- Indexes for table `estados`
--
ALTER TABLE `estados`
  ADD PRIMARY KEY (`codigo`);

--
-- Indexes for table `evaluaciones`
--
ALTER TABLE `evaluaciones`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_leg_fecha` (`nro_legajo`,`fecha`),
  ADD KEY `fk_cod_desempenio` (`cod_desempenio`);

--
-- Indexes for table `puestos`
--
ALTER TABLE `puestos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `puestos_empleados`
--
ALTER TABLE `puestos_empleados`
  ADD UNIQUE KEY `idx_dep_pue_leg_est_des` (`cod_departamento`,`id_puesto`,`nro_legajo`,`cod_estado`,`desde`),
  ADD KEY `fk_id_puesto` (`id_puesto`),
  ADD KEY `fk_pe_nro_legajo` (`nro_legajo`),
  ADD KEY `fk_cod_estado` (`cod_estado`);

--
-- Indexes for table `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `ciudades`
--
ALTER TABLE `ciudades`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `contratados`
--
ALTER TABLE `contratados`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `efectivos`
--
ALTER TABLE `efectivos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `evaluaciones`
--
ALTER TABLE `evaluaciones`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `puestos`
--
ALTER TABLE `puestos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `contratados`
--
ALTER TABLE `contratados`
  ADD CONSTRAINT `contratados_ibfk_1` FOREIGN KEY (`nro_legajo`) REFERENCES `empleados` (`nro_legajo`),
  ADD CONSTRAINT `contratados_ibfk_2` FOREIGN KEY (`cuit_consultora`) REFERENCES `consultoras` (`cuit`);

--
-- Constraints for table `departamentos`
--
ALTER TABLE `departamentos`
  ADD CONSTRAINT `departamentos_ibfk_1` FOREIGN KEY (`id_ciudad`) REFERENCES `ciudades` (`id`);

--
-- Constraints for table `efectivos`
--
ALTER TABLE `efectivos`
  ADD CONSTRAINT `efectivos_ibfk_1` FOREIGN KEY (`nro_legajo`) REFERENCES `empleados` (`nro_legajo`);

--
-- Constraints for table `empleados`
--
ALTER TABLE `empleados`
  ADD CONSTRAINT `empleados_ibfk_1` FOREIGN KEY (`id_ciudad`) REFERENCES `ciudades` (`id`);

--
-- Constraints for table `evaluaciones`
--
ALTER TABLE `evaluaciones`
  ADD CONSTRAINT `evaluaciones_ibfk_1` FOREIGN KEY (`nro_legajo`) REFERENCES `empleados` (`nro_legajo`),
  ADD CONSTRAINT `evaluaciones_ibfk_2` FOREIGN KEY (`cod_desempenio`) REFERENCES `desempenios` (`codigo`);

--
-- Constraints for table `puestos_empleados`
--
ALTER TABLE `puestos_empleados`
  ADD CONSTRAINT `puestos_empleados_ibfk_1` FOREIGN KEY (`cod_departamento`) REFERENCES `departamentos` (`codigo`),
  ADD CONSTRAINT `puestos_empleados_ibfk_2` FOREIGN KEY (`id_puesto`) REFERENCES `puestos` (`id`),
  ADD CONSTRAINT `puestos_empleados_ibfk_3` FOREIGN KEY (`nro_legajo`) REFERENCES `empleados` (`nro_legajo`),
  ADD CONSTRAINT `puestos_empleados_ibfk_4` FOREIGN KEY (`cod_estado`) REFERENCES `estados` (`codigo`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
