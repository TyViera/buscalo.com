-- CREATE DATABASE vuelos_lan;
--
-- Base de datos: `vuelos_lan`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `flights`
--

CREATE TABLE flights(
	id SERIAL NOT NULL,
	airport VARCHAR(100) NOT NULL,
	flight_source VARCHAR(100) NOT NULL,
	flight_destination VARCHAR(100) NOT NULL,
	flight_date TIMESTAMP NOT NULL,
    price NUMERIC(10,2) NOT NULL
	CONSTRAINT flight_pkey PRIMARY KEY ("id")
);

-- Datos