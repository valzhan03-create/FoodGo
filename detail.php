<?php
session_start();
require_once 'config/db.php';

// Получаем id из URL и преобразуем в число
$id = intval($_GET['id'] ?? 0);

// Если id не задан или равен 0 — возвращаем в каталог
if ($id <= 0) {
    header('Location: catalog.php');
    exit;
}

// Ищем блюдо в БД
$stmt = $pdo->prepare('SELECT * FROM menu_items WHERE id = ?');
$stmt->execute([$id]);
$dish = $stmt->fetch();

// Если запись не найдена — возвращаем в каталог
if (!$dish) {
    header('Location: catalog.php');
    exit;
}

// Подготовим изображение с fallback, если поле пустое
$image = !empty($dish['image']) ? $dish['image'] : 'images/placeholder.png';
$image = htmlspecialchars($image, ENT_QUOTES, 'UTF-8');
?>

<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($dish['name']) ?> — FoodGo</title>
  <link rel="stylesheet" href="css/reset.css">
  <link rel="stylesheet" href="css/variables.css">
  <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include 'includes/header.php'; ?>

<main class="main">
  <div class="container">

    <!-- Кнопка «Назад» -->
    <a href="catalog.php"
       style="display:inline-flex;align-items:center;gap:8px;
              color:var(--color-text-muted);font-size:14px;margin-bottom:24px;
              text-decoration:none;">
      ← Вернуться в каталог
    </a>

    <!-- Детальная карточка блюда -->
    <div style="max-width:800px;display:grid;grid-template-columns:1fr 1fr;gap:40px;align-items:start;">

      <!-- Изображение -->
      <div>
        <img src="<?= $image ?>" 
             alt="<?= htmlspecialchars($dish['name']) ?>"
             style="width:100%;border-radius:12px;object-fit:cover;height:400px;">
      </div>

      <!-- Информация -->
      <div>
        <h1 style="font-size:var(--font-size-h1);font-weight:700;
                    margin-bottom:var(--spacing-md);">
          <?= htmlspecialchars($dish['name']) ?>
        </h1>

        <p style="font-size:var(--font-size-lg);color:var(--color-text-muted);
                   margin-bottom:var(--spacing-lg);line-height:1.6;">
          <?= htmlspecialchars($dish['description']) ?>
        </p>

        <!-- Таблица характеристик -->
        <table style="width:100%;border-collapse:collapse;
                       margin-bottom:var(--spacing-xl);">
          <tr style="border-bottom:1px solid var(--color-border);">
            <td style="padding:14px 0;font-weight:600;
                        color:var(--color-text-muted);width:140px;">Категория</td>
            <td style="padding:14px 0;">
              <?= htmlspecialchars($dish['category']) ?>
            </td>
          </tr>
          <tr style="border-bottom:1px solid var(--color-border);">
            <td style="padding:14px 0;font-weight:600;color:var(--color-text-muted);">
              Цена
            </td>
            <td style="padding:14px 0;font-weight:700;
                        color:var(--color-primary);font-size:20px;">
              <?= number_format((float)$dish['price'], 0, '.', ' ') ?> ₸
            </td>
          </tr>
          <tr>
            <td style="padding:14px 0;font-weight:600;color:var(--color-text-muted);">
              Добавлено
            </td>
            <td style="padding:14px 0;">
              <?= date('d.m.Y', strtotime($dish['created_at'])) ?>
            </td>
          </tr>
        </table>

        <!-- Кнопки действий -->
        <div style="display:flex;gap:12px;">
          <form method="POST" action="catalog.php" style="flex:1;">
            <input type="hidden" name="dish_id" value="<?= $dish['id'] ?>">
            <button class="btn btn-primary" type="submit" name="add_to_order"
                    style="width:100%;">
              <?php if (isset($_SESSION['user_id'])): ?>
                Добавить в заказ
              <?php else: ?>
                Войти чтобы заказать
              <?php endif; ?>
            </button>
          </form>
          <?php if (!isset($_SESSION['user_id'])): ?>
            <a href="login.php?redirect=<?= urlencode('detail.php?id=' . $dish['id']) ?>" 
               class="btn btn-outline">Войти</a>
          <?php endif; ?>
        </div>
      </div>
    </div>

  </div>
</main>

<?php include 'includes/footer.php'; ?>

</body>
</html>
