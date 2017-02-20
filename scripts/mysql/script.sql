-- create database avianca;
--
-- Base de datos: `avianca`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vuelo`
--

CREATE TABLE `vuelo` (
  `idVuelo` char(7) COLLATE utf8_spanish_ci NOT NULL,
  `aeropuerto` varchar(50) COLLATE utf8_spanish_ci NOT NULL,
  `origen` varchar(50) COLLATE utf8_spanish_ci NOT NULL,
  `destino` varchar(50) COLLATE utf8_spanish_ci NOT NULL,
  `fechaIda` varchar(20) COLLATE utf8_spanish_ci NOT NULL,
  `fechaVuelta` varchar(20) COLLATE utf8_spanish_ci NOT NULL,
  `tipoVuelo` varchar(40) COLLATE utf8_spanish_ci NOT NULL,
  `horaVuelo` varchar(20) COLLATE utf8_spanish_ci NOT NULL,
  `precio` varchar(20) COLLATE utf8_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `vuelo`
--
ALTER TABLE `vuelo`
  ADD PRIMARY KEY (`idVuelo`);
