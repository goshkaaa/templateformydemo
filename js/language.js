const I18N = {
  ru: {
    nav_home: "Главная", nav_catalog: "Каталог", nav_contacts: "Контакты", nav_profile: "Кабинет",
    hero_title: "Готовый каркас сайта для демоэкзамена",
    hero_text: "Адаптивный коммерческий сайт с каталогом, формами, авторизацией, личным кабинетом и понятной архитектурой.",
    cta_catalog: "Открыть каталог", cta_request: "Оставить заявку"
  },
  en: {
    nav_home: "Home", nav_catalog: "Catalog", nav_contacts: "Contacts", nav_profile: "Profile",
    hero_title: "Ready website scaffold for the demo exam",
    hero_text: "Responsive commercial website with catalog, forms, authentication, profile area and clean architecture.",
    cta_catalog: "Open catalog", cta_request: "Send request"
  }
};

function applyLanguage(lang) {
  localStorage.setItem("demo_exam_lang", lang);
  document.documentElement.lang = lang;
  document.querySelectorAll("[data-i18n]").forEach((node) => {
    const key = node.dataset.i18n;
    if (I18N[lang] && I18N[lang][key]) node.textContent = I18N[lang][key];
  });
}
