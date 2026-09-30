-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 30-09-2026 a las 20:52:21
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `sistema`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `carritos`
--

CREATE TABLE `carritos` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `carritos`
--

INSERT INTO `carritos` (`id`, `usuario_id`, `fecha_creacion`, `fecha_actualizacion`) VALUES
(2, 7, '2026-09-30 18:22:53', '2026-09-30 18:22:53');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `carrito_items`
--

CREATE TABLE `carrito_items` (
  `id` int(11) NOT NULL,
  `carrito_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `compras`
--

CREATE TABLE `compras` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `fecha` datetime DEFAULT current_timestamp(),
  `fecha_confirmacion` datetime DEFAULT NULL,
  `estado` enum('pendiente','confirmada','cancelada') DEFAULT 'pendiente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `compras`
--

INSERT INTO `compras` (`id`, `usuario_id`, `fecha`, `fecha_confirmacion`, `estado`) VALUES
(1, 7, '2026-09-30 15:24:50', NULL, 'confirmada'),
(2, 7, '2026-09-30 15:46:02', '2026-09-30 15:46:31', 'confirmada');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `compra_items`
--

CREATE TABLE `compra_items` (
  `id` int(11) NOT NULL,
  `compra_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `compra_items`
--

INSERT INTO `compra_items` (`id`, `compra_id`, `producto_id`, `cantidad`, `precio`) VALUES
(1, 1, 17, 1, 35000.00),
(2, 1, 18, 1, 25.00),
(3, 2, 15, 1, 10000.00),
(4, 2, 12, 1, 200.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos`
--

CREATE TABLE `pedidos` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `fecha` datetime DEFAULT current_timestamp(),
  `total` decimal(10,2) NOT NULL,
  `estado` varchar(50) DEFAULT 'Pendiente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(20) NOT NULL,
  `precio` int(7) NOT NULL,
  `stock` int(255) NOT NULL,
  `imagen` varchar(255) NOT NULL,
  `categoria` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `nombre`, `precio`, `stock`, `imagen`, `categoria`) VALUES
(10, 'Mate De Madera', 200, 78, 'mate madera.webp', 'mates'),
(11, 'Mate De Calabaza', 200, 77, 'mate calabaza.jpg', 'mates'),
(12, 'Mate De Vidrio', 200, 79, 'mate de vidrio.webp', 'mates'),
(13, 'Pava Electrica', 50000, 800, 'pava electrica.webp', 'pavas'),
(14, 'Pava Aluminio', 20000, 800, 'pava aluminio.webp', 'pavas'),
(15, 'Yerba Canarias', 10000, 899, 'yerba canarias.jpg', 'yerbas'),
(16, 'Termo Stanley', 8000000, 898, 'termo stanley.webp', 'termos'),
(17, 'Termo Lumilagro', 35000, 905, 'termo lumilagro.webp', 'termos'),
(18, 'quinterooo', 25, 911, '6aa1e0528b2c7.webp', 'Termos');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `direccion` varchar(255) NOT NULL,
  `codigo_postal` varchar(20) NOT NULL,
  `localidad` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol` enum('admin','cliente') DEFAULT 'cliente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `email`, `direccion`, `codigo_postal`, `localidad`, `password`, `rol`) VALUES
(1, 'Nahuel Muñiz', 'nahuelmuniz18@gmail.com', '', '', '', '$2y$10$Xo4BN4PXqXwUII.tYiNozeXFT8mQt73gjj2tM2l1K91KhzRjqLcRW', 'admin'),
(4, 'Nahuel 2', 'nahuelbenjamisonic@gmail.com', '', '', '', '123456', 'cliente'),
(7, 'Nahuel Muñiz', 'nahuwurhcp@gmail.com', 'Rawson 4453', '8000', 'Bahía Blanca', '$2y$10$DQjm7nADKllSy/iAyz8yZOqCPEjNrrPiRI7PPz2B8iwS1ng2hXKy.', 'cliente');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `carritos`
--
ALTER TABLE `carritos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_usuario_carrito` (`usuario_id`);

--
-- Indices de la tabla `carrito_items`
--
ALTER TABLE `carrito_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_producto_carrito` (`carrito_id`,`producto_id`),
  ADD KEY `fk_item_producto` (`producto_id`);

--
-- Indices de la tabla `compras`
--
ALTER TABLE `compras`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `compra_items`
--
ALTER TABLE `compra_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `compra_id` (`compra_id`),
  ADD KEY `producto_id` (`producto_id`);

--
-- Indices de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_pedido_usuario` (`usuario_id`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `precio` (`precio`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `carritos`
--
ALTER TABLE `carritos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `carrito_items`
--
ALTER TABLE `carrito_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de la tabla `compras`
--
ALTER TABLE `compras`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `compra_items`
--
ALTER TABLE `compra_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `carritos`
--
ALTER TABLE `carritos`
  ADD CONSTRAINT `fk_carrito_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `carrito_items`
--
ALTER TABLE `carrito_items`
  ADD CONSTRAINT `fk_item_carrito` FOREIGN KEY (`carrito_id`) REFERENCES `carritos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_item_producto` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `compras`
--
ALTER TABLE `compras`
  ADD CONSTRAINT `compras_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `compra_items`
--
ALTER TABLE `compra_items`
  ADD CONSTRAINT `compra_items_ibfk_1` FOREIGN KEY (`compra_id`) REFERENCES `compras` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `compra_items_ibfk_2` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD CONSTRAINT `fk_pedido_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
