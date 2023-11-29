const message = document.querySelector('.session-message');
const time = 5000;

if (message) {
	setTimeout(() => {
		message.classList.add('hide');
	}, time);
}
