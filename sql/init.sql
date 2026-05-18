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
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_menu_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Меню блюд доставки';

INSERT INTO menu_items (name, description, price, category, image) VALUES
  ('Пицца Маргарита', 'Томатный соус, моцарелла, базилик', 2590.00, 'Пицца', 'https://picsum.photos/seed/1/600/400'),
  ('Пицца Пепперони', 'Острая пепперони и сыр', 2790.00, 'Пицца', 'https://picsum.photos/seed/2/600/400'),
  ('Пицца Четыре сыра', 'Моцарелла, пармезан, дорблю, чеддер', 2990.00, 'Пицца', 'https://picsum.photos/seed/3/600/400'),
  ('Бургер Классический', 'Говяжья котлета, салат, томат, соус', 1690.00, 'Бургер', 'https://picsum.photos/seed/4/600/400'),
  ('Бургер Чеддер', 'Сыр чеддер, карамелизированный лук', 1890.00, 'Бургер', 'https://picsum.photos/seed/5/600/400'),
  ('Суши Сет "Классик"', 'Ассорти роллов и нигири (12 шт.)', 4590.00, 'Суши', 'https://picsum.photos/seed/6/600/400'),
  ('Ролл Калифорния', 'Краб, авокадо, огурец, икра', 1690.00, 'Суши', 'https://picsum.photos/seed/7/600/400'),
  ('Рамен с курицей', 'Ароматный бульон, лапша, курица', 1990.00, 'Супы', 'https://picsum.photos/seed/8/600/400'),
  ('Салат Цезарь', 'Курица, пармезан, соус цезарь', 1590.00, 'Салаты', 'https://picsum.photos/seed/9/600/400'),
  ('Салат Греческий', 'Фета, оливки, свежие овощи', 1390.00, 'Салаты', 'https://picsum.photos/seed/10/600/400'),
  ('Лазанья', 'Сытная мясная лазанья с соусом бешамель', 2190.00, 'Паста', 'https://picsum.photos/seed/11/600/400'),
  ('Паста Карбонара', 'Бекон, яйцо, пармезан', 1790.00, 'Паста', 'https://picsum.photos/seed/12/600/400'),
  ('Стейк Рибай', '250 г, прожарка по выбору', 5990.00, 'Стейки', 'https://picsum.photos/seed/13/600/400'),
  ('Куриный гриль', 'Маринованная курица на гриле', 1590.00, 'Гриль', 'https://picsum.photos/seed/14/600/400'),
  ('Тако с говядиной', 'Тако с пикантной говяжьей начинкой', 1290.00, 'Тако', 'https://picsum.photos/seed/15/600/400'),
  ('Фахитас', 'Курица с овощами, тортильи', 1890.00, 'Мексиканская', 'https://picsum.photos/seed/16/600/400'),
  ('Карри с овощами', 'Овощное карри с рисом басмати', 1490.00, 'Карри', 'https://picsum.photos/seed/17/600/400'),
  ('Том Ям', 'Тайский острый суп с креветками', 1890.00, 'Супы', 'https://picsum.photos/seed/18/600/400'),
  ('Блины с лососем', 'Тонкие блины с копченым лососем', 1490.00, 'Закуски', 'https://picsum.photos/seed/19/600/400'),
  ('Чизкейк', 'Нежный чизкейк на печенье', 990.00, 'Десерты', 'https://picsum.photos/seed/20/600/400')
ON DUPLICATE KEY UPDATE
  description = VALUES(description),
  price = VALUES(price),
  category = VALUES(category),
  image = VALUES(image);
