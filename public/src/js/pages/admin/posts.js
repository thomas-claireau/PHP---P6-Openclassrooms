import tinymce from 'tinymce/tinymce';
import 'tinymce/themes/silver';

import functions from '../../functions';

const tinyPlugins = ['paste', 'link', 'autoresize', 'image', 'imagetools'];
const tinyPluginsLimited = ['paste', 'autoresize'];

import 'tinymce/plugins/paste';
import 'tinymce/plugins/link';
import 'tinymce/plugins/autoresize';
import 'tinymce/plugins/image';
import 'tinymce/plugins/imagetools';

window.addEventListener('DOMContentLoaded', (event) => {
	const isPostUpdate = document.querySelector('.posts.update');
	const isPostCreate = document.querySelector('.posts.create');
	const isPostRemove = document.querySelector('.posts.remove');
	const postDetail = document.querySelector('#post');

	if (postDetail) {
		const textareaComment = postDetail.querySelector('#commentaire');

		tinymce.init({
			target: textareaComment,
			plugins: tinyPluginsLimited,
			toolbar: 'undo redo',
			menubar: false,
		});
	}

	if (isPostUpdate || isPostCreate) {
		const textarea = document.querySelector('#editor');
		const textareaDesc = document.querySelector('#description');
		tinymce.init({
			target: textareaDesc,
			plugins: tinyPluginsLimited,
			toolbar: 'undo redo',
			menubar: false,
		});
		tinymce.init({
			target: textarea,
			plugins: tinyPlugins,
			toolbar:
				'undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image',
			menubar: false,
			max_chars: "10",
			images_upload_handler: (blobInfo, success, failure) => {
				const xhr = new XMLHttpRequest();
				xhr.withCredentials = false;
				const url = `${window.location.origin}/?access=post&action=uploadImage&type=uploadTiny&id=${functions.$_GET('id')}`;
				xhr.open('POST', url);

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

	if (isPostRemove) {
		const containerArticles = isPostRemove.querySelector('.articles');

		if (containerArticles) {
			const articles = containerArticles.querySelectorAll('.article .content');

			articles.forEach(article => {
				article.addEventListener('mouseenter', () => {
					article.classList.add('hover');
				});

				article.addEventListener('mouseout', (e) => {
					if (e.toElement.classList.contains('articles') || e.toElement.classList.contains('admin') || e.toElement.classList.contains('liste-articles')) {
						article.classList.remove('hover');
						article.classList.remove('active');
					}
				});

				article.addEventListener('click', (e) => {
					e.preventDefault();

					if (article.classList.contains('hover')) {
						article.classList.add('active');
						const buttonRemove = article.querySelector('button');

						buttonRemove.addEventListener('click', () => {
							const idPost = article.parentNode.dataset.id;
							const origin = window.location.origin;
							window.location.href = origin + '/index.php?access=post&action=remove&id=' + idPost;
						})
					}
				});
			})
		}
	}

});
