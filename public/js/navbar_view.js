const navbarDrop = document.querySelector('#navbar-drop');
const topBar = document.querySelector('#navbar .bar');
const navbarBtn = document.querySelectorAll('#navbar .btn');

if (navbarDrop && topBar && navbarBtn) {
	navbarDrop.addEventListener('click', () => {
		topBar.classList.toggle('hide');
		navbarBtn.forEach((btn) => btn.classList.toggle('hide'));
	});
}
