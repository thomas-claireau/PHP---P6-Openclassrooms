document.addEventListener('DOMContentLoaded', (event) => {
    const pageBlog = document.querySelector('body.blog');
    const homeArticles = document.querySelector('#home');

    if (pageBlog || homeArticles) {
        const containerPosts = document.querySelector('.articles');

        if (containerPosts) {
            const nbPosts = containerPosts.dataset.nb;

            if (nbPosts <= 5) {
                containerPosts.style.gridTemplateColumns = 'repeat(' + nbPosts + ',1fr)';

            }

            if (nbPosts <= 3) {
                containerPosts.classList.add('container');
            }

            if (nbPosts <= 2) {
                containerPosts.style.maxWidth = '900px';
            }
        }

    }
});