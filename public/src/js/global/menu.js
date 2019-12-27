window.addEventListener("DOMContentLoaded", event => {
  const menu = document.querySelector("#menu");
  const barsMenuMobile = document.querySelector('.bars-menu-mobile');

  if (menu) {
    if (barsMenuMobile) {
      const events = ['click', 'touch'];

      events.forEach(event => {
        barsMenuMobile.addEventListener(event, () => {
          document.querySelector('body').classList.toggle('menu-burger-open');

          const clickableItems = menu.querySelectorAll('a');

          if (clickableItems) {
            clickableItems.forEach(item => {
              events.forEach(event => {
                item.addEventListener(event, () => {
                  document.querySelector('body').classList.remove('menu-burger-open');
                })
              })
            })
          }
        })
      })
    }
    window.addEventListener("scroll", () => {
      if (window.scrollY > 30) {
        menu.classList.add("has-scrolled");
        barsMenuMobile.classList.add("has-scrolled");
      } else {
        menu.classList.remove("has-scrolled");
        barsMenuMobile.classList.remove("has-scrolled");
      }
    });
  }
});
