import svgInjector from 'svg-injector';

export default {
	// afficher les svg une fois la page chargée
	injectSvg: () => {
		const svgPromise = new Promise((resolve, reject) => {
			const svgs = document.querySelectorAll('img.js-inject-me');
			svgInjector(svgs, {}, (totalSVGsInjected) => resolve(totalSVGsInjected));
		});

		svgPromise.then((tsi) => {
			const svgs = document.querySelectorAll('.js-inject-me');
			svgs.forEach((svg) => {
				svg.classList.add('activeSvg');
			});
		});
	},
	checkInput: (input, regex, match) => {
		const parentInput = input.parentNode.parentNode;
		const condition = match ? input.value.match(regex) : !input.value.match(regex);

		if (input.value.length > 0) {
			input.classList.add('active');
			if (condition) {
				parentInput.classList.remove('error');
				parentInput.classList.add('ok');
			} else {
				parentInput.classList.remove('ok');
				parentInput.classList.add('error');
			}
		} else {
			input.classList.remove('active');
			parentInput.classList.remove('error');
			parentInput.classList.remove('ok');
		}
	},

	loader: (commentInteraction = false) => {
		const loader = document.querySelector('.LoaderBalls');
		loader.classList.add('active');

		if (commentInteraction) {
			loader.classList.add('comment');
			setTimeout(function () {
				loader.classList.remove('active');
			}, 1500);
		}
	},
	$_GET: (param) => {
		const vars = {};
		window.location.href.replace(location.hash, '').replace(
			/[?&]+([^=&]+)=?([^&]*)?/gi, // regexp
			function (m, key, value) { // callback
				vars[key] = value ? value : '';
			}
		);

		if (param) {
			return vars[param] ? vars[param] : null;
		}
		return vars;
	},
	enableScrollSidebar: () => {
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
