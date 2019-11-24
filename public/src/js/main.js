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
        scrollTop: $(target).offset().top - offset
      },
      400
    );
  });
});
