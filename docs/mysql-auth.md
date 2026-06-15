# MySQL-авторизация

Браузер не должен подключаться к MySQL напрямую. Поэтому проект использует схему:

```
HTML/CSS/JS -> Node.js API -> MySQL
```

Backend написан на чистом Node.js без Express. Он раздает статические файлы сайта и обрабатывает маршруты `/api/auth/*`.

## Быстрый запуск через Docker

```bash
docker compose up --build
```

После запуска:

- сайт: `http://127.0.0.1:3000`
- MySQL: `127.0.0.1:3306`
- база: `demo_exam`
- пользователь БД: `demo_user`
- пароль БД: `demo_password`

Демо-пользователь:

- email: `demo@example.com`
- пароль: `Demo1234`

Если до этого контейнеры уже запускались со старой схемой БД, сбросьте volume и поднимите заново:

```bash
docker compose down -v
docker compose up --build
```

## Что создается автоматически

Файл `sql/init.sql` автоматически создает:

- `users`
- `products`
- `orders`
- `order_items`
- `favorites`
- `requests`

Также добавляются демо-пользователь, товары, заказ, заявка и избранное.

## API

- `POST /api/auth/login` — вход.
- `POST /api/auth/register` — регистрация.
- `POST /api/auth/logout` — выход.
- `GET /api/auth/me` — текущий пользователь, заявки, заказы, избранное.
- `PUT /api/profile` — редактирование профиля.
- `PUT /api/profile/password` — смена пароля.

## Безопасность

- Пароли не хранятся открытым текстом.
- Используется `PBKDF2-SHA512`.
- Сессия хранится в `HttpOnly` cookie.
- SQL-запросы выполняются через prepared statements.

## Локальный запуск без Docker

```bash
npm install
npm start
```

Перед запуском нужно поднять MySQL и создать БД через `sql/init.sql`.
