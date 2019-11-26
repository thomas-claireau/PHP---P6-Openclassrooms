window.addEventListener("DOMContentLoaded", event => {
  const formulaire = document.querySelector(".formulaire");

  if (formulaire) {
    const inputs = formulaire.querySelectorAll(".input");

    inputs.forEach(input => {
      input.addEventListener("input", e => {
        if (input.value.length > 0) {
          input.classList.add("active");
        } else {
          input.classList.remove("active");
        }
      });
    });
  }
});
