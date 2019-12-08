import tinymce from 'tinymce/tinymce';
import 'tinymce/themes/silver';

const tinyPlugins = ['paste', 'link', 'autoresize'];

tinyPlugins.forEach((plugin) => {
	require('tinymce/plugins/' + plugin);
});

window.addEventListener('DOMContentLoaded', (event) => {
	// comments update
	const isCommentsUpdate = document.querySelector('.comments.update');

	if (isCommentsUpdate) {
		const editorSelector = $('.commentaire #contenu');
		const commentaires = isCommentsUpdate.querySelectorAll('.commentaire');

		commentaires.forEach((commentaire) => {
			commentaire.addEventListener('click', (e) => {
				const textarea = commentaire.querySelector('textarea');
				const defaultContenu = textarea.value;

				// tinymce editor commentaire
				tinymce.init({
					target: textarea,
					plugins: tinyPlugins,
					toolbar:
						'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link',
				});

				// fermer les commentaires existants
				commentaires.forEach((item) => {
					if (item.classList.contains('active')) {
						const textareaItem = item.querySelector('textarea');
						const contenu = textareaItem.value;

						tinymce.remove('.commentaire #contenu');

						if (contenu !== defaultContenu) {
							textareaItem.value = defaultContenu;
						}

						item.classList.remove('active');
						item.querySelector('form').classList.add('disabled');
					}
				});

				const comTarget = e.currentTarget;

				if (
					comTarget &&
					!comTarget.classList.contains('active') &&
					!comTarget.classList.contains('disabled')
				) {
					const form = comTarget.querySelector('form');
					const cross = comTarget.querySelector('.close');
					comTarget.classList.add('active');
					form.classList.remove('disabled');

					// tinymce editor commentaire
					tinymce.init({
						target: comTarget.querySelector('textarea'),
						plugins: tinyPlugins,
						toolbar:
							'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link',
					});

					if (cross) {
						cross.addEventListener('click', () => {
							const textareaTarget = comTarget.querySelector('textarea');
							const contenu = textareaTarget.value;

							tinymce.remove('.commentaire #contenu');
							textareaTarget.style.display = 'none';

							if (contenu !== defaultContenu) {
								textareaTarget.value = defaultContenu;
							}

							form.classList.add('disabled');
							comTarget.classList.remove('active');
							comTarget.classList.add('disabled');
						});
					}
				} else {
					comTarget.classList.remove('disabled');
				}
			});
		});
	}

	// comments remove
	const isCommentsRemove = document.querySelector('.comments.remove');

	if (isCommentsRemove) {
		const commentaires = isCommentsRemove.querySelectorAll('.commentaire');

		commentaires.forEach((commentaire) => {
			const btnRetour = commentaire.querySelector('.retour');

			commentaire.addEventListener('click', () => {
				// retour
				btnRetour.addEventListener('click', () => {
					commentaire.classList.remove('active');
					commentaire.classList.add('back-ok');
				});

				// remove element in front
				const btnRemove = isCommentsRemove.querySelector('.confirm');
				btnRemove.addEventListener('click', () => {
					if (commentaire.classList.contains('active')) {
						commentaire.classList.add('remove');

						setTimeout(() => {
							commentaire.remove();
							console.log(commentaires.length);
						}, 300);
					}
				});

				// activer
				commentaires.forEach((commentaire) => {
					if (commentaire.classList.contains('active')) {
						commentaire.classList.remove('active');
					}
				});

				if (
					!commentaire.classList.contains('active') &&
					!commentaire.classList.contains('back-ok')
				) {
					commentaire.classList.add('active');
				} else {
					if (commentaire.classList.contains('back-ok')) {
						commentaire.classList.remove('active');
						commentaire.classList.remove('back-ok');
					}
				}
			});
		});
	}
});
