import functions from "../functions";

window.addEventListener("DOMContentLoaded", event => {
  const formulaire = document.querySelector(".formulaire");

  if (formulaire) {
    const inputs = formulaire.querySelectorAll(".input:not(textarea)");
    const inputSubmit = document.querySelector('input[type="submit"]');
    const regexTel = RegExp(/^((\+)33|0)[1-9](\d{2}){4}$/);
    const regexMail = RegExp(/^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,3}$/);
    const regexText = RegExp(
      /^(\b)(on\S+)(\s*)=|javascript|(<\s*)(\/*)script$/
    );

    inputs.forEach(input => {
      input.addEventListener("input", e => {
        const currentInput = e.currentTarget;

        if (e.target.id == "email") {
          functions.checkInput(currentInput, regexMail, true);
        } else if (e.target.id == "tel") {
          functions.checkInput(currentInput, regexTel, true);
        } else {
          functions.checkInput(currentInput, regexText, false);
        }
      });
    });

    inputSubmit.addEventListener("click", e => {
      inputs.forEach(input => {
        const parentInput = input.parentNode.parentNode;

        if (parentInput.classList.contains("error")) {
          e.preventDefault();
          inputSubmit.classList.add("not-send");
        }
      });
    });
  }
});
