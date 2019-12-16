import './admin/account';
import './admin/comments';
import './admin/posts';

// charge les skins tinymce
require.context(
	'!file-loader?name=[path][name].[ext]&context=node_modules/tinymce&outputPath=js!tinymce/skins',
	true,
	/.*/
);

document.addEventListener('DOMContentLoaded', (event) => {
	const requestUri = location.pathname + location.search;
	const linkTarget = document.querySelector(`.admin .sidebar a[href="${requestUri}"]`);
	if (linkTarget) {
		linkTarget.classList.add('active');
	}

	document.onreadystatechange = function() {
		if (document.readyState == 'complete') {
			// scroll sidebar
			const sidebar = document.querySelector('.admin .sidebar ul');
			const windowHeight = window.innerHeight;

			if (sidebar) {
				if (sidebar.scrollHeight > windowHeight - 100) {
					sidebar.parentNode.classList.add('has-scrolled');
				}
			}
		}
	};
});
