const labelProfileImage = document.querySelector('#label-profile-image');
const profileImage = document.querySelector('#profile-image');

const avatars = document.querySelector('#avatars');
const avatarImages = document.querySelectorAll('#avatars img');

const previewProfileImage = document.querySelector('#preview-profile-image');

if (labelProfileImage && profileImage && avatars && previewProfileImage) {
	labelProfileImage.addEventListener('click', () => {
		avatars.classList.toggle('hide');
	});

	avatarImages.forEach((image) =>
		image.addEventListener('click', () => {
			const imageName = image.dataset.image;
			profileImage.value = imageName;

			const baseUrl = previewProfileImage.dataset.baseUrl;
			previewProfileImage.setAttribute(
				'src',
				baseUrl + '/public/images/' + imageName
			);
		})
	);
}
