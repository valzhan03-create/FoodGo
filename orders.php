<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?redirect=orders.php');
    exit;
}

$orders = $_SESSION['orders'] ?? [];
if (!is_array($orders)) {
    $orders = [];
}

$orderItems = [];
$totalQuantity = 0;
$totalAmount = 0;
if (!empty($orders)) {
    $ids = array_keys($orders);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $pdo->prepare("SELECT * FROM menu_items WHERE id IN ($placeholders) ORDER BY id ASC");
    $stmt->execute($ids);
    $orderItems = $stmt->fetchAll();

    foreach ($orderItems as &$item) {
        $quantity = $orders[$item['id']] ?? 1;
        $item['quantity'] = $quantity;
        $item['line_total'] = $quantity * (float)$item['price'];
        $totalQuantity += $quantity;
        $totalAmount += $item['line_total'];
    }
    unset($item);
}

$historyOrders = [];
$historyItems = [];
$stmt = $pdo->prepare('SELECT * FROM orders WHERE user_id = ? ORDER BY id DESC');
$stmt->execute([$_SESSION['user_id']]);
$historyOrders = $stmt->fetchAll();

if (!empty($historyOrders)) {
    $orderIds = array_column($historyOrders, 'id');
    $placeholders = implode(',', array_fill(0, count($orderIds), '?'));
    $stmt = $pdo->prepare(
        "SELECT oi.*, mi.name AS item_name, mi.price AS item_price FROM order_items oi
         JOIN menu_items mi ON oi.menu_item_id = mi.id
         WHERE oi.order_id IN ($placeholders)
         ORDER BY oi.id ASC"
    );
    $stmt->execute($orderIds);
    $items = $stmt->fetchAll();

    foreach ($items as $item) {
        $historyItems[$item['order_id']][] = $item;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['remove_dish'])) {
        $removeId = intval($_POST['remove_dish']);
        if ($removeId > 0 && isset($orders[$removeId])) {
            unset($orders[$removeId]);
        }
    }

    if (isset($_POST['clear_orders'])) {
        $orders = [];
    }

    if (isset($_POST['save_order']) && !empty($orders)) {
        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare(
                'INSERT INTO orders (user_id, total_quantity, total_amount) VALUES (?, ?, ?)'
            );
            $stmt->execute([$_SESSION['user_id'], $totalQuantity, $totalAmount]);
            $orderId = $pdo->lastInsertId();

            $stmtItem = $pdo->prepare(
                'INSERT INTO order_items (order_id, menu_item_id, quantity, unit_price) VALUES (?, ?, ?, ?)' 
            );
            foreach ($orderItems as $item) {
                $stmtItem->execute([
                    $orderId,
                    $item['id'],
                    $item['quantity'],
                    $item['price'],
                ]);
            }

            $pdo->commit();
            $orders = [];
        } catch (Exception $e) {
            $pdo->rollBack();
        }
    }

    $_SESSION['orders'] = $orders;
    header('Location: catalog.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Заказы — FoodGo</title>
  <link rel="stylesheet" href="css/reset.css">
  <link rel="stylesheet" href="css/variables.css">
  <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include 'includes/header.php'; ?>

<main class="main">
  <div class="container">
    <section class="orders">
      <h1 class="section-title">Мои заказы</h1>

      <?php if (empty($orderItems)): ?>
        <div class="catalog-empty">
          <p>Похоже, у вас ещё нет заказов.</p>
          <a class="btn btn-primary" href="catalog.php">Перейти в каталог</a>
        </div>
      <?php else: ?>
        <div class="orders-summary">
          <p>Всего позиций: <strong><?= $totalQuantity ?></strong></p>
          <p>Общая сумма: <strong><?= number_format($totalAmount, 0, '.', ' ') ?> ₸</strong></p>
        </div>

        <div class="orders-grid">
          <?php foreach ($orderItems as $item): ?>
            <div class="order-card">
              <div class="order-card__image">
                <img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>">
              </div>
              <div class="order-card__body">
                <h3><?= htmlspecialchars($item['name']) ?></h3>
                <p class="order-card__category"><?= htmlspecialchars($item['category']) ?></p>
                <p><?= htmlspecialchars($item['description']) ?></p>
              </div>
              <div class="order-card__footer">
                <div>
                  <p>Количество: <strong><?= $item['quantity'] ?></strong></p>
                  <p>Сумма: <strong><?= number_format($item['line_total'], 0, '.', ' ') ?> ₸</strong></p>
                </div>
                <form method="POST">
                  <input type="hidden" name="remove_dish" value="<?= $item['id'] ?>">
                  <button class="btn btn-outline" type="submit">Удалить</button>
                </form>
              </div>
            </div>
          <?php endforeach; ?>
        </div>

        <div class="orders-actions">
          <form method="POST">
            <button class="btn btn-outline" type="submit" name="clear_orders">Очистить заказы</button>
          </form>
          <form method="POST">
            <button class="btn btn-primary" type="submit" name="save_order">Продолжить покупки</button>
          </form>
        </div>
      <?php endif; ?>

      <?php if (!empty($historyOrders)): ?>
        <div class="order-history">
          <h2 class="section-subtitle">История заказов</h2>
          <?php foreach ($historyOrders as $order): ?>
            <div class="order-history-card">
              <div class="order-history-header">
                <span>Заказ №<?= $order['id'] ?></span>
                <span><?= date('d.m.Y H:i', strtotime($order['created_at'])) ?></span>
              </div>
              <div class="order-history-info">
                <p>Позиции: <strong><?= $order['total_quantity'] ?></strong></p>
                <p>Сумма: <strong><?= number_format((float)$order['total_amount'], 0, '.', ' ') ?> ₸</strong></p>
              </div>
              <ul class="order-history-list">
                <?php foreach ($historyItems[$order['id']] ?? [] as $item): ?>
                  <li>
                    <?= htmlspecialchars($item['item_name']) ?> — <?= $item['quantity'] ?> шт × <?= number_format((float)$item['item_price'], 0, '.', ' ') ?> ₸
                  </li>
                <?php endforeach; ?>
              </ul>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </section>
  </div>
</main>

<?php include 'includes/footer.php'; ?>

</body>
</html>
