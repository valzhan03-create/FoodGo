<?php
session_start();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>FoodGo — Доставка вкусной еды</title>
  <link rel="stylesheet" href="css/reset.css">
  <link rel="stylesheet" href="css/variables.css">
  <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include 'includes/header.php'; ?>

<section class="hero-banner">
  <div class="hero-banner__overlay">
    <div class="hero-banner__content">
      <h1 class="hero-banner__title">Добро пожаловать в ресторан FoodGo</h1>
      <p class="hero-banner__subtitle">Лучшие блюда рядом с вами</p>
    </div>
  </div>
</section>

<main class="main">
  <div class="container">
    <div class="auth-card" style="text-align: center;">
      <h1 class="auth-card__title" style="color: var(--color-primary);">Добро пожаловать в FoodGo!</h1>
      
      <p style="font-size: var(--font-size-lg); margin: var(--spacing-lg) 0; color: var(--color-text-muted);">
        Быстрая и вкусная доставка еды в ваш дом
      </p>

      <div style="background: var(--color-bg); border-radius: var(--radius-md); padding: var(--spacing-lg); margin: var(--spacing-xl) 0;">
        <h2 style="color: var(--color-primary); margin-bottom: var(--spacing-md);">Популярные блюда</h2>
        <p style="color: var(--color-text-muted); margin-bottom: var(--spacing-lg);">
          Посмотрите наше меню и выбирайте самые вкусные блюда!
        </p>
      </div>

      <?php if (isset($_SESSION['user_id'])): ?>
        <p style="color: var(--color-success); font-weight: 600; margin: var(--spacing-lg) 0;">
          ✓ Вы авторизованы как: <strong><?= htmlspecialchars($_SESSION['user_name']) ?></strong>
        </p>
      <?php else: ?>
        <p style="color: var(--color-text-muted); margin: var(--spacing-lg) 0;">
          Не зарегистрированы? Создайте аккаунт чтобы быстрее оформлять заказы!
        </p>
      <?php endif; ?>

      <div style="display: flex; gap: var(--spacing-md); justify-content: center; flex-wrap: wrap; margin-top: var(--spacing-xl);">
        <a href="#menu" class="btn btn-primary">Просмотреть меню</a>
        <?php if (!isset($_SESSION['user_id'])): ?>
          <a href="register.php" class="btn btn-outline">Зарегистрироваться</a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</main>

<?php include 'includes/footer.php'; ?>

</body>
</html>
