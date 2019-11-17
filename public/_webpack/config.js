module.exports = {
  context: "src",
  entry: {
    styles: "./scss/main.scss",
    scripts: "./js/main.js"
  },
  devtool: "cheap-module-eval-source-map",
  outputFolder: "./dist",
  publicFolder: "src",
  proxyTarget: "http://recette.thomas-claireau.fr/", // renseignez ici l'url de dev (recette)
  watch: ["../**/*.php"]
};
