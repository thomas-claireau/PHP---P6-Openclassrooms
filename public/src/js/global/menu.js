window.addEventListener("DOMContentLoaded", (event) => {
  const menu = document.querySelector("#menu");
  const menuAdmin = document.querySelector('.sidebar');
  const barsMenuMobile = document.querySelector('.bars-menu-mobile');

  const menuItems = menu ? menu : menuAdmin;

  if (barsMenuMobile) {
    const events = ['click', 'touch'];

    events.forEach((event) => {
      barsMenuMobile.addEventListener(event, () => {
        document.querySelector('body').classList.toggle('menu-burger-open');

        const clickableItems = menuItems.querySelectorAll('a');

        if (clickableItems) {
          clickableItems.forEach((item) => {
            events.forEach((event) => {
              item.addEventListener(event, () => {
                document.querySelector('body').classList.remove('menu-burger-open');
              })
            })
          })
        }
      })
    });
  }

  if (menu) {
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
