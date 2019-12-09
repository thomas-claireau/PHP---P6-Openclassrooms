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
		const commentaires = isCommentsUpdate.querySelectorAll('.commentaire');

		commentaires.forEach((commentaire, index) => {
			commentaire.addEventListener('click', (e) => {
				const indexCom = index + 1;
				const textarea = commentaire.querySelector('#commentaire-' + indexCom);
				const defaultContenu = textarea.value;

				console.log(e.target.tagName);

				if (e.target.tagName === 'DIV' || e.target.tagName === 'TEXTAREA') {
					if (!commentaire.classList.contains('active')) {
						// tinymce editor commentaire
						tinymce.init({
							target: textarea,
							plugins: tinyPlugins,
							toolbar:
								'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link',
						});

						// fermer les commentaires existants
						commentaires.forEach((item, index) => {
							if (item.classList.contains('active')) {
								const indexCom = index + 1;
								const textareaItem = item.querySelector('#commentaire-' + indexCom);
								const contenu = tinymce.activeEditor.getContent();

								tinymce.remove('#commentaire-' + indexCom);

								if (contenu !== defaultContenu) {
									textareaItem.value = defaultContenu;
								}

								item.classList.remove('active');
								item.querySelector('form').classList.add('disabled');
							}
						});
					}
				}

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

					if (cross) {
						cross.addEventListener('click', (e) => {
							const textareaTarget = comTarget.querySelector('textarea');
							console.log(tinymce.activeEditor);
							const contenu = tinymce.activeEditor.getContent();
							const tinyTarget = comTarget.querySelector('.tox-tinymce');

							tinyTarget.remove();

							tinymce.remove('#' + textareaTarget.id);

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
