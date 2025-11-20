-- =====================================================================
-- User Attendance schema (idempotens, biztonságosan újrafuttatható)
-- Cél: műszakok mérése, fotók tárolása, összesítések nézetekkel
-- Tesztelve MySQL 8.0 / MariaDB 10.4+
-- =====================================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

START TRANSACTION;

-- (Opcionális) adatbázis létrehozása
-- CREATE DATABASE IF NOT EXISTS userattendance
--   DEFAULT CHARACTER SET utf8mb4
--   DEFAULT COLLATE utf8mb4_general_ci;
-- USE userattendance;

-- ---------------------------------------------------------------------
-- Táblák
-- ---------------------------------------------------------------------

-- Műszakok
CREATE TABLE IF NOT EXISTS shifts (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  user_id     INT NOT NULL,
  started_at  DATETIME NOT NULL,
  start_photo VARCHAR(255) NOT NULL,
  ended_at    DATETIME DEFAULT NULL,
  end_photo   VARCHAR(255) DEFAULT NULL,
  note        VARCHAR(255) DEFAULT NULL,
  -- VIRTUÁLIS számított mező a műszak hosszára másodpercben (ha véget ért)
  duration_seconds INT
    AS (IF(ended_at IS NULL, NULL, TIMESTAMPDIFF(SECOND, started_at, ended_at))) VIRTUAL,
  INDEX idx_shifts_user (user_id),
  INDEX idx_shifts_started (started_at),
  INDEX idx_shifts_user_started (user_id, started_at),
  INDEX idx_shifts_user_ended (user_id, ended_at)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_general_ci;

-- Műszakhoz tartozó fájlok (extra mellékletek)
CREATE TABLE IF NOT EXISTS shift_files (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  shift_id    INT NOT NULL,
  user_id     INT NOT NULL,
  file_path   VARCHAR(255) NOT NULL,
  mime        VARCHAR(120) NOT NULL,
  uploaded_at DATETIME NOT NULL,
  INDEX idx_files_shift (shift_id),
  INDEX idx_files_user (user_id),
  INDEX idx_files_uploaded (uploaded_at)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_general_ci;

-- ---------------------------------------------------------------------
-- (OPCIONÁLIS) Külső kulcsok – csak akkor kapcsold be, ha a users tábla létezik
-- és a felhasználók elsődleges kulcsa 'id'.
-- MariaDB/MySQL alatt a kommentből kivéve futtasd le egyszer.
-- ---------------------------------------------------------------------
/*
ALTER TABLE shifts
  ADD CONSTRAINT fk_shifts_user
    FOREIGN KEY (user_id) REFERENCES users(id)
    ON DELETE CASCADE;

ALTER TABLE shift_files
  ADD CONSTRAINT fk_files_shift
    FOREIGN KEY (shift_id) REFERENCES shifts(id)
    ON DELETE CASCADE,
  ADD CONSTRAINT fk_files_user
    FOREIGN KEY (user_id) REFERENCES users(id)
    ON DELETE CASCADE;
*/

-- ---------------------------------------------------------------------
-- Nézetek (Views) – összesítésekhez
-- Ha már léteznek, felülírjuk őket.
-- ---------------------------------------------------------------------

-- 1) Műszakok részlete + formázott idő
CREATE OR REPLACE VIEW v_shift_durations AS
SELECT
  s.id,
  s.user_id,
  s.started_at,
  s.ended_at,
  s.start_photo,
  s.end_photo,
  s.note,
  s.duration_seconds,
  -- HH:MM formázás (nem fordul 24 óránként)
  CONCAT(
    LPAD(FLOOR(COALESCE(s.duration_seconds,0) / 3600), 2, '0'), ':',
    LPAD(FLOOR(MOD(COALESCE(s.duration_seconds,0), 3600) / 60), 2, '0')
  ) AS duration_hhmm
FROM shifts s;

-- 2) Napi összesítés felhasználónként
CREATE OR REPLACE VIEW v_daily_totals AS
SELECT
  user_id,
  DATE(started_at) AS work_date,
  SUM(COALESCE(duration_seconds, 0)) AS total_seconds,
  CONCAT(
    FLOOR(SUM(COALESCE(duration_seconds,0)) / 3600), ':',
    LPAD(FLOOR(MOD(SUM(COALESCE(duration_seconds,0)), 3600) / 60), 2, '0')
  ) AS total_hhmm
FROM shifts
WHERE ended_at IS NOT NULL
GROUP BY user_id, DATE(started_at);

-- 3) Havi összesítés (YYYY-MM) felhasználónként
CREATE OR REPLACE VIEW v_monthly_totals AS
SELECT
  user_id,
  DATE_FORMAT(started_at, '%Y-%m') AS ym,
  SUM(COALESCE(duration_seconds, 0)) AS total_seconds,
  CONCAT(
    FLOOR(SUM(COALESCE(duration_seconds,0)) / 3600), ':',
    LPAD(FLOOR(MOD(SUM(COALESCE(duration_seconds,0)), 3600) / 60), 2, '0')
  ) AS total_hhmm
FROM shifts
WHERE ended_at IS NOT NULL
GROUP BY user_id, DATE_FORMAT(started_at, '%Y-%m');

-- 4) Éves összesítés (YYYY) felhasználónként
CREATE OR REPLACE VIEW v_yearly_totals AS
SELECT
  user_id,
  DATE_FORMAT(started_at, '%Y') AS y,
  SUM(COALESCE(duration_seconds, 0)) AS total_seconds,
  CONCAT(
    FLOOR(SUM(COALESCE(duration_seconds,0)) / 3600), ':',
    LPAD(FLOOR(MOD(SUM(COALESCE(duration_seconds,0)), 3600) / 60), 2, '0')
  ) AS total_hhmm
FROM shifts
WHERE ended_at IS NOT NULL
GROUP BY user_id, DATE_FORMAT(started_at, '%Y');

COMMIT;

-- ---------------------------------------------------------------------
-- Hasznos példa-lekérdezések
-- ---------------------------------------------------------------------
-- Napi összes a mai napra (user_id = ?)
-- SELECT * FROM v_daily_totals WHERE user_id = 123 AND work_date = CURDATE();

-- Havi összes az aktuális hónapra
-- SELECT * FROM v_monthly_totals WHERE user_id = 123 AND ym = DATE_FORMAT(CURDATE(), '%Y-%m');

-- Éves összes az aktuális évre
-- SELECT * FROM v_yearly_totals WHERE user_id = 123 AND y = DATE_FORMAT(CURDATE(), '%Y');

-- Egy felhasználó befejezett műszakai időrendben
-- SELECT * FROM v_shift_durations WHERE user_id = 123 AND ended_at IS NOT NULL ORDER BY started_at DESC;
