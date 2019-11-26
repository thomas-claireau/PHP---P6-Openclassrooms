window.addEventListener("DOMContentLoaded", event => {
  const formulaire = document.querySelector(".formulaire");

  if (formulaire) {
    const inputs = formulaire.querySelectorAll("input");
    console.log(inputs);

    inputs.forEach(input => {
      input.addEventListener("keypress", e => {
        // code here
      });
    });
  }
});
