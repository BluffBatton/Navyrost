/**
 * Головний простір імен App
 */
const App = {
  // Методи для навігації
  navigation: {
    init: function() {
      const navItems = document.querySelectorAll('.nav-item');
      navItems.forEach(item => {
        if (item.querySelector('.dropdown-menu')) {
          const span = item.querySelector('span');
          span.addEventListener('click', this.toggleDropdown.bind(this, item));
        }
      });
    },
    toggleDropdown: function(item, e) {
      e.preventDefault();
      document.querySelectorAll('.dropdown-menu').forEach(menu => {
        if (menu !== item.querySelector('.dropdown-menu')) {
          menu.style.display = 'none';
          menu.style.opacity = '0';
        }
      });
      const menu = item.querySelector('.dropdown-menu');
      if (menu.style.display === 'block') {
        menu.style.display = 'none';
        menu.style.opacity = '0';
      } else {
        menu.style.display = 'block';
        setTimeout(() => {
          menu.style.opacity = '1';
        }, 10);
      }
    }
  },

  // Методи для роботи з розмірами
  sizes: {
    init: function() {
      document.querySelectorAll('.size-option').forEach(option => {
        option.addEventListener('click', function() {
          const checkbox = this.querySelector('input[type="checkbox"]');
          checkbox.checked = !checkbox.checked;
          this.classList.toggle('selected', checkbox.checked);
        });
      });
    }
  },

  // Методи для превью зображень
  imagePreview: {
    init: function() {
      document.getElementById('image')?.addEventListener('change', function(e) {
        const preview = document.querySelector('.image-preview img');
        if (!preview) return;
        if (this.files && this.files[0]) {
          const reader = new FileReader();
          reader.onload = function(e) {
            preview.src = e.target.result;
          };
          reader.readAsDataURL(this.files[0]);
        }
      });
    }
  },

  // Перетворення тексту
  textConverter: {
    markdownToHtml: function(text) {
      return text
        .replace(/^\#\s(.*$)/gm, '<h1>$1</h1>')
        .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
        .replace(/\*(.*?)\*/g, '<em>$1</em>')
        .replace(/`(.*?)`/g, '<code>$1</code>')
        .replace(/\[(.*?)\]\((.*?)\)/g, '<a href="$2">$1</a>');
    },
    htmlToPlainText: function(html) {
      return html.replace(/<[^>]*>/g, '');
    },
    test: function(input) {
      return this.markdownToHtml(input);
    }
  }
};

// Ініціалізація модулів при завантаженні сторінки
document.addEventListener('DOMContentLoaded', function() {
  App.navigation.init();
  App.sizes.init();
  App.imagePreview.init();
  
  // Додатковий код для адмін-панелі
  if (document.getElementById('namespace-structure')) {
    initAdminPanel();
  }
});

/**
 * Функції для адмін-панелі
 */
function initAdminPanel() {
  // Відображаємо структуру простору імен
  displayNamespaceStructure();
  
  // Ініціалізуємо тестування методів
  document.getElementById('module-select').addEventListener('change', function() {
    updateInputPlaceholder();
  });
  
  // Встановлюємо початковий плейсхолдер
  updateInputPlaceholder();
}

function displayNamespaceStructure() {
  const structure = `const App = {
  navigation: {
    init(),
    toggleDropdown()
  },
  sizes: {
    init()
  },
  imagePreview: {
    init()
  },
  textConverter: {
    markdownToHtml(text),
    htmlToPlainText(html),
    test(input)
  }
}`;
  
  document.getElementById('namespace-structure').textContent = structure;
}

function updateInputPlaceholder() {
  const module = document.getElementById('module-select').value;
  const textarea = document.getElementById('input-data');
  
  switch(module) {
    case 'textConverter':
      textarea.placeholder = "Введіть Markdown текст для перетворення (наприклад: **жирний**, *курсив*)";
      break;
    case 'navigation':
      textarea.placeholder = "Цей модуль не потребує введення тексту";
      break;
    case 'sizes':
      textarea.placeholder = "Цей модуль не потребує введення тексту";
      break;
    default:
      textarea.placeholder = "Введіть дані для тестування";
  }
}

function runTest() {
  const module = document.getElementById('module-select').value;
  const input = document.getElementById('input-data').value;
  const resultDiv = document.getElementById('test-result');
  
  let result;
  
  switch(module) {
    case 'textConverter':
      result = App.textConverter.test(input);
      break;
    case 'navigation':
      result = "Навігація ініціалізована. Спробуйте клікнути на елементи меню.";
      break;
    case 'sizes':
      result = "Менеджер розмірів ініціалізований. Спробуйте клікнути на опції розмірів.";
      break;
    default:
      result = "Оберіть модуль для тестування";
  }
  
  resultDiv.innerHTML = `<strong>Результат:</strong><br>${result}`;
}