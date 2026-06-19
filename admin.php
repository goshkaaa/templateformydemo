<?php
require_once __DIR__ . '/config.php';
Auth::requireAdmin();

$statuses = ['Новая', 'Мероприятие назначено', 'Мероприятие завершено'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bookingId = (int) ($_POST['booking_id'] ?? 0);
    $status = (string) ($_POST['status'] ?? '');

    if (in_array($status, $statuses, true)) {
        $stmt = Database::connection()->prepare('UPDATE bookings SET status = ? WHERE id = ?');
        $stmt->execute([$status, $bookingId]);
        flash('Статус заявки обновлен.');
    } else {
        flash('Недопустимый статус заявки.', 'error');
    }
    redirect('admin.php');
}

$filter = (string) ($_GET['status'] ?? '');
$sort = (string) ($_GET['sort'] ?? 'created_at');
$direction = strtoupper((string) ($_GET['direction'] ?? 'DESC')) === 'ASC' ? 'ASC' : 'DESC';
$page = max(1, (int) ($_GET['page'] ?? 1));
$perPage = 5;
$offset = ($page - 1) * $perPage;
$allowedSort = ['created_at' => 'b.created_at', 'event_date' => 'b.event_date', 'status' => 'b.status', 'login' => 'u.login'];
$sortSql = $allowedSort[$sort] ?? $allowedSort['created_at'];

$where = '';
$params = [];
if (in_array($filter, $statuses, true)) {
    $where = 'WHERE b.status = ?';
    $params[] = $filter;
}

$countStmt = Database::connection()->prepare("SELECT COUNT(*) FROM bookings b $where");
$countStmt->execute($params);
$total = (int) $countStmt->fetchColumn();
$pages = max(1, (int) ceil($total / $perPage));

$stmt = Database::connection()->prepare(
    "SELECT b.*, u.login, u.full_name, u.phone, u.email, r.title AS room_title, r.type AS room_type
     FROM bookings b
     JOIN users u ON u.id = b.user_id
     JOIN rooms r ON r.id = b.room_id
     $where
     ORDER BY $sortSql $direction
     LIMIT $perPage OFFSET $offset"
);
$stmt->execute($params);
$bookings = $stmt->fetchAll();

function admin_link(array $extra): string
{
    return 'admin.php?' . http_build_query(array_merge($_GET, $extra));
}

render_header('Панель администратора', 'admin');
?>
<main class="section">
  <div class="container">
    <div class="section__head">
      <span class="badge">Admin26</span>
      <h1>Панель администратора</h1>
      <p class="muted">Просмотр всех заявок, фильтрация, сортировка, постраничная навигация и смена статуса.</p>
    </div>

    <form class="catalog-toolbar" method="get">
      <select class="select form-select" name="status">
        <option value="">Все статусы</option>
        <?php foreach ($statuses as $status): ?>
          <option value="<?= e($status) ?>" <?= $filter === $status ? 'selected' : '' ?>><?= e($status) ?></option>
        <?php endforeach; ?>
      </select>
      <select class="select form-select" name="sort">
        <option value="created_at" <?= $sort === 'created_at' ? 'selected' : '' ?>>По дате создания</option>
        <option value="event_date" <?= $sort === 'event_date' ? 'selected' : '' ?>>По дате конференции</option>
        <option value="status" <?= $sort === 'status' ? 'selected' : '' ?>>По статусу</option>
        <option value="login" <?= $sort === 'login' ? 'selected' : '' ?>>По логину</option>
      </select>
      <select class="select form-select" name="direction">
        <option value="DESC" <?= $direction === 'DESC' ? 'selected' : '' ?>>По убыванию</option>
        <option value="ASC" <?= $direction === 'ASC' ? 'selected' : '' ?>>По возрастанию</option>
      </select>
      <button class="btn" type="submit">Применить</button>
    </form>

    <div class="admin-table table-responsive card">
      <table class="table table-hover align-middle mb-0">
        <thead><tr><th>Пользователь</th><th>Помещение</th><th>Дата</th><th>Оплата</th><th>Статус</th><th>Действие</th></tr></thead>
        <tbody>
          <?php foreach ($bookings as $booking): ?>
            <tr>
              <td><b><?= e($booking['login']) ?></b><br><span class="muted"><?= e($booking['full_name']) ?><br><?= e($booking['phone']) ?><br><?= e($booking['email']) ?></span></td>
              <td><?= e($booking['room_title']) ?><br><span class="muted"><?= e($booking['room_type']) ?></span></td>
              <td><?= e(format_ru_date($booking['event_date'])) ?></td>
              <td><?= e($booking['payment_method']) ?></td>
              <td><span class="status <?= e(status_class($booking['status'])) ?>"><?= e($booking['status']) ?></span></td>
              <td>
                <form class="admin-action" method="post">
                  <input type="hidden" name="booking_id" value="<?= (int) $booking['id'] ?>">
                  <select class="select form-select" name="status">
                    <?php foreach ($statuses as $status): ?>
                      <option value="<?= e($status) ?>" <?= $booking['status'] === $status ? 'selected' : '' ?>><?= e($status) ?></option>
                    <?php endforeach; ?>
                  </select>
                  <button class="btn" type="submit">Сохранить</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
          <?php if (!$bookings): ?>
            <tr><td colspan="6">Заявок по выбранным условиям нет.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <div class="pagination">
      <?php for ($i = 1; $i <= $pages; $i++): ?>
        <a class="btn <?= $i === $page ? '' : 'btn--ghost' ?>" href="<?= e(admin_link(['page' => $i])) ?>"><?= $i ?></a>
      <?php endfor; ?>
    </div>
  </div>
</main>
<?php render_footer(); ?>
