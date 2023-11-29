const imagePreview = document.querySelector('#preview');
const imageInput = document.querySelector('#image');

if (imagePreview && imageInput) {
	imageInput.addEventListener(
		'change',
		(e) => {
			if (e.target.files && e.target.files[0]) {
				let file = new FileReader();
				file.onload = function (e) {
					imagePreview.src = e.target.result;
				};
				file.readAsDataURL(e.target.files[0]);
			}
		},
		false
	);
}
