// Открытие/закрытие корзины
const cartIcon = document.getElementById('cart-icon')
const cartModal = document.getElementById('cart-modal')
const closeModal = document.querySelector('.close-modal')

// Открыть корзину
cartIcon.addEventListener('click', () => {
	cartModal.style.display = 'block'
	document.body.style.overflow = 'hidden'
})

// Закрыть корзину
closeModal.addEventListener('click', () => {
	cartModal.style.display = 'none'
	document.body.style.overflow = 'auto'
})

// Закрыть при клике вне корзины
window.addEventListener('click', e => {
	if (e.target === cartModal) {
		cartModal.style.display = 'none'
		document.body.style.overflow = 'auto'
	}
})

// Добавляем обработчики для выпадающих меню
const navItems = document.querySelectorAll('.nav-item')

navItems.forEach(item => {
	// Для элементов с выпадающим меню
	if (item.querySelector('.dropdown-menu')) {
		const span = item.querySelector('span')

		span.addEventListener('click', e => {
			// Предотвращаем переход по ссылке, если это ссылка
			e.preventDefault()

			// Закрываем все другие открытые меню
			document.querySelectorAll('.dropdown-menu').forEach(menu => {
				if (menu !== item.querySelector('.dropdown-menu')) {
					menu.style.display = 'none'
					menu.style.opacity = '0'
				}
			})

			// Переключаем текущее меню
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

// Закрытие меню при клике вне его
document.addEventListener('click', e => {
	if (!e.target.closest('.nav-item')) {
		document.querySelectorAll('.dropdown-menu').forEach(menu => {
			menu.style.display = 'none'
			menu.style.opacity = '0'
		})
	}
})
