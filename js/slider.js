document.addEventListener("DOMContentLoaded", () => {
  const slider = document.querySelector("[data-slider]");
  if (!slider) return;
  const track = slider.querySelector(".slider__track");
  const slides = [...slider.querySelectorAll(".slide")];
  let index = 0;
  const update = () => { track.style.transform = `translateX(-${index * 100}%)`; };
  slider.querySelector("[data-next]")?.addEventListener("click", () => {
    index = (index + 1) % slides.length;
    update();
  });
  slider.querySelector("[data-prev]")?.addEventListener("click", () => {
    index = (index - 1 + slides.length) % slides.length;
    update();
  });
  setInterval(() => {
    index = (index + 1) % slides.length;
    update();
  }, 3000);
});
