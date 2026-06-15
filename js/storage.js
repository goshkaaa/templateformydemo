const DEMO_PRODUCTS = [
  { id: 1, title: "Digital Starter", category: "web", description: "Адаптивный сайт-визитка с заявкой, контактами и SEO-структурой.", image: "images/gallery/project-1.svg", price: 24900 },
  { id: 2, title: "Commerce Pro", category: "commerce", description: "Каталог услуг или товаров с фильтрацией, избранным и заказами.", image: "images/gallery/project-2.svg", price: 49900 },
  { id: 3, title: "Media Pack", category: "media", description: "Галерея, баннеры, слайдеры и мультимедийные блоки.", image: "images/gallery/project-3.svg", price: 18900 },
  { id: 4, title: "CRM Landing", category: "web", description: "Посадочная страница с формами, отзывами и FAQ.", image: "images/gallery/project-4.svg", price: 32900 },
  { id: 5, title: "Brand Visual", category: "design", description: "UI Kit, цветовая система, иконки и шаблоны блоков.", image: "images/gallery/project-5.svg", price: 15900 },
  { id: 6, title: "Support Suite", category: "service", description: "Поддержка сайта, обновление контента и проверка форм.", image: "images/gallery/project-6.svg", price: 9900 }
];

const DEFAULT_DB = {
  users: [{ id: 1, name: "Demo", surname: "User", phone: "+7 900 000-00-00", email: "demo@example.com", password: "Demo1234", role: "user", createdAt: "2026-06-15T09:00:00.000Z" }],
  orders: [{ id: 101, userId: 1, date: "2026-06-15", status: "В работе", items: [1, 3] }],
  requests: [{ id: 201, userId: 1, date: "2026-06-15", status: "Новая", message: "Нужен сайт под демонстрационный экзамен" }],
  favorites: [{ id: 301, userId: 1, productId: 2 }]
};

const Storage = {
  key: "demo_exam_db",
  sessionKey: "demo_exam_current_user",
  init() {
    if (!localStorage.getItem(this.key)) localStorage.setItem(this.key, JSON.stringify(DEFAULT_DB));
  },
  getDb() {
    this.init();
    return JSON.parse(localStorage.getItem(this.key));
  },
  saveDb(db) {
    localStorage.setItem(this.key, JSON.stringify(db));
  },
  currentUser() {
    const id = Number(localStorage.getItem(this.sessionKey));
    return this.getDb().users.find((user) => user.id === id) || null;
  },
  setCurrentUser(id) {
    localStorage.setItem(this.sessionKey, String(id));
  },
  logout() {
    localStorage.removeItem(this.sessionKey);
  },
  addRequest(message) {
    const db = this.getDb();
    const user = this.currentUser();
    db.requests.unshift({
      id: Date.now(),
      userId: user ? user.id : null,
      date: new Date().toISOString().slice(0, 10),
      status: "Новая",
      message
    });
    this.saveDb(db);
  },
  toggleFavorite(productId) {
    const db = this.getDb();
    const user = this.currentUser();
    if (!user) return false;
    const index = db.favorites.findIndex((item) => item.userId === user.id && item.productId === productId);
    if (index >= 0) db.favorites.splice(index, 1);
    else db.favorites.push({ id: Date.now(), userId: user.id, productId });
    this.saveDb(db);
    return true;
  }
};

Storage.init();
