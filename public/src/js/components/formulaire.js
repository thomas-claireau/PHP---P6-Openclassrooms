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

        if (e.target.id == "mail") {
          functions.checkInput(currentInput, regexMail, true);
        } else if (e.target.id == "tel") {
          functions.checkInput(currentInput, regexTel, true);
        } else if (e.target.id == "password") {
          const inputConfirmPassword = document.querySelector('input#confirm-password');
          inputConfirmPassword.value = "";
          inputConfirmPassword.parentNode.parentNode.classList.remove('ok');
        } else if (e.target.id == "confirm-password") {
          const inputPassword = document.querySelector('input#password');
          const regexPassword = RegExp("^" + inputPassword.value + "$");

          functions.checkInput(currentInput, regexPassword, true);
        }
        else {
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
