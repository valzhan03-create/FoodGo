<?php
// Подключаем базу данных
require_once 'config/db.php';

// Переменные для хранения данных и ошибок
$errors = [];
$name   = '';
$email  = '';

// Обработка формы — только если нажата кнопка «Отправить»
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ── 1. Получаем данные из формы ──────────────────────────
    $name             = trim($_POST['name']           ?? '');
    $email            = trim($_POST['email']          ?? '');
    $password         = trim($_POST['password']       ?? '');
    $password_confirm = trim($_POST['password_confirm'] ?? '');

    // ── 2. Валидация ──────────────────────────────────────────
    if (empty($name)) {
        $errors[] = 'Введите ваше имя.';
    }

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Введите корректный email.';
    }

    if (strlen($password) < 6) {
        $errors[] = 'Пароль должен быть минимум 6 символов.';
    }

    if ($password !== $password_confirm) {
        $errors[] = 'Пароли не совпадают.';
    }

    // ── 3. Проверяем — нет ли такого email в базе ────────────
    if (empty($errors)) {
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = 'Пользователь с таким email уже зарегистрирован.';
        }
    }

    // ── 4. Сохраняем в базу данных ───────────────────────────
    if (empty($errors)) {

        // Хэшируем пароль — никогда не храним пароль в открытом виде!
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare(
            'INSERT INTO users (name, email, password) VALUES (?, ?, ?)'
        );
        $stmt->execute([$name, $email, $password_hash]);

        // ── 5. Перенаправляем на страницу входа ──────────────
        header('Location: login.php?registered=1');
        exit;
    }
}
?>

<!-- Дальше идёт HTML-форма из раздела 5 -->


<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Регистрация</title>
  <link rel="stylesheet" href="css/reset.css">
  <link rel="stylesheet" href="css/variables.css">
  <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include 'includes/header.php'; ?>

<main class="main">
  <div class="container">
    <div class="auth-card">

      <h1 class="auth-card__title">Регистрация</h1>

      <!-- Блок ошибок — показывается если есть ошибки -->
      <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
          <?php foreach ($errors as $error): ?>
            <p><?= htmlspecialchars($error) ?></p>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <!-- Форма регистрации -->
      <form class="form" method="POST" action="">

        <div class="form-group">
          <label class="form-label" for="name">Имя</label>
          <input class="form-input" type="text" id="name" name="name"
            value="<?= htmlspecialchars($name ?? '') ?>"
            placeholder="Введите ваше имя" required>
        </div>

        <div class="form-group">
          <label class="form-label" for="email">Email</label>
          <input class="form-input" type="email" id="email" name="email"
            value="<?= htmlspecialchars($email ?? '') ?>"
            placeholder="ваш@email.com" required>
        </div>

        <div class="form-group">
          <label class="form-label" for="password">Пароль</label>
          <input class="form-input" type="password" id="password" name="password"
            placeholder="Минимум 6 символов" required>
        </div>

        <div class="form-group">
          <label class="form-label" for="password_confirm">Повторите пароль</label>
          <input class="form-input" type="password" id="password_confirm"
            name="password_confirm" placeholder="Повторите пароль" required>
        </div>

        <button class="btn btn-primary" type="submit">
          Зарегистрироваться
        </button>

        <p class="auth-card__footer">
          Уже есть аккаунт? <a href="login.php">Войти</a>
        </p>

      </form>
    </div>
  </div>
</main>

<?php include 'includes/footer.php'; ?>
</body>
</html>
