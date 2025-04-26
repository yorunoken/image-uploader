DROP TABLE IF EXISTS images;
DROP TABLE IF EXISTS users;

CREATE TABLE IF NOT EXISTS users (
    id INT NOT NULL AUTO_INCREMENT,
    username VARCHAR(45) NOT NULL,
    password VARCHAR(255) NOT NULL,
    login_token VARCHAR(255),
    PRIMARY KEY (id)
);

CREATE TABLE IF NOT EXISTS images (
    id int AUTO_INCREMENT,
    file_name VARCHAR(255),
    original_name VARCHAR(255),
    user_id INT DEFAULT NULL,
    upload_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    views INT DEFAULT 0,
    is_public BOOL DEFAULT TRUE,
    PRIMARY KEY (id),
    FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE SET NULL
);