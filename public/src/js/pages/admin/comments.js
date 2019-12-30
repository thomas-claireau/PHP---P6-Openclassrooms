import tinymce from 'tinymce/tinymce';
import 'tinymce/themes/silver';
import functions from '../../functions';

const tinyPlugins = ['paste', 'autoresize'];

import 'tinymce/plugins/paste';
import 'tinymce/plugins/autoresize';

window.addEventListener('DOMContentLoaded', (event) => {
	// comments update
	const isCommentsUpdate = document.querySelector('.comments.update');

	if (isCommentsUpdate) {
		const commentaires = isCommentsUpdate.querySelectorAll('.commentaire');

		commentaires.forEach((commentaire, index) => {
			const btnUpdate = commentaire.querySelector('.right .actions a.triggerForm');
			const btnBack = commentaire.querySelector('.right form a.triggerForm');
			const form = commentaire.querySelector('form');
			const containerContent = commentaire.querySelector('.content');

			btnUpdate.addEventListener('click', () => {
				commentaires.forEach(item => {
					const form = item.querySelector('form');
					const containerContent = item.querySelector('.content');

					form.classList.add('disabled');
					containerContent.classList.remove('disabled');
					tinymce.remove();
				});

				form.classList.toggle('disabled');
				containerContent.classList.toggle('disabled');

				const commentaireId = form.querySelector('input[name="commentId"]').value;
				const textarea = form.querySelector('#commentaire-' + commentaireId);

				tinymce.init({
					target: textarea,
					plugins: tinyPlugins,
					toolbar: 'undo redo',
					menubar: false,
				});
			});

			btnBack.addEventListener('click', () => {
				const form = commentaire.querySelector('form');
				const containerContent = commentaire.querySelector('.content');
				const commentaireId = form.querySelector('input[name="commentId"]').value;

				form.classList.add('disabled');
				containerContent.classList.remove('disabled');
				tinymce.remove('#commentaire-' + commentaireId);
			})
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
				const btnRemove = commentaire.querySelector('.confirm');
				btnRemove.addEventListener('click', () => {
					if (commentaire.classList.contains('active')) {
						commentaire.classList.add('remove');
						const idCom = encodeURIComponent(commentaire.dataset.id);
						const xhr = new XMLHttpRequest();
						const url = window.location.origin + '/index.php?access=comment&action=remove&id=' + idCom;
						xhr.open('GET', url);
						xhr.send(null);

						xhr.addEventListener('readystatechange', function (e) {
							if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
								setTimeout(() => {
									commentaire.remove();
								}, 300);
							}
						});

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
