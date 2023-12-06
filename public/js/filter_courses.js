const courseCards = document.querySelectorAll('.course-card');
const filter = document.querySelector('#filter');

if (courseCards && filter) {
	filter.addEventListener('change', (e) => {
		courseCards.forEach((course) => {
			course.classList.remove('hide');
		});

		switch (e.target.value) {
			case 'all':
				courseCards.forEach((course) => {
					course.classList.remove('hide');
				});
				break;
			case 'active':
				courseCards.forEach((course) => {
					if (course.dataset.visibility == 0) {
						course.classList.add('hide');
					}
				});
				break;
			case 'inactive':
				courseCards.forEach((course) => {
					if (!(course.dataset.visibility == 0)) {
						course.classList.add('hide');
					}
				});
				break;
			default:
				courseCards.forEach((course) => {
					course.classList.remove('hide');
				});
		}
	});
}
