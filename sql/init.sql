CREATE DATABASE IF NOT EXISTS demo_exam
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE demo_exam;

CREATE TABLE IF NOT EXISTS users (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(80) NOT NULL,
  surname VARCHAR(80) NOT NULL,
  phone VARCHAR(30) NOT NULL,
  email VARCHAR(160) NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('user', 'admin') NOT NULL DEFAULT 'user',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_users_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS products (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  title VARCHAR(160) NOT NULL,
  category VARCHAR(80) NOT NULL,
  description TEXT NOT NULL,
  image VARCHAR(255) NOT NULL,
  price DECIMAL(10, 2) NOT NULL,
  PRIMARY KEY (id),
  KEY idx_products_category (category)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS orders (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id INT UNSIGNED NOT NULL,
  date DATE NOT NULL,
  status VARCHAR(80) NOT NULL,
  PRIMARY KEY (id),
  KEY idx_orders_user_id (user_id),
  CONSTRAINT fk_orders_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS order_items (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  order_id INT UNSIGNED NOT NULL,
  product_id INT UNSIGNED NOT NULL,
  quantity INT UNSIGNED NOT NULL DEFAULT 1,
  PRIMARY KEY (id),
  KEY idx_order_items_order_id (order_id),
  KEY idx_order_items_product_id (product_id),
  CONSTRAINT fk_order_items_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
  CONSTRAINT fk_order_items_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS favorites (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id INT UNSIGNED NOT NULL,
  product_id INT UNSIGNED NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_favorites_user_product (user_id, product_id),
  KEY idx_favorites_product_id (product_id),
  CONSTRAINT fk_favorites_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_favorites_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS requests (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id INT UNSIGNED NULL,
  date DATE NOT NULL,
  status VARCHAR(80) NOT NULL DEFAULT 'Новая',
  message TEXT NOT NULL,
  PRIMARY KEY (id),
  KEY idx_requests_user_id (user_id),
  CONSTRAINT fk_requests_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO users (id, name, surname, phone, email, password_hash, role, created_at)
VALUES
  (1, 'Demo', 'User', '+7 900 000-00-00', 'demo@example.com', 'pbkdf2_sha512$120000$demo_exam_salt$661b25accfd328db094c2d6d77fb9f8ab668dbdeabc225079867537cc1583c6c221c78ca6c080b001b0640e233787242891de7c521aa6c0cb89e52ed13b0e785', 'user', '2026-06-15 09:00:00')
ON DUPLICATE KEY UPDATE email = VALUES(email);

INSERT INTO products (id, title, category, description, image, price)
VALUES
  (1, 'Digital Starter', 'web', 'Адаптивный сайт-визитка с заявкой, контактами и базовой SEO-структурой.', 'images/gallery/project-1.svg', 24900.00),
  (2, 'Commerce Pro', 'commerce', 'Каталог услуг или товаров с фильтрацией, избранным и оформлением заказа.', 'images/gallery/project-2.svg', 49900.00),
  (3, 'Media Pack', 'media', 'Галерея, баннеры, слайдеры и мультимедийные блоки для презентации бренда.', 'images/gallery/project-3.svg', 18900.00),
  (4, 'CRM Landing', 'web', 'Посадочная страница с формами, отзывами, FAQ и аналитической структурой.', 'images/gallery/project-4.svg', 32900.00),
  (5, 'Brand Visual', 'design', 'UI Kit, цветовая система, иконки и шаблоны контентных блоков.', 'images/gallery/project-5.svg', 15900.00),
  (6, 'Support Suite', 'service', 'Поддержка сайта, обновление контента, проверка форм и резервные копии.', 'images/gallery/project-6.svg', 9900.00)
ON DUPLICATE KEY UPDATE title = VALUES(title), category = VALUES(category), description = VALUES(description), image = VALUES(image), price = VALUES(price);

INSERT INTO orders (id, user_id, date, status)
VALUES (101, 1, '2026-06-15', 'В работе')
ON DUPLICATE KEY UPDATE status = VALUES(status);

INSERT INTO order_items (order_id, product_id, quantity)
SELECT 101, 1, 1 WHERE NOT EXISTS (SELECT 1 FROM order_items WHERE order_id = 101 AND product_id = 1);

INSERT INTO order_items (order_id, product_id, quantity)
SELECT 101, 3, 1 WHERE NOT EXISTS (SELECT 1 FROM order_items WHERE order_id = 101 AND product_id = 3);

INSERT INTO requests (id, user_id, date, status, message)
VALUES (201, 1, '2026-06-15', 'Новая', 'Нужен сайт под демонстрационный экзамен')
ON DUPLICATE KEY UPDATE status = VALUES(status), message = VALUES(message);

INSERT INTO favorites (id, user_id, product_id)
VALUES (301, 1, 2)
ON DUPLICATE KEY UPDATE product_id = VALUES(product_id);
