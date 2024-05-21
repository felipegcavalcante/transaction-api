-- Up
USE transfer_api;

CREATE TABLE IF NOT EXISTS transactions (
    uuid CHAR(36) PRIMARY KEY,
    payer_id INT NOT NULL,
    payee_id INT NOT NULL,
    value FLOAT NOT NULL CHECK (value > 0),
    occurred_at DATETIME NOT NULL,
    FOREIGN KEY (payer_id) REFERENCES users(id),
    FOREIGN KEY (payee_id) REFERENCES users(id)
);

-- Down
-- DROP TABLE transactions;
