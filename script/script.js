const navItems = document.querySelectorAll('.nav-item')

navItems.forEach(item => {
	if (item.querySelector('.dropdown-menu')) {
		const span = item.querySelector('span')

		span.addEventListener('click', e => {
			e.preventDefault()
			document.querySelectorAll('.dropdown-menu').forEach(menu => {
				if (menu !== item.querySelector('.dropdown-menu')) {
					menu.style.display = 'none'
					menu.style.opacity = '0'
				}
			})
			const menu = item.querySelector('.dropdown-menu')
			if (menu.style.display === 'block') {
				menu.style.display = 'none'
				menu.style.opacity = '0'
			} else {
				menu.style.display = 'block'
				setTimeout(() => {
					menu.style.opacity = '1'
				}, 10)
			}
		})
	}
})

document.addEventListener('click', e => {
	if (!e.target.closest('.nav-item')) {
		document.querySelectorAll('.dropdown-menu').forEach(menu => {
			menu.style.display = 'none'
			menu.style.opacity = '0'
		})
	}
})

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.size-option').forEach(option => {
        option.addEventListener('click', function () {
            const checkbox = this.querySelector('input[type="checkbox"]');
            checkbox.checked = !checkbox.checked;
            this.classList.toggle('selected', checkbox.checked);
        });
    });
});

document.getElementById('image').addEventListener('change', function(e) {
            const preview = document.querySelector('.image-preview img');
            if (!preview) return;
            
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                }
                reader.readAsDataURL(this.files[0]);
            }
        });
	
function selectSize(element) {
            document.querySelectorAll('.size-table td').forEach(td => {
                td.classList.remove('selected');
            });
            
            element.classList.add('selected');
            
            document.getElementById('selected-size').value = element.textContent.trim();
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            const firstSize = document.querySelector('.size-table td');
            if (firstSize) {
                firstSize.classList.add('selected');
            }
        });

        document.querySelectorAll('a[href="#reviews"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });


// ===== Пространство имен App =====
const App = {
  // Методы для навигации
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

  // Методы для работы с размерами
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

  // Методы для превью изображений
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

  // Преобразование текста (решение предыдущей задачи)
  textConverter: {
    markdownToHtml: function(text) {
      return text
        .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
        .replace(/\*(.*?)\*/g, '<em>$1</em>')
        .replace(/`(.*?)`/g, '<code>$1</code>');
    },
    htmlToPlainText: function(html) {
      return html.replace(/<[^>]*>/g, '');
    }
  }
};

// ===== Инициализация всех модулей =====
document.addEventListener('DOMContentLoaded', function() {
  App.navigation.init();
  App.sizes.init();
  App.imagePreview.init();
});

setInterval(function() {
    fetch('/Controller/get_unread_count.php')
        .then(response => response.json())
        .then(data => {
            const badge = document.querySelector('.notification-badge');
            if (data.count > 0) {
                if (badge) {
                    badge.textContent = data.count;
                    badge.classList.add('pulse');
                } else {
                    const link = document.querySelector('.account-nav a[href*="notifications.php"]');
                    if (link) {
                        const newBadge = document.createElement('span');
                        newBadge.className = 'notification-badge pulse';
                        newBadge.textContent = data.count;
                        link.appendChild(newBadge);
                    }
                }
            } else if (badge) {
                badge.remove();
            }
        });
}, 30000);