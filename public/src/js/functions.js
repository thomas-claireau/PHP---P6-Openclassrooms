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
  },
  checkInput: (input, regex, match) => {
    const parentInput = input.parentNode.parentNode;
    const condition = match
      ? input.value.match(regex)
      : !input.value.match(regex);

    if (input.value.length > 0) {
      input.classList.add("active");
      if (condition) {
        parentInput.classList.remove("error");
        parentInput.classList.add("ok");
      } else {
        parentInput.classList.remove("ok");
        parentInput.classList.add("error");
      }
    } else {
      input.classList.remove("active");
      parentInput.classList.remove("error");
      parentInput.classList.remove("ok");
    }
  }
};
