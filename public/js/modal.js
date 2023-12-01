const body = document.querySelector('body');
const modal = document.querySelector('#modal');

const modalQuestion = document.querySelector('#modal-question');
const modalCancelBnt = document.querySelector('#modal-cancel');
const modalConfirmBnt = document.querySelector('#modal-confirm');

const modalActions = document.querySelectorAll('[data-modal]');

if (
	body &&
	modal &&
	modalActions &&
	modalQuestion &&
	modalCancelBnt &&
	modalConfirmBnt
) {
	modalActions.forEach((action) => {
		action.addEventListener('click', () => {
			body.style.overflow = 'hidden';
			modal.classList.toggle('hide');

			const type = action.dataset.modal;
			const title = action.dataset.title;
			const id = action.dataset.id;

			let url = '';

			switch (type) {
				// courses
				case 'disable-course':
					modalQuestion.innerHTML = `Tem certeza que quer desativar o curso: <b>${title}</b>?`;
					url = `/admin/course/disabled/${id}`;
					break;
				case 'activate-course':
					modalQuestion.innerHTML = `Tem certeza que quer ativar o curso: <b>${title}</b>?`;
					url = `/admin/course/activate/${id}`;
					break;
				case 'delete-course':
					modalQuestion.innerHTML = `Tem certeza que quer remover o curso: <b>${title}</b>?`;
					url = `/admin/course/delete/${id}`;
					break;

				// topics
				case 'delete-topic':
					modalQuestion.innerHTML = `Tem certeza que quer remover o tópico: <b>${title}</b>?`;
					url = `/admin/topics/delete/${id}`;
					break;

				default:
					break;
			}

			modalConfirmBnt.setAttribute('href', url);
		});
	});

	modalCancelBnt.addEventListener('click', () => {
		modal.classList.add('hide');
		body.style.overflow = 'visible';
	});
}
