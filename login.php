<?php
session_start();

// Если пользователь уже вошёл — перенаправляем в кабинет
if (isset($_SESSION['user_id'])) {
    header('Location: cabinet.php');
    exit;
}

require_once 'config/db.php';

$errors  = [];   // массив для сообщений об ошибках
$email   = '';   // запомним email чтобы вернуть в форму при ошибке
$success = '';   // сообщение об успешной регистрации
$redirect = 'cabinet.php';

$redirect_param = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $redirect_param = trim($_POST['redirect'] ?? '');
} else {
    $redirect_param = trim($_GET['redirect'] ?? '');
}

if ($redirect_param !== '' && strpos($redirect_param, '://') === false && strpos($redirect_param, '..') === false && $redirect_param[0] !== '/') {
    $redirect = $redirect_param;
}

// Если пришли сюда после регистрации — показываем успех
if (isset($_GET['registered'])) {
    $success = 'Регистрация прошла успешно! Теперь войдите в аккаунт.';
}

// Обработка формы — только если была нажата кнопка «Войти»
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email    = trim($_POST['email']    ?? '');
    $password = trim($_POST['password'] ?? '');

    // Шаг 1: Проверка на пустые поля
    if (empty($email) || empty($password)) {
        $errors[] = 'Заполните все поля.';
    } else {

        // Шаг 2: Ищем пользователя по email в базе данных
        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        // Шаг 3: Проверяем пароль
        if ($user && password_verify($password, $user['password'])) {

            // Шаг 4: Авторизация успешна — сохраняем данные в сессии
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];

            header('Location: ' . $redirect);
            exit;

        } else {
            // Пользователь не найден ИЛИ пароль неверный
            $errors[] = 'Неверный email или пароль.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Вход — FoodGo</title>
  <link rel="stylesheet" href="css/reset.css">
  <link rel="stylesheet" href="css/variables.css">
  <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include 'includes/header.php'; ?>

<main class="main">
  <div class="container">
    <div class="auth-card">

      <h1 class="auth-card__title">Вход в аккаунт</h1>

      <!-- Блок успеха: появляется если пришли после регистрации -->
      <?php if ($success): ?>
        <div class="alert alert-success">
          <p><?= htmlspecialchars($success) ?></p>
        </div>
      <?php endif; ?>

      <!-- Блок ошибок: появляется если email/пароль неверные -->
      <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
          <?php foreach ($errors as $error): ?>
            <p><?= htmlspecialchars($error) ?></p>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <!-- Форма входа -->
      <form class="form" method="POST" action="">

        <div class="form-group">
          <label class="form-label" for="email">Email</label>
          <input class="form-input" type="email" id="email" name="email"
            value="<?= htmlspecialchars($email) ?>"
            placeholder="ваш@email.com" required>
        </div>

        <div class="form-group">
          <label class="form-label" for="password">Пароль</label>
          <input class="form-input" type="password" id="password" name="password"
            placeholder="Ваш пароль" required>
        </div>

        <input type="hidden" name="redirect" value="<?= htmlspecialchars($redirect) ?>">

        <button class="btn btn-primary" type="submit">Войти</button>

        <p class="auth-card__footer">
          Нет аккаунта? <a href="register.php">Зарегистрироваться</a>
        </p>

      </form>
    </div>
  </div>
</main>

<?php include 'includes/footer.php'; ?>

</body>
</html>
