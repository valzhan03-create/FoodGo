<?php
// includes/header.php — общая шапка сайта
// Подключайте в начале каждой страницы с session_start() перед include
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<header class="header">
  <nav class="navbar container">
    <a href="index.php" class="navbar__logo">FoodGo</a>

      <ul class="navbar__menu">
      <li><a href="index.php">Главная</a></li>
      <li><a href="menu.php">Меню</a></li>
      <li><a href="#about">О нас</a></li>
      <li><a href="#contacts">Контакты</a></li>
    </ul>

    <div class="navbar__actions">
      <?php if (isset($_SESSION['user_id'])): ?>
        <span class="navbar__user">
          <?= htmlspecialchars($_SESSION['user_name']) ?>
        </span>
        <a href="cabinet.php" class="btn btn-outline">Кабинет</a>
        <a href="logout.php" class="btn btn-primary">Выйти</a>
      <?php else: ?>
        <a href="login.php" class="btn btn-outline">Войти</a>
        <a href="menu.php" class="btn btn-primary">Заказы</a>
      <?php endif; ?>
    </div>
  </nav>
</header>
