"# FoodGo

Проект доставки вкусной еды с авторизацией пользователей через PHP + MySQL.

## 🚀 Быстрый старт

### 1. Подготовка базы данных

Откройте **phpMyAdmin** (обычно http://localhost:8080/phpmyadmin):

1. Создайте новую БД с именем **foodgo** (charset: utf8mb4)
2. Выберите БД и откройте вкладку **SQL**
3. Скопируйте и выполните содержимое файла [sql/init.sql](sql/init.sql):

```sql
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `phone` VARCHAR(20) DEFAULT NULL,
  `password` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 2. Проверка конфига БД

Убедитесь что в [config/db.php](config/db.php) правильные параметры:

```php
define('DB_NAME', 'foodgo');      // имя БД
define('DB_USER', 'root');         // пользователь
define('DB_PASS', '');             // пароль (для OpenServer - пусто)
```

### 3. Запуск проекта

Откройте в браузере:
- **Главная**: http://localhost:8888/foodgo/
- **Регистрация**: http://localhost:8888/foodgo/register.php
- **Вход**: http://localhost:8888/foodgo/login.php

## 📁 Структура проекта

```
foodgo/
├── index.php              ← Главная страница
├── register.php           ← Регистрация
├── login.php              ← Вход в аккаунт
├── logout.php             ← Выход из аккаунта
├── cabinet.php            ← Личный кабинет пользователя
├── config/
│   └── db.php             ← Подключение к БД (PDO)
├── css/
│   ├── reset.css          ← Сброс стилей браузера
│   ├── variables.css      ← CSS-переменные (цвета, отступы, шрифты)
│   └── style.css          ← Основные стили
├── includes/
│   ├── header.php         ← Общая шапка с навигацией
│   └── footer.php         ← Подвал сайта
└── sql/
    └── init.sql           ← SQL скрипт создания таблиц
```

## 🔐 Авторизация — как это работает

### Регистрация (`register.php`)
1. Пользователь заполняет форму (имя, email, телефон, пароль)
2. Данные валидируются на клиенте и сервере
3. Пароль хэшируется через `password_hash()`
4. Данные сохраняются в таблицу `users`
5. Перенаправление на страницу входа с сообщением об успехе

### Вход (`login.php`)
1. Пользователь вводит email и пароль
2. PHP ищет пользователя по email в БД
3. `password_verify()` проверяет пароль
4. Если верно — данные сохраняются в `$_SESSION`
5. Перенаправление в личный кабинет (`cabinet.php`)

### Выход (`logout.php`)
1. `session_destroy()` удаляет сессию
2. Перенаправление на главную (`index.php`)

### Проверка авторизации (`includes/header.php`)
```php
<?php if (isset($_SESSION['user_id'])): ?>
  <!-- Меню для авторизованного пользователя -->
<?php else: ?>
  <!-- Меню для гостя -->
<?php endif; ?>
```

## 🎨 Дизайн

Все цвета и отступы управляются через CSS-переменные в [css/variables.css](css/variables.css):

```css
--color-primary: #FF8C00;        /* Основной оранжевый */
--color-primary-dark: #E67E00;   /* Тёмный оранжевый */
--color-bg: #F8FAFC;             /* Светлый фон */
--color-text: #1A1A2E;           /* Основной текст */
```

## ✅ Тестирование

Протестируйте все сценарии:

| Сценарий | Действие | Ожидаемый результат |
|----------|----------|-------------------|
| Пустые поля | Нажать «Войти» | Ошибка «Заполните все поля» |
| Несуществующий email | Ввести случайный email | Ошибка «Неверный email или пароль» |
| Неверный пароль | Верный email + неверный пароль | Ошибка «Неверный email или пароль» |
| Верные данные | Верный email + верный пароль | Перенаправление на cabinet.php |
| Выход | Нажать кнопку «Выйти» | Перенаправление на index.php |
| Прямой доступ к cabinet.php | Открыть http://localhost:8888/foodgo/cabinet.php | Перенаправление на login.php |
| Вход авторизованного | Открыть login.php будучи авторизованным | Перенаправление на cabinet.php |

## 📝 Типичные ошибки

**Ошибка: «Cannot modify header information»**
- Причина: PHP-код стоит после HTML или есть пробелы до `<?php`
- Решение: Убедитесь что `<?php` — первые символы файла

**Форма отправляется но ошибок нет и в кабинет не попадаем**
- Причина: Пользователь не зарегистрирован в БД
- Решение: Откройте phpMyAdmin → таблица users → проверьте записи

**Password_verify() возвращает false для верного пароля**
- Причина: Пароль в БД сохранён не через password_hash()
- Решение: Зарегистрируйтесь заново через register.php

## 🔧 Дополнение

Проект использует:
- **PHP 7.0+** (с поддержкой password_hash, PDO, sessions)
- **MySQL 5.7+** (с поддержкой UNIQUE constraints)
- **OpenServer** для локальной разработки
- **CSS 3** с поддержкой CSS-переменных
" 
