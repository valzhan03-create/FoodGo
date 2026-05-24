// main.js — скрипт для оформления корзины и заказов без перезагрузки
document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.order-card-form').forEach(function (form) {
    form.addEventListener('submit', function (event) {
      event.preventDefault();

      var button = form.querySelector('button[type="submit"]');
      var dishId = form.querySelector('input[name="dish_id"]').value;
      var previousText = button.textContent;

      button.disabled = true;
      button.textContent = 'Добавляем...';

      var formData = new FormData(form);
      formData.append('add_to_order', '1');

      fetch('catalog.php', {
        method: 'POST',
        body: formData,
        headers: {
          'X-Requested-With': 'XMLHttpRequest'
        }
      })
        .then(function (response) {
          return response.json();
        })
        .then(function (data) {
          if (data.success) {
            var quantityText = 'В заказе: ' + data.quantity;
            var quantityNode = form.parentNode.querySelector('.order-quantity');
            if (!quantityNode) {
              quantityNode = document.createElement('div');
              quantityNode.className = 'order-quantity';
              var priceBlock = form.parentNode.querySelector('.price');
              if (priceBlock) {
                priceBlock.parentNode.insertBefore(quantityNode, priceBlock.nextSibling);
              } else {
                form.parentNode.insertBefore(quantityNode, form);
              }
            }
            quantityNode.textContent = quantityText;

            if (data.quantity >= 100) {
              button.textContent = 'Максимум 100';
              button.disabled = true;
            } else {
              button.textContent = 'Добавить ещё';
              button.disabled = false;
            }
          } else {
            var messages = Array.isArray(data.errors) ? data.errors.join(' ') : 'Ошибка при добавлении в заказ.';
            alert(messages);
            button.textContent = previousText;
            button.disabled = false;
          }
        })
        .catch(function () {
          alert('Ошибка при добавлении в заказ. Попробуйте ещё раз.');
          button.textContent = previousText;
          button.disabled = false;
        });
    });
  });
});
