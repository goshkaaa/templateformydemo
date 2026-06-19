<?php
require_once __DIR__ . '/config.php';
$user = Auth::requireUser();
$errors = [];
$values = ['room_id' => '', 'event_date' => '', 'payment_method' => ''];

$rooms = Database::connection()->query('SELECT id, title, type, capacity FROM rooms ORDER BY id')->fetchAll();
$payments = ['Банковская карта', 'Счет для организации', 'Наличные при посещении'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $values['room_id'] = trim($_POST['room_id'] ?? '');
    $values['event_date'] = trim($_POST['event_date'] ?? '');
    $values['payment_method'] = trim($_POST['payment_method'] ?? '');
    $date = parse_ru_date($values['event_date']);

    if (!in_array((int) $values['room_id'], array_column($rooms, 'id'), true)) {
        $errors['room_id'] = 'Выберите помещение из списка.';
    }
    if (!$date) {
        $errors['event_date'] = 'Введите дату в формате ДД.ММ.ГГГГ.';
    }
    if (!in_array($values['payment_method'], $payments, true)) {
        $errors['payment_method'] = 'Выберите способ оплаты.';
    }

    if (!$errors) {
        $stmt = Database::connection()->prepare('INSERT INTO bookings (user_id, room_id, event_date, payment_method, status) VALUES (?, ?, ?, ?, "Новая")');
        $stmt->execute([$user['id'], $values['room_id'], $date, $values['payment_method']]);
        flash('Заявка отправлена администратору на согласование.');
        redirect('profile.php');
    }
}

render_header('Оформление заявки', 'booking');
?>
<main class="section">
  <div class="container split">
    <div class="section__head">
      <span class="badge">Новая заявка</span>
      <h1>Оформление бронирования</h1>
      <p class="muted">Заполните все поля. После отправки заявка получит статус «Новая» и появится в личном кабинете.</p>
    </div>
    <form class="card form-grid" method="post" novalidate>
      <label class="field wide"><span>Помещение</span>
        <select class="select form-select" name="room_id" required>
          <option value="">Выберите помещение</option>
          <?php foreach ($rooms as $room): ?>
            <option value="<?= (int) $room['id'] ?>" <?= (int) $values['room_id'] === (int) $room['id'] ? 'selected' : '' ?>>
              <?= e($room['title']) ?>, <?= e($room['type']) ?>, до <?= (int) $room['capacity'] ?> мест
            </option>
          <?php endforeach; ?>
        </select>
        <small class="form-error"><?= e($errors['room_id'] ?? '') ?></small>
      </label>
      <label class="field"><span>Дата начала</span><input class="input form-control" name="event_date" placeholder="ДД.ММ.ГГГГ" value="<?= e($values['event_date']) ?>" required><small class="form-error"><?= e($errors['event_date'] ?? '') ?></small></label>
      <label class="field"><span>Способ оплаты</span>
        <select class="select form-select" name="payment_method" required>
          <option value="">Выберите способ</option>
          <?php foreach ($payments as $payment): ?>
            <option value="<?= e($payment) ?>" <?= $values['payment_method'] === $payment ? 'selected' : '' ?>><?= e($payment) ?></option>
          <?php endforeach; ?>
        </select>
        <small class="form-error"><?= e($errors['payment_method'] ?? '') ?></small>
      </label>
      <button class="btn wide" type="submit">Отправить заявку</button>
    </form>
  </div>
</main>
<?php render_footer(); ?>
