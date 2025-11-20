
-- 1. Création de la base
DROP DATABASE IF EXISTS ecoride_php;
CREATE DATABASE ecoride_php
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE ecoride_php;


-- 2. Table des rôles
CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    label VARCHAR(50) NOT NULL
);

INSERT INTO roles (label) VALUES
('utilisateur'),
('employe'),
('admin');

-- 3. Table des utilisateurs
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pseudo VARCHAR(100) NOT NULL,
    email  VARCHAR(150) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    photo  VARCHAR(255) NULL,
    rating DECIMAL(2,1) NULL,
    preferences TEXT NULL,
    credits DECIMAL(6,2) NOT NULL DEFAULT 100,  
    role_id INT NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_users_role FOREIGN KEY (role_id) REFERENCES roles(id)
);

-- 4. Table des marques
CREATE TABLE brands (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL
);

INSERT INTO brands (name) VALUES
('Tesla'),
('Renault'),
('Peugeot');

-- 5. Table des véhicules
CREATE TABLE vehicles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    brand_id INT NOT NULL,
    model VARCHAR(100) NOT NULL,
    energy VARCHAR(50) NOT NULL,  
    color  VARCHAR(50) NULL,
    plate_number VARCHAR(20) NULL,
    first_registration DATE NULL,
    owner_id INT NOT NULL,
    CONSTRAINT fk_vehicles_brand FOREIGN KEY (brand_id) REFERENCES brands(id),
    CONSTRAINT fk_vehicles_owner FOREIGN KEY (owner_id) REFERENCES users(id)
);

-- 6. Table des covoiturages
CREATE TABLE carpools (
    id INT AUTO_INCREMENT PRIMARY KEY,
    driver_id INT NOT NULL,
    vehicle_id INT NOT NULL,

    departure_city  VARCHAR(100) NOT NULL,
    arrival_city    VARCHAR(100) NOT NULL,
    departure_datetime DATETIME NOT NULL,
    arrival_datetime   DATETIME NOT NULL,

    total_seats     INT NOT NULL,
    remaining_seats INT NOT NULL,

    price           DECIMAL(6,2) NOT NULL,
    is_electric     TINYINT(1) NOT NULL DEFAULT 0,

    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_carpools_driver  FOREIGN KEY (driver_id)  REFERENCES users(id),
    CONSTRAINT fk_carpools_vehicle FOREIGN KEY (vehicle_id) REFERENCES vehicles(id)
);

-- 7. Table des avis
CREATE TABLE reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    carpool_id INT NOT NULL,
    author_id INT NULL,
    rating DECIMAL(2,1) NOT NULL,
    comment TEXT,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_reviews_carpool FOREIGN KEY (carpool_id) REFERENCES carpools(id),
    CONSTRAINT fk_reviews_author  FOREIGN KEY (author_id)  REFERENCES users(id)
);

-- 8. Table des participations passagers
CREATE TABLE passenger_trips (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    carpool_id INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_passenger_user    FOREIGN KEY (user_id) REFERENCES users(id),
    CONSTRAINT fk_passenger_carpool FOREIGN KEY (carpool_id) REFERENCES carpools(id)
);

ALTER TABLE passenger_trips
    ADD CONSTRAINT uq_passenger_trip UNIQUE (user_id, carpool_id);

-- 9. Données de test

-- Utilisateurs (conducteurs)
INSERT INTO users (pseudo, email, password_hash, photo, rating, preferences, credits, role_id) VALUES
('MaxEco',     'maxeco@example.com',    'hash', NULL, 4.9, 'Non fumeur · Pas d’animaux · Musique OK', 100, 1),
('BudgetRide', 'budget@example.com',    'hash', NULL, 2.0, 'Fumeur accepté · Animaux OK',             50,  1),
('CityDrive',  'citydrive@example.com', 'hash', NULL, 3.5, 'Non fumeur · Animaux autorisés',          30,  1);

-- Véhicules
INSERT INTO vehicles (brand_id, model, energy, color, plate_number, first_registration, owner_id) VALUES
(1, 'Model 3', 'Électrique', 'Blanc', 'EV-123-AB', '2023-03-10', 1),
(2, 'Clio 3',  'Essence',    'Rouge', 'AB-456-CD', '2015-06-01', 2),
(3, '308',     'Diesel',     'Bleu',  'CD-789-EF', '2018-09-15', 3);

-- Covoiturages (Paris -> Lyon)
INSERT INTO carpools (
    driver_id, vehicle_id,
    departure_city, arrival_city,
    departure_datetime, arrival_datetime,
    total_seats, remaining_seats,
    price, is_electric
) VALUES
(1, 1, 'Paris', 'Lyon', '2025-11-22 08:00:00', '2025-11-22 12:00:00', 4, 2, 28.50, 1),
(2, 2, 'Paris', 'Lyon', '2025-11-22 09:00:00', '2025-11-22 13:00:00', 3, 3, 15.00, 0),
(3, 3, 'Paris', 'Lyon', '2025-11-22 10:00:00', '2025-11-22 14:30:00', 3, 1, 22.00, 0);

-- Avis
INSERT INTO reviews (carpool_id, author_id, rating, comment, created_at) VALUES
(1, NULL, 5.0, 'Trajet très agréable, conducteur ponctuel et voiture confortable.', '2025-11-10 14:00:00'),
(1, NULL, 4.5, 'Très bon covoiturage, discussion sympathique et conduite sûre.',    '2025-11-11 09:30:00'),
(2, NULL, 2.5, 'Prix intéressant mais voiture un peu ancienne, trajet correct.',     '2025-11-12 18:00:00'),
(3, NULL, 3.5, 'Trajet bien passé, conducteur sérieux, quelques embouteillages.',   '2025-11-13 08:15:00');
