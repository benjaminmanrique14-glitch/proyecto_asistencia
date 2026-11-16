-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 03, 2026 at 03:25 PM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `asistencia_colegio`
--

-- --------------------------------------------------------

--
-- Table structure for table `administradores`
--

CREATE TABLE `administradores` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `administradores`
--

INSERT INTO `administradores` (`id`, `username`, `password`, `created_at`) VALUES
(1, 'admin', '$2y$10$d4TTwLo58zu0dL4X98cqdOc.9B7HRV/YUav0UHjctWVrGsiOoJRFK', '2026-10-29 19:42:31');

-- --------------------------------------------------------

--
-- Table structure for table `asistencias`
--

CREATE TABLE `asistencias` (
  `id` int(11) NOT NULL,
  `profesor_id` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `hora_ingreso` time NOT NULL,
  `hora_salida` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `asistencias`
--

INSERT INTO `asistencias` (`id`, `profesor_id`, `fecha`, `hora_ingreso`, `hora_salida`) VALUES
(3, 1, '2026-10-29', '17:22:47', NULL),
(4, 2, '2026-10-29', '17:49:39', '17:50:25');

-- --------------------------------------------------------

--
-- Table structure for table `cursos`
--

CREATE TABLE `cursos` (
  `id` int(11) NOT NULL,
  `numero` tinyint(4) NOT NULL,
  `nombre` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cursos`
--

INSERT INTO `cursos` (`id`, `numero`, `nombre`) VALUES
(1, 1, '1°'),
(2, 2, '2°'),
(3, 3, '3°'),
(4, 4, '4°'),
(5, 5, '5°'),
(6, 6, '6°');

-- --------------------------------------------------------

--
-- Table structure for table `divisiones`
--

CREATE TABLE `divisiones` (
  `id` int(11) NOT NULL,
  `numero` tinyint(4) NOT NULL,
  `nombre` varchar(10) NOT NULL,
  `turno` enum('manana','tarde') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `divisiones`
--

INSERT INTO `divisiones` (`id`, `numero`, `nombre`, `turno`) VALUES
(1, 1, '1ra', 'manana'),
(2, 2, '2da', 'manana'),
(3, 3, '3ra', 'manana'),
(4, 4, '4ta', 'tarde'),
(5, 5, '5ta', 'tarde'),
(6, 6, '6ta', 'tarde');

-- --------------------------------------------------------

--
-- Table structure for table `horarios_profesores`
--

CREATE TABLE `horarios_profesores` (
  `id` int(11) NOT NULL,
  `profesor_id` int(11) NOT NULL,
  `dia_semana` tinyint(4) NOT NULL,
  `hora_entrada` time NOT NULL,
  `hora_salida` time NOT NULL,
  `profesor_curso_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `horarios_profesores`
--

INSERT INTO `horarios_profesores` (`id`, `profesor_id`, `dia_semana`, `hora_entrada`, `hora_salida`, `profesor_curso_id`) VALUES
(3, 1, 1, '08:00:00', '09:00:00', 1),
(4, 1, 3, '10:00:00', '11:00:00', 1),
(5, 2, 1, '08:00:00', '12:30:00', 2),
(6, 2, 2, '08:00:00', '12:30:00', 2),
(7, 2, 3, '08:00:00', '12:30:00', 2),
(8, 2, 4, '08:00:00', '12:30:00', 2),
(9, 2, 5, '08:00:00', '12:30:00', 2),
(15, 4, 1, '08:00:00', '12:30:00', 4),
(16, 4, 2, '08:00:00', '12:30:00', 4),
(17, 4, 3, '08:00:00', '12:30:00', 4),
(18, 4, 4, '08:00:00', '12:30:00', 4),
(19, 4, 5, '08:00:00', '12:30:00', 4),
(20, 5, 1, '08:00:00', '12:30:00', 5),
(21, 5, 3, '08:00:00', '12:30:00', 5),
(22, 5, 5, '08:00:00', '12:30:00', 5),
(23, 6, 1, '13:10:00', '17:30:00', 6),
(24, 6, 2, '13:10:00', '17:30:00', 6),
(25, 6, 3, '13:10:00', '17:30:00', 6),
(26, 6, 4, '13:10:00', '17:30:00', 6),
(27, 6, 5, '13:10:00', '17:30:00', 6),
(28, 7, 1, '07:50:00', '12:10:00', 7),
(29, 7, 2, '07:50:00', '12:10:00', 7),
(30, 7, 3, '07:50:00', '12:10:00', 7),
(31, 7, 4, '07:50:00', '12:10:00', 7),
(32, 7, 5, '07:50:00', '12:10:00', 7),
(33, 8, 2, '13:00:00', '17:00:00', 8),
(34, 8, 4, '13:00:00', '17:00:00', 8),
(35, 9, 2, '08:15:00', '12:15:00', 9),
(36, 9, 4, '08:15:00', '12:15:00', 9),
(37, 10, 1, '13:20:00', '17:40:00', 10),
(38, 10, 2, '13:20:00', '17:40:00', 10),
(39, 10, 3, '13:20:00', '17:40:00', 10),
(40, 10, 4, '13:20:00', '17:40:00', 10),
(41, 10, 5, '13:20:00', '17:40:00', 10),
(42, 11, 1, '08:00:00', '12:30:00', 11),
(43, 11, 2, '08:00:00', '12:30:00', 11),
(44, 11, 3, '08:00:00', '12:30:00', 11),
(45, 11, 4, '08:00:00', '12:30:00', 11),
(46, 11, 5, '08:00:00', '12:30:00', 11),
(47, 12, 1, '13:00:00', '17:00:00', 12),
(48, 12, 3, '13:00:00', '17:00:00', 12),
(49, 12, 5, '13:00:00', '17:00:00', 12),
(50, 12, 2, '08:00:00', '12:00:00', 13),
(51, 12, 4, '08:00:00', '12:00:00', 13),
(52, 13, 1, '13:30:00', '17:30:00', 14),
(53, 13, 2, '13:30:00', '17:30:00', 14),
(54, 13, 3, '13:30:00', '17:30:00', 14),
(55, 13, 4, '13:30:00', '17:30:00', 14),
(56, 13, 5, '13:30:00', '17:30:00', 14),
(57, 14, 1, '08:00:00', '12:30:00', 15),
(58, 14, 2, '08:00:00', '12:30:00', 15),
(59, 14, 3, '08:00:00', '12:30:00', 15),
(60, 14, 4, '08:00:00', '12:30:00', 15),
(61, 14, 5, '08:00:00', '12:30:00', 15),
(62, 14, 1, '13:10:00', '17:30:00', 16),
(63, 14, 2, '13:10:00', '17:30:00', 16),
(64, 14, 3, '13:10:00', '17:30:00', 16),
(65, 14, 4, '13:10:00', '17:30:00', 16),
(66, 14, 5, '13:10:00', '17:30:00', 16),
(67, 15, 1, '08:00:00', '12:00:00', 17),
(68, 15, 2, '08:00:00', '12:00:00', 17),
(69, 15, 3, '08:00:00', '12:00:00', 17),
(70, 15, 4, '08:00:00', '12:00:00', 17),
(71, 15, 5, '08:00:00', '12:00:00', 17),
(72, 16, 1, '13:10:00', '17:30:00', 18),
(73, 16, 2, '13:10:00', '17:30:00', 18),
(74, 16, 3, '13:10:00', '17:30:00', 18),
(75, 16, 4, '13:10:00', '17:30:00', 18),
(76, 16, 5, '13:10:00', '17:30:00', 18),
(77, 17, 1, '08:00:00', '12:30:00', 19),
(78, 17, 2, '08:00:00', '12:30:00', 19),
(79, 17, 3, '08:00:00', '12:30:00', 19),
(80, 17, 4, '08:00:00', '12:30:00', 19),
(81, 18, 5, '08:00:00', '12:30:00', 20),
(82, 18, 1, '13:10:00', '17:30:00', 21),
(83, 18, 2, '13:10:00', '17:30:00', 21),
(84, 18, 3, '13:10:00', '17:30:00', 21),
(85, 18, 4, '13:10:00', '17:30:00', 21),
(86, 19, 1, '13:10:00', '17:20:00', 22),
(87, 19, 2, '13:10:00', '17:20:00', 22),
(88, 19, 3, '13:10:00', '17:20:00', 22),
(89, 19, 4, '13:10:00', '17:20:00', 22),
(90, 19, 5, '13:10:00', '17:20:00', 22),
(91, 19, 1, '08:00:00', '12:20:00', 23),
(92, 19, 2, '08:00:00', '12:20:00', 23),
(93, 19, 3, '08:00:00', '12:20:00', 23),
(94, 19, 4, '08:00:00', '12:20:00', 23),
(95, 19, 5, '08:00:00', '12:20:00', 23),
(96, 20, 1, '08:00:00', '12:30:00', 24),
(97, 20, 2, '08:00:00', '12:30:00', 24),
(98, 20, 3, '08:00:00', '12:30:00', 24),
(99, 20, 4, '13:10:00', '17:30:00', 25),
(100, 20, 5, '13:10:00', '17:30:00', 25),
(119, 3, 1, '08:00:00', '12:30:00', 3),
(120, 3, 2, '08:00:00', '12:30:00', 3),
(121, 3, 3, '08:00:00', '12:30:00', 3),
(122, 3, 4, '08:00:00', '12:30:00', 3),
(123, 3, 5, '08:00:00', '12:30:00', 3),
(124, 3, 1, '09:00:00', '10:30:00', 27);

-- --------------------------------------------------------

--
-- Table structure for table `justificaciones`
--

CREATE TABLE `justificaciones` (
  `id` int(11) NOT NULL,
  `profesor_id` int(11) NOT NULL,
  `tipo` enum('falta_dia','retiro_temprano','falta_futura') NOT NULL,
  `fecha_afectada` date NOT NULL,
  `motivo` text NOT NULL,
  `archivo_url` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `justificaciones`
--

INSERT INTO `justificaciones` (`id`, `profesor_id`, `tipo`, `fecha_afectada`, `motivo`, `archivo_url`, `created_at`) VALUES
(3, 1, 'falta_futura', '2026-10-29', 'Ez', 'uploads/justificaciones/d502ef25e6a28612b97af30d9d4f3798.jpg', '2026-10-29 20:30:56'),
(4, 1, 'retiro_temprano', '2026-11-02', 'Sixseven', 'uploads/justificaciones/abad0ee06b10adcb4fa775e338b3ceaf.jpg', '2026-11-02 21:25:16');

-- --------------------------------------------------------

--
-- Table structure for table `profesores`
--

CREATE TABLE `profesores` (
  `id` int(11) NOT NULL,
  `dni` varchar(20) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `foto` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `profesores`
--

INSERT INTO `profesores` (`id`, `dni`, `nombre`, `apellido`, `password`, `is_active`, `created_at`, `foto`) VALUES
(1, '30111222', 'Docente', 'Demo', '$2y$10$cffA7j/LzFXeBS19bAHo3uQf2baKZsW6XDeeXMeZYcuZV8Z6HwIja', 1, '2026-10-29 20:15:01', NULL),
(2, '28000001', 'Laura', 'Benítez', '$2y$10$LfM3Agj5G8zTJnjy6x9qme84EwPT0VQEKTO0mIBN3rGDOJLUBaKeW', 1, '2026-10-29 20:43:52', NULL),
(3, '28000002', 'Martín', 'Acosta', '$2y$13$u1YMKX8aMoipOqoZw2VFT.EknX7MQBCKK7RSPIa3tzv1pul0cNrBq', 1, '2026-10-29 20:43:52', 'uploads/profesores/1eb801221883ac8ba6a22e9ad303ae3f.jpg'),
(4, '28000003', 'Sofía', 'Pereyra', '$2y$10$LfM3Agj5G8zTJnjy6x9qme84EwPT0VQEKTO0mIBN3rGDOJLUBaKeW', 1, '2026-10-29 20:43:52', NULL),
(5, '28000004', 'Hugo', 'Molina', '$2y$10$LfM3Agj5G8zTJnjy6x9qme84EwPT0VQEKTO0mIBN3rGDOJLUBaKeW', 1, '2026-10-29 20:43:52', NULL),
(6, '28000005', 'Carla', 'Díaz', '$2y$10$LfM3Agj5G8zTJnjy6x9qme84EwPT0VQEKTO0mIBN3rGDOJLUBaKeW', 1, '2026-10-29 20:43:52', NULL),
(7, '28000006', 'Pablo', 'Ferreyra', '$2y$10$LfM3Agj5G8zTJnjy6x9qme84EwPT0VQEKTO0mIBN3rGDOJLUBaKeW', 1, '2026-10-29 20:43:52', NULL),
(8, '28000007', 'Elena', 'Ruiz', '$2y$10$LfM3Agj5G8zTJnjy6x9qme84EwPT0VQEKTO0mIBN3rGDOJLUBaKeW', 1, '2026-10-29 20:43:52', NULL),
(9, '28000008', 'Diego', 'Sosa', '$2y$10$LfM3Agj5G8zTJnjy6x9qme84EwPT0VQEKTO0mIBN3rGDOJLUBaKeW', 1, '2026-10-29 20:43:52', NULL),
(10, '28000009', 'Natalia', 'Gómez', '$2y$10$LfM3Agj5G8zTJnjy6x9qme84EwPT0VQEKTO0mIBN3rGDOJLUBaKeW', 1, '2026-10-29 20:43:52', NULL),
(11, '28000010', 'Andrés', 'Paz', '$2y$10$LfM3Agj5G8zTJnjy6x9qme84EwPT0VQEKTO0mIBN3rGDOJLUBaKeW', 1, '2026-10-29 20:43:52', NULL),
(12, '28000011', 'Valeria', 'Castro', '$2y$10$LfM3Agj5G8zTJnjy6x9qme84EwPT0VQEKTO0mIBN3rGDOJLUBaKeW', 1, '2026-10-29 20:43:52', NULL),
(13, '28000012', 'Ricardo', 'Núñez', '$2y$10$LfM3Agj5G8zTJnjy6x9qme84EwPT0VQEKTO0mIBN3rGDOJLUBaKeW', 1, '2026-10-29 20:43:52', NULL),
(14, '28000013', 'Ana', 'Ferreyra', '$2y$10$LfM3Agj5G8zTJnjy6x9qme84EwPT0VQEKTO0mIBN3rGDOJLUBaKeW', 1, '2026-10-29 20:43:52', NULL),
(15, '28000014', 'Gustavo', 'Romero', '$2y$10$LfM3Agj5G8zTJnjy6x9qme84EwPT0VQEKTO0mIBN3rGDOJLUBaKeW', 1, '2026-10-29 20:43:52', NULL),
(16, '28000015', 'Mónica', 'Alegre', '$2y$10$LfM3Agj5G8zTJnjy6x9qme84EwPT0VQEKTO0mIBN3rGDOJLUBaKeW', 1, '2026-10-29 20:43:52', NULL),
(17, '28000016', 'Javier', 'Ledesma', '$2y$10$LfM3Agj5G8zTJnjy6x9qme84EwPT0VQEKTO0mIBN3rGDOJLUBaKeW', 1, '2026-10-29 20:43:52', NULL),
(18, '28000017', 'Patricia', 'Ojeda', '$2y$10$LfM3Agj5G8zTJnjy6x9qme84EwPT0VQEKTO0mIBN3rGDOJLUBaKeW', 1, '2026-10-29 20:43:52', NULL),
(19, '28000018', 'Silvina', 'Herrera', '$2y$10$LfM3Agj5G8zTJnjy6x9qme84EwPT0VQEKTO0mIBN3rGDOJLUBaKeW', 1, '2026-10-29 20:43:52', NULL),
(20, '28000019', 'Oscar', 'Vega', '$2y$10$LfM3Agj5G8zTJnjy6x9qme84EwPT0VQEKTO0mIBN3rGDOJLUBaKeW', 1, '2026-10-29 20:43:52', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `profesor_cursos`
--

CREATE TABLE `profesor_cursos` (
  `id` int(11) NOT NULL,
  `profesor_id` int(11) NOT NULL,
  `curso_id` int(11) NOT NULL,
  `division_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `profesor_cursos`
--

INSERT INTO `profesor_cursos` (`id`, `profesor_id`, `curso_id`, `division_id`) VALUES
(1, 1, 1, 1),
(2, 2, 1, 2),
(3, 3, 1, 3),
(27, 3, 3, 2),
(4, 4, 2, 1),
(5, 5, 2, 2),
(6, 6, 2, 4),
(7, 7, 3, 2),
(8, 8, 3, 5),
(9, 9, 4, 2),
(10, 10, 4, 6),
(11, 11, 5, 3),
(12, 12, 5, 4),
(13, 12, 6, 1),
(14, 13, 6, 6),
(15, 14, 3, 1),
(16, 14, 5, 6),
(17, 15, 6, 3),
(18, 16, 1, 4),
(19, 17, 5, 1),
(21, 18, 2, 6),
(20, 18, 4, 3),
(22, 19, 3, 6),
(23, 19, 4, 1),
(25, 20, 5, 5),
(24, 20, 6, 2);

-- --------------------------------------------------------

--
-- Table structure for table `qr_tokens`
--

CREATE TABLE `qr_tokens` (
  `id` int(11) NOT NULL,
  `profesor_id` int(11) NOT NULL,
  `token` varchar(64) NOT NULL,
  `expires_at` datetime NOT NULL,
  `is_used` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `qr_tokens`
--

INSERT INTO `qr_tokens` (`id`, `profesor_id`, `token`, `expires_at`, `is_used`) VALUES
(6, 1, 'ec03806ba6c4f56f097e8abccf63e42071a99a964097109896495223aca3b671', '2026-10-29 17:27:53', 0),
(7, 1, '689490b06ccccbd561e4fe9e28006e2e9d09757c41ec70fa1911c270b9b21222', '2026-10-29 17:28:32', 0),
(8, 1, '2507508282b4a6f717346a78d4f35d4404a411d4a2876e22521101a42b02dd03', '2026-10-29 17:35:03', 1),
(9, 1, '2c2f178bb09d2586aa73e8afd6ab0a0998b70ae20a617fe83df1042184e0dc7f', '2026-11-02 18:29:28', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `administradores`
--
ALTER TABLE `administradores`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `asistencias`
--
ALTER TABLE `asistencias`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unica_asistencia_dia` (`profesor_id`,`fecha`);

--
-- Indexes for table `cursos`
--
ALTER TABLE `cursos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `numero` (`numero`);

--
-- Indexes for table `divisiones`
--
ALTER TABLE `divisiones`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `numero` (`numero`);

--
-- Indexes for table `horarios_profesores`
--
ALTER TABLE `horarios_profesores`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unico_horario_curso_dia` (`profesor_curso_id`,`dia_semana`),
  ADD KEY `profesor_id` (`profesor_id`);

--
-- Indexes for table `justificaciones`
--
ALTER TABLE `justificaciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `profesor_id` (`profesor_id`);

--
-- Indexes for table `profesores`
--
ALTER TABLE `profesores`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `dni` (`dni`);

--
-- Indexes for table `profesor_cursos`
--
ALTER TABLE `profesor_cursos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unica_asignacion` (`profesor_id`,`curso_id`,`division_id`),
  ADD KEY `curso_id` (`curso_id`),
  ADD KEY `division_id` (`division_id`);

--
-- Indexes for table `qr_tokens`
--
ALTER TABLE `qr_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `profesor_id` (`profesor_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `administradores`
--
ALTER TABLE `administradores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `asistencias`
--
ALTER TABLE `asistencias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `cursos`
--
ALTER TABLE `cursos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `divisiones`
--
ALTER TABLE `divisiones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `horarios_profesores`
--
ALTER TABLE `horarios_profesores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=125;

--
-- AUTO_INCREMENT for table `justificaciones`
--
ALTER TABLE `justificaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `profesores`
--
ALTER TABLE `profesores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `profesor_cursos`
--
ALTER TABLE `profesor_cursos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `qr_tokens`
--
ALTER TABLE `qr_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `asistencias`
--
ALTER TABLE `asistencias`
  ADD CONSTRAINT `asistencias_ibfk_1` FOREIGN KEY (`profesor_id`) REFERENCES `profesores` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `horarios_profesores`
--
ALTER TABLE `horarios_profesores`
  ADD CONSTRAINT `horarios_profesor_curso_fk` FOREIGN KEY (`profesor_curso_id`) REFERENCES `profesor_cursos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `horarios_profesores_ibfk_1` FOREIGN KEY (`profesor_id`) REFERENCES `profesores` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `justificaciones`
--
ALTER TABLE `justificaciones`
  ADD CONSTRAINT `justificaciones_ibfk_1` FOREIGN KEY (`profesor_id`) REFERENCES `profesores` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `profesor_cursos`
--
ALTER TABLE `profesor_cursos`
  ADD CONSTRAINT `profesor_cursos_curso_fk` FOREIGN KEY (`curso_id`) REFERENCES `cursos` (`id`),
  ADD CONSTRAINT `profesor_cursos_division_fk` FOREIGN KEY (`division_id`) REFERENCES `divisiones` (`id`),
  ADD CONSTRAINT `profesor_cursos_profesor_fk` FOREIGN KEY (`profesor_id`) REFERENCES `profesores` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `qr_tokens`
--
ALTER TABLE `qr_tokens`
  ADD CONSTRAINT `qr_tokens_ibfk_1` FOREIGN KEY (`profesor_id`) REFERENCES `profesores` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
