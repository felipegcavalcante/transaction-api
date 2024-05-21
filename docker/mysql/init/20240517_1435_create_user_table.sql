-- Up
CREATE DATABASE IF NOT EXISTS transfer_api;

USE transfer_api;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    document VARCHAR(14) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    balance FLOAT NOT NULL DEFAULT 0,
    type ENUM('merchant', 'regular') NOT NULL,
    password VARCHAR(255) NOT NULL
);

INSERT INTO users (name, document, email, balance, type, password)
VALUES ("João da Silva", "68855569040", "joao.silva@example.com", 700.0, "regular", "$2y$10$lRX4JfUlE6tm9L.T/Sj6q.h.G7RJfNmGL/b4Mt3sQbhfAJNwh3BBG");

INSERT INTO users (name, document, email, balance, type, password)
VALUES ("José Souza", "79195876030", "jose.souza@example.com", 1200.0, "regular", "$2y$10$lRX4JfUlE6tm9L.T/Sj6q.h.G7RJfNmGL/b4Mt3sQbhfAJNwh3BBG");

INSERT INTO users (name, document, email, balance, type, password)
VALUES ("Pizzaria do Bosque", "63236161000107", "pizzaria.bosque@example.com", 1000.0, "merchant", "$2y$10$lRX4JfUlE6tm9L.T/Sj6q.h.G7RJfNmGL/b4Mt3sQbhfAJNwh3BBG");

-- Down
-- DROP TABLE users;
-- DROP DATABASE transfer_api;
