<?php
require_once __DIR__ . '/config.php';
render_header('Главная', 'home');
?>
<main>
  <section class="hero conference-hero">
    <div class="container hero__grid">
      <div>
        <span class="badge">Всероссийские конференции</span>
        <h1>Бронирование помещений для конференций</h1>
        <p class="hero__text">Выберите аудиторию, коворкинг или кинозал, укажите дату начала конференции и отправьте заявку администратору на согласование.</p>
        <div class="hero__actions">
          <a class="btn" href="booking.php">Оформить заявку</a>
          <a class="btn btn--ghost" href="register.php">Зарегистрироваться</a>
        </div>
      </div>
      <aside class="hero__panel card" aria-label="Возможности портала">
        <div class="panel-row"><span>Регистрация</span><b>логин + пароль</b></div>
        <div class="panel-row"><span>Заявки</span><b>MySQL</b></div>
        <div class="panel-row"><span>Статусы</span><b>администратор</b></div>
        <div class="panel-meter"><span style="width: 100%"></span></div>
        <p class="muted">Личный кабинет показывает историю заявок и открывает отзывы после обработки заявки администратором.</p>
      </aside>
    </div>
  </section>

  <section class="metric-strip">
    <div class="container metric-strip__grid">
      <div><b>3</b><span>типа помещений</span></div>
      <div><b>3</b><span>статуса заявки</span></div>
      <div><b>4</b><span>слайда в кабинете</span></div>
      <div><b>390×844</b><span>мобильный экран</span></div>
    </div>
  </section>

  <section class="section showcase-section">
    <div class="container">
      <div class="section__head showcase-head">
        <div>
          <span class="badge">Пространства</span>
          <h2>Выберите формат конференции</h2>
        </div>
        <p class="muted">Четыре сценария для докладов, командной работы, презентаций и организации события.</p>
      </div>
      <div class="slider card showcase-slider" data-slider>
        <div class="slider__track">
          <article class="slide" style="background-image:url('images/gallery/project-1.svg')"><div class="slide__content"><h3>Аудитория</h3><p>Классический формат для докладов и тематических секций.</p></div></article>
          <article class="slide" style="background-image:url('images/gallery/project-2.svg')"><div class="slide__content"><h3>Коворкинг</h3><p>Гибкое пространство для команд, дискуссий и круглых столов.</p></div></article>
          <article class="slide" style="background-image:url('images/gallery/project-3.svg')"><div class="slide__content"><h3>Кинозал</h3><p>Большой экран и удобная посадка для масштабных презентаций.</p></div></article>
          <article class="slide" style="background-image:url('images/gallery/project-4.svg')"><div class="slide__content"><h3>Организация</h3><p>Заявка поступает администратору и проходит согласование.</p></div></article>
        </div>
        <div class="slider__controls">
          <button class="icon-btn" data-prev type="button" aria-label="Предыдущий слайд">‹</button>
          <button class="icon-btn" data-next type="button" aria-label="Следующий слайд">›</button>
        </div>
      </div>
    </div>
  </section>

  <section class="section section--alt">
    <div class="container">
      <div class="section__head">
        <h2>Помещения</h2>
        <p class="muted">Пользователь выбирает вариант из выпадающего списка при оформлении заявки.</p>
      </div>
      <div class="grid-3">
        <article class="feature card"><div class="feature__icon">01</div><h3>Аудитория</h3><p class="muted">Формат для докладов, секций и учебных выступлений.</p></article>
        <article class="feature card"><div class="feature__icon">02</div><h3>Коворкинг</h3><p class="muted">Гибкое пространство для командной работы и встреч.</p></article>
        <article class="feature card"><div class="feature__icon">03</div><h3>Кинозал</h3><p class="muted">Большой экран, посадочные места и презентационное оборудование.</p></article>
      </div>
    </div>
  </section>
</main>
<?php render_footer(); ?>
