CREATE TABLE IF NOT EXISTS Orders(
    id int AUTO_INCREMENT PRIMARY  KEY,
    user_id int,
    total_price int,
    address VARCHAR(100),
    created TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)