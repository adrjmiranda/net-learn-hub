const bodyContent = document.querySelector('body');

const toggleTheme = document.querySelector('.toggle-theme');

const darkButton = document.querySelector('#dark');
const lightButton = document.querySelector('#light');

const banner = document.querySelector('#banner');
const bannerImage = document.querySelector('#banner-image');

const courseHome = document.querySelector('#course-home');

const setDarkTheme = () => {
	bodyContent.classList.remove('light');
	bodyContent.classList.add('dark');

	darkButton.classList.toggle('hide');
	lightButton.classList.toggle('hide');

	if (banner) {
		banner.style.backgroundColor = '#040404';
		toggleTheme.style.backgroundColor = '#040404';
	}

	if (bannerImage) {
		bannerImage.setAttribute(
			'src',
			bannerImage.dataset.baseUrl + '/public/images/dark_bg.png'
		);
	}

	localStorage.setItem('user_theme', 'dark');
};

const setLightTheme = () => {
	bodyContent.classList.remove('dark');
	bodyContent.classList.add('light');

	darkButton.classList.toggle('hide');
	lightButton.classList.toggle('hide');

	if (banner) {
		banner.style.backgroundColor = '#f3f6f9';
		toggleTheme.style.backgroundColor = '#f3f6f9';
	}

	if (bannerImage) {
		bannerImage.setAttribute(
			'src',
			bannerImage.dataset.baseUrl + '/public/images/light_bg.png'
		);
	}

	localStorage.setItem('user_theme', 'light');
};

if (bodyContent && darkButton && lightButton && toggleTheme) {
	const theme = localStorage.getItem('user_theme');

	if (theme) {
		switch (theme) {
			case 'dark':
				setDarkTheme();
				break;
			case 'light':
				setLightTheme();
				break;
		}
	}

	darkButton.addEventListener('click', () => {
		setDarkTheme();
	});

	lightButton.addEventListener('click', () => {
		setLightTheme();
	});
}
