// Header darkening after hero scroll
document.addEventListener("scroll", () => {
  const header = document.querySelector(".header");
  const hero = document.querySelector(".hero");

  if (window.scrollY > hero.offsetHeight - 80) {
    header.classList.add("scrolled");
  } else {
    header.classList.remove("scrolled");
  }
});
