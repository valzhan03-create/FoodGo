<?php
session_start();
require_once 'config/db.php';

$success = '';
$errors  = [];

if (!isset($_SESSION['orders']) || !is_array($_SESSION['orders'])) {
    $_SESSION['orders'] = [];
}

$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_order'])) {
    $dish_id = intval($_POST['dish_id'] ?? 0);

    if (!isset($_SESSION['user_id'])) {
        $errors[] = 'Войдите в аккаунт, чтобы добавить блюдо в заказы.';
    } elseif ($dish_id <= 0) {
        $errors[] = 'Неверный идентификатор блюда.';
    } else {
        $stmtOrder = $pdo->prepare('SELECT * FROM menu_items WHERE id = ?');
        $stmtOrder->execute([$dish_id]);
        $dishToOrder = $stmtOrder->fetch();

        if (!$dishToOrder) {
            $errors[] = 'Такого блюда не найдено.';
        } else {
            $currentQty = $_SESSION['orders'][$dish_id] ?? 0;
            if ($currentQty >= 100) {
                $errors[] = 'Максимальное количество для этого блюда — 100 штук.';
            } else {
                $_SESSION['orders'][$dish_id] = $currentQty + 1;
                $newQty = $_SESSION['orders'][$dish_id];
                $message = 'Блюдо «' . $dishToOrder['name'] . '» добавлено в заказы. Всего: ' . $newQty . '.';

                if ($isAjax) {
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode([
                        'success' => true,
                        'message' => $message,
                        'quantity' => $newQty,
                        'dish_id' => $dish_id,
                    ], JSON_UNESCAPED_UNICODE);
                    exit;
                }

                $_SESSION['catalog_success'] = $message;
                $query = array_filter([
                    'search' => $_GET['search'] ?? '',
                    'max_price' => $_GET['max_price'] ?? '',
                    'sort' => $_GET['sort'] ?? 'id',
                ], function ($value) {
                    return $value !== '';
                });
                $location = 'catalog.php' . ($query ? '?' . http_build_query($query) : '');
                header('Location: ' . $location);
                exit;
            }
        }
    }

    if ($isAjax) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success' => false,
            'errors' => $errors,
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

$success = $_SESSION['catalog_success'] ?? '';
unset($_SESSION['catalog_success']);

// Читаем фильтры из GET-параметров
$search    = trim($_GET['search']    ?? '');
$max_price = intval($_GET['max_price'] ?? 0);
$sort      = $_GET['sort'] ?? 'id';

$allowed_sort = ['id', 'price_asc', 'price_desc', 'name'];
if (!in_array($sort, $allowed_sort, true)) {
    $sort = 'id';
}

$where  = [];
$params = [];

if ($search !== '') {
    $where[]  = 'name LIKE ?';
    $params[] = '%' . $search . '%';
}

if ($max_price > 0) {
    $where[]  = 'price <= ?';
    $params[] = $max_price;
}

$where_sql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

switch ($sort) {
    case 'price_asc':
        $order_sql = 'ORDER BY price ASC';
        break;
    case 'price_desc':
        $order_sql = 'ORDER BY price DESC';
        break;
    case 'name':
        $order_sql = 'ORDER BY name ASC';
        break;
    default:
        $order_sql = 'ORDER BY id ASC';
}

$stmt = $pdo->prepare("SELECT * FROM menu_items $where_sql $order_sql");
$stmt->execute($params);
$dishes = $stmt->fetchAll();
$count  = count($dishes);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Каталог — FoodGo</title>
  <link rel="stylesheet" href="css/reset.css">
  <link rel="stylesheet" href="css/variables.css">
  <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include 'includes/header.php'; ?>

<main class="main">
  <div class="container">
    <section class="catalog">
      <h1 class="section-title">Каталог блюд</h1>

      <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
          <?php foreach ($errors as $error): ?>
            <p><?= htmlspecialchars($error) ?></p>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <form class="catalog-filters" method="GET" action="">
        <div class="filter-row">
          <div class="filter-group">
            <label class="form-label" for="search">Поиск по названию</label>
            <input class="form-input" type="text" id="search" name="search"
              value="<?= htmlspecialchars($search) ?>"
              placeholder="Например: пицца">
          </div>

          <div class="filter-group">
            <label class="form-label" for="max_price">Максимальная цена (₸)</label>
            <input class="form-input" type="number" id="max_price" name="max_price"
              value="<?= $max_price > 0 ? $max_price : '' ?>"
              min="0" placeholder="Например: 3000">
          </div>

          <div class="filter-group">
            <label class="form-label" for="sort">Сортировка</label>
            <select class="form-input" id="sort" name="sort">
              <option value="id" <?= $sort === 'id' ? 'selected' : '' ?>>По умолчанию</option>
              <option value="price_asc" <?= $sort === 'price_asc' ? 'selected' : '' ?>>Цена ↑</option>
              <option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : '' ?>>Цена ↓</option>
              <option value="name" <?= $sort === 'name' ? 'selected' : '' ?>>По названию</option>
            </select>
          </div>
        </div>

        <div class="catalog-filters__actions">
          <button class="btn btn-primary" type="submit">Применить</button>
          <?php if ($search !== '' || $max_price > 0): ?>
            <a class="btn btn-outline" href="catalog.php">Сбросить фильтры</a>
          <?php endif; ?>
        </div>
      </form>

      <div class="catalog-results">
        <div class="catalog-results__header">
          <div>
            <h2>
              <?php if ($search !== '' || $max_price > 0): ?>
                Результаты поиска
              <?php else: ?>
                Все блюда
              <?php endif; ?>
            </h2>
            <p class="catalog-results__summary">Найдено: <?= $count ?></p>
          </div>
        </div>

        <?php if (empty($dishes)): ?>
          <div class="catalog-empty">
            <p>По вашему запросу ничего не найдено.</p>
            <a class="btn btn-outline" href="catalog.php">Сбросить фильтры</a>
          </div>
        <?php else: ?>
          <div class="menu-grid">
            <?php foreach ($dishes as $dish): ?>
              <div class="dish-card">
                <div class="dish-image">
                  <img src="<?= htmlspecialchars($dish['image']) ?>" alt="<?= htmlspecialchars($dish['name']) ?>">
                </div>
                <div class="dish-name"><?= htmlspecialchars($dish['name']) ?></div>
                <div class="dish-category"><?= htmlspecialchars($dish['category']) ?></div>
                <div class="dish-desc"><?= htmlspecialchars($dish['description']) ?></div>
                <div class="dish-footer">
                  <div>
                    <div class="price"><?= number_format((float)$dish['price'], 0, '.', ' ') ?> ₸</div>
                    <?php $quantity = $_SESSION['orders'][$dish['id']] ?? 0; ?>
                    <?php if ($quantity > 0): ?>
                      <div class="order-quantity">В заказе: <?= $quantity ?></div>
                    <?php endif; ?>
                  </div>
                  <div style="display:flex;gap:8px;flex-wrap:wrap;">
                    <a href="detail.php?id=<?= $dish['id'] ?>" class="btn btn-outline order-btn" style="flex:1;">
                      Подробнее
                    </a>
                    <?php if (!isset($_SESSION['user_id'])): ?>
                      <a href="login.php?redirect=catalog.php" class="btn btn-outline order-btn" style="flex:1;">Войти</a>
                    <?php else: ?>
                      <form class="order-card-form" method="POST" style="flex:1;">
                        <input type="hidden" name="dish_id" value="<?= $dish['id'] ?>">
                        <button class="btn btn-primary order-submit" type="submit" name="add_to_order" 
                                style="width:100%;" <?= $quantity >= 100 ? 'disabled' : '' ?>>
                          <?= $quantity === 0 ? 'Заказать' : ($quantity >= 100 ? 'Максимум' : 'Ещё') ?>
                        </button>
                      </form>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
    </section>
  </div>
</main>

<?php include 'includes/footer.php'; ?>

</body>
</html>
