# Структура базы данных

Проект поддерживает два режима:

- демо-режим через JSON и `localStorage`;
- полноценный режим через MySQL, `sql/init.sql` и Node.js API.

Файл `data/users.json` показывает стартовую JSON-структуру. MySQL-структура описана в `sql/init.sql`.

## User

```json
{
  "id": 1,
  "name": "Demo",
  "surname": "User",
  "phone": "+7 900 000-00-00",
  "email": "demo@example.com",
  "password": "Demo1234",
  "role": "user",
  "createdAt": "2026-06-15T09:00:00.000Z"
}
```

## Product

```json
{
  "id": 1,
  "title": "Digital Starter",
  "category": "web",
  "description": "Адаптивный сайт-визитка",
  "image": "images/gallery/project-1.svg",
  "price": 24900
}
```

## Order

```json
{
  "id": 101,
  "userId": 1,
  "date": "2026-06-15",
  "status": "В работе",
  "items": [1, 3]
}
```

## Favorite

```json
{
  "id": 301,
  "userId": 1,
  "productId": 2
}
```

## Request

Дополнительная структура для формы заявки:

```json
{
  "id": 201,
  "userId": 1,
  "date": "2026-06-15",
  "status": "Новая",
  "message": "Текст заявки"
}
```
