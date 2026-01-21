-- Database setup for Virtual ID Card System
CREATE DATABASE IF NOT EXISTS virtual_id_cards;
USE virtual_id_cards;

-- Table to store card information
CREATE TABLE IF NOT EXISTS cards (
    id INT AUTO_INCREMENT PRIMARY KEY,
    card_type VARCHAR(50) NOT NULL,
    unique_id VARCHAR(100) UNIQUE NOT NULL,
    qr_code_url VARCHAR(500) NOT NULL,
    front_image_path VARCHAR(500) NOT NULL,
    back_image_path VARCHAR(500) NOT NULL,
    card_data JSON NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table to store specific card field data
CREATE TABLE IF NOT EXISTS card_fields (
    id INT AUTO_INCREMENT PRIMARY KEY,
    card_id INT NOT NULL,
    field_name VARCHAR(100) NOT NULL,
    field_value TEXT,
    FOREIGN KEY (card_id) REFERENCES cards(id) ON DELETE CASCADE
);

-- Index for faster lookups
CREATE INDEX idx_cards_unique_id ON cards(unique_id);
CREATE INDEX idx_cards_qr_url ON cards(qr_code_url);
CREATE INDEX idx_card_fields_card_id ON card_fields(card_id);

