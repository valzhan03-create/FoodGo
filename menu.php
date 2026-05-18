<?php
session_start();
// Простая страница "Меню" с набором блюд и кнопками "Заказать"
$dishes = [
  ['name' => 'Пицца Маргарита', 'desc' => 'Томатный соус, моцарелла, базилик', 'price' => '499', 'image' => 'https://picsum.photos/seed/1/600/400'],
  ['name' => 'Пицца Пепперони', 'desc' => 'Острая пепперони и сыр', 'price' => '559', 'image' => 'https://picsum.photos/seed/2/600/400'],
  ['name' => 'Пицца Четыре сыра', 'desc' => 'Моцарелла, пармезан, дорблю, чеддер', 'price' => '629', 'image' => 'https://picsum.photos/seed/3/600/400'],
  ['name' => 'Бургер Классический', 'desc' => 'Говяжья котлета, салат, томат, соус', 'price' => '399', 'image' => 'https://picsum.photos/seed/4/600/400'],
  ['name' => 'Бургер Чеддер', 'desc' => 'Сыр чеддер, карамелизированный лук', 'price' => '449', 'image' => 'https://picsum.photos/seed/5/600/400'],
  ['name' => 'Суши Сет "Классик"', 'desc' => 'Ассорти роллов и нигири (12 шт.)', 'price' => '799', 'image' => 'https://picsum.photos/seed/6/600/400'],
  ['name' => 'Ролл Калифорния', 'desc' => 'Краб, авокадо, огурец, икра', 'price' => '399', 'image' => 'https://picsum.photos/seed/7/600/400'],
  ['name' => 'Рамен с курицей', 'desc' => 'Ароматный бульон, лапша, курица', 'price' => '459', 'image' => 'https://picsum.photos/seed/8/600/400'],
  ['name' => 'Салат Цезарь', 'desc' => 'Курица, пармезан, соус цезарь', 'price' => '349', 'image' => 'https://picsum.photos/seed/9/600/400'],
  ['name' => 'Салат Греческий', 'desc' => 'Фета, оливки, свежие овощи', 'price' => '319', 'image' => 'https://picsum.photos/seed/10/600/400'],
  ['name' => 'Лазанья', 'desc' => 'Сытная мясная лазанья с соусом бешамель', 'price' => '549', 'image' => 'https://picsum.photos/seed/11/600/400'],
  ['name' => 'Паста Карбонара', 'desc' => 'Бекон, яйцо, пармезан', 'price' => '399', 'image' => 'https://picsum.photos/seed/12/600/400'],
  ['name' => 'Стейк Рибай', 'desc' => '250 г, прожарка по выбору', 'price' => '1299', 'image' => 'https://picsum.photos/seed/13/600/400'],
  ['name' => 'Куриный гриль', 'desc' => 'Маринованная курица на гриле', 'price' => '389', 'image' => 'https://picsum.photos/seed/14/600/400'],
  ['name' => 'Тако с говядиной', 'desc' => 'Тако с пикантной говяжьей начинкой', 'price' => '299', 'image' => 'https://picsum.photos/seed/15/600/400'],
  ['name' => 'Фахитас', 'desc' => 'Курица с овощами, тортильи', 'price' => '429', 'image' => 'https://picsum.photos/seed/16/600/400'],
  ['name' => 'Карри с овощами', 'desc' => 'Овощное карри с рисом басмати', 'price' => '369', 'image' => 'https://picsum.photos/seed/17/600/400'],
  ['name' => 'Том Ям', 'desc' => 'Тайский острый суп с креветками', 'price' => '399', 'image' => 'https://picsum.photos/seed/18/600/400'],
  ['name' => 'Блины с лососем', 'desc' => 'Тонкие блины с копченым лососем', 'price' => '349', 'image' => 'https://picsum.photos/seed/19/600/400'],
  ['name' => 'Чизкейк', 'desc' => 'Нежный чизкейк на печеньевом основании', 'price' => '249', 'image' => 'https://picsum.photos/seed/20/600/400'],
];
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

    <div class="menu-grid" id="menu">
      <?php foreach ($dishes as $dish): ?>
        <div class="dish-card">
          <div class="dish-image">
            <img src="<?= htmlspecialchars($dish['image']) ?>" alt="<?= htmlspecialchars($dish['name']) ?>">
          </div>
          <div class="dish-name"><?= htmlspecialchars($dish['name']) ?></div>
          <div class="dish-desc"><?= htmlspecialchars($dish['desc']) ?></div>
          <div class="dish-footer">
            <div class="price"><?= htmlspecialchars($dish['price']) ?> ₽</div>
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
