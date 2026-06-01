-- LV4 Baza podataka
-- PHP + MySQL aplikacija za filmove i ocjenjivanje slika

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- --------------------------------------------------------
-- Tablica korisnika
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `korisnici` (
  `id` INT(10) NOT NULL AUTO_INCREMENT,
  `korisnicko_ime` VARCHAR(50) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `lozinka` VARCHAR(255) NOT NULL,
  `uloga` ENUM('korisnik','admin') NOT NULL DEFAULT 'korisnik',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `korisnicko_ime` (`korisnicko_ime`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Tablica filmova
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `filmovi` (
  `id` INT(10) NOT NULL AUTO_INCREMENT,
  `naslov` VARCHAR(200) NOT NULL,
  `zanr` VARCHAR(100) NOT NULL,
  `godina` INT NOT NULL,
  `trajanje` INT NOT NULL,
  `ocjena` DECIMAL(3,1) NOT NULL,
  `reziser` VARCHAR(200) NOT NULL,
  `zemlja` VARCHAR(200) NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Tablica zeljenih filmova (osobna videoteka)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `zeljeni_filmovi` (
  `id_korisnika` INT(10) NOT NULL,
  `id_filma` INT(10) NOT NULL,
  `dodano_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_korisnika`, `id_filma`),
  KEY `id_filma` (`id_filma`),
  CONSTRAINT `zeljeni_filmovi_ibfk_1` FOREIGN KEY (`id_korisnika`) REFERENCES `korisnici` (`id`) ON DELETE CASCADE,
  CONSTRAINT `zeljeni_filmovi_ibfk_2` FOREIGN KEY (`id_filma`) REFERENCES `filmovi` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Tablica slika
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `slike` (
  `id` INT(10) NOT NULL AUTO_INCREMENT,
  `naziv_datoteke` VARCHAR(200) NOT NULL,
  `opis` VARCHAR(300) DEFAULT NULL,
  `putanja` VARCHAR(300) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Tablica ocjena slika
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `ocjene` (
  `id_korisnika` INT(10) NOT NULL,
  `id_slike` INT(10) NOT NULL,
  `ocjena` TINYINT(1) NOT NULL CHECK (`ocjena` BETWEEN 1 AND 5),
  `vrijeme_ocjene` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_korisnika`, `id_slike`),
  KEY `id_slike` (`id_slike`),
  CONSTRAINT `ocjene_ibfk_1` FOREIGN KEY (`id_korisnika`) REFERENCES `korisnici` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ocjene_ibfk_2` FOREIGN KEY (`id_slike`) REFERENCES `slike` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Početni podaci - Filmovi (iz filmovi.csv)
-- --------------------------------------------------------
INSERT INTO `filmovi` (`naslov`, `zanr`, `godina`, `trajanje`, `ocjena`, `reziser`, `zemlja`) VALUES
('The Shawshank Redemption', 'Drama', 1994, 142, 9.3, 'Frank Darabont', 'USA'),
('The Godfather', 'Crime, Drama', 1972, 175, 9.2, 'Francis Ford Coppola', 'USA'),
('The Dark Knight', 'Action, Crime', 2008, 152, 9.0, 'Christopher Nolan', 'UK/USA'),
('Schindler''s List', 'Biography, Drama', 1993, 195, 9.0, 'Steven Spielberg', 'USA'),
('12 Angry Men', 'Crime, Drama', 1957, 96, 9.0, 'Sidney Lumet', 'USA'),
('Pulp Fiction', 'Crime, Drama', 1994, 154, 8.9, 'Quentin Tarantino', 'USA'),
('The Lord of the Rings: The Return of the King', 'Action, Adventure', 2003, 201, 9.0, 'Peter Jackson', 'NZ/USA'),
('Il Buono, il Brutto, il Cattivo', 'Western', 1966, 161, 8.8, 'Sergio Leone', 'Italy'),
('Fight Club', 'Drama', 1999, 139, 8.8, 'David Fincher', 'USA'),
('Inception', 'Action, Adventure', 2010, 148, 8.8, 'Christopher Nolan', 'USA/UK'),
('The Matrix', 'Action, Sci-Fi', 1999, 136, 8.7, 'Lana Wachowski', 'USA'),
('Goodfellas', 'Biography, Crime', 1990, 145, 8.7, 'Martin Scorsese', 'USA'),
('One Flew Over the Cuckoo''s Nest', 'Drama', 1975, 133, 8.7, 'Milos Forman', 'USA'),
('Seven Samurai', 'Action, Drama', 1954, 207, 8.6, 'Akira Kurosawa', 'Japan'),
('Se7en', 'Crime, Drama', 1995, 127, 8.6, 'David Fincher', 'USA'),
('The Silence of the Lambs', 'Crime, Drama', 1991, 118, 8.6, 'Jonathan Demme', 'USA'),
('City of God', 'Crime, Drama', 2002, 130, 8.6, 'Fernando Meirelles', 'Brazil'),
('Life Is Beautiful', 'Comedy, Drama', 1997, 116, 8.6, 'Roberto Benigni', 'Italy'),
('Interstellar', 'Adventure, Drama', 2014, 169, 8.7, 'Christopher Nolan', 'USA/UK'),
('Saving Private Ryan', 'Drama, War', 1998, 169, 8.6, 'Steven Spielberg', 'USA'),
('Parasite', 'Drama, Thriller', 2019, 132, 8.5, 'Bong Joon Ho', 'South Korea'),
('The Green Mile', 'Crime, Drama', 1999, 189, 8.6, 'Frank Darabont', 'USA'),
('Star Wars: Episode IV - A New Hope', 'Action, Adventure', 1977, 121, 8.6, 'George Lucas', 'USA'),
('Terminator 2: Judgment Day', 'Action, Sci-Fi', 1991, 137, 8.6, 'James Cameron', 'USA'),
('Back to the Future', 'Adventure, Comedy', 1985, 116, 8.5, 'Robert Zemeckis', 'USA'),
('The Pianist', 'Biography, Drama', 2002, 150, 8.5, 'Roman Polanski', 'France/Poland'),
('Psycho', 'Horror, Mystery', 1960, 109, 8.5, 'Alfred Hitchcock', 'USA'),
('Gladiator', 'Action, Adventure', 2000, 155, 8.5, 'Ridley Scott', 'USA/UK'),
('The Lion King', 'Animation, Adventure', 1994, 88, 8.5, 'Roger Allers', 'USA'),
('The Departed', 'Crime, Drama', 2006, 151, 8.5, 'Martin Scorsese', 'USA');

-- --------------------------------------------------------
-- Početni podaci - Slike
-- --------------------------------------------------------
INSERT INTO `slike` (`naziv_datoteke`, `opis`, `putanja`) VALUES
('slika1.jpg', 'Slika 1', 'images/slika1.jpg'),
('slika2.jpg', 'Slika 2', 'images/slika2.jpg');

COMMIT;
