# Как развернуть проект на Windows в учебном классе

Инструкция рассчитана на обычный компьютер в колледже/шараге с Windows 10/11.

## 1. Что должно быть установлено

Минимально нужно:

- Docker Desktop
- Node.js LTS
- VS Code или любой редактор
- браузер Chrome / Edge

Проверка в PowerShell:

```powershell
node -v
npm -v
docker --version
docker compose version
```

Если Docker не запускается, откройте Docker Desktop вручную и дождитесь статуса `Docker Desktop is running`.

## 2. Куда положить проект

Лучше положить проект в простой путь без русских букв и пробелов:

```text
C:\demo-exam-site
```

Например:

```text
C:\demo-exam-site\index.html
C:\demo-exam-site\docker-compose.yml
C:\demo-exam-site\sql\init.sql
```

## 3. Запуск полной версии с MySQL

Откройте PowerShell в папке проекта:

```powershell
cd C:\demo-exam-site
```

Запустите проект:

```powershell
docker compose up --build
```

Если хотите запустить в фоне:

```powershell
docker compose up -d --build
```

После запуска сайт будет доступен:

```text
http://127.0.0.1:3000
```

Открывать нужно именно этот адрес, а не `index.html` двойным кликом.

## 4. Данные для входа

Демо-пользователь:

```text
Email: demo@example.com
Password: Demo1234
```

На странице входа должен появиться текст:

```text
Режим БД: авторизация идет через Node.js API и MySQL.
```

Если написано про `localStorage`, значит MySQL/API не запущены или сайт открыт неправильно.

## 5. Проверка, что БД работает

В браузере откройте:

```text
http://127.0.0.1:3000/api/health
```

Нормальный ответ:

```json
{"ok":true,"database":"mysql"}
```

Если видите ошибку подключения к MySQL, значит контейнер MySQL еще не поднялся или Docker Desktop не работает.

## 6. Если база уже запускалась раньше

Если меняли SQL или старые данные мешают, полностью сбросьте БД:

```powershell
docker compose down -v
docker compose up --build
```

Флаг `-v` удаляет volume MySQL, после этого `sql/init.sql` выполнится заново.

## 7. Полезные команды

Остановить проект:

```powershell
docker compose down
```

Посмотреть логи:

```powershell
docker compose logs
```

Посмотреть логи только сайта:

```powershell
docker compose logs app
```

Посмотреть логи MySQL:

```powershell
docker compose logs mysql
```

Проверить список контейнеров:

```powershell
docker ps
```

## 8. Типовые ошибки

### Cannot connect to the Docker daemon

Docker Desktop не запущен.

Решение:

1. Откройте Docker Desktop.
2. Дождитесь полной загрузки.
3. Повторите:

```powershell
docker compose up --build
```

### Port 3000 is already allocated

Порт `3000` занят другим приложением.

Решение 1: остановить старые контейнеры:

```powershell
docker compose down
```

Решение 2: поменять порт в `docker-compose.yml`:

```yaml
ports:
  - "3001:3000"
```

После этого сайт будет:

```text
http://127.0.0.1:3001
```

### Port 3306 is already allocated

На компьютере уже запущен MySQL.

Решение: поменять внешний порт MySQL в `docker-compose.yml`:

```yaml
ports:
  - "3307:3306"
```

Внутри Docker приложение все равно подключается к `mysql:3306`, поэтому код менять не нужно.

### На странице входа demo-localStorage вместо MySQL

Причины:

- сайт открыт двойным кликом по `login.html`;
- открыт через Live Server на другом порту;
- Docker Compose не запущен;
- MySQL контейнер еще не готов.

Решение:

```powershell
docker compose up --build
```

И открыть:

```text
http://127.0.0.1:3000/login.html
```

## 9. Если Docker запрещен на компьютере

Можно показать статическую часть проекта:

```powershell
cd C:\demo-exam-site
npm install
npm start
```

Но без запущенного MySQL авторизация через БД работать не будет. Это нормальное ограничение: браузер не подключается к MySQL напрямую, нужен backend API и база.

## 10. Что показать эксперту

1. Главная страница:

```text
http://127.0.0.1:3000
```

2. Каталог:

```text
http://127.0.0.1:3000/catalog.html
```

3. Авторизация:

```text
http://127.0.0.1:3000/login.html
```

4. Личный кабинет:

```text
http://127.0.0.1:3000/profile.html
```

5. Проверка MySQL API:

```text
http://127.0.0.1:3000/api/health
```

## 11. Короткий сценарий запуска на экзамене

```powershell
cd C:\demo-exam-site
docker compose up --build
```

Открыть:

```text
http://127.0.0.1:3000
```

Войти:

```text
demo@example.com
Demo1234
```

Проверить, что на странице входа указан режим MySQL.
