<?php
session_start();

// Если пользователь НЕ авторизован — перенаправляем на вход
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once 'config/db.php';

// Получаем информацию пользователя
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Если пользователя не найти (маловероятно, но обезопасимся)
if (!$user) {
    session_destroy();
    header('Location: login.php');
    exit;
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
    <div class="auth-card">

      <h1 class="auth-card__title">Мой профиль</h1>

      <div class="profile-info">
        <div class="profile-field">
          <label class="profile-label">Имя:</label>
          <p class="profile-value"><?= htmlspecialchars($user['name']) ?></p>
        </div>

        <div class="profile-field">
          <label class="profile-label">Email:</label>
          <p class="profile-value"><?= htmlspecialchars($user['email']) ?></p>
        </div>

        <div class="profile-field">
          <label class="profile-label">Телефон:</label>
          <p class="profile-value"><?= htmlspecialchars($user['phone'] ?? 'Не указан') ?></p>
        </div>

        <div class="profile-field">
          <label class="profile-label">Дата регистрации:</label>
          <p class="profile-value"><?= date('d.m.Y', strtotime($user['created_at'])) ?></p>
        </div>
      </div>

      <div class="profile-actions">
        <a href="edit-profile.php" class="btn btn-primary">Редактировать профиль</a>
        <a href="logout.php" class="btn btn-outline">Выйти</a>
      </div>

    </div>
  </div>
</main>

<?php include 'includes/footer.php'; ?>

</body>
</html>
