<?php
session_start();
require_once 'config/db.php';

// Получаем 3 последних добавленных блюда для показа на главной
$stmt_new = $pdo->query('SELECT * FROM menu_items ORDER BY created_at DESC, id DESC LIMIT 3');
$new_dishes = $stmt_new->fetchAll();
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

<?php if (!empty($new_dishes)): ?>
  <section class="container new-dishes" style="margin-top: var(--spacing-lg);">
    <h2 class="section-title">Новые блюда</h2>
    <div class="new-dishes-grid">
      <?php foreach ($new_dishes as $dish): ?>
        <div class="dish-card">
          <div class="dish-image">
            <img src="<?= htmlspecialchars($dish['image']) ?>" alt="<?= htmlspecialchars($dish['name']) ?>">
          </div>
          <div class="dish-name"><?= htmlspecialchars($dish['name']) ?></div>
          <div class="dish-desc"><?= htmlspecialchars($dish['description']) ?></div>
          <div class="dish-footer">
            <div class="price"><?= number_format((float)$dish['price'], 0, '.', ' ') ?> ₸</div>
            <a href="menu.php#menu" class="btn btn-outline order-btn">Заказать</a>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </section>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>

</body>
</html>
