const path = require("path");

module.exports = (folder, prefix = "") => {
  return `${prefix}/public/${folder}/`;
};
