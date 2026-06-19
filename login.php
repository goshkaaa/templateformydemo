<?php
require_once __DIR__ . '/config.php';

$error = '';
$login = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login'] ?? '');
    $password = (string) ($_POST['password'] ?? '');

    $role = Auth::attempt($login, $password);

    if ($role === 'admin') {
        flash('Вход администратора выполнен.');
        redirect('admin.php');
    }

    if ($role === null) {
        $error = 'Неверный логин или пароль.';
    } else {
        flash('Вы вошли в личный кабинет.');
        redirect('profile.php');
    }
}

render_header('Вход');
?>
<main class="auth-layout">
  <section class="auth-card card">
    <a class="logo" href="index.php"><span class="logo__mark">К</span><span>Конференции.РФ</span></a>
    <h1>Вход</h1>
    <p class="muted">Администратор: Admin26 / Demo20</p>
    <?php if ($error): ?><div class="alert alert--error"><?= e($error) ?></div><?php endif; ?>
    <form class="list" method="post" novalidate>
      <label class="field"><span>Логин</span><input class="input form-control" name="login" value="<?= e($login) ?>" required><small class="form-error"></small></label>
      <label class="field"><span>Пароль</span><input class="input form-control" type="password" name="password" required><small class="form-error"></small></label>
      <button class="btn" type="submit">Войти</button>
      <a class="btn btn--ghost" href="register.php">Еще не зарегистрированы? Регистрация</a>
    </form>
  </section>
</main>
<?php render_footer(); ?>
