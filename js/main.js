function toast(message, type = "success") {
  let wrap = document.querySelector(".toast-wrap");
  if (!wrap) {
    wrap = document.createElement("div");
    wrap.className = "toast-wrap";
    document.body.appendChild(wrap);
  }
  const item = document.createElement("div");
  item.className = `toast toast--${type}`;
  item.textContent = message;
  wrap.appendChild(item);
  setTimeout(() => item.remove(), 3200);
}

document.addEventListener("DOMContentLoaded", () => {
  const savedTheme = localStorage.getItem("demo_exam_theme") || "light";
  document.documentElement.dataset.theme = savedTheme;

  const burger = document.querySelector("[data-burger]");
  const navLinks = document.querySelector("[data-nav-links]");
  burger?.addEventListener("click", () => {
    const isOpen = navLinks?.classList.toggle("is-open") ?? false;
    burger.setAttribute("aria-expanded", String(isOpen));
  });
  navLinks?.querySelectorAll("a").forEach((link) => link.addEventListener("click", () => {
    navLinks.classList.remove("is-open");
    burger?.setAttribute("aria-expanded", "false");
  }));

  document.querySelector("[data-theme-toggle]")?.addEventListener("click", () => {
    const next = document.documentElement.dataset.theme === "dark" ? "light" : "dark";
    document.documentElement.dataset.theme = next;
    localStorage.setItem("demo_exam_theme", next);
    toast(next === "dark" ? "Темная тема включена" : "Светлая тема включена");
  });

  document.querySelector("[data-lang-toggle]")?.addEventListener("click", () => {
    const next = (localStorage.getItem("demo_exam_lang") || "ru") === "ru" ? "en" : "ru";
    applyLanguage(next);
    toast(next === "ru" ? "Язык: русский" : "Language: English");
  });
  if (window.applyLanguage) applyLanguage(localStorage.getItem("demo_exam_lang") || "ru");

  document.querySelectorAll("[data-modal-open]").forEach((button) => {
    button.addEventListener("click", () => document.querySelector(button.dataset.modalOpen)?.classList.add("is-open"));
  });
  document.querySelectorAll("[data-modal-close]").forEach((button) => {
    button.addEventListener("click", () => button.closest(".modal")?.classList.remove("is-open"));
  });

  document.querySelectorAll("[data-faq]").forEach((button) => {
    button.addEventListener("click", () => button.closest(".faq__item")?.classList.toggle("is-open"));
  });

  document.querySelectorAll("[data-tab]").forEach((button) => {
    button.addEventListener("click", () => {
      document.querySelectorAll("[data-tab]").forEach((btn) => btn.classList.remove("btn--accent"));
      document.querySelectorAll(".tab-panel").forEach((panel) => panel.classList.remove("is-active"));
      button.classList.add("btn--accent");
      document.querySelector(button.dataset.tab)?.classList.add("is-active");
    });
  });

  document.querySelectorAll("form[data-validate]").forEach((form) => {
    form.addEventListener("submit", (event) => {
      event.preventDefault();
      if (!validateForm(form)) return toast("Проверьте поля формы", "error");
      if (form.dataset.requestForm !== undefined) Storage.addRequest(new FormData(form).get("message") || "Заявка с сайта");
      form.reset();
      toast("Форма успешно отправлена");
    });
  });
});
