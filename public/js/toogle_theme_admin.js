const bodyContent = document.querySelector('body');

const toggleTheme = document.querySelector('.toggle-theme');

const darkButton = document.querySelector('#dark');
const lightButton = document.querySelector('#light');

const setDarkTheme = () => {
	bodyContent.classList.remove('light');
	bodyContent.classList.add('dark');

	darkButton.classList.toggle('hide');
	lightButton.classList.toggle('hide');

	localStorage.setItem('admin_theme', 'dark');
};

const setLightTheme = () => {
	bodyContent.classList.remove('dark');
	bodyContent.classList.add('light');

	darkButton.classList.toggle('hide');
	lightButton.classList.toggle('hide');

	localStorage.setItem('admin_theme', 'light');
};

if (bodyContent && darkButton && lightButton && toggleTheme) {
	const theme = localStorage.getItem('admin_theme');

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
