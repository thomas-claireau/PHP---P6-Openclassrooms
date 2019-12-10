import tinymce from 'tinymce/tinymce';
import 'tinymce/themes/silver';

const tinyPlugins = ['paste', 'link', 'autoresize', 'image', 'imagetools'];

tinyPlugins.forEach((plugin) => {
	require('tinymce/plugins/' + plugin);
});

window.addEventListener('DOMContentLoaded', (event) => {
	const isPostUpdate = document.querySelector('.posts.update');
	const isPostCreate = document.querySelector('.posts.create');

	if (isPostUpdate || isPostCreate) {
		const textarea = document.querySelector('#editor');
		tinymce.init({
			target: textarea,
			plugins: tinyPlugins,
			toolbar:
				'undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image',
			imagetools_cors_hosts: ['recette.thomas-claireau.fr'],
			imagetools_credentials_hosts: ['recette.thomas-claireau.fr'],
			imagetools_toolbar: 'rotateleft rotateright | flipv fliph | editimage imageoptions',
			images_upload_base_path: '/some/basepath',
		});
	}
});
