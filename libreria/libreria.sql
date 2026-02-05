-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Feb 05, 2026 alle 11:25
-- Versione del server: 10.4.32-MariaDB
-- Versione PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `libreria`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `access_logs`
--

CREATE TABLE `access_logs` (
  `id` int(10) UNSIGNED NOT NULL,
  `student_id` int(10) UNSIGNED DEFAULT NULL,
  `event` enum('login_ok','login_ko','logout') NOT NULL,
  `ip` varchar(60) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `access_logs`
--

INSERT INTO `access_logs` (`id`, `student_id`, `event`, `ip`, `user_agent`, `created_at`) VALUES
(1, 1, 'login_ok', '::1', NULL, '2026-01-23 09:16:17'),
(2, 1, 'login_ok', '::1', NULL, '2026-01-23 09:16:35'),
(3, 1, 'login_ok', '::1', NULL, '2026-01-26 09:20:52'),
(4, 1, 'login_ok', '::1', NULL, '2026-01-26 09:48:00'),
(5, 1, 'logout', '::1', NULL, '2026-01-26 10:02:18'),
(6, 1, 'login_ok', '::1', NULL, '2026-01-26 10:02:25'),
(7, 1, 'logout', '::1', NULL, '2026-01-26 10:02:42'),
(8, 2, 'login_ko', '::1', NULL, '2026-01-26 10:02:49'),
(9, 1, 'login_ok', '::1', NULL, '2026-01-26 10:03:09'),
(10, 1, 'login_ok', '::1', NULL, '2026-01-29 09:31:27'),
(11, 1, 'login_ok', '::1', NULL, '2026-01-29 10:02:07'),
(12, 1, 'logout', '::1', NULL, '2026-01-29 10:10:21'),
(13, 1, 'login_ok', '::1', NULL, '2026-01-29 10:10:25');

-- --------------------------------------------------------

--
-- Struttura della tabella `action_logs`
--

CREATE TABLE `action_logs` (
  `id` int(10) UNSIGNED NOT NULL,
  `actor_student_id` int(10) UNSIGNED DEFAULT NULL,
  `action_type` varchar(50) NOT NULL,
  `laptop_id` int(10) UNSIGNED DEFAULT NULL,
  `customer_id` int(10) UNSIGNED DEFAULT NULL,
  `group_id` int(10) UNSIGNED DEFAULT NULL,
  `note` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `action_logs`
--

INSERT INTO `action_logs` (`id`, `actor_student_id`, `action_type`, `laptop_id`, `customer_id`, `group_id`, `note`, `created_at`) VALUES
(1, 7, 'create_book', NULL, NULL, NULL, 'fffr', '2026-01-22 12:55:22'),
(2, 1, 'create_student', NULL, NULL, NULL, 'dio@gmail.com', '2026-01-26 09:54:26'),
(3, 1, 'update_student', NULL, NULL, NULL, 'dio@gmail.com', '2026-01-26 09:54:49'),
(4, 1, 'update_student', NULL, NULL, NULL, 'dio@gmail.com', '2026-01-26 09:55:03');

-- --------------------------------------------------------

--
-- Struttura della tabella `copie`
--

CREATE TABLE `copie` (
  `id` int(11) NOT NULL,
  `id_opera` int(11) NOT NULL,
  `codice_copia` varchar(50) NOT NULL,
  `posizione` varchar(100) DEFAULT NULL,
  `stato` enum('disponibile','in_prestito') DEFAULT 'disponibile',
  `data_rientro` datetime DEFAULT NULL,
  `qr_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `copie`
--

INSERT INTO `copie` (`id`, `id_opera`, `codice_copia`, `posizione`, `stato`, `data_rientro`, `qr_path`) VALUES
(6, 3, 'CP-2026-000006', '', 'disponibile', NULL, 'public/qrcodes/copia_CP-2026-000006.svg');

-- --------------------------------------------------------

--
-- Struttura della tabella `customers`
--

CREATE TABLE `customers` (
  `id` int(10) UNSIGNED NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `pc_requested_count` int(11) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `group_members`
--

CREATE TABLE `group_members` (
  `group_id` int(10) UNSIGNED NOT NULL,
  `student_id` int(10) UNSIGNED NOT NULL,
  `role` varchar(50) NOT NULL DEFAULT 'member',
  `joined_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `laptops`
--

CREATE TABLE `laptops` (
  `id` int(10) UNSIGNED NOT NULL,
  `code` varchar(50) NOT NULL,
  `brand` varchar(100) DEFAULT NULL,
  `model` varchar(100) DEFAULT NULL,
  `serial` varchar(100) DEFAULT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'available',
  `customer_id` int(10) UNSIGNED DEFAULT NULL,
  `group_id` int(10) UNSIGNED DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `laptop_state_history`
--

CREATE TABLE `laptop_state_history` (
  `id` int(10) UNSIGNED NOT NULL,
  `laptop_id` int(10) UNSIGNED NOT NULL,
  `changed_by_student_id` int(10) UNSIGNED DEFAULT NULL,
  `previous_status` varchar(50) DEFAULT NULL,
  `new_status` varchar(50) NOT NULL,
  `note` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `libri`
--

CREATE TABLE `libri` (
  `id` int(11) NOT NULL,
  `titolo` varchar(255) NOT NULL,
  `autore` varchar(255) DEFAULT NULL,
  `isbn` varchar(20) DEFAULT NULL,
  `editore` varchar(255) DEFAULT NULL,
  `anno` int(11) DEFAULT NULL,
  `genere` varchar(100) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `stato` varchar(50) DEFAULT 'Disponibile',
  `prestito_student_id` int(11) DEFAULT NULL,
  `prestito_data_inizio` date DEFAULT NULL,
  `prestito_data_fine` date DEFAULT NULL,
  `prestito_condizioni` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `libri`
--

INSERT INTO `libri` (`id`, `titolo`, `autore`, `isbn`, `editore`, `anno`, `genere`, `note`, `stato`, `prestito_student_id`, `prestito_data_inizio`, `prestito_data_fine`, `prestito_condizioni`) VALUES
(2, 'Abissi d\'acciaio', 'Isaac Asimov', '978-88-04-40304-3', 'Mondadori', 1995, 'boh', '', 'In Prestito', 2, '2026-02-26', '2026-02-27', 'buono'),
(3, 'Il ragazzo del lago', 'Marcello Foa', '978-88-566-1937-9', '', 2011, '', NULL, 'Disponibile', NULL, NULL, NULL, NULL),
(4, 'Fra le stelle e il cielo', 'Erica Bertelegni', '978-88-418-7981-8', '', 2012, 'Juvenile Fiction', '', 'In Prestito', 1, '2026-01-29', '2026-02-28', '');

-- --------------------------------------------------------

--
-- Struttura della tabella `opere`
--

CREATE TABLE `opere` (
  `id` int(11) NOT NULL,
  `titolo` varchar(255) NOT NULL,
  `autore` varchar(255) DEFAULT NULL,
  `isbn` varchar(20) DEFAULT NULL,
  `editore` varchar(255) DEFAULT NULL,
  `anno` int(11) DEFAULT NULL,
  `genere` varchar(100) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `stato` varchar(50) DEFAULT 'Disponibile',
  `prestito_student_id` int(11) DEFAULT NULL,
  `prestito_data_inizio` date DEFAULT NULL,
  `prestito_data_fine` date DEFAULT NULL,
  `prestito_condizioni` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `opere`
--

INSERT INTO `opere` (`id`, `titolo`, `autore`, `isbn`, `editore`, `anno`, `genere`, `note`, `stato`, `prestito_student_id`, `prestito_data_inizio`, `prestito_data_fine`, `prestito_condizioni`) VALUES
(1, 'Il ragazzo del lago', 'Marcello Foa', '978-88-566-1937-9', '', 2011, 'Biography & Autobiography', '', 'Disponibile', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Struttura della tabella `payment_transfers`
--

CREATE TABLE `payment_transfers` (
  `id` int(10) UNSIGNED NOT NULL,
  `customer_id` int(10) UNSIGNED NOT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `paid_at` datetime NOT NULL,
  `reference` varchar(255) DEFAULT NULL,
  `receipt_path` varchar(255) DEFAULT NULL,
  `pcs_paid_count` int(11) DEFAULT 0,
  `status` varchar(50) DEFAULT 'pending',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `students`
--

CREATE TABLE `students` (
  `id` int(10) UNSIGNED NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` varchar(50) NOT NULL DEFAULT 'user',
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `students`
--

INSERT INTO `students` (`id`, `first_name`, `last_name`, `email`, `password_hash`, `role`, `active`, `created_at`) VALUES
(1, 'Super', 'Admin', 'admin', '$2y$10$EhFG9MD8SNDQ03KBIb8hReQAKI7GKvna45ZyQyF8kS5Ct2g7ENuVq', 'admin', 1, '2026-01-22 12:18:36'),
(2, 'mocan', 'william', 'dio@gmail.com', '$2y$10$.QDdKto.e4f2ZxYxwhhiUeNcg.FB/eZpAHtsGTC6bxy4d54rENhvW', 'student', 1, '2026-01-26 09:54:26');

-- --------------------------------------------------------

--
-- Struttura della tabella `view_cards`
--

CREATE TABLE `view_cards` (
  `id` int(10) UNSIGNED NOT NULL,
  `scope` varchar(50) NOT NULL,
  `metric` varchar(100) NOT NULL,
  `value` varchar(255) DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `view_cards`
--

INSERT INTO `view_cards` (`id`, `scope`, `metric`, `value`, `updated_at`) VALUES
(1, 'dashboard', 'customers_total', '0', '2026-01-26 09:55:03'),
(2, 'dashboard', 'students_total', '2', '2026-01-26 09:55:03'),
(3, 'dashboard', 'groups_total', '0', '2026-01-26 09:55:03'),
(4, 'libri', 'total', '1', '2026-01-23 09:50:21'),
(5, 'libri', 'available', '0', '2026-01-23 09:50:21'),
(6, 'libri', 'borrowed', '1', '2026-01-23 09:50:21'),
(7, 'libri', 'reserved', '0', '2026-01-23 09:50:21'),
(60, 'students', 'students', '2', '2026-01-26 09:55:03'),
(61, 'students', 'leaders', '0', '2026-01-26 09:55:03'),
(62, 'students', 'installers', '0', '2026-01-26 09:55:03'),
(63, 'groups', 'groups', '0', '2026-01-23 09:16:42'),
(64, 'groups', 'students', '0', '2026-01-23 09:16:42'),
(65, 'customers', 'docenti', '0', '2026-01-23 09:17:14');

-- --------------------------------------------------------

--
-- Struttura della tabella `work_groups`
--

CREATE TABLE `work_groups` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `leader_student_id` int(10) UNSIGNED DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `access_logs`
--
ALTER TABLE `access_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `action_logs`
--
ALTER TABLE `action_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `copie`
--
ALTER TABLE `copie`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codice_copia` (`codice_copia`),
  ADD KEY `id_opera` (`id_opera`);

--
-- Indici per le tabelle `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `group_members`
--
ALTER TABLE `group_members`
  ADD PRIMARY KEY (`group_id`,`student_id`);

--
-- Indici per le tabelle `laptops`
--
ALTER TABLE `laptops`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indici per le tabelle `laptop_state_history`
--
ALTER TABLE `laptop_state_history`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `libri`
--
ALTER TABLE `libri`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `opere`
--
ALTER TABLE `opere`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `payment_transfers`
--
ALTER TABLE `payment_transfers`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indici per le tabelle `view_cards`
--
ALTER TABLE `view_cards`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_scope_metric` (`scope`,`metric`);

--
-- Indici per le tabelle `work_groups`
--
ALTER TABLE `work_groups`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `access_logs`
--
ALTER TABLE `access_logs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT per la tabella `action_logs`
--
ALTER TABLE `action_logs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT per la tabella `copie`
--
ALTER TABLE `copie`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT per la tabella `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `laptops`
--
ALTER TABLE `laptops`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `laptop_state_history`
--
ALTER TABLE `laptop_state_history`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `libri`
--
ALTER TABLE `libri`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT per la tabella `opere`
--
ALTER TABLE `opere`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT per la tabella `payment_transfers`
--
ALTER TABLE `payment_transfers`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `students`
--
ALTER TABLE `students`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT per la tabella `view_cards`
--
ALTER TABLE `view_cards`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=112;

--
-- AUTO_INCREMENT per la tabella `work_groups`
--
ALTER TABLE `work_groups`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `copie`
--
ALTER TABLE `copie`
  ADD CONSTRAINT `copie_ibfk_1` FOREIGN KEY (`id_opera`) REFERENCES `libri` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
