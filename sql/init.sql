-- foodgo/sql/init.sql
-- SQL скрипт для инициализации базы данных FoodGo

-- Таблица пользователей
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL COMMENT 'Имя пользователя',
  `email` VARCHAR(150) NOT NULL UNIQUE COMMENT 'Email - уникальное поле',
  `phone` VARCHAR(20) DEFAULT NULL COMMENT 'Номер телефона',
  `password` VARCHAR(255) NOT NULL COMMENT 'Хэш пароля (password_hash)',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Дата регистрации',
  INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Таблица пользователей с информацией для авторизации';

-- Таблица меню (опционально - для расширения функционала)
CREATE TABLE IF NOT EXISTS `menu_items` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(150) NOT NULL,
  `description` TEXT,
  `price` DECIMAL(8, 2) NOT NULL,
  `category` VARCHAR(100),
  `image` VARCHAR(255),
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Меню блюд доставки';
