<?php
require_once __DIR__ . '/config.php';

$errors = [];
$values = ['login' => '', 'full_name' => '', 'phone' => '', 'email' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($values as $key => $_) {
        $values[$key] = trim($_POST[$key] ?? '');
    }
    $password = (string) ($_POST['password'] ?? '');

    $validator = new RegistrationValidator(Database::connection());
    $errors = $validator->validate($values, $password);

    if (!$errors) {
        try {
            $stmt = Database::connection()->prepare(
                'INSERT INTO users (login, password_hash, full_name, phone, email) VALUES (?, ?, ?, ?, ?)'
            );
            $stmt->execute([
                $values['login'],
                password_hash($password, PASSWORD_DEFAULT),
                $values['full_name'],
                $values['phone'],
                $values['email'],
            ]);
            $_SESSION['user_id'] = (int) Database::connection()->lastInsertId();
            unset($_SESSION['is_admin']);
            session_regenerate_id(true);
            flash('Регистрация выполнена. Можно оформлять заявку.');
            redirect('profile.php');
        } catch (PDOException $exception) {
            if ($exception->getCode() !== '23000') {
                throw $exception;
            }
            $errors = $validator->validate($values, $password);
            if (!$errors) {
                $errors['login'] = 'Логин или e-mail уже зарегистрирован.';
            }
        }
    }
}

render_header('Регистрация');
?>
<main class="auth-layout">
  <section class="auth-card card">
    <a class="logo" href="index.php"><span class="logo__mark">К</span><span>Конференции.РФ</span></a>
    <h1>Регистрация</h1>
    <form class="list" method="post" novalidate>
      <label class="field"><span>Логин</span><input class="input form-control" name="login" value="<?= e($values['login']) ?>" required><small class="form-error"><?= e($errors['login'] ?? '') ?></small></label>
      <label class="field"><span>Пароль</span><input class="input form-control" type="password" name="password" required><small class="form-error"><?= e($errors['password'] ?? '') ?></small></label>
      <label class="field"><span>ФИО</span><input class="input form-control" name="full_name" value="<?= e($values['full_name']) ?>" required><small class="form-error"><?= e($errors['full_name'] ?? '') ?></small></label>
      <label class="field"><span>Телефон</span><input class="input form-control" name="phone" value="<?= e($values['phone']) ?>" required><small class="form-error"><?= e($errors['phone'] ?? '') ?></small></label>
      <label class="field"><span>E-mail</span><input class="input form-control" type="email" name="email" value="<?= e($values['email']) ?>" required><small class="form-error"><?= e($errors['email'] ?? '') ?></small></label>
      <button class="btn" type="submit">Зарегистрироваться</button>
      <a class="btn btn--ghost" href="login.php">Уже зарегистрированы? Войти</a>
    </form>
  </section>
</main>
<?php render_footer(); ?>
