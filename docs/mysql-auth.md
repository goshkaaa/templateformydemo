# MySQL и PHP

Проект работает по схеме:

```text
PHP-страницы -> PDO -> MySQL
```

Для экзаменационного запуска используйте OpenServer и phpMyAdmin.

## Импорт базы

1. Откройте phpMyAdmin.
2. Импортируйте файл `sql/init.sql`.
3. Проверьте, что появилась база `conferences_rf`.

При повторном импорте файл полностью пересоздает базу `conferences_rf`, чтобы старые таблицы и несовместимые внешние ключи не мешали установке. Все существующие данные в этой базе при этом удаляются.

Файл создает таблицы:

- `users`;
- `rooms`;
- `bookings`;
- `reviews`.

## Подключение

Настройки находятся в `config.php`:

```php
const DB_HOST = '127.0.0.1';
const DB_PORT = 3306;
const DB_SOCKET = '/tmp/mysql.sock';
const DB_NAME = 'conferences_rf';
const DB_USER = 'root';
const DB_PASS = '';
```

Для стандартного OpenServer эти значения обычно подходят без изменений.

На macOS приложение использует локальный Unix-сокет, если существует `/tmp/mysql.sock`. На Windows автоматически используется `DB_HOST` и `DB_PORT`.

## Безопасность

- Пользовательские пароли хранятся как хеши `password_hash`.
- Проверка пароля выполняется через `password_verify`.
- SQL-запросы используют PDO prepared statements.
- Администратор защищен логином `Admin26` и паролем `Demo20`, как требуется в задании.
