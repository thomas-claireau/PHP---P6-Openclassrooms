import SVGInjector from "svg-injector";

export default {
  // afficher les svg une fois la page chargée
  injectSvg: () => {
    const svgPromise = new Promise((resolve, reject) => {
      const svgs = document.querySelectorAll("img.js-inject-me");
      SVGInjector(svgs, {}, totalSVGsInjected => resolve(totalSVGsInjected));
    });

    svgPromise.then(tsi => {
      const svgs = document.querySelectorAll(".js-inject-me");
      svgs.forEach(svg => {
        svg.classList.add("activeSvg");
      });
    });
  }
};
