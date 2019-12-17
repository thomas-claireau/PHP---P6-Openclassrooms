import tinymce from 'tinymce/tinymce';
import 'tinymce/themes/silver';

import functions from '../../functions';

const tinyPlugins = ['paste', 'link', 'autoresize', 'image', 'imagetools'];
const tinyPluginsLimited = ['paste', 'autoresize'];

tinyPlugins.forEach((plugin) => {
	require('tinymce/plugins/' + plugin);
});

window.addEventListener('DOMContentLoaded', (event) => {
	const isPostUpdate = document.querySelector('.posts.update');
	const isPostCreate = document.querySelector('.posts.create');

	if (isPostUpdate || isPostCreate) {
		const textarea = document.querySelector('#editor');
		const textareaDesc = document.querySelector('#description');
		tinymce.init({
			target: textareaDesc,
			plugins: tinyPluginsLimited,
			toolbar: 'undo redo | bold italic',
			menubar: false,
		});
		tinymce.init({
			target: textarea,
			plugins: tinyPlugins,
			toolbar:
				'undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image',
			menubar: false,
			max_chars: "10",
			images_upload_url: './src/js/pages/admin/upload.php',
			images_upload_handler: (blobInfo, success, failure) => {
				const xhr = new XMLHttpRequest();
				xhr.withCredentials = false;
				xhr.open('POST', './src/assets/img/upload.php/?id=' + functions.$_GET('id'));

				xhr.onload = function () {
					var json;

					if (xhr.status != 200) {
						failure('HTTP Error: ' + xhr.status);
						return;
					}

					json = JSON.parse(xhr.responseText);

					if (!json || typeof json.location != 'string') {
						failure('Invalid JSON: ' + xhr.responseText);
						return;
					}

					json.location = './src/assets/img/' + json.location;

					success(json.location);
				};

				const formData = new FormData();
				formData.append('file', blobInfo.blob(), blobInfo.filename());

				xhr.send(formData);
			},
		});

		// prevent submit if tinymce editor is empty
		const saveButton = document.querySelector('button[type="submit"]');

		if (saveButton) {
			saveButton.addEventListener('click', (e) => {
				if (tinymce.activeEditor.getContent() == "") {
					e.preventDefault();
				}
			})
		}
	}

});
