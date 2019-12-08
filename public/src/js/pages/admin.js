import './admin/account';
import './admin/comments';
import './admin/posts';

// charge les skins tinymce
require.context(
	'!file-loader?name=[path][name].[ext]&context=node_modules/tinymce&outputPath=js!tinymce/skins',
	true,
	/.*/
);

window.addEventListener('DOMContentLoaded', (event) => {
	const requestUri = location.pathname + location.search;
	const linkTarget = document.querySelector(`.admin .sidebar a[href="${requestUri}"]`);
	if (linkTarget) {
		linkTarget.classList.add('active');
	}
});
