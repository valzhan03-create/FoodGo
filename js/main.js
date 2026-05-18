// main.js — простой скрипт для базовой интерактивности
document.addEventListener('DOMContentLoaded', function () {
  // пример: обработка кликов на кнопках заказа
  document.querySelectorAll('.order-btn').forEach(function(btn){
    btn.addEventListener('click', function(e){
      e.preventDefault();
      alert('Функция заказа пока не реализована.');
    });
  });
});
