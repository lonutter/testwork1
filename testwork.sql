/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Дамп структуры для таблица testworks.aquariums
CREATE TABLE IF NOT EXISTS `aquariums` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Идентификатор аквариума',
  `name` char(50) NOT NULL COMMENT 'Название аквариума',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Аквариумы';

-- Экспортируемые данные не выделены.
-- Дамп структуры для таблица testworks.fishes
CREATE TABLE IF NOT EXISTS `fishes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Идентификатор рыбы',
  `type` tinyint(3) unsigned NOT NULL COMMENT 'Тип (разновидность рыбы)',
  `name` varchar(50) NOT NULL COMMENT 'Имя рыбы',
  `speed` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT 'Скорость рыбы',
  `satiety` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT 'Сытость рыбы',
  `aquarium_id` int(10) unsigned NOT NULL COMMENT 'Аквариум',
  PRIMARY KEY (`id`),
  KEY `aquarium_id` (`aquarium_id`),
  CONSTRAINT `fishes_ibfk_1` FOREIGN KEY (`aquarium_id`) REFERENCES `aquariums` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Рыбы';

-- Экспортируемые данные не выделены.
-- Дамп структуры для таблица testworks.games
CREATE TABLE IF NOT EXISTS `games` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Идентификатор игры',
  `aquarium_id` int(10) unsigned NOT NULL COMMENT 'Аквариум',
  `num_peanuts` tinyint(3) unsigned NOT NULL COMMENT 'Количество орешков',
  PRIMARY KEY (`id`),
  KEY `aquarium_id` (`aquarium_id`),
  CONSTRAINT `games_ibfk_1` FOREIGN KEY (`aquarium_id`) REFERENCES `aquariums` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Игры';

-- Экспортируемые данные не выделены.
-- Дамп структуры для таблица testworks.moves
CREATE TABLE IF NOT EXISTS `moves` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Идентификатор хода',
  `game_id` int(10) unsigned NOT NULL COMMENT 'Игра',
  `round` tinyint(3) unsigned NOT NULL COMMENT 'Раунд',
  `message` varchar(500) NOT NULL COMMENT 'Сообщение',
  PRIMARY KEY (`id`),
  KEY `game_id` (`game_id`),
  CONSTRAINT `moves_ibfk_1` FOREIGN KEY (`game_id`) REFERENCES `games` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Ходы';

-- Экспортируемые данные не выделены.
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
