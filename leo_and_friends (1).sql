-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 20, 2026 at 06:41 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `leo_and_friends`
--

-- --------------------------------------------------------

--
-- Table structure for table `cuentos`
--

CREATE TABLE `cuentos` (
  `cuentoID` int(11) NOT NULL,
  `titulo` varchar(150) NOT NULL,
  `imagen` varchar(255) DEFAULT NULL,
  `contenido` text NOT NULL,
  `nivel` enum('1','2','3','4','5') DEFAULT NULL,
  `categoria` enum('principal','nuevo') DEFAULT 'principal',
  `precio_monedas` int(11) DEFAULT 0,
  `orden` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cuentos`
--

INSERT INTO `cuentos` (`cuentoID`, `titulo`, `imagen`, `contenido`, `nivel`, `categoria`, `precio_monedas`, `orden`) VALUES
(1, 'La araña y la manzana', 'anaManzana.png', 'La araña Ana ve una manzana.\r\nAna ama la manzana y la agarra.\r\nLa araña Ana ahora salta y canta.\r\nAna, es una araña muy feliz con su manzana.', '1', 'principal', 0, 1),
(2, 'Leo el camaleón', 'historiaLeo.png', 'Leo es un camaleón que cambia de color. Si está triste, se pone azul. Si está feliz, se pone verde. Un día, jugando con sus amigos, se emocionó y brilló como el arcoíris. ¡A todos les encantó! Leo supo que sus colores eran un gran superpoder.', '2', 'principal', 0, 2),
(3, 'La aventura en la selva', 'aventura.png', 'Leo, Finx y Capy decidieron seguir un mapa misterioso que encontraron bajo una roca. El mapa los llevó por caminos llenos de flores gigantes que cantaban y árboles que daban saltos. Al final del camino no había oro ni joyas, ¡sino un árbol repleto de las frutas más dulces de la selva! Descubrieron que el mejor tesoro de una aventura es compartir el viaje con tus mejores amigos.', '4', 'principal', 0, 3),
(4, 'Mía la mariposa', 'mari.png', 'Mía era una mariposa con alas de color morado brillante a la que le encantaba buscar palabras nuevas. Cada vez que visitaba una flor diferente, la flor le regalaba una palabra hermosa, como \"brillo\", \"aroma\" o \"cielo\". Mía guardaba esas palabras en sus alas y, al volar, dejaba un caminito de letras brillantes para que todos los niños del bosque aprendieran a hablar con el corazón.', '5', 'nuevo', 20, 4);

-- --------------------------------------------------------

--
-- Table structure for table `usuarios`
--

CREATE TABLE `usuarios` (
  `userID` int(11) NOT NULL,
  `nombre_nino` varchar(100) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol` enum('usuario','admin') DEFAULT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `usuarios`
--

INSERT INTO `usuarios` (`userID`, `nombre_nino`, `correo`, `password`, `rol`, `fecha_registro`) VALUES
(1, 'Juan', 'juan123@gmail.com', '$2y$10$2V4aesFRH9ZPmF764I1QeOO9frI.Ln8xhwPLrc6xT/ap5R16Go7RG', 'usuario', '2026-05-07 07:25:07'),
(3, 'Pedrito', 'Pedri8@gmail.com', '$2y$10$763CckqKaRKdPSQ5fOobh.y3i1.CO/ZwPuLLmIuVN8U2cVrKjGK/.', 'usuario', '2026-05-07 07:56:01'),
(4, 'Orlando', 'orlandoperez@gmail.com', '$2y$10$BAUKLH7eD7gn/b39.9GzHeSHwTt28hAPP3vp8sZIMHZFkClTmw/VG', 'usuario', '2026-05-07 03:27:38'),
(5, '', '', '$2y$10$rvJcQJs8Slwh.UX05RrcnO3bUf2oNKOa5yKoGZMWMD/cALoEvO0g6', 'usuario', '2026-05-15 22:02:21');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cuentos`
--
ALTER TABLE `cuentos`
  ADD PRIMARY KEY (`cuentoID`);

--
-- Indexes for table `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`userID`),
  ADD UNIQUE KEY `correo` (`correo`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cuentos`
--
ALTER TABLE `cuentos`
  MODIFY `cuentoID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `userID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
