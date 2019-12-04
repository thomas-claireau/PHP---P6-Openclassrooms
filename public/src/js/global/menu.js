window.addEventListener("DOMContentLoaded", event => {
  const menu = document.querySelector("#menu");

  if (menu) {
    window.addEventListener("scroll", () => {
      if (window.scrollY > 30) {
        menu.classList.add("has-scrolled");
      } else {
        menu.classList.remove("has-scrolled");
      }
    });
  }
});
