<?php
session_start();
require_once 'config/db.php';

$stmt = $pdo->query('SELECT * FROM menu_items ORDER BY id');
$dishes = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Меню — FoodGo</title>
  <link rel="stylesheet" href="css/reset.css">
  <link rel="stylesheet" href="css/variables.css">
  <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include 'includes/header.php'; ?>

<main class="main">
  <div class="container">
    <h1 class="auth-card__title">Меню</h1>

    <?php if (empty($dishes)): ?>
      <p class="menu-empty">Пока нет доступных блюд. Попробуйте позже.</p>
    <?php endif; ?>

    <div class="menu-grid" id="menu">
      <?php foreach ($dishes as $dish): ?>
        <div class="dish-card">
          <div class="dish-image">
            <img src="<?= htmlspecialchars($dish['image']) ?>" alt="<?= htmlspecialchars($dish['name']) ?>">
          </div>
          <div class="dish-name"><?= htmlspecialchars($dish['name']) ?></div>
          <div class="dish-desc"><?= htmlspecialchars($dish['description']) ?></div>
          <div class="dish-footer">
            <div class="price"><?= number_format((float)$dish['price'], 0, '.', ' ') ?> ₸</div>
            <a href="#" class="btn btn-outline order-btn">Заказать</a>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

  </div>
</main>

<?php include 'includes/footer.php'; ?>

</body>
</html>
