<?php
declare(strict_types=1);

session_start();

const DB_HOST = '127.0.0.1';
const DB_NAME = 'conferences_rf';
const DB_USER = 'root';
const DB_PASS = '';

const ADMIN_LOGIN = 'Admin26';
const ADMIN_PASSWORD = 'Demo20';

require_once __DIR__ . '/src/Database.php';
require_once __DIR__ . '/src/Auth.php';
require_once __DIR__ . '/src/RegistrationValidator.php';

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function redirect(string $path): never
{
    header('Location: ' . $path);
    exit;
}

function flash(?string $message = null, string $type = 'success'): ?array
{
    if ($message !== null) {
        $_SESSION['flash'] = ['message' => $message, 'type' => $type];
        return null;
    }

    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);

    return $flash;
}

function parse_ru_date(string $value): ?string
{
    $date = DateTime::createFromFormat('d.m.Y', trim($value));
    $errors = DateTime::getLastErrors();

    if (!$date || ($errors && ($errors['warning_count'] || $errors['error_count']))) {
        return null;
    }

    return $date->format('Y-m-d');
}

function format_ru_date(string $value): string
{
    return date('d.m.Y', strtotime($value));
}

function status_class(string $status): string
{
    return match ($status) {
        'Мероприятие назначено' => 'status--planned',
        'Мероприятие завершено' => 'status--done',
        default => 'status--new',
    };
}

function render_header(string $title, string $active = ''): void
{
    $user = Auth::currentUser();
    ?>
<!doctype html>
<html lang="ru" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($title) ?> | Конференции.РФ</title>
  <link rel="stylesheet" href="css/vendor/bootstrap.min.css">
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/responsive.css">
</head>
<body>
  <header class="header">
    <nav class="nav container" aria-label="Главная навигация">
      <a class="logo" href="index.php"><span class="logo__mark">К</span><span>Конференции.РФ</span></a>
      <div class="nav__links" data-nav-links>
        <a class="<?= $active === 'home' ? 'is-active' : '' ?>" href="index.php">Главная</a>
        <a class="<?= $active === 'booking' ? 'is-active' : '' ?>" href="booking.php">Заявка</a>
        <a class="<?= $active === 'profile' ? 'is-active' : '' ?>" href="profile.php">Кабинет</a>
        <a class="<?= $active === 'admin' ? 'is-active' : '' ?>" href="admin.php">Админка</a>
      </div>
      <div class="nav__actions">
        <button class="icon-btn" data-theme-toggle type="button" aria-label="Переключить тему">◐</button>
        <?php if ($user || Auth::isAdmin()): ?>
          <a class="btn btn--ghost" href="logout.php">Выйти</a>
        <?php else: ?>
          <a class="btn btn--ghost" href="login.php">Войти</a>
        <?php endif; ?>
        <button class="icon-btn burger" data-burger type="button" aria-label="Меню" aria-expanded="false">☰</button>
      </div>
    </nav>
  </header>
  <?php $flash = flash(); if ($flash): ?>
    <div class="toast-wrap"><div class="toast toast--<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div></div>
  <?php endif; ?>
    <?php
}

function render_footer(): void
{
    ?>
  <footer class="footer">
    <div class="container footer__grid">
      <div>
        <a class="logo" href="index.php"><span class="logo__mark">К</span><span>Конференции.РФ</span></a>
        <p class="muted">Бронирование аудиторий, коворкингов и кинозалов для всероссийских конференций.</p>
      </div>
      <div><b>Разделы</b><a href="booking.php">Оформить заявку</a><a href="profile.php">Личный кабинет</a></div>
      <div><b>Аккаунт</b><a href="login.php">Вход</a><a href="register.php">Регистрация</a></div>
      <div><b>Администратор</b><a href="admin.php">Панель управления</a><span class="muted">Admin26 / Demo20</span></div>
    </div>
  </footer>
  <script src="js/main.js"></script>
  <script src="js/slider.js"></script>
</body>
</html>
    <?php
}
