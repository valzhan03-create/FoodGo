<?php
session_start();

// Защита: только для авторизованных пользователей
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once 'config/db.php';

// Получаем данные текущего пользователя из БД
$stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Если пользователя не найти (маловероятно, но обезопасимся)
if (!$user) {
    session_destroy();
    header('Location: login.php');
    exit;
}

// Переменные для формы и сообщений
$success = '';
$errors  = [];

// Обработка формы — только если нажата кнопка «Сохранить»
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_profile'])) {

    $name  = trim($_POST['name']  ?? '');
    $phone = trim($_POST['phone'] ?? '');

    // Валидация
    if (empty($name)) {
        $errors[] = 'Имя не может быть пустым.';
    }

    if (!empty($phone) && !preg_match('/^\+?[0-9]{10,15}$/', $phone)) {
        $errors[] = 'Введите корректный номер телефона (10-15 цифр).';
    }

    // Сохраняем если нет ошибок
    if (empty($errors)) {
        $stmt = $pdo->prepare(
            'UPDATE users SET name = ?, phone = ? WHERE id = ?'
        );
        $stmt->execute([$name, $phone ?: null, $_SESSION['user_id']]);

        // Обновляем имя в сессии — чтобы шапка тоже обновилась
        $_SESSION['user_name'] = $name;

        // Обновляем локальную переменную $user
        $user['name']  = $name;
        $user['phone'] = $phone;

        $success = 'Данные успешно сохранены!';
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Мой профиль — FoodGo</title>
  <link rel="stylesheet" href="css/reset.css">
  <link rel="stylesheet" href="css/variables.css">
  <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include 'includes/header.php'; ?>

<main class="main">
  <div class="container">
    <div class="cabinet">

      <h1 class="cabinet__title">Личный кабинет</h1>

      <!-- Блок: информация о пользователе -->
      <div class="cabinet__info">
        <h2 class="cabinet__subtitle">Мои данные</h2>

        <!-- Сообщение об успехе -->
        <?php if ($success): ?>
          <div class="alert alert-success">
            <p><?= htmlspecialchars($success) ?></p>
          </div>
        <?php endif; ?>

        <!-- Ошибки -->
        <?php if (!empty($errors)): ?>
          <div class="alert alert-error">
            <?php foreach ($errors as $e): ?>
              <p><?= htmlspecialchars($e) ?></p>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

        <!-- Форма редактирования -->
        <form class="form" method="POST" action="">

          <div class="form-group">
            <label class="form-label">Email (изменить нельзя)</label>
            <input class="form-input" type="email"
              value="<?= htmlspecialchars($user['email']) ?>" disabled>
          </div>

          <div class="form-group">
            <label class="form-label" for="name">Имя</label>
            <input class="form-input" type="text" id="name" name="name"
              value="<?= htmlspecialchars($user['name']) ?>" required>
          </div>

          <div class="form-group">
            <label class="form-label" for="phone">Телефон</label>
            <input class="form-input" type="tel" id="phone" name="phone"
              value="<?= htmlspecialchars($user['phone'] ?? '') ?>"
              placeholder="+7 (999) 999-99-99">
          </div>

          <div class="form-group">
            <label class="form-label">Дата регистрации</label>
            <input class="form-input" type="text"
              value="<?= date('d.m.Y', strtotime($user['created_at'])) ?>"
              disabled>
          </div>

          <button class="btn btn-primary" type="submit" name="save_profile">
            Сохранить изменения
          </button>

        </form>

        <div class="cabinet__actions">
          <a href="logout.php" class="btn btn-outline">Выйти из аккаунта</a>
        </div>
      </div>

    </div>
  </div>
</main>

<?php include 'includes/footer.php'; ?>

</body>
</html>
