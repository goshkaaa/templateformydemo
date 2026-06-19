<?php
require_once __DIR__ . '/config.php';
$user = Auth::requireUser();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'review') {
    $bookingId = (int) ($_POST['booking_id'] ?? 0);
    $text = trim($_POST['text'] ?? '');
    $rating = (int) ($_POST['rating'] ?? 0);

    $stmt = Database::connection()->prepare('SELECT id, status FROM bookings WHERE id = ? AND user_id = ?');
    $stmt->execute([$bookingId, $user['id']]);
    $booking = $stmt->fetch();

    if (!$booking || $booking['status'] === 'Новая') {
        flash('Отзыв можно оставить только после изменения статуса администратором.', 'error');
    } elseif ($text === '' || $rating < 1 || $rating > 5) {
        flash('Заполните текст отзыва и оценку от 1 до 5.', 'error');
    } else {
        $stmt = Database::connection()->prepare('INSERT INTO reviews (booking_id, user_id, rating, text) VALUES (?, ?, ?, ?)');
        $stmt->execute([$bookingId, $user['id'], $rating, $text]);
        flash('Отзыв сохранен.');
    }
    redirect('profile.php');
}

$stmt = Database::connection()->prepare(
    'SELECT b.*, r.title AS room_title, r.type AS room_type,
            rv.id AS review_id, rv.rating, rv.text AS review_text
     FROM bookings b
     JOIN rooms r ON r.id = b.room_id
     LEFT JOIN reviews rv ON rv.booking_id = b.id
     WHERE b.user_id = ?
     ORDER BY b.created_at DESC'
);
$stmt->execute([$user['id']]);
$bookings = $stmt->fetchAll();

render_header('Личный кабинет', 'profile');
?>
<main class="section">
  <div class="container">
    <div class="section__head">
      <span class="badge">Личный кабинет</span>
      <h1><?= e($user['full_name']) ?></h1>
      <p class="muted">Логин: <?= e($user['login']) ?> · <?= e($user['phone']) ?> · <?= e($user['email']) ?></p>
    </div>

    <div class="slider card profile-slider" data-slider>
      <div class="slider__track">
        <article class="slide" style="background-image:url('images/gallery/project-1.svg')"><div class="slide__content"><h3>Аудитория</h3><p>Классический формат для докладов.</p></div></article>
        <article class="slide" style="background-image:url('images/gallery/project-2.svg')"><div class="slide__content"><h3>Коворкинг</h3><p>Пространство для команд и секций.</p></div></article>
        <article class="slide" style="background-image:url('images/gallery/project-3.svg')"><div class="slide__content"><h3>Кинозал</h3><p>Экран и удобная посадка участников.</p></div></article>
        <article class="slide" style="background-image:url('images/gallery/project-4.svg')"><div class="slide__content"><h3>Организация</h3><p>Заявка поступает администратору.</p></div></article>
      </div>
      <div class="slider__controls"><button class="icon-btn" data-prev type="button" aria-label="Предыдущий слайд">‹</button><button class="icon-btn" data-next type="button" aria-label="Следующий слайд">›</button></div>
    </div>

    <div class="section__head profile-head">
      <h2>История заявок</h2>
      <a class="btn" href="booking.php">Создать заявку</a>
    </div>

    <div class="list">
      <?php if (!$bookings): ?>
        <article class="card empty-state">Заявок пока нет.</article>
      <?php endif; ?>
      <?php foreach ($bookings as $booking): ?>
        <article class="booking-card card">
          <div>
            <h3><?= e($booking['room_title']) ?></h3>
            <p class="muted"><?= e($booking['room_type']) ?> · <?= e(format_ru_date($booking['event_date'])) ?> · <?= e($booking['payment_method']) ?></p>
          </div>
          <span class="status <?= e(status_class($booking['status'])) ?>"><?= e($booking['status']) ?></span>
          <?php if ($booking['review_id']): ?>
            <div class="review-box wide"><b>Ваш отзыв: <?= (int) $booking['rating'] ?>/5</b><p><?= e($booking['review_text']) ?></p></div>
          <?php elseif ($booking['status'] !== 'Новая'): ?>
            <form class="form-grid wide" method="post">
              <input type="hidden" name="action" value="review">
              <input type="hidden" name="booking_id" value="<?= (int) $booking['id'] ?>">
              <label class="field"><span>Оценка</span><select class="select form-select" name="rating"><option value="5">5</option><option value="4">4</option><option value="3">3</option><option value="2">2</option><option value="1">1</option></select></label>
              <label class="field"><span>Отзыв</span><input class="input form-control" name="text" placeholder="Опишите полученную услугу"></label>
              <button class="btn wide" type="submit">Оставить отзыв</button>
            </form>
          <?php else: ?>
            <p class="muted wide">Отзыв будет доступен после изменения статуса администратором.</p>
          <?php endif; ?>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</main>
<?php render_footer(); ?>
