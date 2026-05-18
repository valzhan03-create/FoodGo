<?php
// config/db.php — подключение к базе данных
// Подключайте этот файл в начале каждого PHP-файла который работает с БД

define('DB_HOST', 'localhost');
define('DB_NAME', 'foodgo');      // замените на имя вашей БД
define('DB_USER', 'root');
define('DB_PASS', '');             // пустой пароль в OpenServer
define('DB_CHARSET', 'utf8mb4');

try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET,
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $e) {
    die('Ошибка подключения к базе данных: ' . $e->getMessage());
}
