-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 25-05-2021 a las 01:45:32
-- Versión del servidor: 10.4.13-MariaDB
-- Versión de PHP: 7.4.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `el_rayo`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `abonos`
--

CREATE TABLE `abonos` (
  `id` int(11) NOT NULL,
  `id_credito` int(11) NOT NULL,
  `fecha` varchar(50) NOT NULL,
  `abono` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `abonos`
--

INSERT INTO `abonos` (`id`, `id_credito`, `fecha`, `abono`) VALUES
(54, 19, '22-05-2021', '100.00'),
(55, 19, '2021-05-22', '50.00'),
(56, 19, '2021-05-22', '700.00'),
(57, 20, '22-05-2021', '100.00'),
(58, 20, '2021-05-22', '10.00'),
(59, 20, '2021-05-22', '740.00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `id` int(11) NOT NULL,
  `Nombre_Cliente` varchar(150) DEFAULT NULL,
  `Telefono` varchar(150) DEFAULT NULL,
  `Direccion` varchar(250) DEFAULT NULL,
  `Correo` varchar(150) DEFAULT NULL,
  `Credito` varchar(100) NOT NULL,
  `RFC` varchar(100) NOT NULL,
  `Latitud` varchar(250) NOT NULL,
  `Longitud` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`id`, `Nombre_Cliente`, `Telefono`, `Direccion`, `Correo`, `Credito`, `RFC`, `Latitud`, `Longitud`) VALUES
(1, 'Publico en general', '0', 'No aplica', 'No aplica', '0', 'No aplica', '', ''),
(2, 'Brayan Maldonado', '8683471939', 'Calle Atapco #613 Mov. 18 de Octubre H. Matamoros Tamaulipass', 'natsu5679@gmail.com', '1', 'HEHJ740317KX5', '25.791644589398373', '-97.50238877908083'),
(3, 'Ricardo Reyna', '8683454545', 'San Benito 123 Calle Itil s', 'No aplica', '0', 'HEHJ740317KX5', '25.80469272879482', '-97.52006471303417'),
(4, 'Javier Cortez', '85245454', 'kvfbdskjcvbdscv', 'No aplica', '1', 'No aplica', '25.859776159143067', '-97.51418324029747'),
(5, 'Saudiel Grajales', '866356885', 'Calle 16 #582-A COlonia Buerna Vista', 'jhernandez@powerpsc.com.mx', '1', 'HEHJ740317KX5', '25.86004585553883', '-97.51279657026969');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `creditos`
--

CREATE TABLE `creditos` (
  `id` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `pagado` decimal(10,2) NOT NULL,
  `restante` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `estatus` int(11) NOT NULL,
  `fecha_inicio` varchar(50) NOT NULL,
  `fecha_final` varchar(50) NOT NULL,
  `plazo` int(11) NOT NULL,
  `id_venta` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `creditos`
--

INSERT INTO `creditos` (`id`, `id_cliente`, `pagado`, `restante`, `total`, `estatus`, `fecha_inicio`, `fecha_final`, `plazo`, `id_venta`) VALUES
(19, 2, '850.00', '0.00', '850.00', 3, '22-05-2021', '29-05-2021', 1, 226),
(20, 2, '850.00', '0.00', '850.00', 3, '22-05-2021', '29-05-2021', 1, 227);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_venta`
--

CREATE TABLE `detalle_venta` (
  `id` int(11) NOT NULL,
  `id_Venta` int(11) NOT NULL,
  `id_Llanta` int(11) NOT NULL,
  `Cantidad` int(11) NOT NULL,
  `Modelo` varchar(250) NOT NULL,
  `Unidad` varchar(60) NOT NULL,
  `precio_Unitario` decimal(11,2) NOT NULL,
  `Importe` decimal(11,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `detalle_venta`
--

INSERT INTO `detalle_venta` (`id`, `id_Venta`, `id_Llanta`, `Cantidad`, `Modelo`, `Unidad`, `precio_Unitario`, `Importe`) VALUES
(20, 226, 11, 1, 'T68', 'pieza', '850.00', '850.00'),
(21, 227, 11, 1, 'T68', 'pieza', '850.00', '850.00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inventario_mat1`
--

CREATE TABLE `inventario_mat1` (
  `id` int(11) NOT NULL,
  `id_Llanta` int(11) NOT NULL,
  `Codigo` varchar(120) NOT NULL,
  `Sucursal` varchar(120) NOT NULL,
  `Stock` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `inventario_mat1`
--

INSERT INTO `inventario_mat1` (`id`, `id_Llanta`, `Codigo`, `Sucursal`, `Stock`) VALUES
(33, 1, 'PEDC1', 'Pedro Cardenas', 10),
(34, 11, 'PEDC11', 'Pedro Cardenas', 31),
(37, 12, 'PEDC12', 'Pedro Cardenas', 10);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inventario_mat2`
--

CREATE TABLE `inventario_mat2` (
  `id` int(11) NOT NULL,
  `id_Llanta` int(11) NOT NULL,
  `Codigo` varchar(120) NOT NULL,
  `Sucursal` varchar(120) NOT NULL,
  `Stock` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `inventario_mat2`
--

INSERT INTO `inventario_mat2` (`id`, `id_Llanta`, `Codigo`, `Sucursal`, `Stock`) VALUES
(12, 11, 'SEND11', 'Sendero', 91),
(14, 12, 'SEND12', 'Sendero', 19);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llantas`
--

CREATE TABLE `llantas` (
  `id` int(11) NOT NULL,
  `Ancho` int(11) NOT NULL,
  `Proporcion` int(11) NOT NULL,
  `Diametro` varchar(150) NOT NULL,
  `Descripcion` varchar(250) NOT NULL,
  `Marca` varchar(150) NOT NULL,
  `Modelo` varchar(150) NOT NULL,
  `precio_Inicial` decimal(11,2) NOT NULL,
  `precio_Venta` decimal(11,2) NOT NULL,
  `precio_Mayoreo` decimal(11,2) NOT NULL,
  `Fecha` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `llantas`
--

INSERT INTO `llantas` (`id`, `Ancho`, `Proporcion`, `Diametro`, `Descripcion`, `Marca`, `Modelo`, `precio_Inicial`, `precio_Venta`, `precio_Mayoreo`, `Fecha`) VALUES
(11, 185, 75, 'R13', 'Llanta 185/75/R13', 'Ling-Long', 'T68', '750.00', '850.00', '550.00', '2021-03-16'),
(12, 175, 65, 'R15', 'Llanta 175/65/R161', 'Sumitomo', '65', '890.00', '960.00', '600.00', '2021-03-02'),
(31, 12, 121, '12', '123', 'Austone', '12', '12.00', '12.00', '12.00', '2021-05-08');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `marcas`
--

CREATE TABLE `marcas` (
  `id` int(11) NOT NULL,
  `Nombre` varchar(250) NOT NULL,
  `Imagen` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `marcas`
--

INSERT INTO `marcas` (`id`, `Nombre`, `Imagen`) VALUES
(1, 'America\'s', 'Americas'),
(2, 'Annaite', 'Annaite'),
(3, 'Antares', 'Antares'),
(4, 'Atlas', 'Atlas'),
(5, 'Austone', 'Austone'),
(8, 'Autogrip', 'Autogrip'),
(9, 'Austone', 'Austone'),
(10, 'Autogrip', 'Autogrip'),
(11, 'Austone', 'Austone'),
(12, 'Black Lion', 'Black-Lion'),
(13, 'Cooper', 'Cooper'),
(14, 'Deestone', 'Deestone'),
(15, 'Durable', 'Durable'),
(16, 'Duraturn', 'Duraturn'),
(17, 'Dynasty', 'Dynasty'),
(18, 'Epsilon', 'Epsilon'),
(19, 'Falken', 'Falken'),
(20, 'Farroad', 'Farroad'),
(21, 'Fromway', 'Fromway'),
(22, 'Dynasty', 'Dynasty'),
(23, 'Golden Crown', 'Golden-Crown'),
(24, 'Goodride', 'Goodride'),
(25, 'Goodyear', 'Goodyear'),
(26, 'Hankook', 'Hankook'),
(27, 'Hifly', 'Hifly'),
(28, 'JK tyre', 'JK-tyre'),
(29, 'Firemax', 'Firemax'),
(30, 'Kpatos', 'Kpatos'),
(31, 'Laufenn', 'Laufenn'),
(32, 'Ling Long', 'Ling-Long'),
(33, 'Marshal', 'Marshal'),
(34, 'Mastercraft', 'Mastercraft'),
(35, 'Maxtrek', 'Maxtrek'),
(36, 'Maxxis', 'Maxxis'),
(37, 'Mazzini', 'Mazzini'),
(38, 'Michelin', 'Michelin'),
(39, 'Mirage', 'Mirage'),
(40, 'Mud Claw', 'Mud-Claw'),
(41, 'Nexen', 'Nexen'),
(42, 'Onyx', 'Onyx'),
(43, 'Power king', 'Power-king'),
(44, 'Primewell', 'Primewell'),
(45, 'Roadmaster', 'Roadmaster'),
(46, 'Royal Black', 'Royal-Black'),
(47, 'Saferich', 'Saferich'),
(48, 'Sailun', 'Sailun'),
(49, 'Sotera', 'Sotera'),
(50, 'Starfire', 'Starfire'),
(51, 'Sumitomo', 'Sumitomo'),
(52, 'Three-A', 'Three-A'),
(53, 'Thunderer', 'Thunderer'),
(54, 'Tornel', 'Tornel'),
(55, 'Torque', 'Torque'),
(56, 'Uniroyal', 'Uniroyal'),
(57, 'Wanli', 'Wanli'),
(58, 'West Lake', 'West-Lake'),
(59, 'Winrun', 'Winrun'),
(60, 'Xcent', 'Xcent'),
(61, 'Zetum', 'Zetum'),
(62, 'Seiberling', 'Seiberling'),
(63, 'BFGoodrich', 'BFGoodrich'),
(64, 'Bridgestone', 'Bridgestone'),
(65, 'American Farmer', 'American-farmer'),
(66, 'Trazano', 'Trazano'),
(67, 'Carlisle', 'Carlisle'),
(68, 'Onix', 'Onix'),
(69, 'BKT', 'BKT'),
(70, 'Aurora', 'Aurora'),
(71, 'Continental', 'Continental'),
(72, 'Dunlop', 'Dunlop'),
(73, 'Firestone', 'Firestone'),
(74, 'Fuzion', 'Fuzion'),
(75, 'Kelly', 'Kelly'),
(76, 'Pirelli', 'Pirelli'),
(77, 'Venezia', 'Venezia'),
(78, 'Yokohama', 'Yokohama');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos_temp1`
--

CREATE TABLE `productos_temp1` (
  `id` int(11) NOT NULL,
  `codigo` varchar(255) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `modelo` varchar(255) DEFAULT NULL,
  `cantidad` int(11) DEFAULT NULL,
  `precio` decimal(10,2) DEFAULT NULL,
  `importe` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rol`
--

CREATE TABLE `rol` (
  `id` int(11) NOT NULL,
  `Rol` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sucursal`
--

CREATE TABLE `sucursal` (
  `id` int(11) NOT NULL,
  `Direccion` varchar(250) DEFAULT NULL,
  `Telefono` varchar(150) DEFAULT NULL,
  `RFC` varchar(80) DEFAULT NULL,
  `CP` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `sucursal`
--

INSERT INTO `sucursal` (`id`, `Direccion`, `Telefono`, `RFC`, `CP`) VALUES
(1, 'Av Pedro Cárdenas, Francisco Castellanos, 87394 Heroica Matamoros, Tamp', '868-1275833', '0', 0),
(2, 'AV SENDERO NACIONAL KM 2 ', '868-1010770', '0', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `token`
--

CREATE TABLE `token` (
  `id` int(11) NOT NULL,
  `codigo` varchar(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `token`
--

INSERT INTO `token` (`id`, `codigo`) VALUES
(1, '3937');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `apellidos` varchar(150) NOT NULL,
  `usuario` varchar(60) NOT NULL,
  `password` varchar(255) NOT NULL,
  `cumple` date NOT NULL,
  `rol` varchar(20) NOT NULL,
  `numero` varchar(60) NOT NULL,
  `direccion` varchar(250) NOT NULL,
  `sucursal` varchar(80) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `apellidos`, `usuario`, `password`, `cumple`, `rol`, `numero`, `direccion`, `sucursal`) VALUES
(1, 'Brayan', 'Maldonado Morgado', 'brayanm03', '$2y$10$pUS2X3fq6u4kItEaChK8dOrk0RCULyzeSLrvrpKIELA90ZgNHDK.e', '2021-03-16', '1', '8683471939', 'Col 18 de Octubre', 'Sendero'),
(2, 'Ricardo', 'Reyna', 'rikii01', '$2y$10$PTEaqWNO3PAAmEbHtTjewOduEjLZ8zpGX/5phnODfBAQzWXHP/60G', '2021-04-06', '1', '86838528400', 'Col las patas u134', 'Pedro Cardenas'),
(3, 'Candelaria', 'Diaz', 'cande01', '$2y$10$1WITVoaYBq9Gy2afmMQ8VeNuqVmt/WjgIDIlwlr9ZOvUefQ3xJt2.', '2021-04-06', '3', '0000', 'Sin asignar', 'Pedro Cardenas');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

CREATE TABLE `ventas` (
  `id` int(11) NOT NULL,
  `Fecha` varchar(50) NOT NULL DEFAULT current_timestamp(),
  `id_Sucursal` varchar(50) NOT NULL,
  `id_Usuarios` int(11) NOT NULL,
  `id_Cliente` int(11) NOT NULL,
  `Cantidad` int(11) NOT NULL,
  `Total` decimal(10,2) NOT NULL,
  `estatus` varchar(50) NOT NULL,
  `metodo_pago` varchar(150) NOT NULL,
  `hora` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `ventas`
--

INSERT INTO `ventas` (`id`, `Fecha`, `id_Sucursal`, `id_Usuarios`, `id_Cliente`, `Cantidad`, `Total`, `estatus`, `metodo_pago`, `hora`) VALUES
(226, '2021-05-22', 'Sendero', 1, 2, 0, '850.00', 'Abierta', 'Targeta', '11:26 am'),
(227, '2021-05-22', 'Sendero', 1, 2, 0, '850.00', 'Abierta', 'Targeta', '01:48 pm');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `abonos`
--
ALTER TABLE `abonos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `creditos`
--
ALTER TABLE `creditos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `detalle_venta`
--
ALTER TABLE `detalle_venta`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `inventario_mat1`
--
ALTER TABLE `inventario_mat1`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `inventario_mat2`
--
ALTER TABLE `inventario_mat2`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `llantas`
--
ALTER TABLE `llantas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `marcas`
--
ALTER TABLE `marcas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `productos_temp1`
--
ALTER TABLE `productos_temp1`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `rol`
--
ALTER TABLE `rol`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `sucursal`
--
ALTER TABLE `sucursal`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `token`
--
ALTER TABLE `token`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_Cliente` (`id_Cliente`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `abonos`
--
ALTER TABLE `abonos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `creditos`
--
ALTER TABLE `creditos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `detalle_venta`
--
ALTER TABLE `detalle_venta`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de la tabla `inventario_mat1`
--
ALTER TABLE `inventario_mat1`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT de la tabla `inventario_mat2`
--
ALTER TABLE `inventario_mat2`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `llantas`
--
ALTER TABLE `llantas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT de la tabla `marcas`
--
ALTER TABLE `marcas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=79;

--
-- AUTO_INCREMENT de la tabla `productos_temp1`
--
ALTER TABLE `productos_temp1`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `rol`
--
ALTER TABLE `rol`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sucursal`
--
ALTER TABLE `sucursal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `token`
--
ALTER TABLE `token`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=228;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD CONSTRAINT `ventas_ibfk_1` FOREIGN KEY (`id_Cliente`) REFERENCES `clientes` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
