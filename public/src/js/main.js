import "../scss/main.scss";

import "./global/menu";
import functions from "./functions";

window.addEventListener("DOMContentLoaded", event => {
  functions.injectSvg();

  // smooth-scrool sur les ancres (vers #target)
  const pageContainers = $("html, body");
  const headerH = $("header").outerHeight();

  $(".js-smooth-scroll").click(function(e) {
    e.preventDefault();
    const target = $(this).attr("href");
    if (!$(target).length) return;
    const offset = parseInt($(this).data("offset")) || headerH;
    pageContainers.animate(
      {
        scrollTop: $(target).offset().top - 50
      },
      400
    );
  });
});

// footer toujours en bas
document.onreadystatechange = function() {
  if (document.readyState == "complete") {
    const footer = document.querySelector("footer");

    if (footer) {
      const heightFooter = Number(footer.getBoundingClientRect().height);

      const contentPage = document.querySelector("main");
      contentPage.style.minHeight = "calc(100vh - " + heightFooter + "px)";
    }
  }
};
