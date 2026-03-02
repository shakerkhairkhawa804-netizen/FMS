CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL
);
CREATE TABLE users(
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE expenses(
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    category_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    description VARCHAR(255),
    expense_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (category_id) REFERENCES categories(id)
);
CREATE TABLE incomes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    category_id INT NOT NULL,          
    amount DECIMAL(10,2) NOT NULL,
    description VARCHAR(255),
    income_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (category_id) REFERENCES categories(id)
);
CREATE TABLE clients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    email VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
ALTER TABLE incomes ADD COLUMN client_id INT NULL;
ALTER TABLE incomes ADD FOREIGN KEY (client_id) REFERENCES clients(id);
CREATE TABLE receipts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    receipt_date DATE NOT NULL,
    description VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES clients(id)
);
ALTER TABLE expenses ADD COLUMN client_id INT NULL;
ALTER TABLE expenses ADD FOREIGN KEY (client_id) REFERENCES clients(id);
CREATE TABLE languages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(10) NOT NULL UNIQUE,   -- ps, fa, en
    name VARCHAR(50) NOT NULL,
    direction ENUM('ltr','rtl') DEFAULT 'ltr',
    status TINYINT DEFAULT 1
);
INSERT INTO languages (code, name, direction) VALUES
('ps', 'Pashto', 'rtl'),
('fa', 'Dari', 'rtl'),
('en', 'English', 'ltr');
CREATE TABLE translations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    lang_code VARCHAR(10) NOT NULL,
    trans_key VARCHAR(100) NOT NULL,
    trans_value TEXT NOT NULL,
    UNIQUE (lang_code, trans_key)
);
-- Pashto
INSERT INTO translations (lang_code, trans_key, trans_value) VALUES
('ps', 'dashboard', 'تشبورډ'),
('ps', 'login', 'ننوتل'),
('ps', 'logout', 'وتل');

-- English
INSERT INTO translations (lang_code, trans_key, trans_value) VALUES
('en', 'dashboard', 'Dashboard'),
('en', 'login', 'Login'),
('en', 'logout', 'Logout');
CREATE TABLE client_payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_date DATE NOT NULL,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
