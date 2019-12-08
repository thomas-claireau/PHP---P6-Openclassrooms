window.addEventListener('DOMContentLoaded', (event) => {
	// account view
	const isAccountView = document.querySelector('.admin .account.view');

	if (isAccountView) {
		const form = isAccountView.querySelector('form');
		const containerAction = isAccountView.querySelector('.actions');

		if (containerAction) {
			const trigerForm = containerAction.querySelector('.triggerForm');

			trigerForm.addEventListener('click', () => {
				if (form.classList.contains('disabled')) {
					trigerForm.textContent = 'Annuler';
					trigerForm.classList.remove('vertFonce');
					trigerForm.classList.add('rougeFonce');
				} else {
					trigerForm.textContent = 'Mettre à jour';
					trigerForm.classList.remove('rougeFonce');
					trigerForm.classList.add('vertFonce');
				}
				form.classList.toggle('disabled');
			});
		}
	}
});
