-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 02-07-2025 a las 15:02:56
-- Versión del servidor: 10.11.10-MariaDB
-- Versión de PHP: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `u589041854_IntranetGlobal`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `actividades`
--

CREATE TABLE `actividades` (
  `id_actividad` int(11) NOT NULL,
  `nombre_actividad` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `duracion` int(10) UNSIGNED DEFAULT NULL,
  `precio` decimal(10,2) DEFAULT 0.00,
  `moneda` varchar(10) DEFAULT NULL,
  `fyh_creacion` datetime DEFAULT current_timestamp(),
  `id_cliente` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `actividades`
--

INSERT INTO `actividades` (`id_actividad`, `nombre_actividad`, `descripcion`, `duracion`, `precio`, `moneda`, `fyh_creacion`, `id_cliente`) VALUES
(1, 'Reuniones', NULL, 0, 0.00, NULL, '2025-06-04 19:19:14', 1),
(2, 'Soporte', NULL, 0, 0.00, NULL, '2025-06-04 19:19:14', 1),
(3, 'Diseno Web', NULL, 0, 0.00, NULL, '2025-06-04 19:19:14', 2),
(4, 'Consultoria', NULL, 0, 0.00, NULL, '2025-06-04 19:19:14', 2),
(5, 'Prueba1', 'nada', 1, 10.00, 'USD', '2025-06-04 19:25:20', 1),
(6, 'Prueba2', 'hola245', 1, 10.00, 'USD', '2025-06-04 19:29:20', 1),
(7, 'Prueba3', 'hola', 7200, 12.00, 'USD', '2025-06-04 19:55:25', 1),
(8, 'Prueba4', 'hola4', 3600, 10.00, 'USD', '2025-06-04 20:03:07', 6),
(9, 'Prueba 5', 'Test12', 1, 1.00, 'USD', '2025-06-13 11:47:24', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cargos`
--

CREATE TABLE `cargos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `nivel_jerarquia` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `cargos`
--

INSERT INTO `cargos` (`id`, `nombre`, `nivel_jerarquia`) VALUES
(1, 'Jefe Superior', 1),
(2, 'Jefe Directo', 2),
(3, 'Trabajador Normal', 3),
(4, 'gestion humana', 3),
(5, 'Gerente General', 2),
(6, 'Project Manager', 2),
(7, 'Coordinador de Marketing', 2),
(8, 'Director de operaciones y talento humano', 2),
(9, 'Coordinador de servicio al cliente y logística', 2),
(10, 'Agente de servicio al cliente y logística', 3),
(11, 'Especialista en Diseño', 3),
(12, 'Productor Audiovisual', 3),
(13, 'Agente de Marketing', 3),
(14, 'Asistente Ejecutivo', 3),
(15, 'Especialista en Redes Sociales', 3),
(16, 'Coordinadora de Contenido', 3),
(17, 'Analista Financiero', 3),
(18, 'Especialista de TI', 3),
(19, 'Analista de datos', 3),
(20, 'Director de operaciones y talento humano', 3),
(21, 'Especialista de formación', 3),
(22, 'Manager de operación y logística', 3),
(23, 'Recepcionista', 3),
(24, 'Servicios generales', 3),
(25, 'Auxiliar Administrativo', 3),
(26, 'Asistente de RRHH', 3),
(27, 'Directora RRHH', 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `id_cliente` int(11) NOT NULL,
  `nombre_cliente` varchar(100) NOT NULL,
  `direccion` text DEFAULT NULL,
  `moneda` varchar(10) DEFAULT 'USD',
  `estado` enum('activo','inactivo') DEFAULT 'activo',
  `fyh_creacion` timestamp NULL DEFAULT current_timestamp(),
  `duracion_cliente` bigint(20) DEFAULT NULL,
  `precio_cliente` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`id_cliente`, `nombre_cliente`, `direccion`, `moneda`, `estado`, `fyh_creacion`, `duracion_cliente`, `precio_cliente`) VALUES
(1, 'Cliente A', '1', 'USD', 'activo', '2025-06-04 14:50:10', 3600, 10.00),
(2, 'Cliente B', '2', 'USD', 'activo', '2025-06-04 14:50:10', 0, 0.00),
(6, 'Cliente C', '3', 'USD', 'activo', '2025-06-04 18:44:07', 0, 0.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cliente_trabajador`
--

CREATE TABLE `cliente_trabajador` (
  `id` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `id_trabajador` int(10) UNSIGNED NOT NULL,
  `fecha_asignacion` datetime DEFAULT current_timestamp(),
  `estado` enum('activo','inactivo') DEFAULT 'activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `cliente_trabajador`
--

INSERT INTO `cliente_trabajador` (`id`, `id_cliente`, `id_trabajador`, `fecha_asignacion`, `estado`) VALUES
(43, 1, 58, '2025-06-19 14:31:12', 'activo'),
(58, 2, 58, '2025-06-19 14:50:01', 'activo'),
(59, 6, 58, '2025-06-19 14:50:26', 'activo'),
(60, 1, 15, '2025-06-24 16:08:13', 'activo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `facturas`
--

CREATE TABLE `facturas` (
  `id` int(11) NOT NULL,
  `invoice_number` varchar(50) NOT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `bill_from` varchar(255) DEFAULT NULL,
  `bill_to` varchar(255) DEFAULT NULL,
  `discount_pct` decimal(5,2) DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `facturas`
--

INSERT INTO `facturas` (`id`, `invoice_number`, `fecha_inicio`, `fecha_fin`, `bill_from`, `bill_to`, `discount_pct`, `notes`, `created_at`) VALUES
(1, 'INV-1749653069141', '2025-01-05', '1969-12-31', 'Global Connection SAS', 'Cliente A', 0.00, 'hola', '2025-06-11 14:45:17'),
(2, 'INV-1749815469615', '2025-01-06', '1969-12-31', 'Global Connection SAS', 'Cliente A', 100.00, '', '2025-06-13 11:51:54');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `factura_items`
--

CREATE TABLE `factura_items` (
  `id` int(11) NOT NULL,
  `factura_id` int(11) NOT NULL,
  `actividad` varchar(255) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `duracion` varchar(50) DEFAULT NULL,
  `precio_unitario` decimal(10,2) DEFAULT NULL,
  `subtotal` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `factura_items`
--

INSERT INTO `factura_items` (`id`, `factura_id`, `actividad`, `descripcion`, `duracion`, `precio_unitario`, `subtotal`) VALUES
(1, 1, 'Soporte', '', '00:29:00', 10.00, 4.83),
(2, 1, 'Reuniones', '', '00:10:00', 10.00, 1.67),
(3, 1, 'Reuniones', '', '00:20:00', 10.00, 3.33),
(4, 1, 'Reuniones', '', '00:20:00', 10.00, 3.33),
(5, 1, 'Prueba3', '', '00:10:00', 10.00, 1.67),
(6, 2, 'Reuniones', '', '00:20:00', 10.00, 3.33),
(7, 2, 'Consultoria', 'hola', '00:01:08', 10.00, 0.19),
(8, 2, 'Prueba1', 'hola 5', '00:01:00', 10.00, 0.17);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `formularios_asignacion`
--

CREATE TABLE `formularios_asignacion` (
  `id` int(11) NOT NULL,
  `numero_formulario` varchar(50) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `documento` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `equipos` text NOT NULL,
  `seriales` text NOT NULL,
  `fecha_registro` datetime NOT NULL DEFAULT current_timestamp(),
  `estado_trabajador` enum('aprobado','rechazado','pendiente') NOT NULL,
  `fecha_trabajador` date DEFAULT NULL,
  `token` varchar(64) NOT NULL,
  `estado_formulario` enum('pendiente','aprobado','denegado') DEFAULT 'pendiente',
  `comentarios_trabajador` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `formularios_asignacion`
--

INSERT INTO `formularios_asignacion` (`id`, `numero_formulario`, `nombre`, `documento`, `email`, `equipos`, `seriales`, `fecha_registro`, `estado_trabajador`, `fecha_trabajador`, `token`, `estado_formulario`, `comentarios_trabajador`) VALUES
(1, 'EQ-20250508072616-863', 'Diego Fernando Silva Acevedo', '1102381559', 'dfsilvaacevedo@gmail.com', '[\"silla\",\"audifonos\",\"computador\",\"monitor\",\"carnet\",\"cinta porta carnet\"]', '{\"silla\":\"OF0197\",\"audifonos\":\"2340AY06533\",\"computador\":\"5CD2108XTG\",\"monitor\":\"OF0455\"}', '2024-10-04 00:00:00', 'aprobado', '2025-05-08', '03c39ae9297f549cfca7741a7059de12', 'pendiente', NULL),
(5, 'EQ-20250508090323-689', 'Cristhian Felipe Olachica Escobedo', '1098826404', 'pipe_19_00@hotmail.com', '[\"silla\",\"audifonos\",\"computador\",\"mouse\",\"monitor\",\"carnet\",\"cinta porta carnet\"]', '{\"silla\":\"OF0269\",\"audifonos\":\"-\",\"computador\":\"5CG2120S68\",\"mouse\":\"OF0489\",\"monitor\":\"OF0475\"}', '2025-05-08 00:00:00', 'aprobado', '2025-05-08', '77a202f5282db0f22ff9442ba323d027', 'pendiente', 'Holis'),
(6, 'EQ-20250508091053-943', 'Álvaro Andrés Sarmiento Rueda', '1098806024', 'alvarosarmiento1998@gmail.com', '[\"silla\",\"audifonos\",\"computador\",\"mouse\",\"monitor\",\"carnet\",\"cinta porta carnet\"]', '{\"silla\":\"OF0212\",\"audifonos\":\"-\",\"computador\":\"5CD2108XVJ\",\"mouse\":\"OF0517\",\"monitor\":\"OF0466\"}', '2025-05-08 00:00:00', 'aprobado', '2025-05-08', '75bf43ad7e41a1184eaa3959fe8e9423', 'pendiente', NULL),
(7, 'EQ-20250508091300-953', 'Andrés Libardo Beltrán Muñoz', '1098810126', 'andresb490@gmail.com', '[\"silla\",\"audifonos\",\"computador\",\"mouse\",\"monitor\",\"carnet\",\"cinta porta carnet\"]', '{\"silla\":\"OF0213\",\"audifonos\":\"-\",\"computador\":\"5CD2108Y2Q\",\"mouse\":\"OF0490\",\"monitor\":\"OF0474\"}', '2025-05-08 00:00:00', 'aprobado', '2025-05-08', '2e55520b394a050913c50e0522ff1845', 'pendiente', '.'),
(8, 'EQ-20250508091511-527', 'Santiago Rodriguez Lopez', '1005483223', 'santiaguito.srl76@gmail.com', '[\"silla\",\"audifonos\",\"computador\",\"mouse\",\"monitor\",\"carnet\",\"cinta porta carnet\"]', '{\"silla\":\"OF0004\",\"audifonos\":\"03FE69\",\"computador\":\"5CD2108Y7Q\",\"mouse\":\"OF0515\",\"monitor\":\"OF0469\"}', '2025-05-08 00:00:00', 'pendiente', NULL, 'ff2fbea17187f83ab0a8a96558a35602', 'pendiente', NULL),
(9, 'EQ-20250508091657-442', 'Oscar Julian Corzo Gutierrez', '1097093725', 'oscarjuliancorzo@gmail.com', '[\"silla\",\"audifonos\",\"computador\",\"mouse\",\"monitor\",\"carnet\",\"cinta porta carnet\"]', '{\"silla\":\"OF0206\",\"audifonos\":\"00475\",\"computador\":\"5CD2108YCZ\",\"mouse\":\"0F0493\",\"monitor\":\"OF0471\"}', '2025-05-08 00:00:00', 'pendiente', NULL, 'df7d7faee9cf67868cb082f8416c5d50', 'pendiente', NULL),
(10, 'EQ-20250508091829-468', 'Sergio Alberto Angulo Amorocho', '1005260372', 'sergioalbertoangulo@gmail.com', '[\"silla\",\"audifonos\",\"computador\",\"mouse\",\"monitor\",\"carnet\",\"cinta porta carnet\"]', '{\"silla\":\"OF0208\",\"audifonos\":\"logitech\",\"computador\":\"5CD2108XTQ\",\"mouse\":\"OF0492\",\"monitor\":\"OF0472\"}', '2025-05-08 00:00:00', 'aprobado', '2025-05-08', 'd331f03dcb3c5e2906bf950d42179911', 'pendiente', 'Aprobado'),
(11, 'EQ-20250508092028-207', 'Giovanny Andrés Pabón Villar', '1005154364', 'giopabon12345@gmail.com', '[\"silla\",\"audifonos\",\"computador\",\"mouse\",\"monitor\",\"carnet\",\"cinta porta carnet\"]', '{\"silla\":\"OF0268\",\"audifonos\":\"2401AY009AQ9\",\"computador\":\"5CD2108XTW\",\"mouse\":\"OF0518\",\"monitor\":\"OF0470\"}', '2025-05-08 00:00:00', 'aprobado', '2025-05-13', 'b5ed4d611d6ccc4ae659efa7a6e25e5d', 'pendiente', NULL),
(12, 'EQ-20250508092241-338', 'Johan Sebastián Lopez Castro', '1098816907', 'seblopez99@hotmail.com', '[\"silla\",\"audifonos\",\"computador\",\"mouse\",\"monitor\",\"carnet\",\"cinta porta carnet\"]', '{\"silla\":\"OF0187\",\"audifonos\":\"LOGITECH\",\"computador\":\"5CD2108YB6\",\"mouse\":\"OF0516\",\"monitor\":\"OF0468\"}', '2025-05-08 00:00:00', 'aprobado', '2025-05-08', '478166696ff63d9168d1cca9729e5c28', 'pendiente', 'Gracias '),
(13, 'EQ-20250508092542-922', 'Juliana Carolina Quiroz Caceres', '1098823097', 'julianaquiroz139@gmail.com', '[\"silla\",\"audifonos\",\"computador\",\"mouse\",\"monitor\",\"carnet\",\"cinta porta carnet\"]', '{\"silla\":\"OF0406\",\"audifonos\":\"2340AY09AC19\",\"computador\":\"4B19ZJ3\",\"mouse\":\"OF0512\",\"monitor\":\"OF0464\"}', '2025-05-08 00:00:00', 'aprobado', '2025-05-08', '61410d1bb9d53b7067574d9f5a869e55', 'pendiente', NULL),
(14, 'EQ-20250508093026-300', 'Brayan Jesus Lizcano Moreno', '1193148710', 'brayanlizcano258@outlook.com', '[\"silla\",\"audifonos\",\"computador\",\"mouse\",\"monitor\",\"carnet\",\"cinta porta carnet\"]', '{\"silla\":\"OF0275\",\"audifonos\":\"LOGITECH\",\"computador\":\"5CD2108Y4L\",\"mouse\":\"OF0491\",\"monitor\":\"OF0473\"}', '2025-05-08 00:00:00', 'pendiente', NULL, '0dd7daab652fadc6fddc50d412e2c25f', 'pendiente', NULL),
(15, 'EQ-20250508093929-133', 'Fabian Andrés Arenas Olave', '1005157464', 'fabianandres0512@gmail.com', '[\"silla\",\"audifonos\",\"computador\",\"mouse\",\"monitor\",\"carnet\",\"cinta porta carnet\"]', '{\"silla\":\"OF0324\",\"audifonos\":\"021919\",\"computador\":\"1X33B44\",\"mouse\":\"OF0500\",\"monitor\":\"OF0480\"}', '2025-05-08 00:00:00', 'pendiente', NULL, 'eed90ffe90833703e0283bf5e11a3dcc', 'pendiente', NULL),
(16, 'EQ-20250508094139-688', 'Alexander Rodriguez Cruz', '1098781014', 'alexrocru1996@gmail.com', '[\"silla\",\"audifonos\",\"computador\",\"mouse\",\"monitor\",\"carnet\",\"cinta porta carnet\"]', '{\"silla\":\"OF0294\",\"audifonos\":\"LOGITECH\",\"computador\":\"5CD2108Y3K\",\"mouse\":\"OF0501\",\"monitor\":\"OF0481\"}', '2025-05-08 00:00:00', 'pendiente', NULL, 'e32511408741887b3061c377237ec880', 'pendiente', NULL),
(17, 'EQ-20250508094448-364', 'Yersi Catalina Mendez Villamizar', '1005329286', 'catalinamendew@gmail.com', '[\"silla\",\"audifonos\",\"computador\",\"mouse\",\"monitor\",\"carnet\",\"cinta porta carnet\"]', '{\"silla\":\"OF0281\",\"audifonos\":\"2402A\",\"computador\":\"5CD2108XX9\",\"mouse\":\"OF0499\",\"monitor\":\"OF0479\"}', '2025-05-08 00:00:00', 'pendiente', NULL, '327e0f43ee90d7d073d3bb50f6ae8e38', 'pendiente', NULL),
(18, 'EQ-20250508094711-918', 'Yuly Marcela Muñoz Estupiñan', '1007439733', 'yulymunoz24@gmail.com', '[\"silla\",\"audifonos\",\"computador\",\"mouse\",\"monitor\",\"carnet\",\"cinta porta carnet\"]', '{\"silla\":\"OF0202\",\"audifonos\":\"LOGITECH\",\"computador\":\"5CD2108Y19\",\"mouse\":\"-\",\"monitor\":\"OF0480\"}', '2025-05-08 00:00:00', 'pendiente', NULL, 'f7f0bb154a049d7d8881932e3d25f3a6', 'pendiente', NULL),
(19, 'EQ-20250508100540-192', 'Juan Diego Velasquez Santos', '1095823345', 'mercaderjuansantos@gmail.com', '[\"silla\",\"audifonos\",\"computador\",\"mouse\",\"monitor\",\"carnet\",\"cinta porta carnet\"]', '{\"silla\":\"OF0114\",\"audifonos\":\"2340AY0712C9\",\"computador\":\"5CD2108XTZ\",\"mouse\":\"OF0502\",\"monitor\":\"OF0483\"}', '2025-05-08 00:00:00', 'pendiente', NULL, '6b8f81b3fd021d134bd7ac39123b71e6', 'pendiente', NULL),
(20, 'EQ-20250508104041-789', 'Andres Giovany Rincon Amaya', '1095839166', 'andresgiorincon@gmail.com', '[\"silla\",\"audifonos\",\"computador\",\"mouse\",\"monitor\",\"carnet\",\"cinta porta carnet\"]', '{\"silla\":\"OF0297\",\"audifonos\":\"LOGITECH\",\"computador\":\"5CD2108YC9\",\"mouse\":\"OF0506\",\"monitor\":\"OF0453\"}', '2025-05-08 00:00:00', 'pendiente', NULL, '5a92bb142124a5e311ab40f50656b73c', 'pendiente', NULL),
(21, 'EQ-20250508104404-811', 'Carlos Felipe Ardila Torres', '1095838717', 'felipeardilatorres@gmail.com', '[\"silla\",\"audifonos\",\"computador\",\"mouse\",\"monitor\",\"carnet\",\"cinta porta carnet\"]', '{\"silla\":\"OF0405\",\"audifonos\":\"Y066CB9\",\"computador\":\"5G23B44\",\"mouse\":\"OF0511\",\"monitor\":\"OF0448\"}', '2025-05-08 00:00:00', 'pendiente', NULL, '682c62f05c3fd5413a570a8de72cd647', 'pendiente', NULL),
(22, 'EQ-20250508104540-733', 'Carlos Rafael Gamarra Quevedo', '531056', 'nanotechvision@hotmail.com', '[\"silla\",\"audifonos\",\"computador\",\"mouse\",\"monitor\",\"carnet\",\"cinta porta carnet\"]', '{\"silla\":\"OF0203\",\"audifonos\":\"LOGITECH\",\"computador\":\"5CD2108Y8S\",\"mouse\":\"OF0505\",\"monitor\":\"OF0452\"}', '2025-05-08 00:00:00', 'pendiente', NULL, 'ab0d59245436796dd86e80183454e31c', 'pendiente', NULL),
(23, 'EQ-20250508104706-905', 'Maria Camila Villanueva Serrano', '1098711545', 'macaviserr@gmail.com', '[\"silla\",\"audifonos\",\"computador\",\"mouse\",\"monitor\",\"carnet\",\"cinta porta carnet\"]', '{\"silla\":\"OF0201\",\"audifonos\":\"Y0672C9\",\"computador\":\"5CD2108Y31\",\"mouse\":\"OF0510\",\"monitor\":\"OF0449\"}', '2025-05-08 00:00:00', 'pendiente', NULL, 'd185ab513e9d59ecd45a344c1e55b651', 'pendiente', NULL),
(24, 'EQ-20250508104842-291', 'Robinson Daniel Ortiz Cala', '1098751434', 'daniel_ortiz9403@hotmail.com', '[\"silla\",\"audifonos\",\"computador\",\"mouse\",\"monitor\",\"carnet\",\"cinta porta carnet\"]', '{\"silla\":\"OF0295\",\"audifonos\":\"A0CA9\",\"computador\":\"5CD2108XT8\",\"mouse\":\"OF0509\",\"monitor\":\"OF0451\"}', '2025-05-08 00:00:00', 'aprobado', '2025-05-12', '6e7540b3cde8ba1c1d65eb5a1e010197', 'pendiente', NULL),
(25, 'EQ-20250508105004-396', 'Silvia Alejandra Hernández Gutiérrez', '1102378878', 'silvia.ale2824@gmail.com', '[\"silla\",\"audifonos\",\"computador\",\"mouse\",\"monitor\",\"carnet\",\"cinta porta carnet\"]', '{\"silla\":\"OF0204\",\"audifonos\":\"2401AY0099\",\"computador\":\"5CD2108Y3K\",\"mouse\":\"OF0507\",\"monitor\":\"OF0454\"}', '2025-05-08 00:00:00', 'pendiente', NULL, '8d142dd67ff485de76f12acbbf4f520d', 'pendiente', NULL),
(26, 'EQ-20250508105819-574', 'Adriana Carolina Graterol Guerrero', '1127573470', 'grateroladriana222@gmail.com', '[\"silla\",\"audifonos\",\"computador\",\"mouse\",\"monitor\",\"carnet\",\"cinta porta carnet\"]', '{\"silla\":\"OF0195\",\"audifonos\":\"LOGITECH\",\"computador\":\"5CD2108Y6P\",\"mouse\":\"OF0526\",\"monitor\":\"OF0456\"}', '2025-05-08 00:00:00', 'aprobado', '2025-05-08', '4590da984aed84882ff8961db59df63e', 'pendiente', NULL),
(27, 'EQ-20250508105928-734', 'Alejandro Enrique Peña Acosta', '1140422584', 'aepa2610@gmail.com', '[\"silla\",\"audifonos\",\"computador\",\"mouse\",\"monitor\",\"carnet\",\"cinta porta carnet\"]', '{\"silla\":\"OF0273\",\"audifonos\":\"LOGITECH\",\"computador\":\"5CD2108YD0\",\"mouse\":\"OF0504\",\"monitor\":\"OF0457\"}', '2025-05-08 00:00:00', 'aprobado', '2025-05-08', 'df4261f19511f84f4158ed268e54e083', 'pendiente', 'NA'),
(28, 'EQ-20250508110117-182', 'Santiago Sergio Tejeiro Mora', '1098821315', 'santiago_tejeiro@yahoo.com', '[\"silla\",\"audifonos\",\"computador\",\"mouse\",\"monitor\",\"carnet\",\"cinta porta carnet\"]', '{\"silla\":\"OF0200\",\"audifonos\":\"070D79\",\"computador\":\"5CD2108Y46\",\"mouse\":\"-\",\"monitor\":\"OF0458\"}', '2025-05-08 00:00:00', 'pendiente', NULL, 'f50e2176de63b8be696aaf764f0bea87', 'pendiente', NULL),
(29, 'EQ-20250508110239-439', 'Jose Alonso Mendoza Guerra', '1098777430', 'ralftogo1995@gmail.com', '[\"silla\",\"audifonos\",\"computador\",\"mouse\",\"monitor\",\"carnet\",\"cinta porta carnet\"]', '{\"silla\":\"OF0284\",\"audifonos\":\"O8S919\",\"computador\":\"5CD2108XV1\",\"mouse\":\"OF0488\",\"monitor\":\"OF0462\"}', '2025-05-08 00:00:00', 'aprobado', '2025-05-08', '2ee28473013d1e25041204b9ec2d5e5a', 'pendiente', 'Los equipos que fueron entregados están en perfectas condiciones. Gracias! '),
(30, 'EQ-20250508110412-743', 'Andrés Felipe Cruz Forero', '1095951257', 'fforero9823@gmail.com', '[\"silla\",\"audifonos\",\"computador\",\"mouse\",\"monitor\",\"carnet\",\"cinta porta carnet\"]', '{\"silla\":\"OF0188\",\"audifonos\":\"0634E9\",\"computador\":\"1M33B44\",\"mouse\":\"OF0486\",\"monitor\":\"OF0460\"}', '2025-05-08 00:00:00', 'pendiente', NULL, '9553c53e05e99d1ad59dd5469a1e6d64', 'pendiente', NULL),
(31, 'EQ-20250508110535-937', 'Daniel Felipe Quintero Vanegas', '1005324902', 'dafelquintero@gmail.com', '[\"silla\",\"audifonos\",\"computador\",\"mouse\",\"monitor\",\"carnet\",\"cinta porta carnet\"]', '{\"silla\":\"OF0188\",\"audifonos\":\"0634E9\",\"computador\":\"5CD2108YFC\",\"mouse\":\"OF0487\",\"monitor\":\"OF0461\"}', '2025-05-08 00:00:00', 'aprobado', '2025-05-08', '3fde80e0cd704dc886259074dfc8e625', 'pendiente', 'N'),
(32, 'EQ-20250516093210-425', 'Juan David Gomez', '1095833816', 'juand.gomezg97@gmail.com', '[\"audifonos\",\"carnet\",\"cinta porta carnet\"]', '{\"audifonos\":\"Logitech\"}', '2025-05-16 00:00:00', 'aprobado', '2025-05-16', '5b77ef265e0261aac206db1a3e2ef298', 'pendiente', NULL),
(33, 'EQ-20250516094902-385', 'Andrés Ojeda Herrera', '1096243909', 'aojedaherrera@gmail.com', '[\"audifonos\",\"carnet\",\"cinta porta carnet\"]', '{\"audifonos\":\"Logitech\"}', '2025-05-16 00:00:00', 'pendiente', NULL, 'ccb1a274289ffac27264090688a3527b', 'pendiente', NULL),
(34, 'EQ-20250516094949-615', 'Angelly Natalia Peña Blanco', '1005371203', 'natpenablanco@gmail.com', '[\"audifonos\",\"carnet\",\"cinta porta carnet\"]', '{\"audifonos\":\"Logitech\"}', '2025-05-16 00:00:00', 'pendiente', NULL, 'e2e3c4993dbb49cf501b3468dd6189bf', 'pendiente', NULL),
(35, 'EQ-20250516095306-239', 'Carlos Enrique Duarte Carreño', '1098808556', 'ce.duarte@hotmail.es', '[\"audifonos\",\"carnet\",\"cinta porta carnet\"]', '{\"audifonos\":\"Logitech\"}', '2025-05-16 00:00:00', 'pendiente', NULL, '49c4eb8f2df96e836acda02fdbc55700', 'pendiente', NULL),
(36, 'EQ-20250516095428-388', 'Diana Lucia Lozano Pinto', '1007733169', 'dianalznp@gmail.com', '[\"audifonos\",\"carnet\",\"cinta porta carnet\"]', '{\"audifonos\":\"Logitech\"}', '2025-05-16 00:00:00', 'pendiente', NULL, '9837982d903a3d73c2af88871aea512c', 'pendiente', NULL),
(37, 'EQ-20250516095507-169', 'Geraldine Bernal Villamizar', '1095843231', 'geralbernal741@gmail.com', '[\"audifonos\",\"carnet\",\"cinta porta carnet\"]', '{\"audifonos\":\"Logitech\"}', '2025-05-16 00:00:00', 'pendiente', NULL, '333cac65a4aa90e4a98b4c6a01044760', 'pendiente', NULL),
(38, 'EQ-20250516095542-575', 'Gustavo Andres Pachon Reyes', '1095821794', 'gustavoandrespachon25@gmail.com', '[\"audifonos\",\"carnet\",\"cinta porta carnet\"]', '{\"audifonos\":\"Logitech\"}', '2025-05-16 00:00:00', 'aprobado', '2025-05-16', 'dfe7241751780656b3aef06e82dfb861', 'pendiente', NULL),
(39, 'EQ-20250516095741-499', 'Ivon Daniela Prada Molano', '1098815291', 'ivondanielaprada@gmail.com', '[\"audifonos\",\"carnet\",\"cinta porta carnet\"]', '{\"audifonos\":\"Logitech\"}', '2025-05-16 00:00:00', 'pendiente', NULL, '4e7f592cfca4bbad3edb5aece8945e3f', 'pendiente', NULL),
(40, 'EQ-20250516095835-660', 'Jessica Manrique Rodriguez', '1095830056', 'jessicamanriquefalcon@gmail.com', '[\"audifonos\",\"carnet\",\"cinta porta carnet\"]', '{\"audifonos\":\"Logitech\"}', '2025-05-16 00:00:00', 'aprobado', '2025-05-16', '6401f4bdf8e9ab96c49eb22bb53aad71', 'pendiente', 'los audífonos están en buen estado. '),
(41, 'EQ-20250516100159-712', 'Juan Sebastián Muñoz Cordero', '1095841259', 'juansemucor99@outlook.es', '[\"audifonos\",\"carnet\",\"cinta porta carnet\"]', '{\"audifonos\":\"Logitech\"}', '2025-05-16 00:00:00', 'aprobado', '2025-05-16', '622e549d6ebece5b7fb7eab3f76109ad', 'pendiente', NULL),
(42, 'EQ-20250516100240-279', 'Kathe Quintero', '1095787771', 'kathe.quintero.0101@gmail.com', '[\"carnet\",\"cinta porta carnet\"]', '{\"audifonos\":\"-\"}', '2025-05-16 00:00:00', 'pendiente', NULL, 'add2b0c84ed3d4cb1418f726b8287051', 'pendiente', NULL),
(43, 'EQ-20250516100307-369', 'Laura Patricia Guerrero Toro', '1005594030', 'Lauratoro0311i@gmail.com', '[\"carnet\",\"cinta porta carnet\"]', '{\"audifonos\":\"-\"}', '2025-05-16 00:00:00', 'pendiente', NULL, '74711b2220e2c582ed31079446ebd6ad', 'pendiente', NULL),
(44, 'EQ-20250516100425-362', 'Lizeth Gabriela Trillos Campos', '1005260713', 'fotografiacamtric@gmail.com', '[\"audifonos\",\"carnet\",\"cinta porta carnet\"]', '{\"audifonos\":\"Logitech\"}', '2025-05-16 00:00:00', 'aprobado', '2025-05-16', 'e3e641019c8ae64ea8a02238ed058a48', 'pendiente', NULL),
(45, 'EQ-20250516100516-564', 'Maria Alejandra Quiroga Manrique', '1005257255', 'mariaquirogamanrique@gmail.com', '[\"audifonos\",\"carnet\",\"cinta porta carnet\"]', '{\"audifonos\":\"Logitech\"}', '2025-05-16 00:00:00', 'pendiente', NULL, '37ce5473acc0ad542fb453998032a7e1', 'pendiente', NULL),
(46, 'EQ-20250516100612-524', 'Maria Camila Espinosa Serrano', '1005258404', 'milaespinosase@gmail.com', '[\"audifonos\",\"carnet\",\"cinta porta carnet\"]', '{\"audifonos\":\"Logitech\"}', '2025-05-16 00:00:00', 'aprobado', '2025-05-16', '3de2ecf68cba57d54356afae3f8971c5', 'pendiente', NULL),
(47, 'EQ-20250516100716-827', 'Nathalia Duque Garcés', '1098800467', 'natydg17@gmail.com', '[\"audifonos\",\"carnet\",\"cinta porta carnet\"]', '{\"audifonos\":\"Logitech\"}', '2025-05-16 00:00:00', 'pendiente', NULL, '115020dc3dec4ac2629d4f727d5fda4c', 'pendiente', NULL),
(48, 'EQ-20250516100754-289', 'Sofia Vitta Serrano', '1005288036', 'sofivitta02@gmail.com', '[\"carnet\",\"cinta porta carnet\"]', '{\"audifonos\":\"-\"}', '2025-05-16 00:00:00', 'pendiente', NULL, '9f4521a78e74d8e1227a6229feb61767', 'pendiente', NULL),
(49, 'EQ-20250516100828-818', 'Sthefany Juliana Arias Gomez', '1005150683', 'sthefanygomez19@gmail.com', '[\"carnet\",\"cinta porta carnet\"]', '{\"audifonos\":\"-\"}', '2025-05-16 00:00:00', 'pendiente', NULL, '545221fb1ed48d62b5a51da538021909', 'pendiente', NULL),
(50, 'EQ-20250516100918-394', 'Wendy Katherine Archila Quintero', '1097911124', 'wendyarchila80@gmail.com', '[\"carnet\",\"cinta porta carnet\"]', '{\"audifonos\":\"-\"}', '2025-05-16 00:00:00', 'pendiente', NULL, '6f08655e1826e014a1f6d41d937f94f5', 'pendiente', NULL),
(51, 'EQ-20250516110704-763', 'Daniel Sebastian Cote Rojas', '1098736602', 'cotedanielcotedaniel2@gmail.com', '[\"silla\",\"audifonos\",\"computador\",\"mouse\",\"monitor\",\"carnet\",\"cinta porta carnet\"]', '{\"silla\":\"-\",\"audifonos\":\"Logitech\",\"computador\":\"5CG2125HX\",\"mouse\":\"OF0514\",\"monitor\":\"OF0465\"}', '2025-05-16 00:00:00', 'pendiente', NULL, '127ef5a31ad82314763a1823771468b0', 'pendiente', NULL),
(52, 'EQ-20250516111013-852', 'Juliana Carolina Quiroz Caceres', '1098823097', 'julianaquiroz139@gmail.com', '[\"silla\",\"audifonos\",\"computador\",\"mouse\",\"monitor\",\"carnet\",\"cinta porta carnet\"]', '{\"silla\":\"OF0406\",\"audifonos\":\"Logitech\",\"computador\":\"4B19ZJ3\",\"mouse\":\"OF0512\",\"monitor\":\"OF0464\"}', '2025-05-16 00:00:00', 'pendiente', NULL, 'ed638d6ef7ab2b2afc8ddb9053f0a4f4', 'pendiente', NULL),
(53, 'EQ-20250519101957-729', 'Geraldine Bernal Villamizar', '1095843231', 'geralbernal741@gmail.com', '[\"carnet\",\"cinta porta carnet\"]', '{\"silla\":\"-\",\"audifonos\":\"-\",\"computador\":\"-\",\"mouse\":\"-\",\"monitor\":\"-\"}', '2025-05-19 00:00:00', 'pendiente', NULL, 'ee8c504373d05bad46703c2ccd3e9e13', 'pendiente', NULL),
(60, 'EQ-20250619120136-580', 'Jose Daniel Velasco Caceres', '1007554983', 'josedanielvell@gmail.com', '[\"audifonos\"]', '{\"audifonos\":\"Logitech H390\"}', '2025-06-19 00:00:00', 'aprobado', '2025-06-19', 'c3337d2830e9c492cbbafec55c80b74e', 'pendiente', 'Thank you '),
(61, 'EQ-20250619120154-774', 'Juan Sebastian Mantilla Porras', '1098789653', 'juanse1008@hotmail.com', '[\"audifonos\"]', '{\"audifonos\":\"Logitech H390\"}', '2025-06-19 00:00:00', 'aprobado', '2025-06-19', 'd5cc309be613463efe18e65318418db0', 'pendiente', 'Received without problems, Thank you!'),
(62, 'EQ-20250619120213-941', 'Maria Valentina Vega Gomez', '1005369976', 'mariavalenvg@gmail.com', '[\"audifonos\"]', '{\"audifonos\":\"Logitech H390\"}', '2025-06-19 00:00:00', 'pendiente', NULL, 'fb8fd8215203603ad18df7acce8f3025', 'pendiente', NULL),
(63, 'EQ-20250619120231-569', 'Maria Paula Restrepo Romero', '1005258055', 'mariapaularestrepo04@gmail.com', '[\"audifonos\"]', '{\"audifonos\":\"Logitech H390\"}', '2025-06-19 00:00:00', 'aprobado', '2025-06-19', 'acb288ac1ac68bccb9eeaf1a0feef088', 'pendiente', 'Gracias '),
(64, 'EQ-20250619120246-919', 'Jhonnatan David Canal Vega', '1005329615', 'jhonnatancanal@gmail.com', '[\"audifonos\"]', '{\"audifonos\":\"Logitech H390\"}', '2025-06-19 00:00:00', 'aprobado', '2025-06-19', '1b6c00966ea1152badcefed9865ef938', 'pendiente', NULL),
(65, 'EQ-20250619120306-770', 'Laura Juliana Serrano Ortiz', '1005542158', 'laurajserrano23@gmail.com', '[\"audifonos\"]', '{\"audifonos\":\"Logitech H390\"}', '2025-06-19 00:00:00', 'aprobado', '2025-06-19', '7a8f74643fbf4f6d48265d6f5f96cb08', 'pendiente', NULL),
(66, 'EQ-20250619120320-152', 'Maliuth Jireth Jimenez Contreras', '1098664908', 'maleja19.mjjc@gmail.com', '[\"audifonos\"]', '{\"audifonos\":\"Logitech H390\"}', '2025-06-19 00:00:00', 'aprobado', '2025-06-19', '989e974789dc9aa782406015c66d8dc6', 'pendiente', 'perfecto'),
(67, 'EQ-20250619120334-256', 'Angela Marcela Leon Fontecha', '1095830000', 'marcela.9628@hotmail.com', '[\"audifonos\"]', '{\"audifonos\":\"Logitech H390\"}', '2025-06-19 00:00:00', 'pendiente', NULL, '05ea4a65665b946afd9257e5b2e3176d', 'pendiente', NULL),
(68, 'EQ-20250619120347-952', 'Laura Cristina Anaya Martinez', '1098820826', 'lauranayam1999@gmail.com', '[\"audifonos\"]', '{\"audifonos\":\"Logitech H390\"}', '2025-06-19 00:00:00', 'aprobado', '2025-06-19', 'd282be61449fbf069b2fefa897425548', 'pendiente', 'OK'),
(69, 'EQ-20250619120401-561', 'Maria Paula Martinez Ortiz', '1018500154', 'Mariapaulam0110@gmail.com', '[\"audifonos\"]', '{\"audifonos\":\"Logitech H390\"}', '2025-06-19 00:00:00', 'aprobado', '2025-06-19', 'f6e8bb1c5f36125f331fec21a0aa827b', 'pendiente', NULL),
(70, 'EQ-20250619120434-412', 'Ivonne Juliet Peña Lopez', '63554787', 'ivonne.pena@dlivrd.io', '[\"audifonos\"]', '{\"audifonos\":\"Logitech H390\"}', '2025-06-19 00:00:00', 'pendiente', NULL, 'cfe01073ce026bac9f534490c78b0207', 'pendiente', NULL),
(71, 'EQ-20250625083726-331', 'Ivonne Juliet Peña Lopez', '63554787', 'ivonne.pena@dlivrd.io', '[\"computador\"]', '{\"computador\":\"Macbook Air, cargador y teclado (Pendiente cable teclado)\"}', '2025-06-25 00:00:00', 'pendiente', NULL, 'bd08d92cffe5cad7dd370204d6661e5e', 'pendiente', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `paginas_abiertas`
--

CREATE TABLE `paginas_abiertas` (
  `id` int(11) NOT NULL,
  `id_conexion` varchar(255) DEFAULT NULL,
  `pagina` varchar(255) DEFAULT NULL,
  `fecha_apertura` datetime DEFAULT NULL,
  `fecha_cierre` datetime DEFAULT NULL,
  `estado` enum('abierta','cerrada') DEFAULT 'abierta',
  `uso_memoria` decimal(10,2) DEFAULT 0.00,
  `tiempo_cpu` decimal(10,4) DEFAULT 0.0000
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proyecto_actividad`
--

CREATE TABLE `proyecto_actividad` (
  `id` int(11) NOT NULL,
  `id_actividad` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `nombre_actividad` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `precio` decimal(10,2) DEFAULT 0.00,
  `fecha_creacion` timestamp NULL DEFAULT current_timestamp(),
  `duracion` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `proyecto_actividad`
--

INSERT INTO `proyecto_actividad` (`id`, `id_actividad`, `id_cliente`, `nombre_actividad`, `descripcion`, `precio`, `fecha_creacion`, `duracion`) VALUES
(2, 6, 1, 'Prueba2', 'hola245', 10.00, '2025-06-04 19:29:20', 3600),
(3, 7, 1, 'Prueba3', 'hola3', 12.00, '2025-06-04 19:55:25', 7200),
(4, 8, 6, 'Prueba4', 'hola4', 10.00, '2025-06-04 20:03:07', 3600),
(100, 110, 1, 'PRUEBA1', 'hola1', 15.00, '2025-06-04 16:07:07', 3600),
(101, 9, 1, 'Prueba 5', 'Test12', 1.00, '2025-06-13 11:47:24', 3600);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_actividades`
--

CREATE TABLE `tb_actividades` (
  `id` int(11) NOT NULL,
  `cliente` varchar(100) DEFAULT NULL,
  `actividad` varchar(100) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `duracion` int(11) DEFAULT NULL,
  `cobrado` tinyint(1) NOT NULL DEFAULT 0,
  `cobrado_general` tinyint(1) NOT NULL DEFAULT 0,
  `fecha` datetime DEFAULT NULL,
  `hora_inicio` time DEFAULT NULL,
  `hora_fin` time DEFAULT NULL,
  `id_usuario` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `tb_actividades`
--

INSERT INTO `tb_actividades` (`id`, `cliente`, `actividad`, `descripcion`, `duracion`, `cobrado`, `cobrado_general`, `fecha`, `hora_inicio`, `hora_fin`, `id_usuario`) VALUES
(1, 'Cliente A', 'Prueba 5', '', 10800, 0, 1, '2025-06-24 00:00:00', '08:00:00', '11:00:00', 2147483654),
(2, 'Cliente A', 'Prueba 5', '', 36060, 0, 1, '2025-06-24 00:00:00', '08:43:00', '18:44:00', 1),
(3, 'Cliente A', 'Prueba 5', '', 32400, 0, 1, '2025-06-28 00:00:00', '10:44:00', '19:44:00', 2147483654),
(4, 'Cliente A', 'Prueba 5', '', 32400, 0, 1, '2025-06-25 00:00:00', '08:40:00', '17:40:00', 2147483654),
(5, 'Cliente A', 'Prueba 5', '', 32400, 0, 1, '2025-06-24 00:00:00', '08:43:00', '17:43:00', 2147483654),
(6, 'Cliente A', 'Prueba 5', 'g', 5, 1, 0, '2025-06-26 12:15:50', '12:15:45', '12:15:50', 1),
(7, 'Cliente A', 'Prueba 5', '2', 34, 0, 0, '2025-06-27 10:21:38', '10:21:04', '10:21:38', 1),
(8, 'Cliente A', 'Prueba 5', '1', 76, 0, 0, '2025-06-27 14:23:40', '14:22:24', '14:23:40', 1),
(9, 'Cliente A', 'Prueba 5', 'e', 103, 0, 0, '2025-06-27 14:36:32', '14:34:49', '14:36:32', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_actividad_diaria`
--

CREATE TABLE `tb_actividad_diaria` (
  `id` int(11) NOT NULL,
  `id_usuario` bigint(20) UNSIGNED NOT NULL,
  `fecha` date NOT NULL,
  `total_paginas` int(11) DEFAULT 0,
  `total_cpu` decimal(10,2) DEFAULT 0.00,
  `total_ram` decimal(10,2) DEFAULT 0.00,
  `tiempo_conectado` time DEFAULT '00:00:00',
  `cantidad_conexiones` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `tb_actividad_diaria`
--

INSERT INTO `tb_actividad_diaria` (`id`, `id_usuario`, `fecha`, `total_paginas`, `total_cpu`, `total_ram`, `tiempo_conectado`, `cantidad_conexiones`) VALUES
(263, 1, '2025-05-16', 13, 0.00, 0.00, '00:14:07', 12),
(264, 2147483647, '2025-05-16', 2, 0.00, 0.00, '01:29:17', 2),
(278, 1, '2025-05-17', 1, 0.00, 0.00, '00:00:16', 1),
(279, 1, '2025-05-19', 8, 0.00, 0.00, '03:04:17', 5),
(283, 2147483647, '2025-05-19', 3, 0.00, 0.00, '00:11:13', 3),
(287, 1, '2025-05-20', 1, 0.00, 0.00, '00:09:51', 0),
(288, 2147483647, '2025-05-20', 1, 0.00, 0.00, '00:22:15', 3),
(292, 1, '2025-05-21', 6, 0.00, 0.00, '02:48:40', 4),
(293, 2147483647, '2025-05-21', 1, 0.00, 0.00, '00:38:08', 1),
(303, 1, '2025-05-22', 1, 0.00, 0.00, '01:31:04', 0),
(304, 1, '2025-05-23', 3, 0.00, 0.00, '00:59:45', 3),
(307, 1, '2025-05-26', 3, 0.00, 0.00, '05:27:40', 2),
(310, 2147483647, '2025-05-26', 1, 0.00, 0.00, '00:16:20', 0),
(311, 1, '2025-05-27', 2, 0.00, 0.00, '01:15:50', 1),
(313, 1, '2025-05-28', 1, 0.00, 0.00, '01:26:28', 0),
(314, 1, '2025-05-29', 8, 0.00, 0.00, '01:15:34', 8),
(315, 2147483647, '2025-05-29', 1, 0.00, 0.00, '00:02:01', 1),
(323, 1, '2025-05-30', 4, 0.00, 0.00, '03:58:34', 5),
(325, 2147483647, '2025-05-30', 2, 0.00, 0.00, '03:04:08', 2),
(330, 1, '2025-06-03', 35, 0.00, 0.00, '04:40:55', 34),
(364, 1, '2025-06-04', 1, 0.00, 0.00, '01:15:02', 0),
(365, 1, '2025-06-05', 18, 0.00, 0.00, '02:06:52', 21),
(386, 1, '2025-06-06', 64, 0.00, 0.00, '06:48:19', 64),
(450, 1, '2025-06-09', 42, 0.00, 0.00, '08:01:59', 42),
(492, 1, '2025-06-10', 40, 0.00, 0.00, '06:47:22', 40),
(532, 1, '2025-06-11', 58, 0.00, 0.00, '07:37:49', 58),
(539, 2147483647, '2025-06-11', 1, 0.00, 0.00, '00:09:09', 0),
(591, 1, '2025-06-12', 60, 0.00, 0.00, '04:29:35', 59),
(612, 2147483647, '2025-06-12', 2, 0.00, 0.00, '00:26:01', 16),
(668, 1, '2025-06-13', 46, 0.00, 0.00, '06:19:49', 43),
(669, 2147483647, '2025-06-13', 4, 0.00, 0.00, '00:56:35', 3),
(715, 1, '2025-06-16', 52, 0.00, 0.00, '06:13:39', 49),
(761, 2147483647, '2025-06-16', 2, 0.00, 0.00, '00:29:13', 2),
(766, 2147483647, '2025-06-17', 1, 0.00, 0.00, '00:19:38', 26),
(772, 1, '2025-06-17', 47, 0.00, 0.00, '05:04:28', 47),
(840, 1, '2025-06-18', 53, 0.00, 0.00, '06:10:57', 52),
(850, 2147483647, '2025-06-18', 12, 0.00, 0.00, '00:23:49', 5),
(897, 1, '2025-06-19', 22, 0.00, 0.00, '01:34:15', 17),
(901, 2147483647, '2025-06-19', 10, 0.00, 0.00, '01:08:08', 35),
(956, 2147483647, '2025-06-20', 1, 0.00, 0.00, '00:32:20', 3),
(957, 1, '2025-06-20', 52, 0.00, 0.00, '06:31:03', 52),
(1013, 2147483647, '2025-06-24', 3, 0.00, 0.00, '00:59:41', 3),
(1014, 1, '2025-06-24', 74, 0.00, 0.00, '07:23:20', 68),
(1084, 2147483654, '2025-06-25', 20, 0.00, 0.00, '02:44:38', 23),
(1085, 1, '2025-06-25', 17, 0.00, 0.00, '06:22:50', 27),
(1136, 2147483654, '2025-06-26', 17, 0.00, 0.00, '02:38:34', 16),
(1137, 1, '2025-06-26', 13, 0.00, 0.00, '01:53:59', 15),
(1159, 2147483650, '2025-06-26', 12, 0.00, 0.00, '01:35:25', 12),
(1162, 2147483652, '2025-06-26', 0, 0.00, 0.00, '00:48:13', 1),
(1180, 2147483650, '2025-06-27', 8, 0.00, 0.00, '01:02:19', 7),
(1181, 1, '2025-06-27', 31, 0.00, 0.00, '03:30:09', 28),
(1182, 2147483654, '2025-06-27', 1, 0.00, 0.00, '00:16:40', 0),
(1212, 2147483652, '2025-06-27', 1, 0.00, 0.00, '00:01:58', 1),
(1219, 2147483652, '2025-07-01', 37, 0.00, 0.00, '04:57:26', 37),
(1220, 2147483650, '2025-07-01', 2, 0.00, 0.00, '00:01:36', 2),
(1258, 2147483652, '2025-07-02', 1, 0.00, 0.00, '00:07:34', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_ausencias`
--

CREATE TABLE `tb_ausencias` (
  `id` int(11) NOT NULL,
  `numero_formulario` varchar(50) NOT NULL,
  `id_trabajador` int(11) NOT NULL,
  `id_campana` int(11) NOT NULL,
  `id_jefe` int(11) NOT NULL,
  `tipo_ausencia` varchar(50) NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `observaciones` text DEFAULT NULL,
  `tareas` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`tareas`)),
  `comprobantes` text DEFAULT NULL,
  `fecha_registro` timestamp NULL DEFAULT current_timestamp(),
  `estado_team_lead` varchar(50) DEFAULT 'Pendiente',
  `fecha_team_lead` datetime DEFAULT NULL,
  `estado_rrhh` varchar(50) DEFAULT 'Pendiente',
  `fecha_rrhh` datetime DEFAULT NULL,
  `razon_rechazo` text DEFAULT NULL,
  `nombre` varchar(255) DEFAULT NULL,
  `documento` varchar(50) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `tb_ausencias`
--

INSERT INTO `tb_ausencias` (`id`, `numero_formulario`, `id_trabajador`, `id_campana`, `id_jefe`, `tipo_ausencia`, `fecha_inicio`, `fecha_fin`, `observaciones`, `tareas`, `comprobantes`, `fecha_registro`, `estado_team_lead`, `fecha_team_lead`, `estado_rrhh`, `fecha_rrhh`, `razon_rechazo`, `nombre`, `documento`, `email`) VALUES
(7, 'FORM-68557c9d33fa9', 148, 4, 80, 'Licencia no remunerada - Permiso personal', '2025-06-20', '2025-06-20', 'I would like to take an early out today for 1 hour. I need to finish my shift by 2:30 PM EST (1:30 PM CSR).', '[]', '', '2025-06-20 10:22:05', 'aprobado', '2025-06-20 15:22:19', 'aprobado', '2025-06-20 15:23:06', NULL, 'santiago rodriguez lopez', '1005483223', 'santiaguito.srl76@gmail.com'),
(8, 'FORM-6859388e807ee', 135, 4, 84, 'Vacaciones', '2025-09-02', '2025-09-06', 'Viaje Familiar, Ya compre los vuelos:))', '[{\"tarea\":\"Support\",\"responsable\":\"Co-workers \",\"fecha\":\"2025-09-02\"}]', '', '2025-06-23 06:20:46', 'aprobado', '2025-06-23 13:43:45', 'aprobado', '2025-06-24 13:37:32', NULL, 'Fabian Andres Arenas Olave', '1005157464', 'fabian.arenas@dlivrd.io'),
(9, 'FORM-685ac35216f7a', 80, 4, 52, 'Vacaciones', '2025-07-25', '2025-07-25', 'Hello, I am requesting this vacation day, as Vacaciones Anticipadas! Thanks', '[{\"tarea\":\"Closing Shift\",\"responsable\":\"No one\",\"fecha\":\"2025-07-25\"}]', '', '2025-06-19 14:33:32', 'aprobado', '2025-06-24 15:42:51', 'Pendiente', NULL, NULL, 'Juliana Carolina Quiroz Caceres', '1098823097', 'juliana.quiroz@dlivrd.io'),
(10, 'FORM-685afc5af221d', 148, 4, 80, 'Citas medicas', '2025-06-26', '2025-06-26', 'de 1 de la tarde a 3 de la tarde ', '[]', 'uploads/ausencias/FORM-685afc5af221d_WhatsApp Image 2025-06-24 at 3.27.16 PM.jpeg,uploads/ausencias/FORM-685afc5af221d_WhatsApp Image 2025-06-24 at 3.26.53 PM.jpeg', '2025-06-24 14:28:26', 'aprobado', '2025-06-24 19:29:04', 'aprobado', '2025-06-24 22:29:38', NULL, 'santiago rodriguez lopez', '1005483223', 'santiaguito.srl76@gmail.com'),
(11, 'FORM-685b0f1395065', 124, 4, 80, 'Vacaciones', '2025-08-13', '2025-08-17', 'Me gustaría solicitar un permiso de vacaciones para asistir a un evento académico de gran relevancia para mi desarrollo profesional, laboral y académico.\r\n\r\nSe trata del evento geocientífico más importante del país, un espacio de encuentro para la ciencia, la exploración y el conocimiento geológico. Contará con conferencias, presentaciones de pósters, muestra comercial, conversatorios, oportunidades de networking, salidas de campo, entre otras actividades. Además de fortalecer mis conocimientos, me permitirá establecer conexiones valiosas con profesionales de la industria, el gremio, el gobierno, la academia y la sociedad civil, tanto a nivel nacional como internacional.\r\n\r\nEstoy convencido de que esta experiencia enriquecerá mi perfil profesional y me permitirá aplicar nuevos aprendizajes en mi labor diaria. Agradezco de antemano tu comprensión y apoyo para poder asistir.', '[{\"tarea\":\"Support Specialist\",\"responsable\":\"\\u00c1lvaro Sarmiento\",\"fecha\":\"2025-08-13\"}]', 'uploads/ausencias/FORM-685b0f1395065_XX congreso de Geologia.png', '2025-06-24 15:48:19', 'aprobado', '2025-06-24 20:54:33', 'Pendiente', NULL, NULL, 'Álvaro Andrés Sarmiento Rueda', '1098806024', 'alvarosarmiento1998@gmail.com'),
(12, 'FORM-685b12016028a', 135, 4, 84, 'Incapacidad medica', '2025-06-24', '2025-06-25', 'Adjunto mi incapacidad medica que cubre los dias 24 y 25 de Junio. El lunes festivo 23 de Junio trabaje mi jornada laboral completa. Gracias:)', '[]', 'uploads/ausencias/FORM-685b12016028a_Incapacidad médica Fabian Arenas Olave.pdf', '2025-06-24 16:00:49', 'aprobado', '2025-06-25 10:11:22', 'aprobado', '2025-06-25 13:46:05', NULL, 'Fabian Andres Arenas Olave', '1005157464', 'fabian.arenas@dlivrd.io'),
(13, 'FORM-685c1fefd9d9d', 148, 4, 80, 'Incapacidad medica', '2025-06-25', '2025-06-25', 'Me caí de la moto y sufrí raspaduras en la mano, el brazo y las piernas. En este momento no puedo usar el computador debido a las heridas en la mano. Fui a urgencias hoy y me asignaron una cita prioritaria para mañana, donde me valorarán y me emitirán la incapacidad médica en dado caso de que aplique.\r\n\r\nPor ahora, adjunto las imágenes del triage y de mis heridas como soporte provisional para justificar mi ausencia de hoy, tal como me indicaron en el centro médico.\r\n\r\nQuedo atento a cualquier indicación y muchas gracias por la comprensión.', '[]', 'uploads/ausencias/FORM-685c1fefd9d9d_2a3a587e-e00e-4258-9aef-5542b1b52cd2.jpg,uploads/ausencias/FORM-685c1fefd9d9d_52cbf3d5-00d0-460a-a401-7f9c29292181.jpg,uploads/ausencias/FORM-685c1fefd9d9d_WhatsApp Image 2025-06-25 at 1.31.27 AM.jpeg,uploads/ausencias/FORM-685c1fefd9d9d_WhatsApp Image 2025-06-25 at 11.49.44 AM.jpeg,uploads/ausencias/FORM-685c1fefd9d9d_WhatsApp Image 2025-06-25 at 11.50.17 AM.jpeg,uploads/ausencias/FORM-685c1fefd9d9d_WhatsApp Image 2025-06-25 at 11.50.02 AM.jpeg', '2025-06-25 11:12:31', 'aprobado', '2025-06-25 16:13:54', 'aprobado', '2025-06-25 16:15:44', NULL, 'santiago rodriguez lopez', '1005483223', 'santiaguito.srl76@gmail.com'),
(14, 'FORM-685c624ec10d6', 121, 4, 82, 'Incapacidad medica', '2025-06-25', '2025-06-26', '', '[]', 'uploads/ausencias/FORM-685c624ec10d6_Incapacidad..pdf', '2025-06-25 15:55:42', 'aprobado', '2025-06-25 21:11:56', 'aprobado', '2025-06-25 21:35:37', NULL, 'Adriana Graterol', '1127573470', 'adriana.graterol@dlivrd.io'),
(15, 'FORM-685c988793cfa', 148, 4, 80, 'Trabajo desde casa', '2025-06-26', '2025-06-26', 'El día de mañana tenía pensado ir a la cita a sacar la incapacidad, sin embargo tengo otra cita de rayos x qué llevo esperando casi un mes, y amabas quedaron casi que para el mismo tiempo, entonces tendré que ir a la cita de rayos x y no podré sacar la incapacidad mañana si no el viernes en dado caso, yo ya tenia aprobado el permiso de la cita sin embargo quisiera saber si podría trabajar desde casa ya que no me siento bien por el golpe y el viernes sacaría la incapacidad ', '[]', 'uploads/ausencias/FORM-685c988793cfa_IMG_0654.png', '2025-06-25 19:47:03', 'aprobado', '2025-06-26 13:05:05', 'aprobado', '2025-06-26 17:36:56', NULL, 'Santiago Rodríguez López ', '1005483223', 'santiaguito.srl76@gmail.com'),
(16, 'FORM-685e82e04efef', 149, 4, 84, 'Licencia no remunerada - Permiso personal', '2025-09-15', '2025-09-15', '', '[]', '', '2025-06-27 06:39:12', 'aprobado', '2025-06-27 18:07:50', 'Pendiente', NULL, NULL, 'Yersi Catalina Mendez Villamizar', '1005329286', 'catalinamendew@gmail.com'),
(17, 'FORM-685e9c520de02', 148, 4, 80, 'Incapacidad medica', '2025-06-27', '2025-06-27', 'Incapacidad por el día de hoy viernes 27 de junio ', '[]', 'uploads/ausencias/FORM-685e9c520de02_IMG_0656.jpeg,uploads/ausencias/FORM-685e9c520de02_IMG_0655.jpeg', '2025-06-27 08:27:46', 'aprobado', '2025-06-27 13:30:32', 'aprobado', '2025-06-27 14:52:11', NULL, 'Santiago Rodríguez López ', '1005483223', 'santiaguito.srl76@gmail.com'),
(18, 'FORM-685eb125074ec', 16, 21, 15, 'Citas medicas', '2025-06-02', '2025-06-06', '1', '[]', '', '2025-06-27 09:56:37', 'Pendiente', NULL, 'Pendiente', NULL, NULL, 's', '1', 'styvenmunera3@gmail.com'),
(19, 'FORM-685ef7d4d08ea', 69, 12, 49, 'Vacaciones', '2025-07-14', '2025-07-21', 'Las tareas y responsables quedarán descritas de manera detalla en un documento el cual estaré enviando días antes al día de inicio de mis vacaciones. Gracias ', '[]', '', '2025-06-27 14:58:12', 'aprobado', '2025-06-27 21:31:48', 'aprobado', '2025-07-01 16:02:18', NULL, 'Sthefany Juliana Arias Gomez', '1005150683', 'sthefanygomez19@gmail.com'),
(20, 'FORM-685efb152d4bf', 64, 12, 49, 'Festivo libre', '2025-06-28', '2025-06-28', 'Las 5 horas del día sábado se compensarán durante la semana del 23 al 27 junio ', '[]', '', '2025-06-27 15:12:05', 'aprobado', '2025-06-27 20:30:02', 'aprobado', '2025-07-01 16:02:06', NULL, 'Kathe Quintero Samaca ', '1095787771', 'kathe.quintero.0101@gmail.com'),
(21, 'FORM-6861c85023672', 149, 4, 80, 'Licencia no remunerada - Permiso personal', '2025-09-26', '2025-09-28', '', '[]', '', '2025-06-29 18:12:16', 'Pendiente', NULL, 'Pendiente', NULL, NULL, 'Yersi Catalina Méndez Villamizar', '1005329286', 'catalina.mendez@dlivrd.io'),
(22, 'FORM-6862809e02e98', 144, 4, 83, 'Trabajo desde casa', '2025-06-30', '2025-06-30', 'Por motivo de malestar estomacal, solicito trabajar desde casa el día de hoy.', '[]', '', '2025-06-30 07:18:38', 'aprobado', '2025-06-30 14:24:53', 'aprobado', '2025-06-30 23:15:18', NULL, 'Camila Villanueva', '1098711545', 'macaviserr@gmail.com'),
(23, 'FORM-686418f8d2072', 68, 17, 49, 'Licencia de luto', '2025-07-02', '2025-07-03', '', '[]', '', '2025-07-01 12:20:56', 'aprobado', '2025-07-01 18:40:30', 'Pendiente', NULL, NULL, 'Sofia Vitta Serrano', '1005288036', 'sofivita02@gmail.com');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_campanas`
--

CREATE TABLE `tb_campanas` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `estado` enum('Activa','Inactiva') DEFAULT 'Activa',
  `fecha_registro` timestamp NULL DEFAULT current_timestamp(),
  `id_padre` int(11) DEFAULT NULL,
  `id_responsable` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `tb_campanas`
--

INSERT INTO `tb_campanas` (`id`, `nombre`, `descripcion`, `estado`, `fecha_registro`, `id_padre`, `id_responsable`) VALUES
(1, 'Direccion de Proyectos\r\n\r\n\r\n', NULL, 'Activa', '2025-04-07 15:03:22', 5, 49),
(2, ' Direccion de Marketing', NULL, 'Activa', '2025-04-07 15:03:22', 5, 49),
(3, 'dlivrd', '', 'Activa', '2025-04-08 15:25:28', 5, 49),
(4, 'dlivrd OPS ', NULL, 'Activa', '2025-04-09 14:55:34', 3, 52),
(5, 'GCS', NULL, 'Activa', '2025-04-21 14:55:01', NULL, NULL),
(12, 'Verde Colab', NULL, 'Activa', '2025-05-06 18:17:42', 5, 49),
(13, 'F1rst Customer', NULL, 'Activa', '2025-05-06 18:22:06', 1, 49),
(14, 'FoodNet', NULL, 'Activa', '2025-05-06 18:24:04', 1, 49),
(15, 'D RRHH', NULL, 'Activa', '2025-05-08 16:22:30', 5, 49),
(16, 'Houston Delivery', NULL, 'Activa', '2025-05-08 16:30:05', 1, 49),
(17, 'Agente Ejecutivo', NULL, 'Activa', '2025-05-08 16:30:32', 1, 49),
(18, 'GCS-IT-DC-ACC', NULL, 'Activa', '2025-05-08 16:30:55', 1, 49),
(21, 'GigSafe', NULL, 'Activa', '2025-06-19 17:19:31', 5, 49);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_carrusel`
--

CREATE TABLE `tb_carrusel` (
  `id` int(11) NOT NULL,
  `titulo` varchar(255) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `imagen_url` varchar(255) DEFAULT NULL,
  `orden` int(11) DEFAULT 0,
  `estado` enum('activo','inactivo') DEFAULT 'activo',
  `creado_en` datetime DEFAULT current_timestamp(),
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_festivos`
--

CREATE TABLE `tb_festivos` (
  `id` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `fyh_creacion` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `tb_festivos`
--

INSERT INTO `tb_festivos` (`id`, `fecha`, `descripcion`, `fyh_creacion`) VALUES
(1, '2025-06-25', NULL, '2025-06-25 17:45:16'),
(2, '2025-05-09', NULL, '2025-06-25 18:40:57');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_horario`
--

CREATE TABLE `tb_horario` (
  `id` int(11) NOT NULL,
  `id_usuario` int(10) UNSIGNED DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `hora_inicio_turno` datetime DEFAULT NULL,
  `hora_fin_turno` datetime DEFAULT NULL,
  `hora_inicio_extra` datetime DEFAULT NULL,
  `hora_fin_extra` datetime DEFAULT NULL,
  `hora_inicio_break1` datetime DEFAULT NULL,
  `hora_fin_break1` datetime DEFAULT NULL,
  `hora_inicio_break2` datetime DEFAULT NULL,
  `hora_fin_break2` datetime DEFAULT NULL,
  `hora_inicio_break3` datetime DEFAULT NULL,
  `hora_fin_break3` datetime DEFAULT NULL,
  `fyh_creacion` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `tb_horario`
--

INSERT INTO `tb_horario` (`id`, `id_usuario`, `fecha`, `hora_inicio_turno`, `hora_fin_turno`, `hora_inicio_extra`, `hora_fin_extra`, `hora_inicio_break1`, `hora_fin_break1`, `hora_inicio_break2`, `hora_fin_break2`, `hora_inicio_break3`, `hora_fin_break3`, `fyh_creacion`) VALUES
(8, 2147483653, '2025-06-10', '2025-06-10 08:00:00', '2025-06-10 16:00:00', NULL, NULL, '2025-06-10 10:00:00', '2025-06-10 10:15:00', '2025-06-10 12:00:00', '2025-06-10 12:30:00', '2025-06-10 14:45:00', '2025-06-10 15:00:00', '2025-06-25 18:39:10'),
(9, 1, '2025-06-13', '2025-06-13 08:00:00', '2025-06-13 16:00:00', NULL, NULL, '2025-06-13 10:00:00', '2025-06-13 10:15:00', '2025-06-13 12:00:00', '2025-06-13 12:30:00', '2025-06-13 14:45:00', '2025-06-13 15:00:00', '2025-06-25 18:39:10'),
(10, 2147483654, '2025-06-10', '2025-06-10 08:00:00', '2025-06-10 16:00:00', NULL, NULL, '2025-06-10 10:00:00', '2025-06-10 10:15:00', '2025-06-10 12:00:00', '2025-06-10 12:30:00', '2025-06-10 14:45:00', '2025-06-10 15:00:00', '2025-06-25 18:39:10'),
(11, 2147483655, '2025-05-31', '2025-05-31 08:00:00', '2025-05-31 16:00:00', NULL, NULL, '2025-05-31 10:00:00', '2025-05-31 10:15:00', '2025-05-31 12:00:00', '2025-05-31 12:30:00', '2025-05-31 14:45:00', '2025-05-31 15:00:00', '2025-06-25 18:39:10'),
(12, 2147483650, '2025-06-29', '2025-06-29 08:00:00', '2025-06-29 16:00:00', NULL, NULL, '2025-06-29 10:00:00', '2025-06-29 10:15:00', '2025-06-29 12:00:00', '2025-06-29 12:30:00', '2025-06-29 14:45:00', '2025-06-29 15:00:00', '2025-06-25 18:39:10'),
(13, 2147483655, '2025-05-20', '2025-05-20 08:00:00', '2025-05-20 16:00:00', NULL, NULL, '2025-05-20 10:00:00', '2025-05-20 10:15:00', '2025-05-20 12:00:00', '2025-05-20 12:30:00', '2025-05-20 14:45:00', '2025-05-20 15:00:00', '2025-06-25 18:39:10'),
(14, 2147483654, '2025-05-31', '2025-05-31 08:00:00', '2025-05-31 16:00:00', NULL, NULL, '2025-05-31 10:00:00', '2025-05-31 10:15:00', '2025-05-31 12:00:00', '2025-05-31 12:30:00', '2025-05-31 14:45:00', '2025-05-31 15:00:00', '2025-06-25 18:39:10'),
(15, 2147483650, '2025-06-01', '2025-06-01 08:00:00', '2025-06-01 16:00:00', NULL, NULL, '2025-06-01 10:00:00', '2025-06-01 10:15:00', '2025-06-01 12:00:00', '2025-06-01 12:30:00', '2025-06-01 14:45:00', '2025-06-01 15:00:00', '2025-06-25 18:39:10'),
(16, 1, '2025-06-04', '2025-06-04 08:00:00', '2025-06-04 16:00:00', NULL, NULL, '2025-06-04 10:00:00', '2025-06-04 10:15:00', '2025-06-04 12:00:00', '2025-06-04 12:30:00', '2025-06-04 14:45:00', '2025-06-04 15:00:00', '2025-06-25 18:39:10'),
(17, 2147483655, '2025-05-25', '2025-05-25 08:00:00', '2025-05-25 16:00:00', NULL, NULL, '2025-05-25 10:00:00', '2025-05-25 10:15:00', '2025-05-25 12:00:00', '2025-05-25 12:30:00', '2025-05-25 14:45:00', '2025-05-25 15:00:00', '2025-06-25 18:39:10'),
(18, 2147483650, '2025-06-30', '2025-06-30 08:00:00', '2025-06-30 16:00:00', NULL, NULL, '2025-06-30 10:00:00', '2025-06-30 10:15:00', '2025-06-30 12:00:00', '2025-06-30 12:30:00', '2025-06-30 14:45:00', '2025-06-30 15:00:00', '2025-06-25 18:39:10'),
(19, 2147483650, '2025-06-06', '2025-06-06 08:00:00', '2025-06-06 16:00:00', NULL, NULL, '2025-06-06 10:00:00', '2025-06-06 10:15:00', '2025-06-06 12:00:00', '2025-06-06 12:30:00', '2025-06-06 14:45:00', '2025-06-06 15:00:00', '2025-06-25 18:39:10'),
(20, 2147483653, '2025-06-28', '2025-06-28 08:00:00', '2025-06-28 16:00:00', NULL, NULL, '2025-06-28 10:00:00', '2025-06-28 10:15:00', '2025-06-28 12:00:00', '2025-06-28 12:30:00', '2025-06-28 14:45:00', '2025-06-28 15:00:00', '2025-06-25 18:39:10'),
(21, 2147483650, '2025-06-29', '2025-06-29 08:00:00', '2025-06-29 16:00:00', NULL, NULL, '2025-06-29 10:00:00', '2025-06-29 10:15:00', '2025-06-29 12:00:00', '2025-06-29 12:30:00', '2025-06-29 14:45:00', '2025-06-29 15:00:00', '2025-06-25 18:39:10'),
(22, 2147483653, '2025-05-02', '2025-05-02 08:00:00', '2025-05-02 16:00:00', NULL, NULL, '2025-05-02 10:00:00', '2025-05-02 10:15:00', '2025-05-02 12:00:00', '2025-05-02 12:30:00', '2025-05-02 14:45:00', '2025-05-02 15:00:00', '2025-06-25 18:39:10'),
(23, 2147483653, '2025-06-25', '2025-06-25 08:00:00', '2025-06-25 16:00:00', NULL, NULL, '2025-06-25 10:00:00', '2025-06-25 10:15:00', '2025-06-25 12:00:00', '2025-06-25 12:30:00', '2025-06-25 14:45:00', '2025-06-25 15:00:00', '2025-06-25 18:39:10'),
(24, 2147483655, '2025-05-04', '2025-05-04 08:00:00', '2025-05-04 16:00:00', NULL, NULL, '2025-05-04 10:00:00', '2025-05-04 10:15:00', '2025-05-04 12:00:00', '2025-05-04 12:30:00', '2025-05-04 14:45:00', '2025-05-04 15:00:00', '2025-06-25 18:39:10'),
(25, 2147483652, '2025-06-19', '2025-06-19 08:00:00', '2025-06-19 16:00:00', NULL, NULL, '2025-06-19 10:00:00', '2025-06-19 10:15:00', '2025-06-19 12:00:00', '2025-06-19 12:30:00', '2025-06-19 14:45:00', '2025-06-19 15:00:00', '2025-06-25 18:39:10'),
(26, 2147483652, '2025-05-21', '2025-05-21 08:00:00', '2025-05-21 16:00:00', NULL, NULL, '2025-05-21 10:00:00', '2025-05-21 10:15:00', '2025-05-21 12:00:00', '2025-05-21 12:30:00', '2025-05-21 14:45:00', '2025-05-21 15:00:00', '2025-06-25 18:39:10'),
(27, 2147483654, '2025-06-14', '2025-06-14 08:00:00', '2025-06-14 16:00:00', NULL, NULL, '2025-06-14 10:00:00', '2025-06-14 10:15:00', '2025-06-14 12:00:00', '2025-06-14 12:30:00', '2025-06-14 14:45:00', '2025-06-14 15:00:00', '2025-06-25 18:39:10'),
(28, 2147483654, '2025-06-07', '2025-06-07 08:00:00', '2025-06-07 16:00:00', NULL, NULL, '2025-06-07 10:00:00', '2025-06-07 10:15:00', '2025-06-07 12:00:00', '2025-06-07 12:30:00', '2025-06-07 14:45:00', '2025-06-07 15:00:00', '2025-06-25 18:39:10'),
(29, 2147483652, '2025-05-16', '2025-05-16 08:00:00', '2025-05-16 16:00:00', NULL, NULL, '2025-05-16 10:00:00', '2025-05-16 10:15:00', '2025-05-16 12:00:00', '2025-05-16 12:30:00', '2025-05-16 14:45:00', '2025-05-16 15:00:00', '2025-06-25 18:39:10'),
(30, 2147483654, '2025-06-25', '2025-06-25 08:00:00', '2025-06-25 16:00:00', NULL, NULL, '2025-06-25 10:00:00', '2025-06-25 10:15:00', '2025-06-25 12:00:00', '2025-06-25 12:30:00', '2025-06-25 14:45:00', '2025-06-25 15:00:00', '2025-06-25 18:39:10'),
(31, 1, '2025-05-12', '2025-05-12 08:00:00', '2025-05-12 16:00:00', NULL, NULL, '2025-05-12 10:00:00', '2025-05-12 10:15:00', '2025-05-12 12:00:00', '2025-05-12 12:30:00', '2025-05-12 14:45:00', '2025-05-12 15:00:00', '2025-06-25 18:39:10'),
(32, 1, '2025-05-11', '2025-05-11 08:00:00', '2025-05-11 16:00:00', NULL, NULL, '2025-05-11 10:00:00', '2025-05-11 10:15:00', '2025-05-11 12:00:00', '2025-05-11 12:30:00', '2025-05-11 14:45:00', '2025-05-11 15:00:00', '2025-06-25 18:39:10'),
(33, 2147483655, '2025-05-26', '2025-05-26 08:00:00', '2025-05-26 16:00:00', NULL, NULL, '2025-05-26 10:00:00', '2025-05-26 10:15:00', '2025-05-26 12:00:00', '2025-05-26 12:30:00', '2025-05-26 14:45:00', '2025-05-26 15:00:00', '2025-06-25 18:39:10'),
(34, 2147483653, '2025-06-06', '2025-06-06 08:00:00', '2025-06-06 16:00:00', NULL, NULL, '2025-06-06 10:00:00', '2025-06-06 10:15:00', '2025-06-06 12:00:00', '2025-06-06 12:30:00', '2025-06-06 14:45:00', '2025-06-06 15:00:00', '2025-06-25 18:39:10'),
(35, 2147483652, '2025-06-26', '2025-06-26 08:00:00', '2025-06-26 16:00:00', NULL, NULL, '2025-06-26 10:00:00', '2025-06-26 10:15:00', '2025-06-26 12:00:00', '2025-06-26 12:30:00', '2025-06-26 14:45:00', '2025-06-26 15:00:00', '2025-06-25 18:39:10'),
(36, 2147483650, '2025-06-17', '2025-06-17 08:00:00', '2025-06-17 16:00:00', NULL, NULL, '2025-06-17 10:00:00', '2025-06-17 10:15:00', '2025-06-17 12:00:00', '2025-06-17 12:30:00', '2025-06-17 14:45:00', '2025-06-17 15:00:00', '2025-06-25 18:39:10'),
(37, 2147483650, '2025-05-09', '2025-05-09 08:00:00', '2025-05-09 16:00:00', NULL, NULL, '2025-05-09 10:00:00', '2025-05-09 10:15:00', '2025-05-09 12:00:00', '2025-05-09 12:30:00', '2025-05-09 14:45:00', '2025-05-09 15:00:00', '2025-06-25 18:39:10'),
(38, 2147483652, '2025-05-26', '2025-05-26 08:00:00', '2025-05-26 16:00:00', NULL, NULL, '2025-05-26 10:00:00', '2025-05-26 10:15:00', '2025-05-26 12:00:00', '2025-05-26 12:30:00', '2025-05-26 14:45:00', '2025-05-26 15:00:00', '2025-06-25 18:39:10'),
(39, 2147483655, '2025-06-15', '2025-06-15 08:00:00', '2025-06-15 16:00:00', NULL, NULL, '2025-06-15 10:00:00', '2025-06-15 10:15:00', '2025-06-15 12:00:00', '2025-06-15 12:30:00', '2025-06-15 14:45:00', '2025-06-15 15:00:00', '2025-06-25 18:39:10'),
(40, 2147483652, '2025-06-19', '2025-06-19 08:00:00', '2025-06-19 16:00:00', NULL, NULL, '2025-06-19 10:00:00', '2025-06-19 10:15:00', '2025-06-19 12:00:00', '2025-06-19 12:30:00', '2025-06-19 14:45:00', '2025-06-19 15:00:00', '2025-06-25 18:39:10'),
(41, 1, '2025-05-02', '2025-05-02 08:00:00', '2025-05-02 16:00:00', NULL, NULL, '2025-05-02 10:00:00', '2025-05-02 10:15:00', '2025-05-02 12:00:00', '2025-05-02 12:30:00', '2025-05-02 14:45:00', '2025-05-02 15:00:00', '2025-06-25 18:39:10'),
(42, 2147483652, '2025-06-04', '2025-06-04 08:00:00', '2025-06-04 16:00:00', NULL, NULL, '2025-06-04 10:00:00', '2025-06-04 10:15:00', '2025-06-04 12:00:00', '2025-06-04 12:30:00', '2025-06-04 14:45:00', '2025-06-04 15:00:00', '2025-06-25 18:39:10'),
(43, 2147483650, '2025-06-19', '2025-06-19 08:00:00', '2025-06-19 16:00:00', NULL, NULL, '2025-06-19 10:00:00', '2025-06-19 10:15:00', '2025-06-19 12:00:00', '2025-06-19 12:30:00', '2025-06-19 14:45:00', '2025-06-19 15:00:00', '2025-06-25 18:39:10'),
(44, 2147483654, '2025-06-29', '2025-06-29 08:00:00', '2025-06-29 16:00:00', NULL, NULL, '2025-06-29 10:00:00', '2025-06-29 10:15:00', '2025-06-29 12:00:00', '2025-06-29 12:30:00', '2025-06-29 14:45:00', '2025-06-29 15:00:00', '2025-06-25 18:39:10'),
(45, 2147483653, '2025-05-18', '2025-05-18 08:00:00', '2025-05-18 16:00:00', NULL, NULL, '2025-05-18 10:00:00', '2025-05-18 10:15:00', '2025-05-18 12:00:00', '2025-05-18 12:30:00', '2025-05-18 14:45:00', '2025-05-18 15:00:00', '2025-06-25 18:39:10'),
(46, 2147483652, '2025-05-25', '2025-05-25 08:00:00', '2025-05-25 16:00:00', NULL, NULL, '2025-05-25 10:00:00', '2025-05-25 10:15:00', '2025-05-25 12:00:00', '2025-05-25 12:30:00', '2025-05-25 14:45:00', '2025-05-25 15:00:00', '2025-06-25 18:39:10'),
(47, 2147483653, '2025-05-17', '2025-05-17 08:00:00', '2025-05-17 16:00:00', NULL, NULL, '2025-05-17 10:00:00', '2025-05-17 10:15:00', '2025-05-17 12:00:00', '2025-05-17 12:30:00', '2025-05-17 14:45:00', '2025-05-17 15:00:00', '2025-06-25 18:39:10'),
(48, 2147483655, '2025-06-01', '2025-06-01 08:00:00', '2025-06-01 16:00:00', NULL, NULL, '2025-06-01 10:00:00', '2025-06-01 10:15:00', '2025-06-01 12:00:00', '2025-06-01 12:30:00', '2025-06-01 14:45:00', '2025-06-01 15:00:00', '2025-06-25 18:39:10'),
(49, 2147483653, '2025-06-12', '2025-06-12 08:00:00', '2025-06-12 16:00:00', NULL, NULL, '2025-06-12 10:00:00', '2025-06-12 10:15:00', '2025-06-12 12:00:00', '2025-06-12 12:30:00', '2025-06-12 14:45:00', '2025-06-12 15:00:00', '2025-06-25 18:39:10'),
(50, 2147483655, '2025-05-14', '2025-05-14 08:00:00', '2025-05-14 16:00:00', NULL, NULL, '2025-05-14 10:00:00', '2025-05-14 10:15:00', '2025-05-14 12:00:00', '2025-05-14 12:30:00', '2025-05-14 14:45:00', '2025-05-14 15:00:00', '2025-06-25 18:39:10'),
(51, 2147483650, '2025-06-27', '2025-06-27 08:00:00', '2025-06-27 16:00:00', NULL, NULL, '2025-06-27 10:00:00', '2025-06-27 10:15:00', '2025-06-27 12:00:00', '2025-06-27 12:30:00', '2025-06-27 14:45:00', '2025-06-27 15:00:00', '2025-06-25 18:39:10'),
(52, 2147483654, '2025-05-26', '2025-05-26 08:00:00', '2025-05-26 16:00:00', NULL, NULL, '2025-05-26 10:00:00', '2025-05-26 10:15:00', '2025-05-26 12:00:00', '2025-05-26 12:30:00', '2025-05-26 14:45:00', '2025-05-26 15:00:00', '2025-06-25 18:39:10'),
(53, 2147483652, '2025-06-13', '2025-06-13 08:00:00', '2025-06-13 16:00:00', NULL, NULL, '2025-06-13 10:00:00', '2025-06-13 10:15:00', '2025-06-13 12:00:00', '2025-06-13 12:30:00', '2025-06-13 14:45:00', '2025-06-13 15:00:00', '2025-06-25 18:39:10'),
(54, 2147483655, '2025-05-05', '2025-05-05 08:00:00', '2025-05-05 16:00:00', NULL, NULL, '2025-05-05 10:00:00', '2025-05-05 10:15:00', '2025-05-05 12:00:00', '2025-05-05 12:30:00', '2025-05-05 14:45:00', '2025-05-05 15:00:00', '2025-06-25 18:39:10'),
(55, 2147483654, '2025-06-04', '2025-06-04 08:00:00', '2025-06-04 16:00:00', NULL, NULL, '2025-06-04 10:00:00', '2025-06-04 10:15:00', '2025-06-04 12:00:00', '2025-06-04 12:30:00', '2025-06-04 14:45:00', '2025-06-04 15:00:00', '2025-06-25 18:39:10'),
(56, 2147483653, '2025-06-14', '2025-06-14 08:00:00', '2025-06-14 16:00:00', NULL, NULL, '2025-06-14 10:00:00', '2025-06-14 10:15:00', '2025-06-14 12:00:00', '2025-06-14 12:30:00', '2025-06-14 14:45:00', '2025-06-14 15:00:00', '2025-06-25 18:39:10'),
(57, 2147483654, '2025-05-03', '2025-05-03 08:00:00', '2025-05-03 16:00:00', NULL, NULL, '2025-05-03 10:00:00', '2025-05-03 10:15:00', '2025-05-03 12:00:00', '2025-05-03 12:30:00', '2025-05-03 14:45:00', '2025-05-03 15:00:00', '2025-06-25 18:39:10'),
(58, 2147483654, '2025-06-14', '2025-06-14 08:00:00', '2025-06-14 16:00:00', NULL, NULL, '2025-06-14 10:00:00', '2025-06-14 10:15:00', '2025-06-14 12:00:00', '2025-06-14 12:30:00', '2025-06-14 14:45:00', '2025-06-14 15:00:00', '2025-06-25 18:39:10'),
(59, 2147483650, '2025-05-31', '2025-05-31 08:00:00', '2025-05-31 16:00:00', NULL, NULL, '2025-05-31 10:00:00', '2025-05-31 10:15:00', '2025-05-31 12:00:00', '2025-05-31 12:30:00', '2025-05-31 14:45:00', '2025-05-31 15:00:00', '2025-06-25 18:39:10'),
(60, 2147483652, '2025-05-06', '2025-05-06 08:00:00', '2025-05-06 16:00:00', NULL, NULL, '2025-05-06 10:00:00', '2025-05-06 10:15:00', '2025-05-06 12:00:00', '2025-05-06 12:30:00', '2025-05-06 14:45:00', '2025-05-06 15:00:00', '2025-06-25 18:39:10'),
(61, 2147483652, '2025-05-12', '2025-05-12 08:00:00', '2025-05-12 16:00:00', NULL, NULL, '2025-05-12 10:00:00', '2025-05-12 10:15:00', '2025-05-12 12:00:00', '2025-05-12 12:30:00', '2025-05-12 14:45:00', '2025-05-12 15:00:00', '2025-06-25 18:39:10'),
(62, 2147483652, '2025-05-12', '2025-05-12 08:00:00', '2025-05-12 16:00:00', NULL, NULL, '2025-05-12 10:00:00', '2025-05-12 10:15:00', '2025-05-12 12:00:00', '2025-05-12 12:30:00', '2025-05-12 14:45:00', '2025-05-12 15:00:00', '2025-06-25 18:39:10'),
(63, 2147483653, '2025-05-22', '2025-05-22 08:00:00', '2025-05-22 16:00:00', NULL, NULL, '2025-05-22 10:00:00', '2025-05-22 10:15:00', '2025-05-22 12:00:00', '2025-05-22 12:30:00', '2025-05-22 14:45:00', '2025-05-22 15:00:00', '2025-06-25 18:39:10'),
(64, 2147483652, '2025-05-06', '2025-05-06 08:00:00', '2025-05-06 16:00:00', NULL, NULL, '2025-05-06 10:00:00', '2025-05-06 10:15:00', '2025-05-06 12:00:00', '2025-05-06 12:30:00', '2025-05-06 14:45:00', '2025-05-06 15:00:00', '2025-06-25 18:39:10'),
(65, 2147483653, '2025-05-25', '2025-05-25 08:00:00', '2025-05-25 16:00:00', NULL, NULL, '2025-05-25 10:00:00', '2025-05-25 10:15:00', '2025-05-25 12:00:00', '2025-05-25 12:30:00', '2025-05-25 14:45:00', '2025-05-25 15:00:00', '2025-06-25 18:39:10'),
(66, 2147483654, '2025-05-09', '2025-05-09 08:00:00', '2025-05-09 16:00:00', NULL, NULL, '2025-05-09 10:00:00', '2025-05-09 10:15:00', '2025-05-09 12:00:00', '2025-05-09 12:30:00', '2025-05-09 14:45:00', '2025-05-09 15:00:00', '2025-06-25 18:39:10'),
(67, 2147483653, '2025-06-08', '2025-06-08 08:00:00', '2025-06-08 16:00:00', NULL, NULL, '2025-06-08 10:00:00', '2025-06-08 10:15:00', '2025-06-08 12:00:00', '2025-06-08 12:30:00', '2025-06-08 14:45:00', '2025-06-08 15:00:00', '2025-06-25 18:39:10'),
(68, 2147483653, '2025-06-01', '2025-06-01 08:00:00', '2025-06-01 16:00:00', NULL, NULL, '2025-06-01 10:00:00', '2025-06-01 10:15:00', '2025-06-01 12:00:00', '2025-06-01 12:30:00', '2025-06-01 14:45:00', '2025-06-01 15:00:00', '2025-06-25 18:39:10'),
(69, 2147483655, '2025-06-14', '2025-06-14 08:00:00', '2025-06-14 16:00:00', NULL, NULL, '2025-06-14 10:00:00', '2025-06-14 10:15:00', '2025-06-14 12:00:00', '2025-06-14 12:30:00', '2025-06-14 14:45:00', '2025-06-14 15:00:00', '2025-06-25 18:39:10'),
(70, 2147483653, '2025-05-25', '2025-05-25 08:00:00', '2025-05-25 16:00:00', NULL, NULL, '2025-05-25 10:00:00', '2025-05-25 10:15:00', '2025-05-25 12:00:00', '2025-05-25 12:30:00', '2025-05-25 14:45:00', '2025-05-25 15:00:00', '2025-06-25 18:39:10'),
(71, 2147483652, '2025-06-17', '2025-06-17 08:00:00', '2025-06-17 16:00:00', NULL, NULL, '2025-06-17 10:00:00', '2025-06-17 10:15:00', '2025-06-17 12:00:00', '2025-06-17 12:30:00', '2025-06-17 14:45:00', '2025-06-17 15:00:00', '2025-06-25 18:39:10'),
(72, 2147483652, '2025-05-12', '2025-05-12 08:00:00', '2025-05-12 16:00:00', NULL, NULL, '2025-05-12 10:00:00', '2025-05-12 10:15:00', '2025-05-12 12:00:00', '2025-05-12 12:30:00', '2025-05-12 14:45:00', '2025-05-12 15:00:00', '2025-06-25 18:39:10'),
(73, 2147483653, '2025-05-29', '2025-05-29 08:00:00', '2025-05-29 16:00:00', NULL, NULL, '2025-05-29 10:00:00', '2025-05-29 10:15:00', '2025-05-29 12:00:00', '2025-05-29 12:30:00', '2025-05-29 14:45:00', '2025-05-29 15:00:00', '2025-06-25 18:39:10'),
(74, 2147483655, '2025-05-11', '2025-05-11 08:00:00', '2025-05-11 16:00:00', NULL, NULL, '2025-05-11 10:00:00', '2025-05-11 10:15:00', '2025-05-11 12:00:00', '2025-05-11 12:30:00', '2025-05-11 14:45:00', '2025-05-11 15:00:00', '2025-06-25 18:39:10'),
(75, 2147483652, '2025-05-28', '2025-05-28 08:00:00', '2025-05-28 16:00:00', NULL, NULL, '2025-05-28 10:00:00', '2025-05-28 10:15:00', '2025-05-28 12:00:00', '2025-05-28 12:30:00', '2025-05-28 14:45:00', '2025-05-28 15:00:00', '2025-06-25 18:39:10'),
(76, 2147483654, '2025-05-29', '2025-05-29 08:00:00', '2025-05-29 16:00:00', NULL, NULL, '2025-05-29 10:00:00', '2025-05-29 10:15:00', '2025-05-29 12:00:00', '2025-05-29 12:30:00', '2025-05-29 14:45:00', '2025-05-29 15:00:00', '2025-06-25 18:39:10'),
(77, 2147483650, '2025-06-16', '2025-06-16 08:00:00', '2025-06-16 16:00:00', NULL, NULL, '2025-06-16 10:00:00', '2025-06-16 10:15:00', '2025-06-16 12:00:00', '2025-06-16 12:30:00', '2025-06-16 14:45:00', '2025-06-16 15:00:00', '2025-06-25 18:39:10'),
(78, 2147483655, '2025-05-03', '2025-05-03 08:00:00', '2025-05-03 16:00:00', NULL, NULL, '2025-05-03 10:00:00', '2025-05-03 10:15:00', '2025-05-03 12:00:00', '2025-05-03 12:30:00', '2025-05-03 14:45:00', '2025-05-03 15:00:00', '2025-06-25 18:39:10'),
(79, 1, '2025-05-14', '2025-05-14 08:00:00', '2025-05-14 16:00:00', NULL, NULL, '2025-05-14 10:00:00', '2025-05-14 10:15:00', '2025-05-14 12:00:00', '2025-05-14 12:30:00', '2025-05-14 14:45:00', '2025-05-14 15:00:00', '2025-06-25 18:39:10'),
(80, 2147483653, '2025-05-26', '2025-05-26 08:00:00', '2025-05-26 16:00:00', NULL, NULL, '2025-05-26 10:00:00', '2025-05-26 10:15:00', '2025-05-26 12:00:00', '2025-05-26 12:30:00', '2025-05-26 14:45:00', '2025-05-26 15:00:00', '2025-06-25 18:39:10'),
(81, 2147483650, '2025-06-28', '2025-06-28 08:00:00', '2025-06-28 16:00:00', NULL, NULL, '2025-06-28 10:00:00', '2025-06-28 10:15:00', '2025-06-28 12:00:00', '2025-06-28 12:30:00', '2025-06-28 14:45:00', '2025-06-28 15:00:00', '2025-06-25 18:39:10'),
(82, 2147483650, '2025-06-19', '2025-06-19 08:00:00', '2025-06-19 16:00:00', NULL, NULL, '2025-06-19 10:00:00', '2025-06-19 10:15:00', '2025-06-19 12:00:00', '2025-06-19 12:30:00', '2025-06-19 14:45:00', '2025-06-19 15:00:00', '2025-06-25 18:39:10'),
(83, 1, '2025-06-15', '2025-06-15 08:00:00', '2025-06-15 16:00:00', NULL, NULL, '2025-06-15 10:00:00', '2025-06-15 10:15:00', '2025-06-15 12:00:00', '2025-06-15 12:30:00', '2025-06-15 14:45:00', '2025-06-15 15:00:00', '2025-06-25 18:39:10'),
(84, 2147483652, '2025-06-11', '2025-06-11 08:00:00', '2025-06-11 16:00:00', NULL, NULL, '2025-06-11 10:00:00', '2025-06-11 10:15:00', '2025-06-11 12:00:00', '2025-06-11 12:30:00', '2025-06-11 14:45:00', '2025-06-11 15:00:00', '2025-06-25 18:39:10'),
(85, 2147483653, '2025-05-19', '2025-05-19 08:00:00', '2025-05-19 16:00:00', NULL, NULL, '2025-05-19 10:00:00', '2025-05-19 10:15:00', '2025-05-19 12:00:00', '2025-05-19 12:30:00', '2025-05-19 14:45:00', '2025-05-19 15:00:00', '2025-06-25 18:39:10'),
(86, 2147483652, '2025-05-04', '2025-05-04 08:00:00', '2025-05-04 16:00:00', NULL, NULL, '2025-05-04 10:00:00', '2025-05-04 10:15:00', '2025-05-04 12:00:00', '2025-05-04 12:30:00', '2025-05-04 14:45:00', '2025-05-04 15:00:00', '2025-06-25 18:39:10'),
(87, 2147483650, '2025-05-13', '2025-05-13 08:00:00', '2025-05-13 16:00:00', NULL, NULL, '2025-05-13 10:00:00', '2025-05-13 10:15:00', '2025-05-13 12:00:00', '2025-05-13 12:30:00', '2025-05-13 14:45:00', '2025-05-13 15:00:00', '2025-06-25 18:39:10'),
(88, 2147483650, '2025-06-25', '2025-06-25 08:00:00', '2025-06-25 16:00:00', NULL, NULL, '2025-06-25 10:00:00', '2025-06-25 10:15:00', '2025-06-25 12:00:00', '2025-06-25 12:30:00', '2025-06-25 14:45:00', '2025-06-25 15:00:00', '2025-06-25 18:39:10'),
(89, 2147483654, '2025-06-15', '2025-06-15 08:00:00', '2025-06-15 16:00:00', NULL, NULL, '2025-06-15 10:00:00', '2025-06-15 10:15:00', '2025-06-15 12:00:00', '2025-06-15 12:30:00', '2025-06-15 14:45:00', '2025-06-15 15:00:00', '2025-06-25 18:39:10'),
(90, 2147483650, '2025-05-27', '2025-05-27 08:00:00', '2025-05-27 16:00:00', NULL, NULL, '2025-05-27 10:00:00', '2025-05-27 10:15:00', '2025-05-27 12:00:00', '2025-05-27 12:30:00', '2025-05-27 14:45:00', '2025-05-27 15:00:00', '2025-06-25 18:39:10'),
(91, 2147483653, '2025-06-05', '2025-06-05 08:00:00', '2025-06-05 16:00:00', NULL, NULL, '2025-06-05 10:00:00', '2025-06-05 10:15:00', '2025-06-05 12:00:00', '2025-06-05 12:30:00', '2025-06-05 14:45:00', '2025-06-05 15:00:00', '2025-06-25 18:39:10'),
(92, 1, '2025-05-03', '2025-05-03 08:00:00', '2025-05-03 16:00:00', NULL, NULL, '2025-05-03 10:00:00', '2025-05-03 10:15:00', '2025-05-03 12:00:00', '2025-05-03 12:30:00', '2025-05-03 14:45:00', '2025-05-03 15:00:00', '2025-06-25 18:39:10'),
(93, 2147483652, '2025-06-15', '2025-06-15 08:00:00', '2025-06-15 16:00:00', NULL, NULL, '2025-06-15 10:00:00', '2025-06-15 10:15:00', '2025-06-15 12:00:00', '2025-06-15 12:30:00', '2025-06-15 14:45:00', '2025-06-15 15:00:00', '2025-06-25 18:39:10'),
(94, 2147483655, '2025-06-25', '2025-06-25 08:00:00', '2025-06-25 16:00:00', NULL, NULL, '2025-06-25 10:00:00', '2025-06-25 10:15:00', '2025-06-25 12:00:00', '2025-06-25 12:30:00', '2025-06-25 14:45:00', '2025-06-25 15:00:00', '2025-06-25 18:39:10'),
(95, 2147483650, '2025-06-12', '2025-06-12 08:00:00', '2025-06-12 16:00:00', NULL, NULL, '2025-06-12 10:00:00', '2025-06-12 10:15:00', '2025-06-12 12:00:00', '2025-06-12 12:30:00', '2025-06-12 14:45:00', '2025-06-12 15:00:00', '2025-06-25 18:39:10'),
(96, 2147483654, '2025-06-05', '2025-06-05 08:00:00', '2025-06-05 16:00:00', NULL, NULL, '2025-06-05 10:00:00', '2025-06-05 10:15:00', '2025-06-05 12:00:00', '2025-06-05 12:30:00', '2025-06-05 14:45:00', '2025-06-05 15:00:00', '2025-06-25 18:39:10'),
(97, 2147483655, '2025-06-24', '2025-06-24 08:00:00', '2025-06-24 16:00:00', NULL, NULL, '2025-06-24 10:00:00', '2025-06-24 10:15:00', '2025-06-24 12:00:00', '2025-06-24 12:30:00', '2025-06-24 14:45:00', '2025-06-24 15:00:00', '2025-06-25 18:39:10'),
(98, 2147483650, '2025-05-24', '2025-05-24 08:00:00', '2025-05-24 16:00:00', NULL, NULL, '2025-05-24 10:00:00', '2025-05-24 10:15:00', '2025-05-24 12:00:00', '2025-05-24 12:30:00', '2025-05-24 14:45:00', '2025-05-24 15:00:00', '2025-06-25 18:39:10'),
(99, 2147483652, '2025-06-08', '2025-06-08 08:00:00', '2025-06-08 16:00:00', NULL, NULL, '2025-06-08 10:00:00', '2025-06-08 10:15:00', '2025-06-08 12:00:00', '2025-06-08 12:30:00', '2025-06-08 14:45:00', '2025-06-08 15:00:00', '2025-06-25 18:39:10'),
(100, 2147483653, '2025-06-13', '2025-06-13 08:00:00', '2025-06-13 16:00:00', NULL, NULL, '2025-06-13 10:00:00', '2025-06-13 10:15:00', '2025-06-13 12:00:00', '2025-06-13 12:30:00', '2025-06-13 14:45:00', '2025-06-13 15:00:00', '2025-06-25 18:39:10'),
(101, 1, '2025-05-10', '2025-05-10 08:00:00', '2025-05-10 16:00:00', NULL, NULL, '2025-05-10 10:00:00', '2025-05-10 10:15:00', '2025-05-10 12:00:00', '2025-05-10 12:30:00', '2025-05-10 14:45:00', '2025-05-10 15:00:00', '2025-06-25 18:39:10'),
(102, 2147483650, '2025-05-15', '2025-05-15 08:00:00', '2025-05-15 16:00:00', NULL, NULL, '2025-05-15 10:00:00', '2025-05-15 10:15:00', '2025-05-15 12:00:00', '2025-05-15 12:30:00', '2025-05-15 14:45:00', '2025-05-15 15:00:00', '2025-06-25 18:39:10'),
(103, 2147483652, '2025-06-22', '2025-06-22 08:00:00', '2025-06-22 16:00:00', NULL, NULL, '2025-06-22 10:00:00', '2025-06-22 10:15:00', '2025-06-22 12:00:00', '2025-06-22 12:30:00', '2025-06-22 14:45:00', '2025-06-22 15:00:00', '2025-06-25 18:39:10'),
(104, 2147483653, '2025-05-18', '2025-05-18 08:00:00', '2025-05-18 16:00:00', NULL, NULL, '2025-05-18 10:00:00', '2025-05-18 10:15:00', '2025-05-18 12:00:00', '2025-05-18 12:30:00', '2025-05-18 14:45:00', '2025-05-18 15:00:00', '2025-06-25 18:39:10'),
(105, 2147483653, '2025-06-23', '2025-06-23 08:00:00', '2025-06-23 16:00:00', NULL, NULL, '2025-06-23 10:00:00', '2025-06-23 10:15:00', '2025-06-23 12:00:00', '2025-06-23 12:30:00', '2025-06-23 14:45:00', '2025-06-23 15:00:00', '2025-06-25 18:39:10'),
(106, 2147483650, '2025-06-27', '2025-06-27 08:00:00', '2025-06-27 16:00:00', NULL, NULL, '2025-06-27 10:00:00', '2025-06-27 10:15:00', '2025-06-27 12:00:00', '2025-06-27 12:30:00', '2025-06-27 14:45:00', '2025-06-27 15:00:00', '2025-06-25 18:39:10'),
(107, 2147483650, '2025-05-28', '2025-05-28 08:00:00', '2025-05-28 16:00:00', NULL, NULL, '2025-05-28 10:00:00', '2025-05-28 10:15:00', '2025-05-28 12:00:00', '2025-05-28 12:30:00', '2025-05-28 14:45:00', '2025-05-28 15:00:00', '2025-06-25 18:39:10'),
(108, 2147483655, '2025-06-24', '2025-06-24 08:00:00', '2025-06-24 16:00:00', NULL, NULL, '2025-06-24 10:00:00', '2025-06-24 10:15:00', '2025-06-24 12:00:00', '2025-06-24 12:30:00', '2025-06-24 14:45:00', '2025-06-24 15:00:00', '2025-06-25 18:39:10'),
(109, 2147483654, '2025-06-23', '2025-06-23 08:00:00', '2025-06-23 16:00:00', NULL, NULL, '2025-06-23 10:00:00', '2025-06-23 10:15:00', '2025-06-23 12:00:00', '2025-06-23 12:30:00', '2025-06-23 14:45:00', '2025-06-23 15:00:00', '2025-06-25 18:39:10'),
(110, 2147483653, '2025-05-10', '2025-05-10 08:00:00', '2025-05-10 16:00:00', NULL, NULL, '2025-05-10 10:00:00', '2025-05-10 10:15:00', '2025-05-10 12:00:00', '2025-05-10 12:30:00', '2025-05-10 14:45:00', '2025-05-10 15:00:00', '2025-06-25 18:39:10'),
(111, 2147483655, '2025-06-24', '2025-06-24 08:00:00', '2025-06-24 16:00:00', NULL, NULL, '2025-06-24 10:00:00', '2025-06-24 10:15:00', '2025-06-24 12:00:00', '2025-06-24 12:30:00', '2025-06-24 14:45:00', '2025-06-24 15:00:00', '2025-06-25 18:39:10'),
(112, 2147483652, '2025-05-15', '2025-05-15 08:00:00', '2025-05-15 16:00:00', NULL, NULL, '2025-05-15 10:00:00', '2025-05-15 10:15:00', '2025-05-15 12:00:00', '2025-05-15 12:30:00', '2025-05-15 14:45:00', '2025-05-15 15:00:00', '2025-06-25 18:39:10'),
(113, 2147483653, '2025-05-15', '2025-05-15 08:00:00', '2025-05-15 16:00:00', NULL, NULL, '2025-05-15 10:00:00', '2025-05-15 10:15:00', '2025-05-15 12:00:00', '2025-05-15 12:30:00', '2025-05-15 14:45:00', '2025-05-15 15:00:00', '2025-06-25 18:39:10'),
(114, 2147483653, '2025-06-08', '2025-06-08 08:00:00', '2025-06-08 16:00:00', NULL, NULL, '2025-06-08 10:00:00', '2025-06-08 10:15:00', '2025-06-08 12:00:00', '2025-06-08 12:30:00', '2025-06-08 14:45:00', '2025-06-08 15:00:00', '2025-06-25 18:39:10'),
(115, 2147483654, '2025-05-17', '2025-05-17 08:00:00', '2025-05-17 16:00:00', NULL, NULL, '2025-05-17 10:00:00', '2025-05-17 10:15:00', '2025-05-17 12:00:00', '2025-05-17 12:30:00', '2025-05-17 14:45:00', '2025-05-17 15:00:00', '2025-06-25 18:39:10'),
(116, 2147483654, '2025-06-21', '2025-06-21 08:00:00', '2025-06-21 16:00:00', NULL, NULL, '2025-06-21 10:00:00', '2025-06-21 10:15:00', '2025-06-21 12:00:00', '2025-06-21 12:30:00', '2025-06-21 14:45:00', '2025-06-21 15:00:00', '2025-06-25 18:39:10'),
(117, 2147483655, '2025-05-19', '2025-05-19 08:00:00', '2025-05-19 16:00:00', NULL, NULL, '2025-05-19 10:00:00', '2025-05-19 10:15:00', '2025-05-19 12:00:00', '2025-05-19 12:30:00', '2025-05-19 14:45:00', '2025-05-19 15:00:00', '2025-06-25 18:39:10'),
(118, 2147483654, '2025-05-14', '2025-05-14 08:00:00', '2025-05-14 16:00:00', NULL, NULL, '2025-05-14 10:00:00', '2025-05-14 10:15:00', '2025-05-14 12:00:00', '2025-05-14 12:30:00', '2025-05-14 14:45:00', '2025-05-14 15:00:00', '2025-06-25 18:39:10'),
(119, 2147483654, '2025-05-27', '2025-05-27 08:00:00', '2025-05-27 16:00:00', NULL, NULL, '2025-05-27 10:00:00', '2025-05-27 10:15:00', '2025-05-27 12:00:00', '2025-05-27 12:30:00', '2025-05-27 14:45:00', '2025-05-27 15:00:00', '2025-06-25 18:39:10'),
(120, 2147483655, '2025-06-17', '2025-06-17 08:00:00', '2025-06-17 16:00:00', NULL, NULL, '2025-06-17 10:00:00', '2025-06-17 10:15:00', '2025-06-17 12:00:00', '2025-06-17 12:30:00', '2025-06-17 14:45:00', '2025-06-17 15:00:00', '2025-06-25 18:39:10'),
(121, 2147483653, '2025-06-04', '2025-06-04 08:00:00', '2025-06-04 16:00:00', NULL, NULL, '2025-06-04 10:00:00', '2025-06-04 10:15:00', '2025-06-04 12:00:00', '2025-06-04 12:30:00', '2025-06-04 14:45:00', '2025-06-04 15:00:00', '2025-06-25 18:39:10'),
(122, 2147483654, '2025-05-05', '2025-05-05 08:00:00', '2025-05-05 16:00:00', NULL, NULL, '2025-05-05 10:00:00', '2025-05-05 10:15:00', '2025-05-05 12:00:00', '2025-05-05 12:30:00', '2025-05-05 14:45:00', '2025-05-05 15:00:00', '2025-06-25 18:39:10'),
(123, 1, '2025-06-18', '2025-06-18 08:00:00', '2025-06-18 16:00:00', NULL, NULL, '2025-06-18 10:00:00', '2025-06-18 10:15:00', '2025-06-18 12:00:00', '2025-06-18 12:30:00', '2025-06-18 14:45:00', '2025-06-18 15:00:00', '2025-06-25 18:39:10'),
(124, 2147483654, '2025-06-14', '2025-06-14 08:00:00', '2025-06-14 16:00:00', NULL, NULL, '2025-06-14 10:00:00', '2025-06-14 10:15:00', '2025-06-14 12:00:00', '2025-06-14 12:30:00', '2025-06-14 14:45:00', '2025-06-14 15:00:00', '2025-06-25 18:39:10'),
(125, 2147483653, '2025-06-24', '2025-06-24 08:00:00', '2025-06-24 16:00:00', NULL, NULL, '2025-06-24 10:00:00', '2025-06-24 10:15:00', '2025-06-24 12:00:00', '2025-06-24 12:30:00', '2025-06-24 14:45:00', '2025-06-24 15:00:00', '2025-06-25 18:39:10'),
(126, 2147483653, '2025-05-03', '2025-05-03 08:00:00', '2025-05-03 16:00:00', NULL, NULL, '2025-05-03 10:00:00', '2025-05-03 10:15:00', '2025-05-03 12:00:00', '2025-05-03 12:30:00', '2025-05-03 14:45:00', '2025-05-03 15:00:00', '2025-06-25 18:39:10'),
(127, 2147483654, '2025-05-06', '2025-05-06 08:00:00', '2025-05-06 16:00:00', NULL, NULL, '2025-05-06 10:00:00', '2025-05-06 10:15:00', '2025-05-06 12:00:00', '2025-05-06 12:30:00', '2025-05-06 14:45:00', '2025-05-06 15:00:00', '2025-06-25 18:39:10'),
(128, 2147483650, '2025-05-27', '2025-05-27 08:00:00', '2025-05-27 16:00:00', NULL, NULL, '2025-05-27 10:00:00', '2025-05-27 10:15:00', '2025-05-27 12:00:00', '2025-05-27 12:30:00', '2025-05-27 14:45:00', '2025-05-27 15:00:00', '2025-06-25 18:39:10'),
(129, 2147483653, '2025-05-03', '2025-05-03 08:00:00', '2025-05-03 16:00:00', NULL, NULL, '2025-05-03 10:00:00', '2025-05-03 10:15:00', '2025-05-03 12:00:00', '2025-05-03 12:30:00', '2025-05-03 14:45:00', '2025-05-03 15:00:00', '2025-06-25 18:39:10'),
(130, 2147483652, '2025-06-17', '2025-06-17 08:00:00', '2025-06-17 16:00:00', NULL, NULL, '2025-06-17 10:00:00', '2025-06-17 10:15:00', '2025-06-17 12:00:00', '2025-06-17 12:30:00', '2025-06-17 14:45:00', '2025-06-17 15:00:00', '2025-06-25 18:39:10'),
(131, 2147483650, '2025-06-29', '2025-06-29 08:00:00', '2025-06-29 16:00:00', NULL, NULL, '2025-06-29 10:00:00', '2025-06-29 10:15:00', '2025-06-29 12:00:00', '2025-06-29 12:30:00', '2025-06-29 14:45:00', '2025-06-29 15:00:00', '2025-06-25 18:39:10'),
(132, 2147483652, '2025-06-11', '2025-06-11 08:00:00', '2025-06-11 16:00:00', NULL, NULL, '2025-06-11 10:00:00', '2025-06-11 10:15:00', '2025-06-11 12:00:00', '2025-06-11 12:30:00', '2025-06-11 14:45:00', '2025-06-11 15:00:00', '2025-06-25 18:39:10'),
(133, 2147483654, '2025-05-24', '2025-05-24 08:00:00', '2025-05-24 16:00:00', NULL, NULL, '2025-05-24 10:00:00', '2025-05-24 10:15:00', '2025-05-24 12:00:00', '2025-05-24 12:30:00', '2025-05-24 14:45:00', '2025-05-24 15:00:00', '2025-06-25 18:39:10'),
(134, 2147483654, '2025-06-12', '2025-06-12 08:00:00', '2025-06-12 16:00:00', NULL, NULL, '2025-06-12 10:00:00', '2025-06-12 10:15:00', '2025-06-12 12:00:00', '2025-06-12 12:30:00', '2025-06-12 14:45:00', '2025-06-12 15:00:00', '2025-06-25 18:39:10'),
(135, 2147483654, '2025-05-25', '2025-05-25 08:00:00', '2025-05-25 16:00:00', NULL, NULL, '2025-05-25 10:00:00', '2025-05-25 10:15:00', '2025-05-25 12:00:00', '2025-05-25 12:30:00', '2025-05-25 14:45:00', '2025-05-25 15:00:00', '2025-06-25 18:39:10'),
(136, 2147483653, '2025-05-27', '2025-05-27 08:00:00', '2025-05-27 16:00:00', NULL, NULL, '2025-05-27 10:00:00', '2025-05-27 10:15:00', '2025-05-27 12:00:00', '2025-05-27 12:30:00', '2025-05-27 14:45:00', '2025-05-27 15:00:00', '2025-06-25 18:39:10'),
(137, 2147483650, '2025-06-08', '2025-06-08 08:00:00', '2025-06-08 16:00:00', NULL, NULL, '2025-06-08 10:00:00', '2025-06-08 10:15:00', '2025-06-08 12:00:00', '2025-06-08 12:30:00', '2025-06-08 14:45:00', '2025-06-08 15:00:00', '2025-06-25 18:39:10'),
(138, 2147483652, '2025-05-15', '2025-05-15 08:00:00', '2025-05-15 16:00:00', NULL, NULL, '2025-05-15 10:00:00', '2025-05-15 10:15:00', '2025-05-15 12:00:00', '2025-05-15 12:30:00', '2025-05-15 14:45:00', '2025-05-15 15:00:00', '2025-06-25 18:39:10'),
(139, 2147483654, '2025-05-06', '2025-05-06 08:00:00', '2025-05-06 16:00:00', NULL, NULL, '2025-05-06 10:00:00', '2025-05-06 10:15:00', '2025-05-06 12:00:00', '2025-05-06 12:30:00', '2025-05-06 14:45:00', '2025-05-06 15:00:00', '2025-06-25 18:39:10'),
(140, 2147483655, '2025-06-21', '2025-06-21 08:00:00', '2025-06-21 16:00:00', NULL, NULL, '2025-06-21 10:00:00', '2025-06-21 10:15:00', '2025-06-21 12:00:00', '2025-06-21 12:30:00', '2025-06-21 14:45:00', '2025-06-21 15:00:00', '2025-06-25 18:39:10'),
(141, 2147483655, '2025-05-16', '2025-05-16 08:00:00', '2025-05-16 16:00:00', NULL, NULL, '2025-05-16 10:00:00', '2025-05-16 10:15:00', '2025-05-16 12:00:00', '2025-05-16 12:30:00', '2025-05-16 14:45:00', '2025-05-16 15:00:00', '2025-06-25 18:39:10'),
(142, 2147483653, '2025-05-26', '2025-05-26 08:00:00', '2025-05-26 16:00:00', NULL, NULL, '2025-05-26 10:00:00', '2025-05-26 10:15:00', '2025-05-26 12:00:00', '2025-05-26 12:30:00', '2025-05-26 14:45:00', '2025-05-26 15:00:00', '2025-06-25 18:39:10'),
(143, 2147483654, '2025-06-13', '2025-06-13 08:00:00', '2025-06-13 16:00:00', NULL, NULL, '2025-06-13 10:00:00', '2025-06-13 10:15:00', '2025-06-13 12:00:00', '2025-06-13 12:30:00', '2025-06-13 14:45:00', '2025-06-13 15:00:00', '2025-06-25 18:39:10'),
(144, 2147483652, '2025-06-10', '2025-06-10 08:00:00', '2025-06-10 16:00:00', NULL, NULL, '2025-06-10 10:00:00', '2025-06-10 10:15:00', '2025-06-10 12:00:00', '2025-06-10 12:30:00', '2025-06-10 14:45:00', '2025-06-10 15:00:00', '2025-06-25 18:39:10'),
(145, 2147483652, '2025-05-20', '2025-05-20 08:00:00', '2025-05-20 16:00:00', NULL, NULL, '2025-05-20 10:00:00', '2025-05-20 10:15:00', '2025-05-20 12:00:00', '2025-05-20 12:30:00', '2025-05-20 14:45:00', '2025-05-20 15:00:00', '2025-06-25 18:39:10'),
(146, 2147483652, '2025-06-06', '2025-06-06 08:00:00', '2025-06-06 16:00:00', NULL, NULL, '2025-06-06 10:00:00', '2025-06-06 10:15:00', '2025-06-06 12:00:00', '2025-06-06 12:30:00', '2025-06-06 14:45:00', '2025-06-06 15:00:00', '2025-06-25 18:39:10'),
(147, 1, '2025-06-24', '2025-06-24 08:00:00', '2025-06-24 16:00:00', NULL, NULL, '2025-06-24 10:00:00', '2025-06-24 10:15:00', '2025-06-24 12:00:00', '2025-06-24 12:30:00', '2025-06-24 14:45:00', '2025-06-24 15:00:00', '2025-06-25 18:39:10'),
(148, 1, '2025-05-08', '2025-05-08 08:00:00', '2025-05-08 16:00:00', NULL, NULL, '2025-05-08 10:00:00', '2025-05-08 10:15:00', '2025-05-08 12:00:00', '2025-05-08 12:30:00', '2025-05-08 14:45:00', '2025-05-08 15:00:00', '2025-06-25 18:39:10'),
(149, 1, '2025-06-06', '2025-06-06 08:00:00', '2025-06-06 16:00:00', NULL, NULL, '2025-06-06 10:00:00', '2025-06-06 10:15:00', '2025-06-06 12:00:00', '2025-06-06 12:30:00', '2025-06-06 14:45:00', '2025-06-06 15:00:00', '2025-06-25 18:39:10'),
(150, 2147483653, '2025-05-23', '2025-05-23 08:00:00', '2025-05-23 16:00:00', NULL, NULL, '2025-05-23 10:00:00', '2025-05-23 10:15:00', '2025-05-23 12:00:00', '2025-05-23 12:30:00', '2025-05-23 14:45:00', '2025-05-23 15:00:00', '2025-06-25 18:39:10'),
(151, 2147483653, '2025-05-09', '2025-05-09 08:00:00', '2025-05-09 16:00:00', NULL, NULL, '2025-05-09 10:00:00', '2025-05-09 10:15:00', '2025-05-09 12:00:00', '2025-05-09 12:30:00', '2025-05-09 14:45:00', '2025-05-09 15:00:00', '2025-06-25 18:39:10'),
(152, 2147483652, '2025-05-08', '2025-05-08 08:00:00', '2025-05-08 16:00:00', NULL, NULL, '2025-05-08 10:00:00', '2025-05-08 10:15:00', '2025-05-08 12:00:00', '2025-05-08 12:30:00', '2025-05-08 14:45:00', '2025-05-08 15:00:00', '2025-06-25 18:39:10'),
(153, 2147483653, '2025-06-16', '2025-06-16 08:00:00', '2025-06-16 16:00:00', NULL, NULL, '2025-06-16 10:00:00', '2025-06-16 10:15:00', '2025-06-16 12:00:00', '2025-06-16 12:30:00', '2025-06-16 14:45:00', '2025-06-16 15:00:00', '2025-06-25 18:39:10'),
(154, 2147483653, '2025-06-16', '2025-06-16 08:00:00', '2025-06-16 16:00:00', NULL, NULL, '2025-06-16 10:00:00', '2025-06-16 10:15:00', '2025-06-16 12:00:00', '2025-06-16 12:30:00', '2025-06-16 14:45:00', '2025-06-16 15:00:00', '2025-06-25 18:39:10'),
(155, 2147483653, '2025-05-29', '2025-05-29 08:00:00', '2025-05-29 16:00:00', NULL, NULL, '2025-05-29 10:00:00', '2025-05-29 10:15:00', '2025-05-29 12:00:00', '2025-05-29 12:30:00', '2025-05-29 14:45:00', '2025-05-29 15:00:00', '2025-06-25 18:39:10'),
(156, 2147483652, '2025-06-04', '2025-06-04 08:00:00', '2025-06-04 16:00:00', NULL, NULL, '2025-06-04 10:00:00', '2025-06-04 10:15:00', '2025-06-04 12:00:00', '2025-06-04 12:30:00', '2025-06-04 14:45:00', '2025-06-04 15:00:00', '2025-06-25 18:39:10'),
(157, 2147483655, '2025-05-22', '2025-05-22 08:00:00', '2025-05-22 16:00:00', NULL, NULL, '2025-05-22 10:00:00', '2025-05-22 10:15:00', '2025-05-22 12:00:00', '2025-05-22 12:30:00', '2025-05-22 14:45:00', '2025-05-22 15:00:00', '2025-06-25 18:39:10'),
(158, 2147483655, '2025-05-23', '2025-05-23 08:00:00', '2025-05-23 16:00:00', NULL, NULL, '2025-05-23 10:00:00', '2025-05-23 10:15:00', '2025-05-23 12:00:00', '2025-05-23 12:30:00', '2025-05-23 14:45:00', '2025-05-23 15:00:00', '2025-06-25 18:39:10'),
(159, 2147483653, '2025-05-06', '2025-05-06 08:00:00', '2025-05-06 16:00:00', NULL, NULL, '2025-05-06 10:00:00', '2025-05-06 10:15:00', '2025-05-06 12:00:00', '2025-05-06 12:30:00', '2025-05-06 14:45:00', '2025-05-06 15:00:00', '2025-06-25 18:39:10'),
(160, 2147483654, '2025-06-30', '2025-06-30 21:00:00', '2025-06-30 06:00:00', NULL, NULL, '2025-06-30 10:00:00', '2025-06-30 10:15:00', '2025-06-30 12:00:00', '2025-06-30 12:30:00', '2025-06-30 14:45:00', '2025-06-30 15:00:00', '2025-06-25 18:39:10'),
(161, 2147483650, '2025-06-12', '2025-06-12 08:00:00', '2025-06-12 16:00:00', NULL, NULL, '2025-06-12 10:00:00', '2025-06-12 10:15:00', '2025-06-12 12:00:00', '2025-06-12 12:30:00', '2025-06-12 14:45:00', '2025-06-12 15:00:00', '2025-06-25 18:39:10'),
(162, 2147483652, '2025-06-23', '2025-06-23 08:00:00', '2025-06-23 16:00:00', NULL, NULL, '2025-06-23 10:00:00', '2025-06-23 10:15:00', '2025-06-23 12:00:00', '2025-06-23 12:30:00', '2025-06-23 14:45:00', '2025-06-23 15:00:00', '2025-06-25 18:39:10'),
(163, 2147483654, '2025-05-26', '2025-05-26 08:00:00', '2025-05-26 16:00:00', NULL, NULL, '2025-05-26 10:00:00', '2025-05-26 10:15:00', '2025-05-26 12:00:00', '2025-05-26 12:30:00', '2025-05-26 14:45:00', '2025-05-26 15:00:00', '2025-06-25 18:39:10'),
(164, 2147483653, '2025-06-07', '2025-06-07 08:00:00', '2025-06-07 16:00:00', NULL, NULL, '2025-06-07 10:00:00', '2025-06-07 10:15:00', '2025-06-07 12:00:00', '2025-06-07 12:30:00', '2025-06-07 14:45:00', '2025-06-07 15:00:00', '2025-06-25 18:39:10'),
(165, 1, '2025-06-09', '2025-06-09 08:00:00', '2025-06-09 16:00:00', NULL, NULL, '2025-06-09 10:00:00', '2025-06-09 10:15:00', '2025-06-09 12:00:00', '2025-06-09 12:30:00', '2025-06-09 14:45:00', '2025-06-09 15:00:00', '2025-06-25 18:39:10'),
(166, 2147483650, '2025-06-21', '2025-06-21 08:00:00', '2025-06-21 16:00:00', NULL, NULL, '2025-06-21 10:00:00', '2025-06-21 10:15:00', '2025-06-21 12:00:00', '2025-06-21 12:30:00', '2025-06-21 14:45:00', '2025-06-21 15:00:00', '2025-06-25 18:39:10'),
(167, 2147483653, '2025-06-13', '2025-06-13 08:00:00', '2025-06-13 16:00:00', NULL, NULL, '2025-06-13 10:00:00', '2025-06-13 10:15:00', '2025-06-13 12:00:00', '2025-06-13 12:30:00', '2025-06-13 14:45:00', '2025-06-13 15:00:00', '2025-06-25 18:39:10'),
(168, 2147483654, '2025-06-28', '2025-06-28 08:00:00', '2025-06-28 16:00:00', NULL, NULL, '2025-06-28 10:00:00', '2025-06-28 10:15:00', '2025-06-28 12:00:00', '2025-06-28 12:30:00', '2025-06-28 14:45:00', '2025-06-28 15:00:00', '2025-06-25 18:39:10'),
(169, 2147483650, '2025-06-06', '2025-06-06 08:00:00', '2025-06-06 16:00:00', NULL, NULL, '2025-06-06 10:00:00', '2025-06-06 10:15:00', '2025-06-06 12:00:00', '2025-06-06 12:30:00', '2025-06-06 14:45:00', '2025-06-06 15:00:00', '2025-06-25 18:39:10'),
(170, 2147483653, '2025-05-12', '2025-05-12 08:00:00', '2025-05-12 16:00:00', NULL, NULL, '2025-05-12 10:00:00', '2025-05-12 10:15:00', '2025-05-12 12:00:00', '2025-05-12 12:30:00', '2025-05-12 14:45:00', '2025-05-12 15:00:00', '2025-06-25 18:39:10'),
(171, 2147483655, '2025-06-01', '2025-06-01 08:00:00', '2025-06-01 16:00:00', NULL, NULL, '2025-06-01 10:00:00', '2025-06-01 10:15:00', '2025-06-01 12:00:00', '2025-06-01 12:30:00', '2025-06-01 14:45:00', '2025-06-01 15:00:00', '2025-06-25 18:39:10'),
(172, 2147483652, '2025-05-02', '2025-05-02 08:00:00', '2025-05-02 16:00:00', NULL, NULL, '2025-05-02 10:00:00', '2025-05-02 10:15:00', '2025-05-02 12:00:00', '2025-05-02 12:30:00', '2025-05-02 14:45:00', '2025-05-02 15:00:00', '2025-06-25 18:39:10'),
(173, 1, '2025-06-05', '2025-06-05 08:00:00', '2025-06-05 16:00:00', NULL, NULL, '2025-06-05 10:00:00', '2025-06-05 10:15:00', '2025-06-05 12:00:00', '2025-06-05 12:30:00', '2025-06-05 14:45:00', '2025-06-05 15:00:00', '2025-06-25 18:39:10'),
(174, 2147483655, '2025-05-18', '2025-05-18 08:00:00', '2025-05-18 16:00:00', NULL, NULL, '2025-05-18 10:00:00', '2025-05-18 10:15:00', '2025-05-18 12:00:00', '2025-05-18 12:30:00', '2025-05-18 14:45:00', '2025-05-18 15:00:00', '2025-06-25 18:39:10'),
(175, 2147483655, '2025-05-29', '2025-05-29 08:00:00', '2025-05-29 16:00:00', NULL, NULL, '2025-05-29 10:00:00', '2025-05-29 10:15:00', '2025-05-29 12:00:00', '2025-05-29 12:30:00', '2025-05-29 14:45:00', '2025-05-29 15:00:00', '2025-06-25 18:39:10'),
(176, 2147483654, '2025-06-07', '2025-06-07 08:00:00', '2025-06-07 16:00:00', NULL, NULL, '2025-06-07 10:00:00', '2025-06-07 10:15:00', '2025-06-07 12:00:00', '2025-06-07 12:30:00', '2025-06-07 14:45:00', '2025-06-07 15:00:00', '2025-06-25 18:39:10'),
(177, 1, '2025-05-11', '2025-05-11 08:00:00', '2025-05-11 16:00:00', NULL, NULL, '2025-05-11 10:00:00', '2025-05-11 10:15:00', '2025-05-11 12:00:00', '2025-05-11 12:30:00', '2025-05-11 14:45:00', '2025-05-11 15:00:00', '2025-06-25 18:39:10'),
(178, 2147483652, '2025-05-17', '2025-05-17 08:00:00', '2025-05-17 16:00:00', NULL, NULL, '2025-05-17 10:00:00', '2025-05-17 10:15:00', '2025-05-17 12:00:00', '2025-05-17 12:30:00', '2025-05-17 14:45:00', '2025-05-17 15:00:00', '2025-06-25 18:39:10'),
(179, 2147483653, '2025-05-20', '2025-05-20 08:00:00', '2025-05-20 16:00:00', NULL, NULL, '2025-05-20 10:00:00', '2025-05-20 10:15:00', '2025-05-20 12:00:00', '2025-05-20 12:30:00', '2025-05-20 14:45:00', '2025-05-20 15:00:00', '2025-06-25 18:39:10'),
(180, 2147483650, '2025-06-17', '2025-06-17 08:00:00', '2025-06-17 16:00:00', NULL, NULL, '2025-06-17 10:00:00', '2025-06-17 10:15:00', '2025-06-17 12:00:00', '2025-06-17 12:30:00', '2025-06-17 14:45:00', '2025-06-17 15:00:00', '2025-06-25 18:39:10'),
(181, 2147483653, '2025-06-29', '2025-06-29 08:00:00', '2025-06-29 16:00:00', NULL, NULL, '2025-06-29 10:00:00', '2025-06-29 10:15:00', '2025-06-29 12:00:00', '2025-06-29 12:30:00', '2025-06-29 14:45:00', '2025-06-29 15:00:00', '2025-06-25 18:39:10'),
(182, 2147483655, '2025-06-11', '2025-06-11 08:00:00', '2025-06-11 16:00:00', NULL, NULL, '2025-06-11 10:00:00', '2025-06-11 10:15:00', '2025-06-11 12:00:00', '2025-06-11 12:30:00', '2025-06-11 14:45:00', '2025-06-11 15:00:00', '2025-06-25 18:39:10'),
(183, 2147483653, '2025-05-23', '2025-05-23 08:00:00', '2025-05-23 16:00:00', NULL, NULL, '2025-05-23 10:00:00', '2025-05-23 10:15:00', '2025-05-23 12:00:00', '2025-05-23 12:30:00', '2025-05-23 14:45:00', '2025-05-23 15:00:00', '2025-06-25 18:39:10'),
(184, 2147483654, '2025-05-04', '2025-05-04 08:00:00', '2025-05-04 16:00:00', NULL, NULL, '2025-05-04 10:00:00', '2025-05-04 10:15:00', '2025-05-04 12:00:00', '2025-05-04 12:30:00', '2025-05-04 14:45:00', '2025-05-04 15:00:00', '2025-06-25 18:39:10'),
(185, 2147483653, '2025-06-23', '2025-06-23 08:00:00', '2025-06-23 16:00:00', NULL, NULL, '2025-06-23 10:00:00', '2025-06-23 10:15:00', '2025-06-23 12:00:00', '2025-06-23 12:30:00', '2025-06-23 14:45:00', '2025-06-23 15:00:00', '2025-06-25 18:39:10'),
(186, 1, '2025-06-24', '2025-06-24 08:00:00', '2025-06-24 16:00:00', NULL, NULL, '2025-06-24 10:00:00', '2025-06-24 10:15:00', '2025-06-24 12:00:00', '2025-06-24 12:30:00', '2025-06-24 14:45:00', '2025-06-24 15:00:00', '2025-06-25 18:39:10'),
(187, 2147483650, '2025-05-30', '2025-05-30 08:00:00', '2025-05-30 16:00:00', NULL, NULL, '2025-05-30 10:00:00', '2025-05-30 10:15:00', '2025-05-30 12:00:00', '2025-05-30 12:30:00', '2025-05-30 14:45:00', '2025-05-30 15:00:00', '2025-06-25 18:39:10'),
(188, 1, '2025-05-18', '2025-05-18 08:00:00', '2025-05-18 16:00:00', NULL, NULL, '2025-05-18 10:00:00', '2025-05-18 10:15:00', '2025-05-18 12:00:00', '2025-05-18 12:30:00', '2025-05-18 14:45:00', '2025-05-18 15:00:00', '2025-06-25 18:39:10'),
(189, 1, '2025-06-15', '2025-06-15 08:00:00', '2025-06-15 16:00:00', NULL, NULL, '2025-06-15 10:00:00', '2025-06-15 10:15:00', '2025-06-15 12:00:00', '2025-06-15 12:30:00', '2025-06-15 14:45:00', '2025-06-15 15:00:00', '2025-06-25 18:39:10'),
(190, 2147483653, '2025-05-24', '2025-05-24 08:00:00', '2025-05-24 16:00:00', NULL, NULL, '2025-05-24 10:00:00', '2025-05-24 10:15:00', '2025-05-24 12:00:00', '2025-05-24 12:30:00', '2025-05-24 14:45:00', '2025-05-24 15:00:00', '2025-06-25 18:39:10'),
(191, 2147483654, '2025-05-28', '2025-05-28 08:00:00', '2025-05-28 16:00:00', NULL, NULL, '2025-05-28 10:00:00', '2025-05-28 10:15:00', '2025-05-28 12:00:00', '2025-05-28 12:30:00', '2025-05-28 14:45:00', '2025-05-28 15:00:00', '2025-06-25 18:39:10'),
(192, 2147483655, '2025-06-27', '2025-06-27 08:00:00', '2025-06-27 16:00:00', NULL, NULL, '2025-06-27 10:00:00', '2025-06-27 10:15:00', '2025-06-27 12:00:00', '2025-06-27 12:30:00', '2025-06-27 14:45:00', '2025-06-27 15:00:00', '2025-06-25 18:39:10'),
(193, 2147483653, '2025-05-30', '2025-05-30 08:00:00', '2025-05-30 16:00:00', NULL, NULL, '2025-05-30 10:00:00', '2025-05-30 10:15:00', '2025-05-30 12:00:00', '2025-05-30 12:30:00', '2025-05-30 14:45:00', '2025-05-30 15:00:00', '2025-06-25 18:39:10'),
(194, 2147483650, '2025-06-28', '2025-06-28 08:00:00', '2025-06-28 16:00:00', NULL, NULL, '2025-06-28 10:00:00', '2025-06-28 10:15:00', '2025-06-28 12:00:00', '2025-06-28 12:30:00', '2025-06-28 14:45:00', '2025-06-28 15:00:00', '2025-06-25 18:39:10'),
(195, 2147483650, '2025-05-04', '2025-05-04 08:00:00', '2025-05-04 16:00:00', NULL, NULL, '2025-05-04 10:00:00', '2025-05-04 10:15:00', '2025-05-04 12:00:00', '2025-05-04 12:30:00', '2025-05-04 14:45:00', '2025-05-04 15:00:00', '2025-06-25 18:39:10'),
(196, 2147483652, '2025-05-18', '2025-05-18 08:00:00', '2025-05-18 16:00:00', NULL, NULL, '2025-05-18 10:00:00', '2025-05-18 10:15:00', '2025-05-18 12:00:00', '2025-05-18 12:30:00', '2025-05-18 14:45:00', '2025-05-18 15:00:00', '2025-06-25 18:39:10'),
(197, 2147483652, '2025-06-15', '2025-06-15 08:00:00', '2025-06-15 16:00:00', NULL, NULL, '2025-06-15 10:00:00', '2025-06-15 10:15:00', '2025-06-15 12:00:00', '2025-06-15 12:30:00', '2025-06-15 14:45:00', '2025-06-15 15:00:00', '2025-06-25 18:39:10'),
(198, 1, '2025-06-23', '2025-06-23 08:00:00', '2025-06-23 16:00:00', NULL, NULL, '2025-06-23 10:00:00', '2025-06-23 10:15:00', '2025-06-23 12:00:00', '2025-06-23 12:30:00', '2025-06-23 14:45:00', '2025-06-23 15:00:00', '2025-06-25 18:39:10'),
(199, 2147483652, '2025-05-20', '2025-05-20 08:00:00', '2025-05-20 16:00:00', NULL, NULL, '2025-05-20 10:00:00', '2025-05-20 10:15:00', '2025-05-20 12:00:00', '2025-05-20 12:30:00', '2025-05-20 14:45:00', '2025-05-20 15:00:00', '2025-06-25 18:39:10'),
(200, 1, '2025-06-26', '2025-06-26 08:00:00', '2025-06-26 16:00:00', NULL, NULL, '2025-06-26 10:00:00', '2025-06-26 10:15:00', '2025-06-26 12:00:00', '2025-06-26 12:30:00', '2025-06-26 14:45:00', '2025-06-26 15:00:00', '2025-06-25 18:39:10'),
(201, 2147483654, '2025-06-27', '2025-06-27 08:00:00', '2025-06-27 16:00:00', NULL, NULL, '2025-06-27 10:00:00', '2025-06-27 10:15:00', '2025-06-27 12:00:00', '2025-06-27 12:30:00', '2025-06-27 14:45:00', '2025-06-27 15:00:00', '2025-06-25 18:39:10'),
(202, 2147483653, '2025-06-10', '2025-06-10 08:00:00', '2025-06-10 16:00:00', NULL, NULL, '2025-06-10 10:00:00', '2025-06-10 10:15:00', '2025-06-10 12:00:00', '2025-06-10 12:30:00', '2025-06-10 14:45:00', '2025-06-10 15:00:00', '2025-06-25 18:39:10'),
(203, 2147483653, '2025-05-27', '2025-05-27 08:00:00', '2025-05-27 16:00:00', NULL, NULL, '2025-05-27 10:00:00', '2025-05-27 10:15:00', '2025-05-27 12:00:00', '2025-05-27 12:30:00', '2025-05-27 14:45:00', '2025-05-27 15:00:00', '2025-06-25 18:39:10'),
(204, 2147483653, '2025-05-10', '2025-05-10 08:00:00', '2025-05-10 16:00:00', NULL, NULL, '2025-05-10 10:00:00', '2025-05-10 10:15:00', '2025-05-10 12:00:00', '2025-05-10 12:30:00', '2025-05-10 14:45:00', '2025-05-10 15:00:00', '2025-06-25 18:39:10'),
(205, 1, '2025-05-28', '2025-05-28 08:00:00', '2025-05-28 16:00:00', NULL, NULL, '2025-05-28 10:00:00', '2025-05-28 10:15:00', '2025-05-28 12:00:00', '2025-05-28 12:30:00', '2025-05-28 14:45:00', '2025-05-28 15:00:00', '2025-06-25 18:39:10'),
(206, 2147483654, '2025-05-13', '2025-05-13 08:00:00', '2025-05-13 16:00:00', NULL, NULL, '2025-05-13 10:00:00', '2025-05-13 10:15:00', '2025-05-13 12:00:00', '2025-05-13 12:30:00', '2025-05-13 14:45:00', '2025-05-13 15:00:00', '2025-06-25 18:39:10'),
(207, 2147483650, '2025-06-26', '2025-06-26 08:00:00', '2025-06-26 16:00:00', NULL, NULL, '2025-06-26 10:00:00', '2025-06-26 10:15:00', '2025-06-26 12:00:00', '2025-06-26 12:30:00', '2025-06-26 14:45:00', '2025-06-26 15:00:00', '2025-06-25 18:39:10'),
(208, 2147483652, '2025-05-19', '2025-05-19 08:00:00', '2025-05-19 16:00:00', NULL, NULL, '2025-05-19 10:00:00', '2025-05-19 10:15:00', '2025-05-19 12:00:00', '2025-05-19 12:30:00', '2025-05-19 14:45:00', '2025-05-19 15:00:00', '2025-06-25 18:39:10'),
(209, 2147483654, '2025-05-11', '2025-05-11 08:00:00', '2025-05-11 16:00:00', NULL, NULL, '2025-05-11 10:00:00', '2025-05-11 10:15:00', '2025-05-11 12:00:00', '2025-05-11 12:30:00', '2025-05-11 14:45:00', '2025-05-11 15:00:00', '2025-06-25 18:39:10'),
(210, 2147483652, '2025-06-16', '2025-06-16 08:00:00', '2025-06-16 16:00:00', NULL, NULL, '2025-06-16 10:00:00', '2025-06-16 10:15:00', '2025-06-16 12:00:00', '2025-06-16 12:30:00', '2025-06-16 14:45:00', '2025-06-16 15:00:00', '2025-06-25 18:39:10'),
(211, 1, '2025-06-06', '2025-06-06 08:00:00', '2025-06-06 16:00:00', NULL, NULL, '2025-06-06 10:00:00', '2025-06-06 10:15:00', '2025-06-06 12:00:00', '2025-06-06 12:30:00', '2025-06-06 14:45:00', '2025-06-06 15:00:00', '2025-06-25 18:39:10'),
(212, 1, '2025-05-28', '2025-05-28 08:00:00', '2025-05-28 16:00:00', NULL, NULL, '2025-05-28 10:00:00', '2025-05-28 10:15:00', '2025-05-28 12:00:00', '2025-05-28 12:30:00', '2025-05-28 14:45:00', '2025-05-28 15:00:00', '2025-06-25 18:39:10'),
(213, 1, '2025-05-15', '2025-05-15 08:00:00', '2025-05-15 16:00:00', NULL, NULL, '2025-05-15 10:00:00', '2025-05-15 10:15:00', '2025-05-15 12:00:00', '2025-05-15 12:30:00', '2025-05-15 14:45:00', '2025-05-15 15:00:00', '2025-06-25 18:39:10'),
(214, 2147483653, '2025-05-29', '2025-05-29 08:00:00', '2025-05-29 16:00:00', NULL, NULL, '2025-05-29 10:00:00', '2025-05-29 10:15:00', '2025-05-29 12:00:00', '2025-05-29 12:30:00', '2025-05-29 14:45:00', '2025-05-29 15:00:00', '2025-06-25 18:39:10'),
(215, 2147483652, '2025-06-01', '2025-06-01 08:00:00', '2025-06-01 16:00:00', NULL, NULL, '2025-06-01 10:00:00', '2025-06-01 10:15:00', '2025-06-01 12:00:00', '2025-06-01 12:30:00', '2025-06-01 14:45:00', '2025-06-01 15:00:00', '2025-06-25 18:39:10'),
(216, 1, '2025-05-21', '2025-05-21 08:00:00', '2025-05-21 16:00:00', NULL, NULL, '2025-05-21 10:00:00', '2025-05-21 10:15:00', '2025-05-21 12:00:00', '2025-05-21 12:30:00', '2025-05-21 14:45:00', '2025-05-21 15:00:00', '2025-06-25 18:39:10'),
(217, 2147483652, '2025-05-22', '2025-05-22 08:00:00', '2025-05-22 16:00:00', NULL, NULL, '2025-05-22 10:00:00', '2025-05-22 10:15:00', '2025-05-22 12:00:00', '2025-05-22 12:30:00', '2025-05-22 14:45:00', '2025-05-22 15:00:00', '2025-06-25 18:39:10'),
(218, 1, '2025-05-29', '2025-05-29 08:00:00', '2025-05-29 16:00:00', NULL, NULL, '2025-05-29 10:00:00', '2025-05-29 10:15:00', '2025-05-29 12:00:00', '2025-05-29 12:30:00', '2025-05-29 14:45:00', '2025-05-29 15:00:00', '2025-06-25 18:39:10'),
(219, 2147483653, '2025-06-23', '2025-06-23 08:00:00', '2025-06-23 16:00:00', NULL, NULL, '2025-06-23 10:00:00', '2025-06-23 10:15:00', '2025-06-23 12:00:00', '2025-06-23 12:30:00', '2025-06-23 14:45:00', '2025-06-23 15:00:00', '2025-06-25 18:39:10'),
(220, 2147483653, '2025-05-10', '2025-05-10 08:00:00', '2025-05-10 16:00:00', NULL, NULL, '2025-05-10 10:00:00', '2025-05-10 10:15:00', '2025-05-10 12:00:00', '2025-05-10 12:30:00', '2025-05-10 14:45:00', '2025-05-10 15:00:00', '2025-06-25 18:39:10'),
(221, 2147483653, '2025-05-16', '2025-05-16 08:00:00', '2025-05-16 16:00:00', NULL, NULL, '2025-05-16 10:00:00', '2025-05-16 10:15:00', '2025-05-16 12:00:00', '2025-05-16 12:30:00', '2025-05-16 14:45:00', '2025-05-16 15:00:00', '2025-06-25 18:39:10'),
(222, 2147483654, '2025-06-29', '2025-06-29 08:00:00', '2025-06-29 16:00:00', NULL, NULL, '2025-06-29 10:00:00', '2025-06-29 10:15:00', '2025-06-29 12:00:00', '2025-06-29 12:30:00', '2025-06-29 14:45:00', '2025-06-29 15:00:00', '2025-06-25 18:39:10'),
(223, 1, '2025-05-13', '2025-05-13 08:00:00', '2025-05-13 16:00:00', NULL, NULL, '2025-05-13 10:00:00', '2025-05-13 10:15:00', '2025-05-13 12:00:00', '2025-05-13 12:30:00', '2025-05-13 14:45:00', '2025-05-13 15:00:00', '2025-06-25 18:39:10'),
(224, 2147483653, '2025-06-20', '2025-06-20 08:00:00', '2025-06-20 16:00:00', NULL, NULL, '2025-06-20 10:00:00', '2025-06-20 10:15:00', '2025-06-20 12:00:00', '2025-06-20 12:30:00', '2025-06-20 14:45:00', '2025-06-20 15:00:00', '2025-06-25 18:39:10'),
(225, 1, '2025-05-22', '2025-05-22 08:00:00', '2025-05-22 16:00:00', NULL, NULL, '2025-05-22 10:00:00', '2025-05-22 10:15:00', '2025-05-22 12:00:00', '2025-05-22 12:30:00', '2025-05-22 14:45:00', '2025-05-22 15:00:00', '2025-06-25 18:39:10'),
(226, 2147483653, '2025-05-12', '2025-05-12 08:00:00', '2025-05-12 16:00:00', NULL, NULL, '2025-05-12 10:00:00', '2025-05-12 10:15:00', '2025-05-12 12:00:00', '2025-05-12 12:30:00', '2025-05-12 14:45:00', '2025-05-12 15:00:00', '2025-06-25 18:39:10'),
(227, 2147483655, '2025-05-06', '2025-05-06 08:00:00', '2025-05-06 16:00:00', NULL, NULL, '2025-05-06 10:00:00', '2025-05-06 10:15:00', '2025-05-06 12:00:00', '2025-05-06 12:30:00', '2025-05-06 14:45:00', '2025-05-06 15:00:00', '2025-06-25 18:39:10'),
(228, 2147483652, '2025-06-07', '2025-06-07 08:00:00', '2025-06-07 16:00:00', NULL, NULL, '2025-06-07 10:00:00', '2025-06-07 10:15:00', '2025-06-07 12:00:00', '2025-06-07 12:30:00', '2025-06-07 14:45:00', '2025-06-07 15:00:00', '2025-06-25 18:39:10'),
(229, 2147483650, '2025-06-22', '2025-06-22 08:00:00', '2025-06-22 16:00:00', NULL, NULL, '2025-06-22 10:00:00', '2025-06-22 10:15:00', '2025-06-22 12:00:00', '2025-06-22 12:30:00', '2025-06-22 14:45:00', '2025-06-22 15:00:00', '2025-06-25 18:39:10'),
(230, 2147483654, '2025-06-29', '2025-06-29 08:00:00', '2025-06-29 16:00:00', NULL, NULL, '2025-06-29 10:00:00', '2025-06-29 10:15:00', '2025-06-29 12:00:00', '2025-06-29 12:30:00', '2025-06-29 14:45:00', '2025-06-29 15:00:00', '2025-06-25 18:39:10'),
(231, 2147483655, '2025-05-11', '2025-05-11 08:00:00', '2025-05-11 16:00:00', NULL, NULL, '2025-05-11 10:00:00', '2025-05-11 10:15:00', '2025-05-11 12:00:00', '2025-05-11 12:30:00', '2025-05-11 14:45:00', '2025-05-11 15:00:00', '2025-06-25 18:39:10'),
(232, 2147483655, '2025-05-20', '2025-05-20 08:00:00', '2025-05-20 16:00:00', NULL, NULL, '2025-05-20 10:00:00', '2025-05-20 10:15:00', '2025-05-20 12:00:00', '2025-05-20 12:30:00', '2025-05-20 14:45:00', '2025-05-20 15:00:00', '2025-06-25 18:39:10'),
(233, 2147483654, '2025-05-10', '2025-05-10 08:00:00', '2025-05-10 16:00:00', NULL, NULL, '2025-05-10 10:00:00', '2025-05-10 10:15:00', '2025-05-10 12:00:00', '2025-05-10 12:30:00', '2025-05-10 14:45:00', '2025-05-10 15:00:00', '2025-06-25 18:39:10'),
(234, 2147483653, '2025-06-03', '2025-06-03 08:00:00', '2025-06-03 16:00:00', NULL, NULL, '2025-06-03 10:00:00', '2025-06-03 10:15:00', '2025-06-03 12:00:00', '2025-06-03 12:30:00', '2025-06-03 14:45:00', '2025-06-03 15:00:00', '2025-06-25 18:39:10'),
(235, 2147483654, '2025-05-22', '2025-05-22 08:00:00', '2025-05-22 16:00:00', NULL, NULL, '2025-05-22 10:00:00', '2025-05-22 10:15:00', '2025-05-22 12:00:00', '2025-05-22 12:30:00', '2025-05-22 14:45:00', '2025-05-22 15:00:00', '2025-06-25 18:39:10'),
(236, 2147483653, '2025-05-04', '2025-05-04 08:00:00', '2025-05-04 16:00:00', NULL, NULL, '2025-05-04 10:00:00', '2025-05-04 10:15:00', '2025-05-04 12:00:00', '2025-05-04 12:30:00', '2025-05-04 14:45:00', '2025-05-04 15:00:00', '2025-06-25 18:39:10'),
(237, 2147483653, '2025-06-18', '2025-06-18 08:00:00', '2025-06-18 16:00:00', NULL, NULL, '2025-06-18 10:00:00', '2025-06-18 10:15:00', '2025-06-18 12:00:00', '2025-06-18 12:30:00', '2025-06-18 14:45:00', '2025-06-18 15:00:00', '2025-06-25 18:39:10'),
(238, 2147483654, '2025-05-14', '2025-05-14 08:00:00', '2025-05-14 16:00:00', NULL, NULL, '2025-05-14 10:00:00', '2025-05-14 10:15:00', '2025-05-14 12:00:00', '2025-05-14 12:30:00', '2025-05-14 14:45:00', '2025-05-14 15:00:00', '2025-06-25 18:39:10'),
(239, 2147483654, '2025-05-15', '2025-05-15 08:00:00', '2025-05-15 16:00:00', NULL, NULL, '2025-05-15 10:00:00', '2025-05-15 10:15:00', '2025-05-15 12:00:00', '2025-05-15 12:30:00', '2025-05-15 14:45:00', '2025-05-15 15:00:00', '2025-06-25 18:39:10'),
(240, 2147483652, '2025-06-22', '2025-06-22 08:00:00', '2025-06-22 16:00:00', NULL, NULL, '2025-06-22 10:00:00', '2025-06-22 10:15:00', '2025-06-22 12:00:00', '2025-06-22 12:30:00', '2025-06-22 14:45:00', '2025-06-22 15:00:00', '2025-06-25 18:39:10'),
(241, 2147483652, '2025-05-03', '2025-05-03 08:00:00', '2025-05-03 16:00:00', NULL, NULL, '2025-05-03 10:00:00', '2025-05-03 10:15:00', '2025-05-03 12:00:00', '2025-05-03 12:30:00', '2025-05-03 14:45:00', '2025-05-03 15:00:00', '2025-06-25 18:39:10'),
(242, 2147483654, '2025-05-11', '2025-05-11 08:00:00', '2025-05-11 16:00:00', NULL, NULL, '2025-05-11 10:00:00', '2025-05-11 10:15:00', '2025-05-11 12:00:00', '2025-05-11 12:30:00', '2025-05-11 14:45:00', '2025-05-11 15:00:00', '2025-06-25 18:39:10'),
(243, 2147483650, '2025-05-19', '2025-05-19 08:00:00', '2025-05-19 16:00:00', NULL, NULL, '2025-05-19 10:00:00', '2025-05-19 10:15:00', '2025-05-19 12:00:00', '2025-05-19 12:30:00', '2025-05-19 14:45:00', '2025-05-19 15:00:00', '2025-06-25 18:39:10'),
(244, 1, '2025-06-25', '2025-06-25 08:00:00', '2025-06-25 16:00:00', NULL, NULL, '2025-06-25 10:00:00', '2025-06-25 10:15:00', '2025-06-25 12:00:00', '2025-06-25 12:30:00', '2025-06-25 14:45:00', '2025-06-25 15:00:00', '2025-06-25 18:39:10'),
(245, 2147483650, '2025-06-05', '2025-06-05 08:00:00', '2025-06-05 16:00:00', NULL, NULL, '2025-06-05 10:00:00', '2025-06-05 10:15:00', '2025-06-05 12:00:00', '2025-06-05 12:30:00', '2025-06-05 14:45:00', '2025-06-05 15:00:00', '2025-06-25 18:39:10'),
(246, 2147483652, '2025-06-14', '2025-06-14 08:00:00', '2025-06-14 16:00:00', NULL, NULL, '2025-06-14 10:00:00', '2025-06-14 10:15:00', '2025-06-14 12:00:00', '2025-06-14 12:30:00', '2025-06-14 14:45:00', '2025-06-14 15:00:00', '2025-06-25 18:39:10'),
(247, 2147483653, '2025-05-22', '2025-05-22 08:00:00', '2025-05-22 16:00:00', NULL, NULL, '2025-05-22 10:00:00', '2025-05-22 10:15:00', '2025-05-22 12:00:00', '2025-05-22 12:30:00', '2025-05-22 14:45:00', '2025-05-22 15:00:00', '2025-06-25 18:39:10'),
(248, 2147483650, '2025-05-02', '2025-05-02 08:00:00', '2025-05-02 16:00:00', NULL, NULL, '2025-05-02 10:00:00', '2025-05-02 10:15:00', '2025-05-02 12:00:00', '2025-05-02 12:30:00', '2025-05-02 14:45:00', '2025-05-02 15:00:00', '2025-06-25 18:39:10'),
(249, 2147483653, '2025-05-15', '2025-05-15 08:00:00', '2025-05-15 16:00:00', NULL, NULL, '2025-05-15 10:00:00', '2025-05-15 10:15:00', '2025-05-15 12:00:00', '2025-05-15 12:30:00', '2025-05-15 14:45:00', '2025-05-15 15:00:00', '2025-06-25 18:39:10'),
(250, 1, '2025-05-27', '2025-05-27 08:00:00', '2025-05-27 16:00:00', NULL, NULL, '2025-05-27 10:00:00', '2025-05-27 10:15:00', '2025-05-27 12:00:00', '2025-05-27 12:30:00', '2025-05-27 14:45:00', '2025-05-27 15:00:00', '2025-06-25 18:39:10'),
(251, 2147483650, '2025-06-12', '2025-06-12 08:00:00', '2025-06-12 16:00:00', NULL, NULL, '2025-06-12 10:00:00', '2025-06-12 10:15:00', '2025-06-12 12:00:00', '2025-06-12 12:30:00', '2025-06-12 14:45:00', '2025-06-12 15:00:00', '2025-06-25 18:39:10'),
(252, 2147483655, '2025-05-22', '2025-05-22 08:00:00', '2025-05-22 16:00:00', NULL, NULL, '2025-05-22 10:00:00', '2025-05-22 10:15:00', '2025-05-22 12:00:00', '2025-05-22 12:30:00', '2025-05-22 14:45:00', '2025-05-22 15:00:00', '2025-06-25 18:39:10'),
(253, 1, '2025-05-22', '2025-05-22 08:00:00', '2025-05-22 16:00:00', NULL, NULL, '2025-05-22 10:00:00', '2025-05-22 10:15:00', '2025-05-22 12:00:00', '2025-05-22 12:30:00', '2025-05-22 14:45:00', '2025-05-22 15:00:00', '2025-06-25 18:39:10'),
(254, 2147483653, '2025-05-04', '2025-05-04 08:00:00', '2025-05-04 16:00:00', NULL, NULL, '2025-05-04 10:00:00', '2025-05-04 10:15:00', '2025-05-04 12:00:00', '2025-05-04 12:30:00', '2025-05-04 14:45:00', '2025-05-04 15:00:00', '2025-06-25 18:39:10'),
(255, 2147483654, '2025-06-25', '2025-06-25 08:00:00', '2025-06-25 16:00:00', NULL, NULL, '2025-06-25 10:00:00', '2025-06-25 10:15:00', '2025-06-25 12:00:00', '2025-06-25 12:30:00', '2025-06-25 14:45:00', '2025-06-25 15:00:00', '2025-06-25 18:39:10'),
(256, 2147483652, '2025-06-15', '2025-06-15 08:00:00', '2025-06-15 16:00:00', NULL, NULL, '2025-06-15 10:00:00', '2025-06-15 10:15:00', '2025-06-15 12:00:00', '2025-06-15 12:30:00', '2025-06-15 14:45:00', '2025-06-15 15:00:00', '2025-06-25 18:39:10'),
(257, 2147483654, '2025-06-22', '2025-06-22 08:00:00', '2025-06-22 16:00:00', NULL, NULL, '2025-06-22 10:00:00', '2025-06-22 10:15:00', '2025-06-22 12:00:00', '2025-06-22 12:30:00', '2025-06-22 14:45:00', '2025-06-22 15:00:00', '2025-06-25 18:39:10'),
(258, 2147483652, '2025-05-13', '2025-05-13 08:00:00', '2025-05-13 16:00:00', NULL, NULL, '2025-05-13 10:00:00', '2025-05-13 10:15:00', '2025-05-13 12:00:00', '2025-05-13 12:30:00', '2025-05-13 14:45:00', '2025-05-13 15:00:00', '2025-06-25 18:39:10'),
(259, 2147483652, '2025-06-18', '2025-06-18 08:00:00', '2025-06-18 16:00:00', NULL, NULL, '2025-06-18 10:00:00', '2025-06-18 10:15:00', '2025-06-18 12:00:00', '2025-06-18 12:30:00', '2025-06-18 14:45:00', '2025-06-18 15:00:00', '2025-06-25 18:39:10'),
(260, 2147483655, '2025-05-14', '2025-05-14 08:00:00', '2025-05-14 16:00:00', NULL, NULL, '2025-05-14 10:00:00', '2025-05-14 10:15:00', '2025-05-14 12:00:00', '2025-05-14 12:30:00', '2025-05-14 14:45:00', '2025-05-14 15:00:00', '2025-06-25 18:39:10'),
(261, 2147483650, '2025-06-17', '2025-06-17 08:00:00', '2025-06-17 16:00:00', NULL, NULL, '2025-06-17 10:00:00', '2025-06-17 10:15:00', '2025-06-17 12:00:00', '2025-06-17 12:30:00', '2025-06-17 14:45:00', '2025-06-17 15:00:00', '2025-06-25 18:39:10'),
(262, 2147483655, '2025-05-27', '2025-05-27 08:00:00', '2025-05-27 16:00:00', NULL, NULL, '2025-05-27 10:00:00', '2025-05-27 10:15:00', '2025-05-27 12:00:00', '2025-05-27 12:30:00', '2025-05-27 14:45:00', '2025-05-27 15:00:00', '2025-06-25 18:39:10'),
(263, 2147483652, '2025-06-14', '2025-06-14 08:00:00', '2025-06-14 16:00:00', NULL, NULL, '2025-06-14 10:00:00', '2025-06-14 10:15:00', '2025-06-14 12:00:00', '2025-06-14 12:30:00', '2025-06-14 14:45:00', '2025-06-14 15:00:00', '2025-06-25 18:39:10'),
(264, 1, '2025-05-11', '2025-05-11 08:00:00', '2025-05-11 16:00:00', NULL, NULL, '2025-05-11 10:00:00', '2025-05-11 10:15:00', '2025-05-11 12:00:00', '2025-05-11 12:30:00', '2025-05-11 14:45:00', '2025-05-11 15:00:00', '2025-06-25 18:39:10'),
(265, 2147483655, '2025-05-09', '2025-05-09 08:00:00', '2025-05-09 16:00:00', NULL, NULL, '2025-05-09 10:00:00', '2025-05-09 10:15:00', '2025-05-09 12:00:00', '2025-05-09 12:30:00', '2025-05-09 14:45:00', '2025-05-09 15:00:00', '2025-06-25 18:39:10'),
(266, 2147483650, '2025-06-23', '2025-06-23 08:00:00', '2025-06-23 16:00:00', NULL, NULL, '2025-06-23 10:00:00', '2025-06-23 10:15:00', '2025-06-23 12:00:00', '2025-06-23 12:30:00', '2025-06-23 14:45:00', '2025-06-23 15:00:00', '2025-06-25 18:39:10'),
(267, 2147483653, '2025-05-23', '2025-05-23 08:00:00', '2025-05-23 16:00:00', NULL, NULL, '2025-05-23 10:00:00', '2025-05-23 10:15:00', '2025-05-23 12:00:00', '2025-05-23 12:30:00', '2025-05-23 14:45:00', '2025-05-23 15:00:00', '2025-06-25 18:39:10'),
(268, 2147483653, '2025-06-02', '2025-06-02 08:00:00', '2025-06-02 16:00:00', NULL, NULL, '2025-06-02 10:00:00', '2025-06-02 10:15:00', '2025-06-02 12:00:00', '2025-06-02 12:30:00', '2025-06-02 14:45:00', '2025-06-02 15:00:00', '2025-06-25 18:39:10'),
(269, 2147483654, '2025-06-03', '2025-06-03 08:00:00', '2025-06-03 16:00:00', NULL, NULL, '2025-06-03 10:00:00', '2025-06-03 10:15:00', '2025-06-03 12:00:00', '2025-06-03 12:30:00', '2025-06-03 14:45:00', '2025-06-03 15:00:00', '2025-06-25 18:39:10'),
(270, 2147483655, '2025-06-02', '2025-06-02 08:00:00', '2025-06-02 16:00:00', NULL, NULL, '2025-06-02 10:00:00', '2025-06-02 10:15:00', '2025-06-02 12:00:00', '2025-06-02 12:30:00', '2025-06-02 14:45:00', '2025-06-02 15:00:00', '2025-06-25 18:39:10'),
(271, 2147483652, '2025-05-06', '2025-05-06 08:00:00', '2025-05-06 16:00:00', NULL, NULL, '2025-05-06 10:00:00', '2025-05-06 10:15:00', '2025-05-06 12:00:00', '2025-05-06 12:30:00', '2025-05-06 14:45:00', '2025-05-06 15:00:00', '2025-06-25 18:39:10'),
(272, 1, '2025-05-07', '2025-05-07 08:00:00', '2025-05-07 16:00:00', NULL, NULL, '2025-05-07 10:00:00', '2025-05-07 10:15:00', '2025-05-07 12:00:00', '2025-05-07 12:30:00', '2025-05-07 14:45:00', '2025-05-07 15:00:00', '2025-06-25 18:39:10'),
(273, 2147483654, '2025-06-12', '2025-06-12 08:00:00', '2025-06-12 16:00:00', NULL, NULL, '2025-06-12 10:00:00', '2025-06-12 10:15:00', '2025-06-12 12:00:00', '2025-06-12 12:30:00', '2025-06-12 14:45:00', '2025-06-12 15:00:00', '2025-06-25 18:39:10'),
(274, 2147483652, '2025-05-15', '2025-05-15 08:00:00', '2025-05-15 16:00:00', NULL, NULL, '2025-05-15 10:00:00', '2025-05-15 10:15:00', '2025-05-15 12:00:00', '2025-05-15 12:30:00', '2025-05-15 14:45:00', '2025-05-15 15:00:00', '2025-06-25 18:39:10'),
(275, 2147483650, '2025-06-14', '2025-06-14 08:00:00', '2025-06-14 16:00:00', NULL, NULL, '2025-06-14 10:00:00', '2025-06-14 10:15:00', '2025-06-14 12:00:00', '2025-06-14 12:30:00', '2025-06-14 14:45:00', '2025-06-14 15:00:00', '2025-06-25 18:39:10'),
(276, 1, '2025-06-18', '2025-06-18 08:00:00', '2025-06-18 16:00:00', NULL, NULL, '2025-06-18 10:00:00', '2025-06-18 10:15:00', '2025-06-18 12:00:00', '2025-06-18 12:30:00', '2025-06-18 14:45:00', '2025-06-18 15:00:00', '2025-06-25 18:39:10'),
(277, 2147483653, '2025-05-31', '2025-05-31 08:00:00', '2025-05-31 16:00:00', NULL, NULL, '2025-05-31 10:00:00', '2025-05-31 10:15:00', '2025-05-31 12:00:00', '2025-05-31 12:30:00', '2025-05-31 14:45:00', '2025-05-31 15:00:00', '2025-06-25 18:39:10'),
(278, 2147483652, '2025-05-26', '2025-05-26 08:00:00', '2025-05-26 16:00:00', NULL, NULL, '2025-05-26 10:00:00', '2025-05-26 10:15:00', '2025-05-26 12:00:00', '2025-05-26 12:30:00', '2025-05-26 14:45:00', '2025-05-26 15:00:00', '2025-06-25 18:39:10'),
(279, 2147483653, '2025-05-06', '2025-05-06 08:00:00', '2025-05-06 16:00:00', NULL, NULL, '2025-05-06 10:00:00', '2025-05-06 10:15:00', '2025-05-06 12:00:00', '2025-05-06 12:30:00', '2025-05-06 14:45:00', '2025-05-06 15:00:00', '2025-06-25 18:39:10'),
(280, 1, '2025-06-11', '2025-06-11 08:00:00', '2025-06-11 16:00:00', NULL, NULL, '2025-06-11 10:00:00', '2025-06-11 10:15:00', '2025-06-11 12:00:00', '2025-06-11 12:30:00', '2025-06-11 14:45:00', '2025-06-11 15:00:00', '2025-06-25 18:39:10'),
(281, 2147483650, '2025-05-11', '2025-05-11 08:00:00', '2025-05-11 16:00:00', NULL, NULL, '2025-05-11 10:00:00', '2025-05-11 10:15:00', '2025-05-11 12:00:00', '2025-05-11 12:30:00', '2025-05-11 14:45:00', '2025-05-11 15:00:00', '2025-06-25 18:39:10'),
(282, 1, '2025-06-23', '2025-06-23 08:00:00', '2025-06-23 16:00:00', NULL, NULL, '2025-06-23 10:00:00', '2025-06-23 10:15:00', '2025-06-23 12:00:00', '2025-06-23 12:30:00', '2025-06-23 14:45:00', '2025-06-23 15:00:00', '2025-06-25 18:39:10'),
(283, 2147483653, '2025-06-25', '2025-06-25 08:00:00', '2025-06-25 16:00:00', NULL, NULL, '2025-06-25 10:00:00', '2025-06-25 10:15:00', '2025-06-25 12:00:00', '2025-06-25 12:30:00', '2025-06-25 14:45:00', '2025-06-25 15:00:00', '2025-06-25 18:39:10'),
(284, 2147483650, '2025-06-23', '2025-06-23 08:00:00', '2025-06-23 16:00:00', NULL, NULL, '2025-06-23 10:00:00', '2025-06-23 10:15:00', '2025-06-23 12:00:00', '2025-06-23 12:30:00', '2025-06-23 14:45:00', '2025-06-23 15:00:00', '2025-06-25 18:39:10'),
(285, 2147483654, '2025-05-10', '2025-05-10 08:00:00', '2025-05-10 16:00:00', NULL, NULL, '2025-05-10 10:00:00', '2025-05-10 10:15:00', '2025-05-10 12:00:00', '2025-05-10 12:30:00', '2025-05-10 14:45:00', '2025-05-10 15:00:00', '2025-06-25 18:39:10'),
(286, 2147483655, '2025-05-15', '2025-05-15 08:00:00', '2025-05-15 16:00:00', NULL, NULL, '2025-05-15 10:00:00', '2025-05-15 10:15:00', '2025-05-15 12:00:00', '2025-05-15 12:30:00', '2025-05-15 14:45:00', '2025-05-15 15:00:00', '2025-06-25 18:39:10'),
(287, 1, '2025-06-24', '2025-06-24 08:00:00', '2025-06-24 16:00:00', NULL, NULL, '2025-06-24 10:00:00', '2025-06-24 10:15:00', '2025-06-24 12:00:00', '2025-06-24 12:30:00', '2025-06-24 14:45:00', '2025-06-24 15:00:00', '2025-06-25 18:39:10');
INSERT INTO `tb_horario` (`id`, `id_usuario`, `fecha`, `hora_inicio_turno`, `hora_fin_turno`, `hora_inicio_extra`, `hora_fin_extra`, `hora_inicio_break1`, `hora_fin_break1`, `hora_inicio_break2`, `hora_fin_break2`, `hora_inicio_break3`, `hora_fin_break3`, `fyh_creacion`) VALUES
(288, 1, '2025-05-28', '2025-05-28 08:00:00', '2025-05-28 16:00:00', NULL, NULL, '2025-05-28 10:00:00', '2025-05-28 10:15:00', '2025-05-28 12:00:00', '2025-05-28 12:30:00', '2025-05-28 14:45:00', '2025-05-28 15:00:00', '2025-06-25 18:39:10'),
(289, 2147483652, '2025-06-10', '2025-06-10 08:00:00', '2025-06-10 16:00:00', NULL, NULL, '2025-06-10 10:00:00', '2025-06-10 10:15:00', '2025-06-10 12:00:00', '2025-06-10 12:30:00', '2025-06-10 14:45:00', '2025-06-10 15:00:00', '2025-06-25 18:39:10'),
(290, 2147483650, '2025-06-09', '2025-06-09 08:00:00', '2025-06-09 16:00:00', NULL, NULL, '2025-06-09 10:00:00', '2025-06-09 10:15:00', '2025-06-09 12:00:00', '2025-06-09 12:30:00', '2025-06-09 14:45:00', '2025-06-09 15:00:00', '2025-06-25 18:39:10'),
(291, 2147483653, '2025-06-28', '2025-06-28 08:00:00', '2025-06-28 16:00:00', NULL, NULL, '2025-06-28 10:00:00', '2025-06-28 10:15:00', '2025-06-28 12:00:00', '2025-06-28 12:30:00', '2025-06-28 14:45:00', '2025-06-28 15:00:00', '2025-06-25 18:39:10'),
(292, 2147483653, '2025-05-03', '2025-05-03 08:00:00', '2025-05-03 16:00:00', NULL, NULL, '2025-05-03 10:00:00', '2025-05-03 10:15:00', '2025-05-03 12:00:00', '2025-05-03 12:30:00', '2025-05-03 14:45:00', '2025-05-03 15:00:00', '2025-06-25 18:39:10'),
(293, 2147483652, '2025-06-11', '2025-06-11 08:00:00', '2025-06-11 16:00:00', NULL, NULL, '2025-06-11 10:00:00', '2025-06-11 10:15:00', '2025-06-11 12:00:00', '2025-06-11 12:30:00', '2025-06-11 14:45:00', '2025-06-11 15:00:00', '2025-06-25 18:39:10'),
(294, 2147483653, '2025-05-01', '2025-05-01 08:00:00', '2025-05-01 16:00:00', NULL, NULL, '2025-05-01 10:00:00', '2025-05-01 10:15:00', '2025-05-01 12:00:00', '2025-05-01 12:30:00', '2025-05-01 14:45:00', '2025-05-01 15:00:00', '2025-06-25 18:39:10'),
(295, 1, '2025-06-13', '2025-06-13 08:00:00', '2025-06-13 16:00:00', NULL, NULL, '2025-06-13 10:00:00', '2025-06-13 10:15:00', '2025-06-13 12:00:00', '2025-06-13 12:30:00', '2025-06-13 14:45:00', '2025-06-13 15:00:00', '2025-06-25 18:39:10'),
(296, 2147483655, '2025-06-08', '2025-06-08 08:00:00', '2025-06-08 16:00:00', NULL, NULL, '2025-06-08 10:00:00', '2025-06-08 10:15:00', '2025-06-08 12:00:00', '2025-06-08 12:30:00', '2025-06-08 14:45:00', '2025-06-08 15:00:00', '2025-06-25 18:39:10'),
(297, 1, '2025-06-02', '2025-06-02 08:00:00', '2025-06-02 16:00:00', NULL, NULL, '2025-06-02 10:00:00', '2025-06-02 10:15:00', '2025-06-02 12:00:00', '2025-06-02 12:30:00', '2025-06-02 14:45:00', '2025-06-02 15:00:00', '2025-06-25 18:39:10'),
(298, 1, '2025-05-21', '2025-05-21 08:00:00', '2025-05-21 16:00:00', NULL, NULL, '2025-05-21 10:00:00', '2025-05-21 10:15:00', '2025-05-21 12:00:00', '2025-05-21 12:30:00', '2025-05-21 14:45:00', '2025-05-21 15:00:00', '2025-06-25 18:39:10'),
(299, 2147483653, '2025-06-20', '2025-06-20 08:00:00', '2025-06-20 16:00:00', NULL, NULL, '2025-06-20 10:00:00', '2025-06-20 10:15:00', '2025-06-20 12:00:00', '2025-06-20 12:30:00', '2025-06-20 14:45:00', '2025-06-20 15:00:00', '2025-06-25 18:39:10'),
(300, 2147483654, '2025-06-03', '2025-06-03 08:00:00', '2025-06-03 16:00:00', NULL, NULL, '2025-06-03 10:00:00', '2025-06-03 10:15:00', '2025-06-03 12:00:00', '2025-06-03 12:30:00', '2025-06-03 14:45:00', '2025-06-03 15:00:00', '2025-06-25 18:39:10'),
(301, 2147483653, '2025-05-06', '2025-05-06 08:00:00', '2025-05-06 16:00:00', NULL, NULL, '2025-05-06 10:00:00', '2025-05-06 10:15:00', '2025-05-06 12:00:00', '2025-05-06 12:30:00', '2025-05-06 14:45:00', '2025-05-06 15:00:00', '2025-06-25 18:39:10'),
(302, 2147483654, '2025-05-23', '2025-05-23 08:00:00', '2025-05-23 16:00:00', NULL, NULL, '2025-05-23 10:00:00', '2025-05-23 10:15:00', '2025-05-23 12:00:00', '2025-05-23 12:30:00', '2025-05-23 14:45:00', '2025-05-23 15:00:00', '2025-06-25 18:39:10'),
(303, 1, '2025-06-29', '2025-06-29 08:00:00', '2025-06-29 16:00:00', NULL, NULL, '2025-06-29 10:00:00', '2025-06-29 10:15:00', '2025-06-29 12:00:00', '2025-06-29 12:30:00', '2025-06-29 14:45:00', '2025-06-29 15:00:00', '2025-06-25 18:39:10'),
(304, 2147483654, '2025-06-25', '2025-06-25 08:00:00', '2025-06-25 16:00:00', NULL, NULL, '2025-06-25 10:00:00', '2025-06-25 10:15:00', '2025-06-25 12:00:00', '2025-06-25 12:30:00', '2025-06-25 14:45:00', '2025-06-25 15:00:00', '2025-06-25 18:39:10'),
(305, 2147483655, '2025-06-26', '2025-06-26 08:00:00', '2025-06-26 16:00:00', NULL, NULL, '2025-06-26 10:00:00', '2025-06-26 10:15:00', '2025-06-26 12:00:00', '2025-06-26 12:30:00', '2025-06-26 14:45:00', '2025-06-26 15:00:00', '2025-06-25 18:39:10'),
(306, 2147483655, '2025-06-01', '2025-06-01 08:00:00', '2025-06-01 16:00:00', NULL, NULL, '2025-06-01 10:00:00', '2025-06-01 10:15:00', '2025-06-01 12:00:00', '2025-06-01 12:30:00', '2025-06-01 14:45:00', '2025-06-01 15:00:00', '2025-06-25 18:39:10'),
(307, 2147483650, '2025-06-23', '2025-06-23 08:00:00', '2025-06-23 16:00:00', NULL, NULL, '2025-06-23 10:00:00', '2025-06-23 10:15:00', '2025-06-23 12:00:00', '2025-06-23 12:30:00', '2025-06-23 14:45:00', '2025-06-23 15:00:00', '2025-06-25 18:39:10');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_jerarquia_trabajadores`
--

CREATE TABLE `tb_jerarquia_trabajadores` (
  `id` int(11) NOT NULL,
  `id_trabajador` int(11) NOT NULL,
  `id_jefe` int(11) NOT NULL,
  `id_campana` int(11) NOT NULL,
  `fecha_registro` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `tb_jerarquia_trabajadores`
--

INSERT INTO `tb_jerarquia_trabajadores` (`id`, `id_trabajador`, `id_jefe`, `id_campana`, `fecha_registro`) VALUES
(43, 136, 82, 4, '2025-04-21 14:33:28'),
(44, 136, 84, 4, '2025-04-21 14:33:28'),
(46, 136, 80, 4, '2025-04-21 14:33:28'),
(47, 136, 83, 4, '2025-04-21 14:33:28'),
(48, 135, 82, 4, '2025-04-21 14:33:28'),
(49, 135, 84, 4, '2025-04-21 14:33:28'),
(51, 135, 80, 4, '2025-04-21 14:33:28'),
(52, 135, 83, 4, '2025-04-21 14:33:28'),
(53, 143, 82, 4, '2025-04-21 14:33:28'),
(54, 143, 84, 4, '2025-04-21 14:33:28'),
(56, 143, 80, 4, '2025-04-21 14:33:28'),
(57, 143, 83, 4, '2025-04-21 14:33:28'),
(58, 132, 82, 4, '2025-04-21 14:33:28'),
(59, 132, 84, 4, '2025-04-21 14:33:28'),
(61, 132, 80, 4, '2025-04-21 14:33:28'),
(62, 132, 83, 4, '2025-04-21 14:33:28'),
(63, 149, 82, 4, '2025-04-21 14:33:28'),
(64, 149, 84, 4, '2025-04-21 14:33:28'),
(66, 149, 80, 4, '2025-04-21 14:33:28'),
(67, 149, 83, 4, '2025-04-21 14:33:28'),
(68, 150, 82, 4, '2025-04-21 14:33:28'),
(69, 150, 84, 4, '2025-04-21 14:33:28'),
(71, 150, 80, 4, '2025-04-21 14:33:28'),
(72, 150, 83, 4, '2025-04-21 14:33:28'),
(73, 148, 82, 4, '2025-04-21 14:33:28'),
(74, 148, 84, 4, '2025-04-21 14:33:28'),
(76, 148, 80, 4, '2025-04-21 14:33:28'),
(77, 148, 83, 4, '2025-04-21 14:33:28'),
(78, 141, 82, 4, '2025-04-21 14:33:28'),
(79, 141, 84, 4, '2025-04-21 14:33:28'),
(81, 141, 80, 4, '2025-04-21 14:33:28'),
(82, 141, 83, 4, '2025-04-21 14:33:28'),
(83, 129, 82, 4, '2025-04-21 14:33:28'),
(84, 129, 84, 4, '2025-04-21 14:33:28'),
(86, 129, 80, 4, '2025-04-21 14:33:28'),
(87, 129, 83, 4, '2025-04-21 14:33:28'),
(88, 126, 82, 4, '2025-04-21 14:33:28'),
(89, 126, 84, 4, '2025-04-21 14:33:28'),
(91, 126, 80, 4, '2025-04-21 14:33:28'),
(92, 126, 83, 4, '2025-04-21 14:33:28'),
(93, 61, 82, 4, '2025-04-21 14:33:28'),
(94, 61, 84, 4, '2025-04-21 14:33:28'),
(96, 61, 80, 4, '2025-04-21 14:33:28'),
(97, 61, 83, 4, '2025-04-21 14:33:28'),
(98, 134, 82, 4, '2025-04-21 14:33:28'),
(99, 134, 84, 4, '2025-04-21 14:33:28'),
(101, 134, 80, 4, '2025-04-21 14:33:28'),
(102, 134, 83, 4, '2025-04-21 14:33:28'),
(103, 125, 82, 4, '2025-04-21 14:33:28'),
(104, 125, 84, 4, '2025-04-21 14:33:28'),
(106, 125, 80, 4, '2025-04-21 14:33:28'),
(107, 125, 83, 4, '2025-04-21 14:33:28'),
(108, 146, 82, 4, '2025-04-21 14:33:28'),
(109, 146, 84, 4, '2025-04-21 14:33:28'),
(111, 146, 80, 4, '2025-04-21 14:33:28'),
(112, 146, 83, 4, '2025-04-21 14:33:28'),
(113, 144, 82, 4, '2025-04-21 14:33:28'),
(114, 144, 84, 4, '2025-04-21 14:33:28'),
(116, 144, 80, 4, '2025-04-21 14:33:28'),
(117, 144, 83, 4, '2025-04-21 14:33:28'),
(118, 133, 82, 4, '2025-04-21 14:33:28'),
(119, 133, 84, 4, '2025-04-21 14:33:28'),
(121, 133, 80, 4, '2025-04-21 14:33:28'),
(122, 133, 83, 4, '2025-04-21 14:33:28'),
(128, 139, 82, 4, '2025-04-21 14:33:28'),
(129, 139, 84, 4, '2025-04-21 14:33:28'),
(131, 139, 80, 4, '2025-04-21 14:33:28'),
(132, 139, 83, 4, '2025-04-21 14:33:28'),
(138, 124, 82, 4, '2025-04-21 14:33:28'),
(139, 124, 84, 4, '2025-04-21 14:33:28'),
(141, 124, 80, 4, '2025-04-21 14:33:28'),
(142, 124, 83, 4, '2025-04-21 14:33:28'),
(143, 142, 82, 4, '2025-04-21 14:33:28'),
(144, 142, 84, 4, '2025-04-21 14:33:28'),
(146, 142, 80, 4, '2025-04-21 14:33:28'),
(147, 142, 83, 4, '2025-04-21 14:33:28'),
(148, 127, 82, 4, '2025-04-21 14:33:28'),
(149, 127, 84, 4, '2025-04-21 14:33:28'),
(151, 127, 80, 4, '2025-04-21 14:33:28'),
(152, 127, 83, 4, '2025-04-21 14:33:28'),
(153, 145, 82, 4, '2025-04-21 14:33:28'),
(154, 145, 84, 4, '2025-04-21 14:33:28'),
(156, 145, 80, 4, '2025-04-21 14:33:28'),
(157, 145, 83, 4, '2025-04-21 14:33:28'),
(158, 138, 82, 4, '2025-04-21 14:33:28'),
(159, 138, 84, 4, '2025-04-21 14:33:28'),
(161, 138, 80, 4, '2025-04-21 14:33:28'),
(162, 138, 83, 4, '2025-04-21 14:33:28'),
(163, 131, 82, 4, '2025-04-21 14:33:28'),
(164, 131, 84, 4, '2025-04-21 14:33:28'),
(166, 131, 80, 4, '2025-04-21 14:33:28'),
(167, 131, 83, 4, '2025-04-21 14:33:28'),
(168, 137, 82, 4, '2025-04-21 14:33:28'),
(169, 137, 84, 4, '2025-04-21 14:33:28'),
(171, 137, 80, 4, '2025-04-21 14:33:28'),
(172, 137, 83, 4, '2025-04-21 14:33:28'),
(173, 121, 82, 4, '2025-04-21 14:33:28'),
(174, 121, 84, 4, '2025-04-21 14:33:28'),
(176, 121, 80, 4, '2025-04-21 14:33:28'),
(177, 121, 83, 4, '2025-04-21 14:33:28'),
(178, 122, 82, 4, '2025-04-21 14:33:28'),
(179, 122, 84, 4, '2025-04-21 14:33:28'),
(181, 122, 80, 4, '2025-04-21 14:33:28'),
(182, 122, 83, 4, '2025-04-21 14:33:28'),
(183, 128, 82, 4, '2025-04-21 14:33:28'),
(184, 128, 84, 4, '2025-04-21 14:33:28'),
(186, 128, 80, 4, '2025-04-21 14:33:28'),
(187, 128, 83, 4, '2025-04-21 14:33:28'),
(188, 130, 82, 4, '2025-04-21 14:33:28'),
(189, 130, 84, 4, '2025-04-21 14:33:28'),
(191, 130, 80, 4, '2025-04-21 14:33:28'),
(192, 130, 83, 4, '2025-04-21 14:33:28'),
(193, 140, 82, 4, '2025-04-21 14:33:28'),
(194, 140, 84, 4, '2025-04-21 14:33:28'),
(196, 140, 80, 4, '2025-04-21 14:33:28'),
(197, 140, 83, 4, '2025-04-21 14:33:28'),
(354, 53, 49, 5, '2025-04-21 18:20:00'),
(411, 54, 152, 14, '2025-05-06 18:28:36'),
(412, 55, 152, 14, '2025-05-06 18:28:56'),
(447, 160, 152, 14, '2025-06-19 16:30:45'),
(448, 161, 152, 14, '2025-06-19 16:30:59'),
(452, 154, 83, 4, '2025-06-19 16:38:12'),
(453, 155, 83, 4, '2025-06-19 16:38:27'),
(454, 156, 83, 4, '2025-06-19 16:38:54'),
(455, 157, 83, 4, '2025-06-19 16:39:08'),
(456, 158, 83, 4, '2025-06-19 16:39:19'),
(457, 159, 83, 4, '2025-06-19 16:39:30'),
(458, 162, 83, 4, '2025-06-19 16:39:51'),
(459, 75, 163, 2, '2025-06-19 16:51:06'),
(460, 76, 163, 2, '2025-06-19 16:52:01'),
(461, 74, 163, 2, '2025-06-19 16:54:12'),
(462, 78, 163, 2, '2025-06-19 16:54:35'),
(463, 73, 163, 2, '2025-06-19 16:54:53'),
(464, 72, 163, 2, '2025-06-19 16:55:17'),
(465, 79, 163, 2, '2025-06-19 16:55:48'),
(467, 154, 84, 4, '2025-06-19 17:16:46'),
(468, 154, 80, 4, '2025-06-19 17:16:46'),
(469, 154, 82, 4, '2025-06-19 17:16:46'),
(470, 155, 84, 4, '2025-06-19 17:16:46'),
(471, 155, 80, 4, '2025-06-19 17:16:46'),
(472, 155, 82, 4, '2025-06-19 17:16:46'),
(473, 156, 84, 4, '2025-06-19 17:16:46'),
(474, 156, 80, 4, '2025-06-19 17:16:46'),
(475, 156, 82, 4, '2025-06-19 17:16:46'),
(476, 157, 84, 4, '2025-06-19 17:16:46'),
(477, 157, 80, 4, '2025-06-19 17:16:46'),
(478, 157, 82, 4, '2025-06-19 17:16:46'),
(479, 158, 84, 4, '2025-06-19 17:16:46'),
(480, 158, 80, 4, '2025-06-19 17:16:46'),
(481, 158, 82, 4, '2025-06-19 17:16:46'),
(482, 159, 84, 4, '2025-06-19 17:16:46'),
(483, 159, 80, 4, '2025-06-19 17:16:46'),
(484, 159, 82, 4, '2025-06-19 17:16:46'),
(485, 162, 84, 4, '2025-06-19 17:16:46'),
(486, 162, 80, 4, '2025-06-19 17:16:46'),
(487, 162, 82, 4, '2025-06-19 17:16:46');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_roles`
--

CREATE TABLE `tb_roles` (
  `id_rol` int(11) NOT NULL,
  `rol` varchar(255) NOT NULL,
  `fyh_creacion` datetime NOT NULL,
  `fyh_actualizacion` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `tb_roles`
--

INSERT INTO `tb_roles` (`id_rol`, `rol`, `fyh_creacion`, `fyh_actualizacion`) VALUES
(1, 'SuperAdmin', '2025-04-03 13:47:56', '2025-06-24 19:11:35'),
(2, 'Gestion Humana', '2025-04-07 14:14:16', '2025-04-07 14:14:16'),
(3, 'Administracion', '2025-05-02 20:10:52', '2025-06-24 19:15:48');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_usuarios`
--

CREATE TABLE `tb_usuarios` (
  `id_usuarios` int(10) UNSIGNED NOT NULL,
  `nombres` varchar(255) NOT NULL,
  `email` varchar(250) NOT NULL,
  `password_user` text NOT NULL,
  `token` varchar(100) NOT NULL,
  `id_rol` int(11) NOT NULL,
  `fyh_creacion` datetime NOT NULL,
  `fyh_actualizacion` datetime NOT NULL,
  `trabajador_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `tb_usuarios`
--

INSERT INTO `tb_usuarios` (`id_usuarios`, `nombres`, `email`, `password_user`, `token`, `id_rol`, `fyh_creacion`, `fyh_actualizacion`, `trabajador_id`) VALUES
(1, 'styven1', 'styvenmunera6@gmail.com', '$2y$10$H0uqYdi9UZBXxB75TDIijOqXjuQhDg0An8sMA7W5kVJ7fj605M58C', '', 1, '0000-00-00 00:00:00', '2025-06-24 18:32:18', 15),
(2147483650, 'styven2', 'styvenmunera3@gmail.com', '$2y$10$qPa8ELvi2ceLnSVFGbYKXuvLFkYoH3sLoFzTNv9kiiTpiJUiw0RZy', '', 2, '2025-04-08 20:18:26', '2025-04-08 20:18:26', 14),
(2147483652, 'styven3', 'styvenmunera1@gmail.com', '$2y$10$4FeIKo/.FnrrBRWqLkMQUOb7Kd9gCtitS2yQ.RF7onmm2dPrhPaNW', '', 3, '2025-05-02 20:11:08', '2025-05-02 20:11:08', 16),
(2147483653, 'Juan David Gomez', 'juandavid@gcshelps.com', '$2y$10$e295rbSc9R5pqUas4gtUm.KUN9dHu6a8Xx5MFt41CAif4qZy7LJBW', '', 3, '2025-05-06 14:12:50', '2025-05-06 14:12:50', 53),
(2147483654, 'Diego Silva', 'diego.silva@dlivrd.io', '$2y$10$6Sfb661r5iYRhZOyHHqkf..lrXWUKhtmDNcx3UgqyMtEqPbObOJZ6', '', 1, '2025-06-18 20:00:38', '2025-06-18 20:00:38', 58),
(2147483655, 'Yesika', 'HR@gcshelps.com', '$2y$10$xhQvXqoIZCxfioC8NMBUruIP0/89M0Xbh4UzNpx7.TYLS/vHF9sBG', '', 2, '2025-06-19 17:28:12', '2025-06-25 14:43:40', 55);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `trabajadores`
--

CREATE TABLE `trabajadores` (
  `id` int(11) NOT NULL,
  `nombre_completo` varchar(150) NOT NULL,
  `grupo_sanguineo` varchar(5) DEFAULT NULL,
  `estado_civil` varchar(50) DEFAULT NULL,
  `edad` int(11) DEFAULT NULL,
  `tipo_documento` varchar(50) DEFAULT NULL,
  `numero_documento` varchar(30) DEFAULT NULL,
  `fecha_expedicion` date DEFAULT NULL,
  `lugar_expedicion` varchar(100) DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `lugar_nacimiento` varchar(100) DEFAULT NULL,
  `nivel_estudio` varchar(100) DEFAULT NULL,
  `profesion` varchar(100) DEFAULT NULL,
  `cargo_certificado` varchar(255) DEFAULT NULL,
  `domicilio` varchar(200) DEFAULT NULL,
  `ciudad` varchar(100) DEFAULT NULL,
  `celular` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `nombre_contacto_emergencia` varchar(100) DEFAULT NULL,
  `numero_contacto_emergencia` varchar(20) DEFAULT NULL,
  `cuenta_bancaria` varchar(50) DEFAULT NULL,
  `banco` varchar(100) DEFAULT NULL,
  `tipo_cuenta` varchar(50) DEFAULT NULL,
  `tipo_contrato` varchar(50) DEFAULT NULL,
  `horas_contratadas` int(11) DEFAULT NULL,
  `salario_basico` decimal(10,2) DEFAULT NULL,
  `auxilio_transporte` decimal(10,2) DEFAULT NULL,
  `eps` varchar(100) DEFAULT NULL,
  `codigo_pension` varchar(50) DEFAULT NULL,
  `codigo_cesantias` varchar(50) DEFAULT NULL,
  `estado` enum('Activo','No Activo','No Disponible') NOT NULL DEFAULT 'Activo',
  `fecha_ingreso_falcon` date DEFAULT NULL,
  `fecha_retiro_falcon` date DEFAULT NULL,
  `salario_falcon_pesos` decimal(10,2) DEFAULT NULL,
  `salario_falcon_usd` decimal(10,2) DEFAULT NULL,
  `fecha_ingreso_gcs` date DEFAULT NULL,
  `fecha_retiro_gcs` date DEFAULT NULL,
  `tipo_retiro` enum('Retiro voluntario','Despido sin justa causa','Despido con justa causa','Finalización contrato de aprendizaje','Finalización por mutuo acuerdo') DEFAULT NULL,
  `finalizacion_contractual` date DEFAULT NULL,
  `fecha_registro` datetime DEFAULT current_timestamp(),
  `foto_perfil` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `trabajadores`
--

INSERT INTO `trabajadores` (`id`, `nombre_completo`, `grupo_sanguineo`, `estado_civil`, `edad`, `tipo_documento`, `numero_documento`, `fecha_expedicion`, `lugar_expedicion`, `fecha_nacimiento`, `lugar_nacimiento`, `nivel_estudio`, `profesion`, `cargo_certificado`, `domicilio`, `ciudad`, `celular`, `email`, `nombre_contacto_emergencia`, `numero_contacto_emergencia`, `cuenta_bancaria`, `banco`, `tipo_cuenta`, `tipo_contrato`, `horas_contratadas`, `salario_basico`, `auxilio_transporte`, `eps`, `codigo_pension`, `codigo_cesantias`, `estado`, `fecha_ingreso_falcon`, `fecha_retiro_falcon`, `salario_falcon_pesos`, `salario_falcon_usd`, `fecha_ingreso_gcs`, `fecha_retiro_gcs`, `tipo_retiro`, `finalizacion_contractual`, `fecha_registro`, `foto_perfil`) VALUES
(14, 'gestion humana nombre', 'O+', 'Casado', 45, 'CC', '3', '2010-01-01', 'Medellín', '1979-05-20', 'Medellín', 'Universitario', 'Gerente', NULL, 'Calle 10 #5-89', 'Medellín', '3105551234', 'jenn.gcshelps@gmail.com', 'Juan Perez', '3115554321', '1234567890', 'Bancolombia', 'Corriente', 'Indefinido', 40, 3000000.00, 150000.00, 'Sura', '987654321', '123456', 'Activo', '2015-06-01', NULL, 3000000.00, 0.00, '2015-06-01', NULL, '', NULL, '2025-04-16 17:29:23', '/sistema/pages/informacion/uploads/perfil/perfil_685d71524ca665.29610680.png'),
(15, 'Jefe Directo', 'O-', 'Soltero', 36, 'CC', '2', '2011-03-15', 'Medellín', '1985-08-12', 'Medellín', 'Técnico', 'Supervisor', 'Team Lead', 'Calle 20 #10-56', 'Medellín', '3105556789', 'styvenmunera3@gmail.com', 'Carlos García', '3115556789', NULL, NULL, NULL, 'Indefinido', 40, 2500000.00, 120000.00, 'Coomeva', '987654322', '654321', 'Activo', '2016-05-01', NULL, 2500000.00, 0.00, '2016-05-01', NULL, '', NULL, '2025-04-16 17:29:23', '/sistema/pages/informacion/uploads/perfil/perfil_685d5062323085.85941512.webp'),
(16, 'Trabajador Normal', 'A+', 'Soltero', 28, 'CC', '1', '2012-05-20', 'Medellín', '1991-10-10', 'Medellín', 'Bachiller', 'Operario', NULL, 'Calle 30 #15-75', 'Medellín', '3105554321', 'trabajador@ejemplo.com', 'María Pérez', '3115559876', '3456789012', 'BBVA', 'Corriente', 'Indefinido', 40, 1800000.00, 100000.00, 'Compensar', '876543210', '543210', 'Activo', '2017-08-01', NULL, 1800000.00, 0.00, '2017-08-01', NULL, '', NULL, '2025-04-16 17:29:23', NULL),
(49, 'Jennifer Manrique Rodriguez', '', '', 0, '', '1020798964', NULL, '', NULL, '', '', '', NULL, '', '', '', 'jenni@gcshelps.com', '', '', '', '', '', '', 0, 0.00, 0.00, '', '', '', 'Activo', NULL, NULL, 0.00, 0.00, NULL, NULL, '', NULL, '2025-04-16 20:38:36', NULL),
(50, 'Silvia Juliana Bernal Villamizar', NULL, NULL, NULL, NULL, '1098813389', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'silvia@gcshelps.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Activo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-16 20:38:36', NULL),
(51, 'Michelle Monick Jiménez Gerena', '', '', 0, '', '1095809304', NULL, '', NULL, '', '', '', NULL, '', '', '', 'michellejimenez99@gmail.com', '', '', '', '', '', '', 0, 0.00, 0.00, '', '', '', 'No Activo', NULL, NULL, 0.00, 0.00, NULL, NULL, 'Retiro voluntario', '2025-05-02', '2025-04-16 20:38:36', NULL),
(52, 'Jose Carlos Caballero Uribe', NULL, NULL, NULL, NULL, '1098832683', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'jose.caballero@dlivrd.io', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Activo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-16 20:38:36', NULL),
(53, 'Juan David Gomez', NULL, NULL, NULL, NULL, '1095833816', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'juand.gomezg97@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Activo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-16 20:38:36', NULL),
(54, 'Jessica Manrique Rodriguez', NULL, NULL, NULL, NULL, '1095830056', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'jessicamanriquefalcon@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Activo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-16 20:38:36', NULL),
(55, 'Yesika Alejandra Leon Fontecha', NULL, NULL, NULL, NULL, '1095822658', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'yesikaleon511@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Activo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-16 20:38:36', NULL),
(56, 'Alex Yesid Cárdenas Granados', NULL, NULL, NULL, NULL, '1094278866', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'ayc2503@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Activo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-16 20:38:36', NULL),
(57, 'Andrés Ojeda Herrera', NULL, NULL, NULL, NULL, '1096243909', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'aojedaherrera@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Activo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-16 20:38:36', NULL),
(58, 'Diego Fernando Silva Acevedo', NULL, NULL, NULL, NULL, '1102381559', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'diego.silva@dlivrd.io', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Activo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-16 20:38:36', '/sistema/pages/informacion/uploads/perfil/perfil_685d79c7bf4cf5.02414496.png'),
(59, 'Jorge Alfredo Jaimes Teheran', NULL, NULL, NULL, NULL, '1238138101', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'jjalfredo68@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Activo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-16 20:38:36', NULL),
(60, 'Juan David Esparza Castillo', NULL, NULL, NULL, NULL, '1005336311', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'davidesparzac59@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Activo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-16 20:38:36', NULL),
(61, 'Juan Sebastián Muñoz Cordero', NULL, NULL, NULL, NULL, '1095841259', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'juansemucor99@outlook.es', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Activo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-16 20:38:36', NULL),
(62, 'Diana Lucia Lozano Pinto', NULL, NULL, NULL, NULL, '1007733169', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'dianalznp@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Activo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-16 20:38:36', NULL),
(63, 'Geraldine Bernal Villamizar', NULL, NULL, NULL, NULL, '1095843231', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'geralbernal741@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Activo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-16 20:38:36', NULL),
(64, 'Kathe Quintero', NULL, NULL, NULL, NULL, '1095787771', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'kathe.quintero.0101@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Activo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-16 20:38:36', NULL),
(65, 'Laura Patricia Guerrero Toro', NULL, NULL, NULL, NULL, '1005594030', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Lauratoro0311i@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Activo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-16 20:38:36', NULL),
(66, 'Nathalia Duque Garcés', NULL, NULL, NULL, NULL, '1098800467', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Activo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-16 20:38:36', NULL),
(67, 'Nury Vanessa Avellaneda Vesga', NULL, NULL, NULL, NULL, '1098754179', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'vanessa.avellaneda18@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Activo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-16 20:38:36', NULL),
(68, 'Sofia Vitta Serrano', NULL, NULL, NULL, NULL, '1005288036', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'sofivitta02@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Activo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-16 20:38:36', NULL),
(69, 'Sthefany Juliana Arias Gomez', NULL, NULL, NULL, NULL, '1005150683', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'sthefanygomez19@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Activo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-16 20:38:36', NULL),
(70, 'Wendy Katherine Archila Quintero', NULL, NULL, NULL, NULL, '1097911124', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'wendyarchila80@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Activo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-16 20:38:36', NULL),
(71, 'Carlos Enrique Duarte Carreño', NULL, NULL, NULL, NULL, '1098808556', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'ce.duarte@hotmail.es', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Activo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-16 20:38:36', NULL),
(72, 'Juan Carlos Tapias Rueda', NULL, NULL, NULL, NULL, '1098621671', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'jutapdg@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Activo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-16 20:38:36', NULL),
(73, 'Carlos Adrián Joya Cadena', NULL, NULL, NULL, NULL, '1095841585', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'cjjoya99@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Activo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-16 20:38:36', NULL),
(74, 'Lizeth Gabriela Trillos Campos', NULL, NULL, NULL, NULL, '1005260713', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'fotografiacamtric@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Activo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-16 20:38:36', NULL),
(75, 'Maria Alejandra Quiroga Manrique', NULL, NULL, NULL, NULL, '1005257255', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'mariaquirogamanrique@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Activo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-16 20:38:36', NULL),
(76, 'Maria Camila Espinosa Serrano', NULL, NULL, NULL, NULL, '1005258404', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'milaespinosase@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Activo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-16 20:38:36', NULL),
(77, 'Angelly Natalia Peña Blanco', NULL, NULL, NULL, NULL, '1005371203', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'natpenablanco@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Activo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-16 20:38:36', NULL),
(78, 'Gustavo Andres Pachon Reyes', NULL, NULL, NULL, NULL, '1095821794', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'gustavoandrespachon25@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Activo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-16 20:38:36', NULL),
(79, 'Ivon Daniela Prada Molano', NULL, NULL, NULL, NULL, '1098815291', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'ivondanielaprada@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Activo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-16 20:38:36', NULL),
(80, 'Juliana Carolina Quiroz Caceres', NULL, NULL, NULL, NULL, '1098823097', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'juliana.quiroz@dlivrd.io', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Activo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-16 20:38:36', NULL),
(81, 'Santiago Sergio Tejeiro Mora', NULL, NULL, NULL, NULL, '1098821315', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'santiago.tejeiro@dlivrd.io', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Activo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-16 20:38:36', NULL),
(82, 'Sergio Alberto Angulo Amorocho', NULL, NULL, NULL, NULL, '1005260372', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'sergio.angulo@dlivrd.io', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Activo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-16 20:38:36', NULL),
(83, 'Silvia Alejandra Hernández Gutiérrez', NULL, NULL, NULL, NULL, '1102378878', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'silvia.gutierrez@dlivrd.io', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Activo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-16 20:38:36', NULL),
(84, 'Yuly Marcela Muñoz Estupiñan', NULL, NULL, NULL, NULL, '1007439733', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'yuly.mu-oz@dlivrd.io', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Activo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-16 20:38:36', NULL),
(121, 'Adriana Carolina Graterol Guerrero', '', '', 0, '', '1127573470', NULL, '', NULL, '', '3', '', '', '', '', '', 'grateroladriana222@gmail.com', '', '', NULL, NULL, NULL, '', 0, 0.00, 0.00, '', '', '', 'Activo', NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, '2025-04-21 14:06:03', NULL),
(122, 'Alejandro Enrique Peña Acosta', NULL, NULL, NULL, NULL, '1140422584', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Activo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-21 14:06:03', NULL),
(123, 'Alexander Rodriguez Cruz', NULL, NULL, NULL, NULL, '1098781014', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'alexrocru1996@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Activo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-21 14:06:03', NULL),
(124, 'Álvaro Andrés Sarmiento Rueda', NULL, NULL, NULL, NULL, '1098806024', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'alvarosarmiento1998@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Activo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-21 14:06:03', NULL),
(125, 'Andrés Felipe Cruz Forero', NULL, NULL, NULL, NULL, '1095951257', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'fforero9823@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Activo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-21 14:06:03', NULL),
(126, 'Andres Giovany Rincon Amaya', NULL, NULL, NULL, NULL, '1095839166', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'andresgiorincon@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Activo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-21 14:06:03', NULL),
(127, 'Andrés Libardo Beltrán Muñoz', NULL, NULL, NULL, NULL, '1098810126', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'andresb490@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Activo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-21 14:06:03', NULL),
(128, 'Brayan Jesus Lizcano Moreno', NULL, NULL, NULL, NULL, '1193148710', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'brayanlizcano258@outlook.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Activo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-21 14:06:03', NULL),
(129, 'Carlos Felipe Ardila Torres', NULL, NULL, NULL, NULL, '1095838717', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'felipeardilatorres@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Activo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-21 14:06:03', NULL),
(130, 'Carlos Rafael Gamarra Quevedo', NULL, NULL, NULL, NULL, '531056', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'nanotechvision@hotmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Activo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-21 14:06:03', NULL),
(131, 'Cristhian Felipe Olachica Escobedo', NULL, NULL, NULL, NULL, '1098826404', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pipe_19_00@hotmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Activo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-21 14:06:03', NULL),
(132, 'Daniel Felipe Quintero Vanegas', NULL, NULL, NULL, NULL, '1005324902', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'dafelquintero@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Activo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-21 14:06:03', NULL),
(133, 'Daniel Sebastian Cote Rojas', NULL, NULL, NULL, NULL, '1098736602', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'cotedanielcotedaniel2@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Activo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-21 14:06:03', NULL),
(134, 'Diana Carolina Rolon Velazquez', NULL, NULL, NULL, NULL, '1095842942', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'rolonv2010@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Activo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-21 14:06:03', NULL),
(135, 'Fabian Andrés Arenas Olave', NULL, NULL, NULL, NULL, '1005157464', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'fabianandres0512@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Activo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-21 14:06:03', NULL),
(136, 'Giovanny Andrés Pabón Villar', NULL, NULL, NULL, NULL, '1005154364', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'giopabon12345@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Activo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-21 14:06:03', NULL),
(137, 'Javier Fernando Jaime Vera', NULL, NULL, NULL, NULL, '1099735325', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'javierfernando0821@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Activo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-21 14:06:03', NULL),
(138, 'Johan Sebastián Lopez Castro', NULL, NULL, NULL, NULL, '1098816907', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'seblopez99@hotmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Activo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-21 14:06:03', NULL),
(139, 'Jose Alonso Mendoza Guerra', NULL, NULL, NULL, NULL, '1098777430', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'ralftogo1995@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Activo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-21 14:06:03', NULL),
(140, 'Jose Enrique Gamarra Quevedo', NULL, NULL, NULL, NULL, '742169', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'enriquegamarray9@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Activo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-21 14:06:03', NULL),
(141, 'Juan Diego Velasquez Santos', NULL, NULL, NULL, NULL, '1095823345', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'mercaderjuansantos@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Activo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-21 14:06:03', NULL),
(142, 'Juliana Camila Pinto Fonseca', NULL, NULL, NULL, NULL, '1098807066', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'julianafonseca98@hotmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Activo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-21 14:06:03', NULL),
(143, 'Kevin Santiago Serrano Rojas', NULL, NULL, NULL, NULL, '1005257574', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'santiblack-04@hotmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Activo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-21 14:06:03', NULL),
(144, 'Maria Camila Villanueva Serrano', NULL, NULL, NULL, NULL, '1098711545', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'macaviserr@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Activo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-21 14:06:03', NULL),
(145, 'María Claudia Jiménez Torres', NULL, NULL, NULL, NULL, '1098811047', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'maclisjimenez15@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Activo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-21 14:06:03', NULL),
(146, 'Oscar Julian Corzo Gutierrez', NULL, NULL, NULL, NULL, '1097093725', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'oscarjuliancorzo@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Activo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-21 14:06:03', NULL),
(147, 'Robinson Daniel Ortiz Cala', NULL, NULL, NULL, NULL, '1098751434', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'daniel_ortiz9403@hotmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Activo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-21 14:06:03', NULL),
(148, 'Santiago Rodriguez Lopez', NULL, NULL, NULL, NULL, '1005483223', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'santiaguito.srl76@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Activo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-21 14:06:03', NULL),
(149, 'Yersi Catalina Mendez Villamizar', NULL, NULL, NULL, NULL, '1005329286', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'catalinamendew@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Activo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-21 14:06:03', NULL),
(150, 'Yulieth Dayana Vargas Sanchez', NULL, NULL, NULL, NULL, '1005345127', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'yuliethvargass22@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Activo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-21 14:06:03', NULL),
(152, 'Maria Camila Suarez Cala', NULL, NULL, NULL, NULL, '1005282129', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'camilacala1904@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Activo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-05-06 18:26:40', NULL),
(154, 'Jose Daniel Velasco Caceres', NULL, NULL, NULL, NULL, '1007554983', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'josedanielvell@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Activo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-06-19 13:26:53', NULL),
(155, 'Juan Sebastian Mantilla Porras', NULL, NULL, NULL, NULL, '1098789653', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'juanse1008@hotmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Activo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-06-19 13:26:53', NULL),
(156, 'Maria Valentina Vega Gomez', NULL, NULL, NULL, NULL, '1005369976', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'mariavalenvg@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Activo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-06-19 13:26:53', NULL),
(157, 'Maria Paula Restrepo Romero', NULL, NULL, NULL, NULL, '1005258055', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'mariapaularestrepo04@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Activo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-06-19 13:26:53', NULL),
(158, 'Jhonnatan David Canal Vega', NULL, NULL, NULL, NULL, '1005329615', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'jhonnatancanal@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Activo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-06-19 13:26:53', NULL),
(159, 'Laura Juliana Serrano Ortiz', NULL, NULL, NULL, NULL, '1005542158', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'laurajserrano23@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Activo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-06-19 13:26:53', NULL),
(160, 'Maliuth Jireth Jimenez Contreras', NULL, NULL, NULL, NULL, '1098664908', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'maleja19.mjjc@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Activo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-06-19 13:26:53', NULL),
(161, 'Angela Marcela Leon Fontecha', NULL, NULL, NULL, NULL, '1095830000', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'marcela.9628@hotmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Activo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-06-19 13:26:53', NULL),
(162, 'Laura Cristina Anaya Martinez', NULL, NULL, NULL, NULL, '1098820826', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'lauranayam1999@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Activo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-06-19 13:26:53', NULL),
(163, 'Ivonne Juliet Peña Lopez', NULL, NULL, NULL, NULL, '63554787', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'ivonne.pena@dlivrd.io', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Activo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-06-19 13:26:53', NULL),
(164, 'Maria Paula Martinez Ortiz', NULL, NULL, NULL, NULL, '1018500154', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Mariapaulam0110@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Activo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-06-19 13:26:53', NULL),
(165, 'Silvia Juliana Reyes Melendez', NULL, NULL, NULL, NULL, '1007900998', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'reyesssilvia@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Activo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-06-19 17:25:55', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `trabajadores_campanas`
--

CREATE TABLE `trabajadores_campanas` (
  `id` int(11) NOT NULL,
  `trabajador_id` int(11) DEFAULT NULL,
  `campana_id` int(11) DEFAULT NULL,
  `cargo_id` int(11) DEFAULT NULL,
  `puesto_id` int(11) DEFAULT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `trabajadores_campanas`
--

INSERT INTO `trabajadores_campanas` (`id`, `trabajador_id`, `campana_id`, `cargo_id`, `puesto_id`, `fecha_inicio`, `fecha_fin`) VALUES
(1, 136, 4, 3, NULL, '2025-04-21', NULL),
(2, 135, 4, 3, NULL, '2025-04-21', NULL),
(3, 143, 4, 3, NULL, '2025-04-21', NULL),
(4, 132, 4, 3, NULL, '2025-04-21', NULL),
(5, 149, 4, 3, NULL, '2025-04-21', NULL),
(6, 150, 4, 3, NULL, '2025-04-21', NULL),
(7, 148, 4, 3, NULL, '2025-04-21', NULL),
(8, 141, 4, 3, NULL, '2025-04-21', NULL),
(9, 129, 4, 3, NULL, '2025-04-21', NULL),
(10, 126, 4, 3, NULL, '2025-04-21', NULL),
(11, 61, 4, 3, NULL, '2025-04-21', NULL),
(12, 134, 4, 3, NULL, '2025-04-21', NULL),
(13, 125, 4, 3, NULL, '2025-04-21', NULL),
(14, 146, 4, 3, NULL, '2025-04-21', NULL),
(15, 144, 4, 3, NULL, '2025-04-21', NULL),
(16, 133, 4, 3, NULL, '2025-04-21', NULL),
(18, 139, 4, 3, NULL, '2025-04-21', NULL),
(20, 124, 4, 3, NULL, '2025-04-21', NULL),
(21, 142, 4, 3, NULL, '2025-04-21', NULL),
(22, 127, 4, 3, NULL, '2025-04-21', NULL),
(23, 145, 4, 3, NULL, '2025-04-21', NULL),
(24, 138, 4, 3, NULL, '2025-04-21', NULL),
(25, 131, 4, 3, NULL, '2025-04-21', NULL),
(26, 137, 4, 3, NULL, '2025-04-21', NULL),
(27, 121, 4, 3, 1, '2025-04-21', NULL),
(28, 122, 4, 3, NULL, '2025-04-21', NULL),
(29, 128, 4, 3, NULL, '2025-04-21', NULL),
(30, 130, 4, 3, NULL, '2025-04-21', NULL),
(31, 140, 4, 3, NULL, '2025-04-21', NULL),
(32, 82, 4, 2, NULL, '2025-04-21', NULL),
(33, 84, 4, 2, NULL, '2025-04-21', NULL),
(35, 80, 4, 2, NULL, '2025-04-21', NULL),
(36, 83, 4, 2, NULL, '2025-04-21', NULL),
(46, 52, 3, 2, 2, '2025-04-21', NULL),
(47, 75, 2, 3, NULL, '2025-04-21', NULL),
(48, 76, 2, 3, NULL, '2025-04-21', NULL),
(49, 74, 2, 3, NULL, '2025-04-21', NULL),
(51, 78, 2, 3, NULL, '2025-04-21', NULL),
(52, 73, 2, 3, NULL, '2025-04-21', NULL),
(53, 72, 2, 3, NULL, '2025-04-21', NULL),
(54, 79, 2, 3, NULL, '2025-04-21', NULL),
(98, 53, 5, 3, NULL, '2025-04-21', NULL),
(102, 49, 5, 2, 5, '2025-04-21', NULL),
(152, 69, 12, 3, NULL, NULL, NULL),
(153, 65, 12, 3, NULL, NULL, NULL),
(154, 63, 12, 3, NULL, NULL, NULL),
(155, 70, 12, 3, NULL, NULL, NULL),
(156, 64, 12, 3, NULL, NULL, NULL),
(158, 56, 13, 3, NULL, NULL, NULL),
(159, 60, 13, 3, NULL, NULL, NULL),
(160, 152, 14, 2, NULL, NULL, NULL),
(161, 54, 14, 3, NULL, NULL, NULL),
(162, 55, 14, 3, NULL, NULL, NULL),
(165, 54, 15, 3, NULL, NULL, NULL),
(166, 55, 15, 3, NULL, NULL, NULL),
(168, 71, 18, 3, NULL, NULL, NULL),
(169, 59, 18, 3, NULL, NULL, NULL),
(170, 58, 18, 3, NULL, NULL, NULL),
(171, 57, 18, 3, NULL, NULL, NULL),
(173, 68, 17, 3, NULL, NULL, NULL),
(174, 61, 17, 3, NULL, NULL, NULL),
(178, 62, 16, 3, NULL, NULL, NULL),
(179, 61, 16, 3, NULL, NULL, NULL),
(206, 160, 14, 3, NULL, NULL, NULL),
(207, 161, 14, 3, NULL, NULL, NULL),
(209, 161, 18, 3, NULL, NULL, NULL),
(211, 164, 17, 3, NULL, NULL, NULL),
(213, 154, 4, 3, NULL, NULL, NULL),
(214, 155, 4, 3, NULL, NULL, NULL),
(215, 156, 4, 3, NULL, NULL, NULL),
(216, 157, 4, 3, NULL, NULL, NULL),
(217, 158, 4, 3, NULL, NULL, NULL),
(218, 159, 4, 3, NULL, NULL, NULL),
(219, 162, 4, 3, NULL, NULL, NULL),
(220, 163, 2, 2, NULL, NULL, NULL),
(222, 123, 21, 3, NULL, NULL, NULL),
(223, 165, 21, 3, NULL, NULL, NULL),
(224, 14, 15, 4, 4, NULL, NULL),
(225, 49, 1, 2, NULL, NULL, NULL),
(228, 147, 4, 2, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios_conectados`
--

CREATE TABLE `usuarios_conectados` (
  `id` int(11) NOT NULL,
  `id_usuario` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `sistema` varchar(100) DEFAULT NULL,
  `ip` varchar(100) DEFAULT NULL,
  `fecha_ingreso` datetime DEFAULT NULL,
  `ultima_actividad` datetime DEFAULT NULL,
  `estado` varchar(20) DEFAULT 'conectado',
  `email` varchar(255) DEFAULT NULL,
  `id_conexion` varchar(255) DEFAULT NULL,
  `ubicacion` varchar(255) DEFAULT NULL,
  `latitud` varchar(50) DEFAULT NULL,
  `longitud` varchar(50) DEFAULT NULL,
  `rango` varchar(50) DEFAULT 'Dentro'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `actividades`
--
ALTER TABLE `actividades`
  ADD PRIMARY KEY (`id_actividad`),
  ADD KEY `id_cliente` (`id_cliente`);

--
-- Indices de la tabla `cargos`
--
ALTER TABLE `cargos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id_cliente`);

--
-- Indices de la tabla `cliente_trabajador`
--
ALTER TABLE `cliente_trabajador`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_cliente` (`id_cliente`);

--
-- Indices de la tabla `facturas`
--
ALTER TABLE `facturas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `invoice_number` (`invoice_number`);

--
-- Indices de la tabla `factura_items`
--
ALTER TABLE `factura_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `factura_id` (`factura_id`);

--
-- Indices de la tabla `formularios_asignacion`
--
ALTER TABLE `formularios_asignacion`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `paginas_abiertas`
--
ALTER TABLE `paginas_abiertas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_id_conexion` (`id_conexion`);

--
-- Indices de la tabla `proyecto_actividad`
--
ALTER TABLE `proyecto_actividad`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `tb_actividades`
--
ALTER TABLE `tb_actividades`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `tb_actividad_diaria`
--
ALTER TABLE `tb_actividad_diaria`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_usuario` (`id_usuario`,`fecha`);

--
-- Indices de la tabla `tb_ausencias`
--
ALTER TABLE `tb_ausencias`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `numero_formulario` (`numero_formulario`),
  ADD KEY `id_trabajador` (`id_trabajador`),
  ADD KEY `id_campana` (`id_campana`),
  ADD KEY `id_jefe` (`id_jefe`);

--
-- Indices de la tabla `tb_campanas`
--
ALTER TABLE `tb_campanas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_campana_padre` (`id_padre`),
  ADD KEY `fk_campana_responsable` (`id_responsable`);

--
-- Indices de la tabla `tb_carrusel`
--
ALTER TABLE `tb_carrusel`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `tb_festivos`
--
ALTER TABLE `tb_festivos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `fecha` (`fecha`);

--
-- Indices de la tabla `tb_horario`
--
ALTER TABLE `tb_horario`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `tb_jerarquia_trabajadores`
--
ALTER TABLE `tb_jerarquia_trabajadores`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_trabajador` (`id_trabajador`),
  ADD KEY `id_jefe` (`id_jefe`),
  ADD KEY `id_campana` (`id_campana`);

--
-- Indices de la tabla `tb_roles`
--
ALTER TABLE `tb_roles`
  ADD PRIMARY KEY (`id_rol`);

--
-- Indices de la tabla `tb_usuarios`
--
ALTER TABLE `tb_usuarios`
  ADD PRIMARY KEY (`id_usuarios`),
  ADD KEY `id_rol` (`id_rol`);

--
-- Indices de la tabla `trabajadores`
--
ALTER TABLE `trabajadores`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `numero_documento` (`numero_documento`),
  ADD UNIQUE KEY `numero_documento_2` (`numero_documento`);

--
-- Indices de la tabla `trabajadores_campanas`
--
ALTER TABLE `trabajadores_campanas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `trabajador_id` (`trabajador_id`),
  ADD KEY `campana_id` (`campana_id`),
  ADD KEY `cargo_id` (`cargo_id`);

--
-- Indices de la tabla `usuarios_conectados`
--
ALTER TABLE `usuarios_conectados`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_conexion` (`id_conexion`),
  ADD UNIQUE KEY `id_conexion_2` (`id_conexion`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `actividades`
--
ALTER TABLE `actividades`
  MODIFY `id_actividad` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `cargos`
--
ALTER TABLE `cargos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id_cliente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `cliente_trabajador`
--
ALTER TABLE `cliente_trabajador`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT de la tabla `facturas`
--
ALTER TABLE `facturas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `factura_items`
--
ALTER TABLE `factura_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `formularios_asignacion`
--
ALTER TABLE `formularios_asignacion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=77;

--
-- AUTO_INCREMENT de la tabla `paginas_abiertas`
--
ALTER TABLE `paginas_abiertas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1186;

--
-- AUTO_INCREMENT de la tabla `proyecto_actividad`
--
ALTER TABLE `proyecto_actividad`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=102;

--
-- AUTO_INCREMENT de la tabla `tb_actividades`
--
ALTER TABLE `tb_actividades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `tb_actividad_diaria`
--
ALTER TABLE `tb_actividad_diaria`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1259;

--
-- AUTO_INCREMENT de la tabla `tb_ausencias`
--
ALTER TABLE `tb_ausencias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT de la tabla `tb_campanas`
--
ALTER TABLE `tb_campanas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de la tabla `tb_carrusel`
--
ALTER TABLE `tb_carrusel`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `tb_festivos`
--
ALTER TABLE `tb_festivos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `tb_horario`
--
ALTER TABLE `tb_horario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=308;

--
-- AUTO_INCREMENT de la tabla `tb_jerarquia_trabajadores`
--
ALTER TABLE `tb_jerarquia_trabajadores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=502;

--
-- AUTO_INCREMENT de la tabla `tb_roles`
--
ALTER TABLE `tb_roles`
  MODIFY `id_rol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `tb_usuarios`
--
ALTER TABLE `tb_usuarios`
  MODIFY `id_usuarios` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2147483658;

--
-- AUTO_INCREMENT de la tabla `trabajadores`
--
ALTER TABLE `trabajadores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=168;

--
-- AUTO_INCREMENT de la tabla `trabajadores_campanas`
--
ALTER TABLE `trabajadores_campanas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=231;

--
-- AUTO_INCREMENT de la tabla `usuarios_conectados`
--
ALTER TABLE `usuarios_conectados`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1269;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `actividades`
--
ALTER TABLE `actividades`
  ADD CONSTRAINT `actividades_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`) ON DELETE CASCADE;

--
-- Filtros para la tabla `cliente_trabajador`
--
ALTER TABLE `cliente_trabajador`
  ADD CONSTRAINT `cliente_trabajador_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`) ON DELETE CASCADE;

--
-- Filtros para la tabla `factura_items`
--
ALTER TABLE `factura_items`
  ADD CONSTRAINT `factura_items_ibfk_1` FOREIGN KEY (`factura_id`) REFERENCES `facturas` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `paginas_abiertas`
--
ALTER TABLE `paginas_abiertas`
  ADD CONSTRAINT `paginas_abiertas_ibfk_1` FOREIGN KEY (`id_conexion`) REFERENCES `usuarios_conectados` (`id_conexion`);

--
-- Filtros para la tabla `tb_ausencias`
--
ALTER TABLE `tb_ausencias`
  ADD CONSTRAINT `tb_ausencias_ibfk_1` FOREIGN KEY (`id_trabajador`) REFERENCES `trabajadores` (`id`),
  ADD CONSTRAINT `tb_ausencias_ibfk_3` FOREIGN KEY (`id_jefe`) REFERENCES `trabajadores` (`id`);

--
-- Filtros para la tabla `tb_campanas`
--
ALTER TABLE `tb_campanas`
  ADD CONSTRAINT `fk_campana_padre` FOREIGN KEY (`id_padre`) REFERENCES `tb_campanas` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_campana_responsable` FOREIGN KEY (`id_responsable`) REFERENCES `trabajadores` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `tb_jerarquia_trabajadores`
--
ALTER TABLE `tb_jerarquia_trabajadores`
  ADD CONSTRAINT `tb_jerarquia_trabajadores_ibfk_1` FOREIGN KEY (`id_trabajador`) REFERENCES `trabajadores` (`id`),
  ADD CONSTRAINT `tb_jerarquia_trabajadores_ibfk_2` FOREIGN KEY (`id_jefe`) REFERENCES `trabajadores` (`id`),
  ADD CONSTRAINT `tb_jerarquia_trabajadores_ibfk_3` FOREIGN KEY (`id_campana`) REFERENCES `tb_campanas` (`id`);

--
-- Filtros para la tabla `tb_usuarios`
--
ALTER TABLE `tb_usuarios`
  ADD CONSTRAINT `tb_usuarios_ibfk_1` FOREIGN KEY (`id_rol`) REFERENCES `tb_roles` (`id_rol`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `trabajadores_campanas`
--
ALTER TABLE `trabajadores_campanas`
  ADD CONSTRAINT `trabajadores_campanas_ibfk_1` FOREIGN KEY (`trabajador_id`) REFERENCES `trabajadores` (`id`),
  ADD CONSTRAINT `trabajadores_campanas_ibfk_2` FOREIGN KEY (`campana_id`) REFERENCES `tb_campanas` (`id`),
  ADD CONSTRAINT `trabajadores_campanas_ibfk_3` FOREIGN KEY (`cargo_id`) REFERENCES `cargos` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
