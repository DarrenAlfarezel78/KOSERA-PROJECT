CREATE DATABASE IF NOT EXISTS kosera_db;
USE kosera_db;

DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS services;
DROP TABLE IF EXISTS sub_categories;
DROP TABLE IF EXISTS categories;

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE sub_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    name VARCHAR(120) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_sub_category_category
        FOREIGN KEY (category_id) REFERENCES categories(id)
        ON DELETE CASCADE,
    UNIQUE KEY uk_sub_category (category_id, name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    phone VARCHAR(30) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    sub_category_id INT NOT NULL,
    title VARCHAR(160) NOT NULL,
    description TEXT NOT NULL,
    price DECIMAL(12,2) NOT NULL,
    provider_name VARCHAR(120) NOT NULL,
    image LONGBLOB,
    certificate LONGBLOB,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_service_category
        FOREIGN KEY (category_id) REFERENCES categories(id)
        ON DELETE RESTRICT,
    CONSTRAINT fk_service_sub_category
        FOREIGN KEY (sub_category_id) REFERENCES sub_categories(id)
        ON DELETE RESTRICT,
    INDEX idx_services_category (category_id),
    INDEX idx_services_sub_category (sub_category_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO categories (name) VALUES
('Perbaikan'),
('Kebersihan'),
('Antar Jemput'),
('Lainnya');

INSERT INTO sub_categories (category_id, name) VALUES
(1, 'Perbaikan AC'),
(1, 'Perbaikan Listrik'),
(1, 'Perbaikan Pipa'),
(2, 'Bersih Kamar'),
(2, 'Cuci Setrika'),
(3, 'Antar Laundry'),
(3, 'Belanja Harian'),
(4, 'Jasa Titip');

INSERT INTO services (category_id, sub_category_id, title, description, price, provider_name) VALUES
(1, 1, 'Servis AC Kost', 'Pembersihan unit indoor/outdoor + cek freon.', 150000, 'Raka Teknik'),
(1, 2, 'Perbaikan Lampu Mati', 'Perbaikan titik lampu, fitting, dan saklar.', 85000, 'Bima Elektro'),
(2, 4, 'Cleaning Kamar Harian', 'Bersih kamar + pel lantai + buang sampah.', 50000, 'Nina Clean'),
(2, 5, 'Cuci Setrika Express', 'Cuci + setrika selesai di hari yang sama.', 30000, 'Laundry Maju'),
(3, 6, 'Antar Laundry Pulang-Pergi', 'Jemput dan antar laundry ke tempat tujuan.', 20000, 'Ari Kurir'),
(3, 7, 'Belanja Kebutuhan Anak Kos', 'Belanja kebutuhan harian sesuai list.', 25000, 'Doni Helper');

INSERT INTO users (name, phone, email, password_hash) VALUES
('Admin KOSERA', '081234567890', 'admin@kosera.local', '$2y$10$x0eDzBEHlkCVRFIxvk69w.N/GNCiUhGKfKGhnjM4rIUonhJVnq3VK');
