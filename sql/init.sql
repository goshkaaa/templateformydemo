DROP DATABASE IF EXISTS conferences_rf;

CREATE DATABASE conferences_rf
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE conferences_rf;

CREATE TABLE users (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  login VARCHAR(40) NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  full_name VARCHAR(160) NOT NULL,
  phone VARCHAR(30) NOT NULL,
  email VARCHAR(160) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_users_login (login),
  UNIQUE KEY uq_users_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE rooms (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  title VARCHAR(160) NOT NULL,
  type ENUM('Аудитория', 'Коворкинг', 'Кинозал') NOT NULL,
  capacity INT UNSIGNED NOT NULL,
  description TEXT NOT NULL,
  PRIMARY KEY (id),
  KEY idx_rooms_type (type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE bookings (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id INT UNSIGNED NOT NULL,
  room_id INT UNSIGNED NOT NULL,
  event_date DATE NOT NULL,
  payment_method VARCHAR(80) NOT NULL,
  status ENUM('Новая', 'Мероприятие назначено', 'Мероприятие завершено') NOT NULL DEFAULT 'Новая',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_bookings_user_id (user_id),
  KEY idx_bookings_room_id (room_id),
  KEY idx_bookings_status (status),
  CONSTRAINT fk_bookings_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_bookings_room FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE reviews (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  booking_id INT UNSIGNED NOT NULL,
  user_id INT UNSIGNED NOT NULL,
  rating TINYINT UNSIGNED NOT NULL,
  text TEXT NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_reviews_booking (booking_id),
  KEY idx_reviews_user_id (user_id),
  CONSTRAINT fk_reviews_booking FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
  CONSTRAINT fk_reviews_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT chk_reviews_rating CHECK (rating BETWEEN 1 AND 5)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO rooms (id, title, type, capacity, description)
VALUES
  (1, 'Аудитория «Север»', 'Аудитория', 80, 'Помещение для докладов, секций и учебных конференций.'),
  (2, 'Коворкинг «Старт»', 'Коворкинг', 45, 'Гибкая зона для командной работы, круглых столов и переговоров.'),
  (3, 'Кинозал «Премьер»', 'Кинозал', 160, 'Большой экран, акустика и посадочные места для презентаций.')
ON DUPLICATE KEY UPDATE title = VALUES(title), type = VALUES(type), capacity = VALUES(capacity), description = VALUES(description);
