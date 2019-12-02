window.addEventListener("DOMContentLoaded", event => {
  const requestUri = location.pathname + location.search;
  console.log(requestUri);
  const linkTarget = document.querySelector(
    `.admin .sidebar a[href="${requestUri}"]`
  );
  linkTarget.classList.add("active");
});
