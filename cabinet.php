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

$stmt = $pdo->prepare(
  'SELECT o.id, o.created_at, o.status, o.total_quantity, o.total_amount,
      GROUP_CONCAT(DISTINCT mi.name ORDER BY mi.name SEPARATOR ", ") AS items_names,
      GROUP_CONCAT(DISTINCT mi.id ORDER BY mi.id SEPARATOR ",") AS items_ids
   FROM orders o
   LEFT JOIN order_items oi ON o.id = oi.order_id
   LEFT JOIN menu_items mi ON oi.menu_item_id = mi.id
   WHERE o.user_id = ?
   GROUP BY o.id
   ORDER BY o.created_at DESC'
);
$stmt->execute([$_SESSION['user_id']]);
$orders_history = $stmt->fetchAll();

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

      <!-- История заказов (JOIN таблиц) -->
      <div style="margin-top: var(--spacing-xl);">
        <h2 class="cabinet__subtitle">История заказов</h2>

        <?php if (empty($orders_history)): ?>
          <p style="color:var(--color-text-muted);">
            У вас пока нет заказов.
            <a href="catalog.php">Перейти в каталог</a>
          </p>

        <?php else: ?>
          <table style="width:100%;border-collapse:collapse;">
            <thead>
              <tr style="border-bottom:2px solid var(--color-border);">
                <th style="padding:10px;text-align:left;font-size:14px;">Номер заказа</th>
                <th style="padding:10px;text-align:left;font-size:14px;">Блюда</th>
                <th style="padding:10px;text-align:left;font-size:14px;">Сумма</th>
                <th style="padding:10px;text-align:left;font-size:14px;">Дата</th>
                <th style="padding:10px;text-align:left;font-size:14px;">Статус</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($orders_history as $order): ?>
                <tr style="border-bottom:1px solid var(--color-border);">
                  <td style="padding:12px 10px;">
                    <?php
                      $order_number = '#'.str_pad($order['id'], 4, '0', STR_PAD_LEFT);
                      $first_item_id = null;
                      if (!empty($order['items_ids'])) {
                          $ids = explode(',', $order['items_ids']);
                          $first_item_id = intval($ids[0] ?? 0);
                      }
                    ?>
                    <?php if ($first_item_id > 0): ?>
                      <a href="detail.php?id=<?= $first_item_id ?>" style="color:var(--color-primary);text-decoration:none;">
                        <?= $order_number ?>
                      </a>
                    <?php else: ?>
                      <?= $order_number ?>
                    <?php endif; ?>
                  </td>
                  <td style="padding:12px 10px;">
                    <?= htmlspecialchars($order['items_names'] ?? '—') ?>
                  </td>
                  <td style="padding:12px 10px;">
                    <strong><?= number_format((float)$order['total_amount'], 0, '.', ' ') ?> ₸</strong>
                  </td>
                  <td style="padding:12px 10px;">
                    <?= date('d.m.Y H:i', strtotime($order['created_at'])) ?>
                  </td>
                  <td style="padding:12px 10px;">
                    <?php
                    $status_labels = [
                      'pending'   => '⏱ В обработке',
                      'confirmed' => '✓ Подтверждено',
                      'delivered' => '✓✓ Доставлено',
                      'cancelled' => '✗ Отменено',
                    ];
                    echo $status_labels[$order['status']] ?? htmlspecialchars($order['status']);
                    ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php endif; ?>
      </div>

    </div>
  </div>
</main>

<?php include 'includes/footer.php'; ?>

</body>
</html>
