// main.js — скрипт для оформления корзины и заказов без перезагрузки
document.addEventListener('DOMContentLoaded', function () {
  var notice = document.getElementById('catalog-message');

  function showMessage(text, type) {
    if (!notice) {
      return;
    }
    notice.innerHTML = '<div class="alert alert-' + (type === 'success' ? 'success' : 'error') + '"><p>' + text + '</p></div>';
  }

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
            showMessage(data.message, 'success');
            var quantityText = 'В заказе: ' + data.quantity;
            var quantityNode = form.querySelector('.order-quantity');
            if (!quantityNode) {
              quantityNode = document.createElement('div');
              quantityNode.className = 'order-quantity';
              form.parentNode.insertBefore(quantityNode, form.nextSibling);
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
            showMessage(messages, 'error');
            button.textContent = previousText;
            button.disabled = false;
          }
        })
        .catch(function () {
          showMessage('Ошибка при добавлении в заказ. Попробуйте ещё раз.', 'error');
          button.textContent = previousText;
          button.disabled = false;
        });
    });
  });
});
