import '../scss/main.scss';

import './global/menu';
import functions from './functions';

import './components/formulaire';

import './pages/admin';
import './pages/blog';

window.addEventListener('DOMContentLoaded', (event) => {
	functions.injectSvg();

	// smooth-scrool sur les ancres (vers #target)
	const pageContainers = $('html, body');

	$('.js-smooth-scroll').click(function (e) {
		e.preventDefault();
		const target = $(this).attr('href');
		if (!$(target).length) return;

		pageContainers.animate(
			{
				scrollTop: $(target).offset().top - 50,
			},
			400
		);
	});
});

// footer toujours en bas (sauf page formulaire)
document.onreadystatechange = function () {
	if (document.readyState == 'complete') {
		const pageContact = document.querySelector('body.contact');
		const pagesLog = document.querySelector('#log');
		const footer = document.querySelector('footer');

		if (footer && !pageContact && !pagesLog) {
			const heightFooter = Number(footer.getBoundingClientRect().height);

			const contentPage = document.querySelector('main');
			contentPage.style.minHeight = 'calc(100vh - ' + heightFooter + 'px)';
		}
	}
};
