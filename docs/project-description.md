# DemoSite: описание проекта

## Архитектура проекта

```
project/
├── index.html
├── login.html
├── register.html
├── profile.html
├── catalog.html
├── contacts.html
├── 404.html
├── assets/
├── css/
│   ├── style.css
│   ├── variables.css
│   ├── components.css
│   └── responsive.css
├── js/
│   ├── main.js
│   ├── slider.js
│   ├── auth.js
│   ├── validation.js
│   ├── language.js
│   ├── storage.js
│   ├── api.js
│   └── catalog.js
├── data/
│   ├── products.json
│   └── users.json
├── images/
│   ├── hero/
│   ├── gallery/
│   └── icons/
├── docs/
│   ├── database-structure.md
│   ├── mysql-auth.md
│   └── project-description.md
├── server/
│   └── server.js
├── sql/
│   └── init.sql
├── docker-compose.yml
├── package.json
└── .env.example
```

## Sitemap

- `/index.html` — главная: Hero, преимущества, услуги, галерея, отзывы, FAQ, контакты, форма заявки.
- `/catalog.html` — каталог с поиском, фильтрацией и избранным.
- `/contacts.html` — контакты и форма обратной связи.
- `/login.html` — авторизация.
- `/register.html` — регистрация.
- `/profile.html` — защищенный личный кабинет.
- `/404.html` — страница ошибки.

## Backend и MySQL

- `server/server.js` — Node.js API без Express для авторизации и профиля.
- `sql/init.sql` — SQL-код создания и автозаполнения MySQL.
- `docker-compose.yml` — автоматический запуск MySQL и приложения.
- `docs/mysql-auth.md` — инструкция по запуску и API.

## Цветовая палитра

- Primary: `#0f766e`
- Primary light: `#14b8a6`
- Accent: `#f59e0b`
- Text: `#17202a`
- Muted: `#64748b`
- Light background: `#f6f8fb`
- Dark background: `#101820`
- Surface: `#ffffff`

Все цвета вынесены в `css/variables.css`, поэтому тему можно быстро изменить под новое задание.

## UI Kit

- Кнопки: `.btn`, `.btn--ghost`, `.btn--accent`, `.btn--danger`
- Карточки: `.card`
- Поля формы: `.input`, `.select`, `.textarea`, `.field`
- Бейджи: `.badge`
- Сетки: `.grid-3`, `.split`, `.catalog-grid`, `.form-grid`
- Интерактив: `.modal`, `.toast`, `.faq`, `.slider`, `.tabs`

## Реализованный JavaScript

- Бургер-меню.
- Плавная прокрутка через `scroll-behavior`.
- Слайдер.
- Модальные окна.
- Табы.
- FAQ-аккордеон.
- Валидация форм.
- Авторизация и регистрация через MySQL API с fallback на `localStorage`.
- Защита личного кабинета.
- Переключение RU/EN.
- Поиск и фильтрация каталога.
- Toast-уведомления.
- Темная и светлая тема.

## Как адаптировать под новое ТЗ

1. Заменить тексты в HTML.
2. Заменить товары или услуги в `data/products.json` и `js/storage.js`.
3. Изменить палитру в `css/variables.css`.
4. Заменить изображения в `images/hero/` и `images/gallery/`.
5. При необходимости добавить поля формы и правила в `js/validation.js`.
